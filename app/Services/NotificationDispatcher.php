<?php

namespace App\Services;

use App\Models\NotificationLog;
use App\Models\NotificationTemplate;
use App\Models\ScheduledNotification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NotificationDispatcher
{
    /**
     * Dispatch a notification from a template to a set of users.
     */
    public function dispatchFromTemplate(NotificationTemplate $template, array $userIds, array $variables = []): int
    {
        $sentCount = 0;
        $users = User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            try {
                $body = $this->interpolate($template->body_html ?? $template->body ?? '', array_merge($variables, [
                    'name'       => $user->name,
                    'email'      => $user->email,
                    'tenant'     => optional(app()->bound('currentTenant') ? app('currentTenant') : null)->name ?? 'Institution',
                ]));

                $this->sendViaChannel($template->channel, $user, $template->subject, $body);

                NotificationLog::create([
                    'tenant_id'   => $template->tenant_id,
                    'template_id' => $template->id,
                    'user_id'     => $user->id,
                    'channel'     => $template->channel,
                    'status'      => 'SENT',
                    'sent_at'     => now(),
                ]);

                $sentCount++;
            } catch (\Throwable $e) {
                Log::error('NotificationDispatcher: Failed to send to user ' . $user->id, ['error' => $e->getMessage()]);

                NotificationLog::create([
                    'tenant_id'    => $template->tenant_id,
                    'template_id'  => $template->id,
                    'user_id'      => $user->id,
                    'channel'      => $template->channel,
                    'status'       => 'FAILED',
                    'error_message'=> $e->getMessage(),
                ]);
            }
        }

        return $sentCount;
    }

    /**
     * Dispatch a scheduled notification.
     */
    public function dispatchScheduled(ScheduledNotification $scheduled): int
    {
        $scheduled->update(['status' => 'PROCESSING']);
        $tenantId = $scheduled->tenant_id;

        $users = $this->resolveAudience($tenantId, $scheduled->audience, $scheduled->audience_filter ?? []);
        $sentCount = 0;

        foreach ($users as $user) {
            try {
                $body = $this->interpolate($scheduled->body, [
                    'name'  => $user->name,
                    'email' => $user->email,
                ]);

                $this->sendViaChannel($scheduled->channel, $user, $scheduled->subject, $body);

                NotificationLog::create([
                    'tenant_id'  => $tenantId,
                    'template_id'=> $scheduled->template_id,
                    'user_id'    => $user->id,
                    'channel'    => $scheduled->channel,
                    'status'     => 'SENT',
                    'sent_at'    => now(),
                ]);

                $sentCount++;
            } catch (\Throwable $e) {
                Log::error('Scheduled dispatch failed for user ' . $user->id, ['error' => $e->getMessage()]);
            }
        }

        $scheduled->update(['status' => $scheduled->is_recurring ? 'PENDING' : 'SENT']);
        return $sentCount;
    }

    /**
     * Quick ad-hoc dispatch without a template.
     */
    public function dispatchAdHoc(string $tenantId, string $channel, string $audience, string $subject, string $body, array $filter = []): int
    {
        $users = $this->resolveAudience($tenantId, $audience, $filter);
        $sentCount = 0;

        foreach ($users as $user) {
            try {
                $interpolated = $this->interpolate($body, ['name' => $user->name, 'email' => $user->email]);
                $this->sendViaChannel($channel, $user, $subject, $interpolated);

                NotificationLog::create([
                    'tenant_id' => $tenantId,
                    'user_id'   => $user->id,
                    'channel'   => $channel,
                    'status'    => 'SENT',
                    'sent_at'   => now(),
                ]);

                $sentCount++;
            } catch (\Throwable $e) {
                Log::error('AdHoc notification failed', ['user' => $user->id, 'error' => $e->getMessage()]);
            }
        }

        return $sentCount;
    }

    // ── Internals ─────────────────────────────────────────────────────────────

    private function sendViaChannel(string $channel, User $user, string $subject, string $body): void
    {
        match ($channel) {
            'email' => $this->sendEmail($user, $subject, $body),
            'sms'   => $this->sendSms($user, $body),
            'push'  => $this->sendPush($user, $subject, $body),
            default => $this->sendInApp($user, $subject, $body), // in_app
        };
    }

    private function sendEmail(User $user, string $subject, string $body): void
    {
        // In production: use a proper Mailable class
        // Mail::to($user->email)->send(new GenericNotificationMail($subject, $body));
        Log::info("[EMAIL] To: {$user->email} | Subject: {$subject}");
    }

    private function sendSms(User $user, string $body): void
    {
        // Integrate with Twilio / Vonage / Pakistan SMS Gateway
        Log::info("[SMS] To: {$user->phone} | Body: " . Str::limit($body, 50));
    }

    private function sendPush(User $user, string $subject, string $body): void
    {
        // Integrate with FCM / APNs
        Log::info("[PUSH] To user: {$user->id} | Title: {$subject}");
    }

    private function sendInApp(User $user, string $subject, string $body): void
    {
        // Stored in notification_logs; picked up by frontend polling or WebSocket
        Log::info("[IN-APP] To user: {$user->id} | Subject: {$subject}");
    }

    private function interpolate(string $template, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $template = str_replace('{{' . $key . '}}', (string)$value, $template);
            $template = str_replace('{{ ' . $key . ' }}', (string)$value, $template);
        }
        return $template;
    }

    private function resolveAudience(string $tenantId, string $audience, array $filter): \Illuminate\Database\Eloquent\Collection
    {
        $query = User::where('tenant_id', $tenantId)->where('is_active', true);

        match ($audience) {
            'students' => $query->whereHas('roles', fn($q) => $q->where('name', 'Student')),
            'teachers' => $query->whereHas('roles', fn($q) => $q->where('name', 'Trainer')),
            'parents'  => $query->whereHas('roles', fn($q) => $q->where('name', 'Parent')),
            'admins'   => $query->whereHas('roles', fn($q) => $q->whereIn('name', ['Tenant Admin', 'Super Admin'])),
            default    => null, // 'all' — no filter
        };

        return $query->limit(2000)->get();
    }
}
