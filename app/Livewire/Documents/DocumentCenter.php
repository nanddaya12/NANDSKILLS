<?php

namespace App\Livewire\Documents;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\DocumentCategory;
use App\Models\ManagedDocument;
use App\Models\ManagedDocumentVersion;
use App\Models\ManagedDocumentApproval;
use App\Models\DocumentAuditTrail;
use Illuminate\Support\Facades\Storage;

class DocumentCenter extends Component
{
    use WithFileUploads;

    public $categories = [];
    public $documents = [];
    public $selectedDocument = null;

    // Creation fields
    public bool $isFormOpen = false;
    public string $selectedCategoryId = '';
    public string $title = '';
    public string $description = '';
    public $fileUpload;
    public string $owner_type = 'tenant'; // student, staff, admission, tenant
    public string $expiry_date = '';
    public bool $is_public = false;

    // Folder creation
    public bool $isFolderFormOpen = false;
    public string $new_folder_name = '';
    public string $new_folder_icon = '📄';

    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin', 'Trainer'])) {
            abort(403, 'Document Center is restricted.');
        }

        $this->loadData();
    }

    public function loadData()
    {
        $this->categories = DocumentCategory::orderBy('sort_order')->get();
        $this->documents = ManagedDocument::with('category', 'uploader', 'versions')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function selectDocument(string $id)
    {
        $this->selectedDocument = ManagedDocument::with('versions', 'approvals', 'auditTrails.user')->findOrFail($id);
    }

    public function saveFolder()
    {
        $this->validate([
            'new_folder_name' => 'required|string|max:100',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        DocumentCategory::create([
            'tenant_id' => $tenantId,
            'name' => $this->new_folder_name,
            'icon' => $this->new_folder_icon,
        ]);

        $this->isFolderFormOpen = false;
        $this->new_folder_name = '';
        $this->loadData();
    }

    public function saveDocument()
    {
        $this->validate([
            'title' => 'required|string|max:150',
            'fileUpload' => 'required|file|max:10240', // 10MB limit
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        // Store file
        $filePath = $this->fileUpload->store('managed_documents', 'local');

        $doc = ManagedDocument::create([
            'tenant_id' => $tenantId,
            'category_id' => $this->selectedCategoryId ?: null,
            'owner_type' => $this->owner_type,
            'title' => $this->title,
            'description' => $this->description,
            'file_path' => $filePath,
            'original_filename' => $this->fileUpload->getClientOriginalName(),
            'file_size' => $this->fileUpload->getSize(),
            'mime_type' => $this->fileUpload->getMimeType(),
            'disk' => 'local',
            'version' => 1,
            'status' => 'PENDING_APPROVAL',
            'expiry_date' => $this->expiry_date ?: null,
            'uploaded_by' => auth()->id(),
            'is_public' => $this->is_public,
        ]);

        // Version record
        ManagedDocumentVersion::create([
            'document_id' => $doc->id,
            'version_no' => 1,
            'file_path' => $filePath,
            'file_size' => $this->fileUpload->getSize(),
            'uploaded_by' => auth()->id(),
            'change_notes' => 'Initial upload.',
        ]);

        // Audit entry
        DocumentAuditTrail::create([
            'document_id' => $doc->id,
            'user_id' => auth()->id(),
            'action' => 'UPLOADED',
            'ip_address' => request()->ip(),
            'details' => 'Initial document uploaded: ' . $doc->original_filename,
        ]);

        $this->isFormOpen = false;
        $this->reset(['title', 'description', 'fileUpload', 'selectedCategoryId']);
        $this->loadData();
    }

    public function approveDocument(string $id)
    {
        $doc = ManagedDocument::findOrFail($id);
        $doc->update(['status' => 'APPROVED']);

        ManagedDocumentApproval::create([
            'document_id' => $doc->id,
            'approver_user_id' => auth()->id(),
            'status' => 'APPROVED',
            'actioned_at' => now(),
            'notes' => 'Approved by administrator.',
        ]);

        DocumentAuditTrail::create([
            'document_id' => $doc->id,
            'user_id' => auth()->id(),
            'action' => 'APPROVED',
            'ip_address' => request()->ip(),
        ]);

        $this->loadData();
        if ($this->selectedDocument && $this->selectedDocument->id === $id) {
            $this->selectDocument($id);
        }
    }

    public function rejectDocument(string $id, string $notes)
    {
        $doc = ManagedDocument::findOrFail($id);
        $doc->update(['status' => 'REJECTED']);

        ManagedDocumentApproval::create([
            'document_id' => $doc->id,
            'approver_user_id' => auth()->id(),
            'status' => 'REJECTED',
            'actioned_at' => now(),
            'notes' => $notes ?: 'Rejected by administrator.',
        ]);

        DocumentAuditTrail::create([
            'document_id' => $doc->id,
            'user_id' => auth()->id(),
            'action' => 'REJECTED',
            'ip_address' => request()->ip(),
            'details' => 'Rejected notes: ' . $notes,
        ]);

        $this->loadData();
        if ($this->selectedDocument && $this->selectedDocument->id === $id) {
            $this->selectDocument($id);
        }
    }

    public function deleteDocument(string $id)
    {
        $doc = ManagedDocument::findOrFail($id);
        Storage::disk($doc->disk)->delete($doc->file_path);
        $doc->delete();

        $this->selectedDocument = null;
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.documents.document-center')
            ->layout('layouts.app');
    }
}
