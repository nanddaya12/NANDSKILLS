<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Message;
use App\Models\Announcement;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;

class CommunicationHub extends Component
{
    public $contacts = [];
    public $selectedContactId = null;
    public $messages = [];
    public string $messageText = '';

    // Announcement Broadcast Builder fields
    public string $announcementTitle = '';
    public string $announcementContent = '';
    public array $targetRoles = ['Student'];
    public ?string $selectedBranch = null;

    // View data
    public $announcements = [];
    public $branches = [];
    public string $activeTab = 'messages'; // 'messages', 'announcements', 'broadcast'

    public function mount()
    {
        $this->loadContacts();
        $this->loadAnnouncements();
        $this->branches = Branch::all();

        // Default select first contact if available
        if (!empty($this->contacts)) {
            $this->selectContact($this->contacts[0]['id']);
        }
    }

    public function loadContacts()
    {
        $user = auth()->user();
        if (!$user) return;

        // Based on user role, match appropriate contacts in the active tenant
        // To keep it simple and vast, we load users of the tenant and identify their roles
        $tenantUsers = User::with('roles')
            ->where('id', '!=', $user->id)
            ->get();

        $this->contacts = [];
        foreach ($tenantUsers as $u) {
            $roles = $u->roles->pluck('name')->toArray();
            
            // Check matching rules
            $canMessage = false;
            if ($user->hasRole(['Super Admin', 'Tenant Admin'])) {
                $canMessage = true; // Admin can message anyone
            } elseif ($user->hasRole('Trainer')) {
                // Trainer can message students and admins
                if (in_array('Student', $roles) || in_array('Tenant Admin', $roles) || in_array('Super Admin', $roles)) {
                    $canMessage = true;
                }
            } elseif ($user->hasRole('Student')) {
                // Student can message trainers and admins
                if (in_array('Trainer', $roles) || in_array('Tenant Admin', $roles) || in_array('Super Admin', $roles)) {
                    $canMessage = true;
                }
            } elseif ($user->hasRole('Parent')) {
                // Parent can message trainers and admins
                if (in_array('Trainer', $roles) || in_array('Tenant Admin', $roles) || in_array('Super Admin', $roles)) {
                    $canMessage = true;
                }
            }

            if ($canMessage) {
                $this->contacts[] = [
                    'id' => $u->id,
                    'name' => $u->first_name . ' ' . $u->last_name,
                    'email' => $u->email,
                    'role' => !empty($roles) ? $roles[0] : 'User',
                ];
            }
        }
    }

    public function loadAnnouncements()
    {
        $user = auth()->user();
        if (!$user) return;

        $userRoles = $user->roles->pluck('name')->toArray();

        // Fetch announcements targetting active user's roles
        $this->announcements = Announcement::with('creator')
            ->where(function ($query) use ($userRoles) {
                foreach ($userRoles as $role) {
                    $query->orWhereJsonContains('target_roles', $role);
                }
                // Also allow administrators to see all
                if (in_array('Tenant Admin', $userRoles) || in_array('Super Admin', $userRoles)) {
                    $query->orWhereNotNull('target_roles');
                }
            })
            ->latest()
            ->get();
    }

    public function selectContact($contactId)
    {
        $this->selectedContactId = $contactId;
        $this->loadMessages();
    }

    public function loadMessages()
    {
        if (!$this->selectedContactId) return;

        $userId = auth()->id();
        $contactId = $this->selectedContactId;

        // Fetch messages between these two users
        $this->messages = Message::where(function ($query) use ($userId, $contactId) {
                $query->where('sender_id', $userId)->where('receiver_id', $contactId);
            })
            ->orWhere(function ($query) use ($userId, $contactId) {
                $query->where('sender_id', $contactId)->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark incoming messages as read
        Message::where('sender_id', $contactId)
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function sendMessage()
    {
        $this->validate([
            'messageText' => 'required|string|max:1000',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        if (!$tenantId || !$this->selectedContactId) return;

        Message::create([
            'tenant_id' => $tenantId,
            'sender_id' => auth()->id(),
            'receiver_id' => $this->selectedContactId,
            'content' => $this->messageText,
            'is_read' => false,
        ]);

        $this->messageText = '';
        $this->loadMessages();
    }

    public function broadcastAnnouncement()
    {
        $this->validate([
            'announcementTitle' => 'required|string|max:200',
            'announcementContent' => 'required|string|max:5000',
            'targetRoles' => 'required|array',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        if (!$tenantId) return;

        Announcement::create([
            'tenant_id' => $tenantId,
            'branch_id' => $this->selectedBranch ?: null,
            'title' => $this->announcementTitle,
            'content' => $this->announcementContent,
            'target_roles' => $this->targetRoles,
            'created_by_id' => auth()->id(),
        ]);

        $this->announcementTitle = '';
        $this->announcementContent = '';
        $this->targetRoles = ['Student'];
        $this->selectedBranch = null;

        session()->flash('success', 'Announcement published and broadcast successfully.');
        $this->loadAnnouncements();
        $this->activeTab = 'announcements';
    }

    public function render()
    {
        return view('livewire.dashboard.communication-hub')
            ->layout('layouts.app');
    }
}
