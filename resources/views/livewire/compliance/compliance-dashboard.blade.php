<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">🏛️ Accreditation & Compliance Center</h1>
            <p class="text-sm text-gray-500 mt-1">Manage compliance frameworks, requirements, evidence & audits.</p>
        </div>
        <div class="text-right">
            <div class="text-3xl font-bold text-{{ $overallPct >= 75 ? 'green' : ($overallPct >= 50 ? 'yellow' : 'red') }}-600">{{ $overallPct }}%</div>
            <div class="text-xs text-gray-400">Overall Compliance ({{ $metReqs }}/{{ $totalReqs }} met)</div>
        </div>
    </div>

    {{-- Progress bar --}}
    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
        <div class="h-3 rounded-full transition-all {{ $overallPct >= 75 ? 'bg-green-500' : ($overallPct >= 50 ? 'bg-yellow-400' : 'bg-red-500') }}"
             style="width: {{ $overallPct }}%"></div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 border-b border-gray-200 dark:border-gray-700 flex-wrap">
        @foreach([
            'frameworks'   => '🏷️ Frameworks',
            'requirements' => '📋 Requirements',
            'evidence'     => '📎 Evidence',
            'audits'       => '🔍 Audits',
            'actions'      => '⚡ Corrective Actions',
        ] as $tab => $label)
        <button wire:click="setTab('{{ $tab }}')"
            class="px-4 py-2 text-sm font-medium border-b-2 transition {{ $activeTab === $tab ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- ── FRAMEWORKS ─────────────────────────────────────────────────── --}}
    @if($activeTab === 'frameworks')
    <div class="flex justify-end">
        <button wire:click="$set('isFrameworkFormOpen', true)"
            class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 font-medium transition">
            + Add Framework
        </button>
    </div>

    @if($isFrameworkFormOpen)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-blue-200 dark:border-blue-800">
        <h3 class="font-semibold text-gray-800 dark:text-white mb-4">{{ $framework_id ? 'Edit' : 'New' }} Compliance Framework</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Framework Name *</label>
                <input wire:model="fw_name" type="text" placeholder="e.g. HEC Accreditation"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Accreditation Agency</label>
                <input wire:model="fw_agency" type="text" placeholder="HEC, ISO, PEC, NCEAC…"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Standard Version</label>
                <input wire:model="fw_version" type="text" placeholder="v3.0, 2024 edition…"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Status</label>
                <select wire:model="fw_status"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="ACTIVE">Active</option>
                    <option value="PENDING">Pending</option>
                    <option value="EXPIRED">Expired</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Valid From</label>
                <input wire:model="fw_valid_from" type="date"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Valid Until</label>
                <input wire:model="fw_valid_until" type="date"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Description</label>
                <textarea wire:model="fw_description" rows="2" placeholder="Brief description of the framework…"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
            </div>
        </div>
        <div class="flex gap-3 mt-4">
            <button wire:click="saveFramework" class="px-5 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 font-medium transition">
                Save Framework
            </button>
            <button wire:click="$set('isFrameworkFormOpen', false)" class="px-5 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm rounded-lg hover:bg-gray-200 font-medium transition">
                Cancel
            </button>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($frameworks as $fw)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 border border-gray-100 dark:border-gray-700 hover:shadow-md transition">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $fw->name }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $fw->agency }} {{ $fw->standard_version }}</p>
                </div>
                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $fw->status === 'ACTIVE' ? 'bg-green-100 text-green-700' : ($fw->status === 'EXPIRED' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                    {{ $fw->status }}
                </span>
            </div>
            <div class="text-sm text-gray-500 mb-3">{{ $fw->requirements_count }} requirements</div>
            @if($fw->valid_until)
            <div class="text-xs text-gray-400 mb-3">Valid until: {{ $fw->valid_until }}</div>
            @endif
            <div class="flex gap-2">
                <button wire:click="editFramework('{{ $fw->id }}')"
                    class="flex-1 text-center text-xs py-1.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 rounded-lg hover:bg-blue-100 transition font-medium">
                    ✏️ Edit
                </button>
                <button wire:click="$set('req_framework_id', '{{ $fw->id }}'); $set('activeTab', 'requirements'); $set('isRequirementFormOpen', true)"
                    class="flex-1 text-center text-xs py-1.5 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 rounded-lg hover:bg-emerald-100 transition font-medium">
                    + Req
                </button>
                <button wire:click="deleteFramework('{{ $fw->id }}')" onclick="return confirm('Delete this framework?')"
                    class="text-xs py-1.5 px-3 bg-red-50 dark:bg-red-900/30 text-red-600 rounded-lg hover:bg-red-100 transition font-medium">
                    🗑️
                </button>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center text-gray-400 py-12">
            No compliance frameworks yet. Add your first one.
        </div>
        @endforelse
    </div>
    @endif

    {{-- ── REQUIREMENTS ────────────────────────────────────────────────── --}}
    @if($activeTab === 'requirements')
    <div class="flex justify-end gap-3">
        <button wire:click="$set('isRequirementFormOpen', true)"
            class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 font-medium transition">
            + Add Requirement
        </button>
    </div>

    @if($isRequirementFormOpen)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-blue-200 dark:border-blue-800">
        <h3 class="font-semibold text-gray-800 dark:text-white mb-4">New Compliance Requirement</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Framework *</label>
                <select wire:model="req_framework_id"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">Select Framework…</option>
                    @foreach($frameworks as $fw)
                    <option value="{{ $fw->id }}">{{ $fw->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Req. Code</label>
                <input wire:model="req_code" type="text" placeholder="e.g. HEC-3.1"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Category *</label>
                <select wire:model="req_category"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    @foreach($categories as $cat)
                    <option value="{{ $cat }}">{{ str_replace('_', ' ', $cat) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Status</label>
                <select wire:model="req_status"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    @foreach(['PENDING', 'IN_PROGRESS', 'MET', 'NOT_MET', 'WAIVED'] as $st)
                    <option value="{{ $st }}">{{ str_replace('_', ' ', $st) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Title *</label>
                <input wire:model="req_title" type="text" placeholder="Requirement title…"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Description</label>
                <textarea wire:model="req_description" rows="2"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
            </div>
        </div>
        <div class="flex gap-3 mt-4">
            <button wire:click="saveRequirement" class="px-5 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 font-medium transition">Save</button>
            <button wire:click="$set('isRequirementFormOpen', false)" class="px-5 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm rounded-lg font-medium transition">Cancel</button>
        </div>
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <table class="min-w-full text-sm divide-y divide-gray-100 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900 text-xs text-gray-500 uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Code</th>
                    <th class="px-4 py-3 text-left">Framework</th>
                    <th class="px-4 py-3 text-left">Category</th>
                    <th class="px-4 py-3 text-left">Title</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($requirements as $req)
                @php $statusColors = ['PENDING'=>'bg-gray-100 text-gray-700','IN_PROGRESS'=>'bg-blue-100 text-blue-700','MET'=>'bg-green-100 text-green-700','NOT_MET'=>'bg-red-100 text-red-700','WAIVED'=>'bg-yellow-100 text-yellow-700']; @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $req->requirement_code ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $req->framework?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ str_replace('_', ' ', $req->category) }}</td>
                    <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">{{ $req->title }}</td>
                    <td class="px-4 py-3">
                        <select wire:change="updateRequirementStatus('{{ $req->id }}', $event.target.value)"
                            class="text-xs border-0 rounded-full px-2 py-1 font-medium {{ $statusColors[$req->status] ?? 'bg-gray-100 text-gray-700' }}">
                            @foreach(['PENDING','IN_PROGRESS','MET','NOT_MET','WAIVED'] as $st)
                            <option value="{{ $st }}" @selected($req->status === $st)>{{ str_replace('_',' ',$st) }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="px-4 py-3">
                        <button wire:click="$set('ev_requirement_id', '{{ $req->id }}'); $set('activeTab', 'evidence'); $set('isEvidenceFormOpen', true)"
                            class="text-xs px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100 transition font-medium">
                            + Evidence
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">No requirements found. Add them above.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

    {{-- ── EVIDENCE ─────────────────────────────────────────────────────── --}}
    @if($activeTab === 'evidence')
    <div class="flex justify-end">
        <button wire:click="$set('isEvidenceFormOpen', true)"
            class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 font-medium transition">
            + Upload Evidence
        </button>
    </div>

    @if($isEvidenceFormOpen)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-blue-200 dark:border-blue-800">
        <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Upload Compliance Evidence</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Requirement *</label>
                <select wire:model="ev_requirement_id"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">Select Requirement…</option>
                    @foreach($requirements as $req)
                    <option value="{{ $req->id }}">{{ $req->requirement_code }} – {{ Str::limit($req->title, 50) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Evidence Title *</label>
                <input wire:model="ev_title" type="text" placeholder="e.g. Faculty Qualifications Report 2024"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">File (optional)</label>
                <input wire:model="ev_file" type="file"
                    class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Description</label>
                <textarea wire:model="ev_description" rows="2"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
            </div>
        </div>
        <div class="flex gap-3 mt-4">
            <button wire:click="saveEvidence" class="px-5 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 font-medium transition">Upload</button>
            <button wire:click="$set('isEvidenceFormOpen', false)" class="px-5 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm rounded-lg font-medium transition">Cancel</button>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($evidences as $ev)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 border border-gray-100 dark:border-gray-700">
            <div class="flex items-start justify-between mb-2">
                <h4 class="font-medium text-gray-900 dark:text-white text-sm">{{ $ev->title }}</h4>
                @if($ev->is_verified)
                <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-medium">✓ Verified</span>
                @else
                <button wire:click="verifyEvidence('{{ $ev->id }}')" class="text-xs px-2 py-0.5 bg-yellow-50 text-yellow-700 rounded-full hover:bg-yellow-100">Verify</button>
                @endif
            </div>
            <p class="text-xs text-gray-400 mb-1">{{ $ev->requirement?->requirement_code }} – {{ Str::limit($ev->requirement?->title, 40) }}</p>
            <p class="text-xs text-gray-500">Uploaded by {{ $ev->uploader?->name ?? 'Unknown' }}</p>
            @if($ev->file_path)
            <a href="#" class="text-xs text-blue-500 mt-1 inline-block">📎 Download</a>
            @endif
        </div>
        @empty
        <div class="col-span-3 text-center text-gray-400 py-10">No evidence uploaded yet.</div>
        @endforelse
    </div>
    @endif

    {{-- ── AUDITS ────────────────────────────────────────────────────────── --}}
    @if($activeTab === 'audits')
    <div class="flex justify-end">
        <button wire:click="$set('isAuditFormOpen', true)"
            class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 font-medium transition">
            + Record Audit
        </button>
    </div>

    @if($isAuditFormOpen)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-blue-200 dark:border-blue-800">
        <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Record Compliance Audit</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Framework</label>
                <select wire:model="audit_framework_id"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">None</option>
                    @foreach($frameworks as $fw)
                    <option value="{{ $fw->id }}">{{ $fw->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Audit Date *</label>
                <input wire:model="audit_date" type="date"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Auditor Name</label>
                <input wire:model="audit_auditor" type="text"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Type</label>
                <select wire:model="audit_type"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="INTERNAL">Internal</option>
                    <option value="EXTERNAL">External</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Result</label>
                <select wire:model="audit_result"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    @foreach(['PENDING', 'PASS', 'PARTIAL', 'FAIL'] as $r)
                    <option value="{{ $r }}">{{ $r }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Findings</label>
                <textarea wire:model="audit_findings" rows="3"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
            </div>
        </div>
        <div class="flex gap-3 mt-4">
            <button wire:click="saveAudit" class="px-5 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 font-medium transition">Save Audit</button>
            <button wire:click="$set('isAuditFormOpen', false)" class="px-5 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm rounded-lg font-medium transition">Cancel</button>
        </div>
    </div>
    @endif

    <div class="space-y-3">
        @forelse($audits as $audit)
        @php $rColors = ['PASS'=>'bg-green-100 text-green-700','FAIL'=>'bg-red-100 text-red-700','PARTIAL'=>'bg-yellow-100 text-yellow-700','PENDING'=>'bg-gray-100 text-gray-700']; @endphp
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex items-center gap-4">
            <div class="flex-1">
                <div class="font-medium text-gray-900 dark:text-white">{{ $audit->framework?->name ?? 'General Audit' }}</div>
                <div class="text-xs text-gray-400 mt-0.5">{{ $audit->audit_type }} | Auditor: {{ $audit->auditor_name ?? 'N/A' }} | {{ $audit->audit_date }}</div>
                @if($audit->findings)
                <p class="text-xs text-gray-500 mt-1">{{ Str::limit($audit->findings, 120) }}</p>
                @endif
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $rColors[$audit->result] ?? 'bg-gray-100 text-gray-700' }}">
                {{ $audit->result }}
            </span>
        </div>
        @empty
        <div class="text-center text-gray-400 py-10">No audits recorded yet.</div>
        @endforelse
    </div>
    @endif

    {{-- ── CORRECTIVE ACTIONS ───────────────────────────────────────────── --}}
    @if($activeTab === 'actions')
    <div class="flex justify-end">
        <button wire:click="$set('isActionFormOpen', true)"
            class="px-4 py-2 bg-orange-600 text-white text-sm rounded-lg hover:bg-orange-700 font-medium transition">
            + New Action
        </button>
    </div>

    @if($isActionFormOpen)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-orange-200 dark:border-orange-800">
        <h3 class="font-semibold text-gray-800 dark:text-white mb-4">New Corrective Action</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Title *</label>
                <input wire:model="action_title" type="text" placeholder="Action title…"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Priority</label>
                <select wire:model="action_priority"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    @foreach(['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'] as $p)
                    <option value="{{ $p }}">{{ $p }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Due Date</label>
                <input wire:model="action_due_date" type="date"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Description *</label>
                <textarea wire:model="action_description" rows="3"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
            </div>
        </div>
        <div class="flex gap-3 mt-4">
            <button wire:click="saveCorrectiveAction" class="px-5 py-2 bg-orange-600 text-white text-sm rounded-lg hover:bg-orange-700 font-medium transition">Save</button>
            <button wire:click="$set('isActionFormOpen', false)" class="px-5 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm rounded-lg font-medium transition">Cancel</button>
        </div>
    </div>
    @endif

    <div class="space-y-3">
        @forelse($actions as $action)
        @php $pColors = ['LOW'=>'bg-blue-50 text-blue-600','MEDIUM'=>'bg-yellow-50 text-yellow-700','HIGH'=>'bg-orange-50 text-orange-700','CRITICAL'=>'bg-red-100 text-red-700']; @endphp
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex items-center gap-4">
            <div class="flex-1">
                <div class="font-medium text-gray-900 dark:text-white">{{ $action->title }}</div>
                <div class="text-xs text-gray-400 mt-0.5">Due: {{ $action->due_date ?? 'No deadline' }}</div>
                @if($action->resolution_notes)
                <p class="text-xs text-green-600 mt-1">✓ {{ $action->resolution_notes }}</p>
                @endif
            </div>
            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $pColors[$action->priority] ?? '' }}">{{ $action->priority }}</span>
            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $action->status === 'CLOSED' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">{{ $action->status }}</span>
            @if($action->status !== 'CLOSED')
            <button wire:click="closeAction('{{ $action->id }}', 'Resolved')"
                class="text-xs px-3 py-1 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition">
                Close
            </button>
            @endif
        </div>
        @empty
        <div class="text-center text-gray-400 py-10">No corrective actions yet.</div>
        @endforelse
    </div>
    @endif

</div>
