<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\Certificate;
use App\Models\CertificateVerification;

class PublicVerification extends Component
{
    public string $code = '';
    public ?Certificate $certificate = null;
    public bool $searched = false;
    public bool $verified = false;

    public function mount($code = null)
    {
        if ($code) {
            $this->code = $code;
            $this->verify();
        }
    }

    public function verify()
    {
        $this->validate([
            'code' => 'required|string|max:100',
        ]);

        $this->searched = true;
        
        // Find certificate (globally, ignore tenant scoping for public verification if necessary, but since public portal has identified tenants, we lookup within currentTenant)
        $tenant = app('currentTenant');
        if ($tenant) {
            $this->certificate = Certificate::where('tenant_id', $tenant->id)
                ->where('certificate_code', trim($this->code))
                ->with(['user', 'course'])
                ->first();
        } else {
            $this->certificate = Certificate::where('certificate_code', trim($this->code))
                ->with(['user', 'course'])
                ->first();
        }

        if ($this->certificate) {
            $this->verified = true;

            // Log verification audit record
            CertificateVerification::create([
                'tenant_id' => $this->certificate->tenant_id,
                'certificate_id' => $this->certificate->id,
                'verified_at' => now(),
                'ip_address' => request()->ip(),
                'browser' => request()->userAgent(),
                'status' => 'SUCCESS',
            ]);
        } else {
            $this->verified = false;
        }
    }

    public function render()
    {
        return view('livewire.auth.public-verification')
            ->layout('layouts.portal'); // Uses clean public card layout
    }
}
