<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Tenant;

class WhiteLabelSettings extends Component
{
    public ?Tenant $tenant = null;
    public string $name = '';
    public string $primary_color = '';
    public string $secondary_color = '';
    public string $typography = 'Inter';
    public string $dashboard_theme = 'light';
    public string $logo_url = '';
    public string $favicon_url = '';
    public string $custom_domain = '';
    public string $custom_css = '';
    public string $email_header = '';
    public string $email_footer = '';
    public string $successMessage = '';

    protected array $rules = [
        'name' => 'required|string|max:100',
        'primary_color' => 'required|regex:/^#[a-fA-F0-9]{6}$/',
        'secondary_color' => 'required|regex:/^#[a-fA-F0-9]{6}$/',
        'typography' => 'required|string|in:Inter,Outfit,Roboto,Montserrat,Poppins',
        'dashboard_theme' => 'required|in:light,dark',
        'logo_url' => 'nullable|url',
        'favicon_url' => 'nullable|url',
        'custom_domain' => 'nullable|string|max:255',
        'custom_css' => 'nullable|string',
        'email_header' => 'nullable|string',
        'email_footer' => 'nullable|string',
    ];

    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin'])) {
            abort(403, 'Unauthorized action. White-label settings are restricted to Administrators.');
        }

        $this->tenant = app()->bound('currentTenant') ? app('currentTenant') : null;

        if ($this->tenant) {
            $this->name = $this->tenant->name;
            $this->primary_color = $this->tenant->primary_color;
            $this->secondary_color = $this->tenant->secondary_color;
            $this->typography = $this->tenant->typography;
            $this->dashboard_theme = $this->tenant->dashboard_theme;
            $this->logo_url = $this->tenant->logo_url ?? '';
            $this->favicon_url = $this->tenant->favicon_url ?? '';
            $this->custom_domain = $this->tenant->custom_domain ?? '';

            // Decode email template to load builder data and custom css overrides
            $templateData = [];
            if ($this->tenant->email_template) {
                $templateData = json_decode($this->tenant->email_template, true) ?: [];
            }
            $this->custom_css = $templateData['custom_css'] ?? '';
            $this->email_header = $templateData['email_header'] ?? '';
            $this->email_footer = $templateData['email_footer'] ?? '';
        }
    }

    public function saveBranding()
    {
        $this->validate();

        if ($this->tenant) {
            $templateData = [
                'custom_css' => $this->custom_css,
                'email_header' => $this->email_header,
                'email_footer' => $this->email_footer,
            ];

            $this->tenant->update([
                'name' => $this->name,
                'primary_color' => $this->primary_color,
                'secondary_color' => $this->secondary_color,
                'typography' => $this->typography,
                'dashboard_theme' => $this->dashboard_theme,
                'logo_url' => $this->logo_url ?: null,
                'favicon_url' => $this->favicon_url ?: null,
                'custom_domain' => $this->custom_domain ?: null,
                'email_template' => json_encode($templateData),
            ]);

            $this->successMessage = 'Branding settings updated successfully.';
        }
    }

    public function render()
    {
        return view('livewire.admin.white-label-settings')
            ->layout('layouts.app');
    }
}
