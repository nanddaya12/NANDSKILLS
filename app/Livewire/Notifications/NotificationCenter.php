<?php

namespace App\Livewire\Notifications;

use Livewire\Component;
use App\Models\NotificationCategory;
use App\Models\NotificationTemplate;
use App\Models\ScheduledNotification;
use App\Models\NotificationLog;

class NotificationCenter extends Component
{
    // Active tabs: 'templates', 'scheduler', 'logs', 'categories'
    public string $activeTab = 'templates';

    public $categories = [];
    public $templates = [];
    public $scheduledList = [];
    public $deliveryLogs = [];

    // Category form
    public bool $isCategoryFormOpen = false;
    public string $cat_name = '';
    public string $cat_icon = '🔔';
    public string $cat_color = '#3B82F6';

    // Template form
    public bool $isTemplateFormOpen = false;
    public string $template_id = '';
    public string $selectedCategoryId = '';
    public string $temp_name = '';
    public string $temp_subject = '';
    public string $temp_body = '';
    public string $temp_body_sms = '';
    public string $temp_channel = 'in_app'; // in_app, email, sms, push
    public string $temp_audience = 'all'; // students, teachers, parents, all

    // Scheduler Form
    public bool $isSchedulerFormOpen = false;
    public string $sched_template_id = '';
    public string $sched_subject = '';
    public string $sched_body = '';
    public string $sched_channel = 'in_app';
    public string $sched_audience = 'all';
    public string $sched_at = '';
    public bool $sched_is_recurring = false;
    public string $sched_cron = '';

    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin'])) {
            abort(403, 'Notification Configuration is restricted.');
        }

        $this->loadData();
    }

    public function loadData()
    {
        $this->categories = NotificationCategory::all();
        $this->templates = NotificationTemplate::with('category')->get();
        $this->scheduledList = ScheduledNotification::with('template', 'creator')->orderBy('scheduled_at', 'desc')->get();
        $this->deliveryLogs = NotificationLog::with('template', 'user')
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
        $this->resetForms();
    }

    private function resetForms()
    {
        $this->isCategoryFormOpen = false;
        $this->isTemplateFormOpen = false;
        $this->isSchedulerFormOpen = false;

        $this->cat_name = '';
        $this->cat_icon = '🔔';
        $this->cat_color = '#3B82F6';

        $this->template_id = '';
        $this->selectedCategoryId = '';
        $this->temp_name = '';
        $this->temp_subject = '';
        $this->temp_body = '';
        $this->temp_body_sms = '';
        $this->temp_channel = 'in_app';
        $this->temp_audience = 'all';

        $this->sched_template_id = '';
        $this->sched_subject = '';
        $this->sched_body = '';
        $this->sched_channel = 'in_app';
        $this->sched_audience = 'all';
        $this->sched_at = '';
        $this->sched_is_recurring = false;
        $this->sched_cron = '';
    }

    public function saveCategory()
    {
        $this->validate([
            'cat_name' => 'required|string|max:100',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        NotificationCategory::create([
            'tenant_id' => $tenantId,
            'name' => $this->cat_name,
            'icon' => $this->cat_icon,
            'color' => $this->cat_color,
        ]);

        $this->isCategoryFormOpen = false;
        $this->cat_name = '';
        $this->loadData();
    }

    public function saveTemplate()
    {
        $this->validate([
            'temp_name' => 'required|string|max:100',
            'temp_subject' => 'required|string|max:150',
            'temp_body' => 'required|string',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        NotificationTemplate::updateOrCreate(
            ['id' => $this->template_id ?: null],
            [
                'tenant_id' => $tenantId,
                'category_id' => $this->selectedCategoryId ?: null,
                'name' => $this->temp_name,
                'subject' => $this->temp_subject,
                'body' => $this->temp_body,
                'body_html' => $this->temp_body, // Align both body/body_html
                'body_sms' => $this->temp_body_sms ?: null,
                'channel' => $this->temp_channel,
                'audience' => $this->temp_audience,
                'is_active' => true,
            ]
        );

        $this->isTemplateFormOpen = false;
        $this->loadData();
    }

    public function editTemplate(string $id)
    {
        $t = NotificationTemplate::findOrFail($id);
        $this->template_id = $t->id;
        $this->selectedCategoryId = $t->category_id ?: '';
        $this->temp_name = $t->name;
        $this->temp_subject = $t->subject;
        $this->temp_body = $t->body;
        $this->temp_body_sms = $t->body_sms ?: '';
        $this->temp_channel = $t->channel;
        $this->temp_audience = $t->audience;

        $this->isTemplateFormOpen = true;
    }

    public function toggleTemplate(string $id)
    {
        $t = NotificationTemplate::findOrFail($id);
        $t->update(['is_active' => !$t->is_active]);
        $this->loadData();
    }

    public function deleteTemplate(string $id)
    {
        NotificationTemplate::destroy($id);
        $this->loadData();
    }

    public function saveScheduledNotification()
    {
        $this->validate([
            'sched_subject' => 'required|string|max:150',
            'sched_body' => 'required|string',
            'sched_at' => 'required',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        ScheduledNotification::create([
            'tenant_id' => $tenantId,
            'template_id' => $this->sched_template_id ?: null,
            'subject' => $this->sched_subject,
            'body' => $this->sched_body,
            'channel' => $this->sched_channel,
            'audience' => $this->sched_audience,
            'scheduled_at' => $this->sched_at,
            'is_recurring' => $this->sched_is_recurring,
            'cron_expression' => $this->sched_is_recurring ? $this->sched_cron : null,
            'status' => 'PENDING',
            'created_by' => auth()->id(),
        ]);

        $this->isSchedulerFormOpen = false;
        $this->loadData();
    }

    public function deleteScheduled(string $id)
    {
        ScheduledNotification::destroy($id);
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.notifications.notification-center')
            ->layout('layouts.app');
    }
}
