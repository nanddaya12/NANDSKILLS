<?php

namespace App\Livewire\Helpdesk;

use Livewire\Component;
use App\Models\HelpdeskTicket;
use App\Models\TicketCategory;
use App\Models\TicketComment;
use Illuminate\Support\Str;

class TicketDashboard extends Component
{
    public $tickets = [];
    public $categories = [];
    public ?HelpdeskTicket $selectedTicket = null;
    
    // Form fields
    public string $subject = '';
    public string $description = '';
    public string $priority = 'MEDIUM';
    public string $selectedCategory = '';
    public string $commentMessage = '';
    public bool $isCreating = false;

    protected array $rules = [
        'subject' => 'required|string|max:200',
        'description' => 'required|string',
        'priority' => 'required|in:LOW,MEDIUM,HIGH,URGENT',
        'selectedCategory' => 'required|exists:ticket_categories,id',
    ];

    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin', 'Trainer'])) {
            abort(403, 'Unauthorized action. Helpdesk Dashboard is restricted.');
        }
        $this->loadTickets();
    }

    public function loadTickets()
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        if ($tenantId) {
            // Seed sample category if empty
            if (TicketCategory::count() === 0) {
                TicketCategory::create([
                    'tenant_id' => $tenantId,
                    'name' => 'LMS Course Support',
                    'description' => 'Help with course access or player bugs.',
                ]);
                TicketCategory::create([
                    'tenant_id' => $tenantId,
                    'name' => 'Billing & Payment Support',
                    'description' => 'Help with invoices and payment gateways.',
                ]);
            }
        }

        $this->tickets = HelpdeskTicket::with('requester', 'category')->orderBy('created_at', 'desc')->get();
        $this->categories = TicketCategory::all();
    }

    public function createTicket()
    {
        $this->validate();

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        if ($tenantId) {
            HelpdeskTicket::create([
                'tenant_id' => $tenantId,
                'requester_id' => auth()->id(),
                'subject' => $this->subject,
                'description' => $this->description,
                'priority' => $this->priority,
                'category_id' => $this->selectedCategory,
                'status' => 'OPEN',
                'sla_deadline' => now()->addHours(24),
            ]);
        }

        $this->resetInputFields();
        $this->loadTickets();
        $this->isCreating = false;
    }

    public function viewTicket(string $ticketId)
    {
        $this->selectedTicket = HelpdeskTicket::with('requester', 'category', 'comments.user')->find($ticketId);
    }

    public function addComment()
    {
        if (empty($this->commentMessage) || !$this->selectedTicket) {
            return;
        }

        TicketComment::create([
            'ticket_id' => $this->selectedTicket->id,
            'user_id' => auth()->id(),
            'message' => $this->commentMessage,
            'is_internal' => false,
        ]);

        $this->commentMessage = '';
        $this->viewTicket($this->selectedTicket->id);
    }

    public function closeTicket(string $ticketId)
    {
        $ticket = HelpdeskTicket::find($ticketId);
        if ($ticket) {
            $ticket->update(['status' => 'CLOSED']);
        }
        $this->loadTickets();
        if ($this->selectedTicket && $this->selectedTicket->id === $ticketId) {
            $this->viewTicket($ticketId);
        }
    }

    private function resetInputFields()
    {
        $this->subject = '';
        $this->description = '';
        $this->priority = 'MEDIUM';
        $this->selectedCategory = '';
    }

    public function render()
    {
        return view('livewire.helpdesk.ticket-dashboard')
            ->layout('layouts.app');
    }
}
