<div class="space-y-8">
    <!-- Header -->
    <div class="flex justify-between items-center bg-slate-950/40 p-6 rounded-2xl border border-white/5">
        <div>
            <h2 class="text-xl font-bold text-white">Advanced Notification Engine</h2>
            <p class="text-slate-400 text-sm mt-1">Design message templates, schedule automated push/email campaigns, structure categories, and audit delivery logs.</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="setTab('categories'); $set('isCategoryFormOpen', true)" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-750 font-semibold text-xs text-slate-350 transition-all border border-white/5">
                New Category
            </button>
            <button wire:click="setTab('templates'); $set('isTemplateFormOpen', true)" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 font-semibold text-xs text-white transition-all shadow-md shadow-blue-600/10">
                Create Template
            </button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-white/10 gap-2 overflow-x-auto">
        <button wire:click="setTab('templates')" class="px-5 py-3 font-semibold text-sm transition-all border-b-2 {{ $activeTab === 'templates' ? 'border-blue-500 text-white' : 'border-transparent text-slate-400 hover:text-white' }}">
            Message Templates ({{ count($templates) }})
        </button>
        <button wire:click="setTab('scheduler')" class="px-5 py-3 font-semibold text-sm transition-all border-b-2 {{ $activeTab === 'scheduler' ? 'border-blue-500 text-white' : 'border-transparent text-slate-400 hover:text-white' }}">
            Campaign Scheduler ({{ count($scheduledList) }})
        </button>
        <button wire:click="setTab('logs')" class="px-5 py-3 font-semibold text-sm transition-all border-b-2 {{ $activeTab === 'logs' ? 'border-blue-500 text-white' : 'border-transparent text-slate-400 hover:text-white' }}">
            Delivery Logs
        </button>
        <button wire:click="setTab('categories')" class="px-5 py-3 font-semibold text-sm transition-all border-b-2 {{ $activeTab === 'categories' ? 'border-blue-500 text-white' : 'border-transparent text-slate-400 hover:text-white' }}">
            Notification Categories
        </button>
    </div>

    <!-- Category creation form -->
    @if($isCategoryFormOpen)
        <div class="p-6 rounded-2xl bg-white/5 border border-white/10 max-w-md mx-auto space-y-4">
            <h3 class="text-xs font-bold text-white uppercase tracking-wider">Create Notification Category</h3>
            <form wire:submit.prevent="saveCategory" class="space-y-3">
                <div>
                    <label class="block text-[10px] text-slate-400 mb-1">Category Name</label>
                    <input type="text" wire:model.defer="cat_name" required placeholder="e.g. Exams & Results" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2 px-3 text-xs focus:border-blue-500">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] text-slate-400 mb-1">Emoji Icon</label>
                        <input type="text" wire:model.defer="cat_icon" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2 px-3 text-xs focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-[10px] text-slate-400 mb-1">Color Tag</label>
                        <input type="color" wire:model.defer="cat_color" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl h-10 p-1 focus:border-blue-500 text-sm">
                    </div>
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="submit" class="flex-1 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-xs">Create Category</button>
                    <button type="button" wire:click="$set('isCategoryFormOpen', false)" class="px-4 py-2 bg-slate-800 text-slate-350 rounded-xl text-xs">Cancel</button>
                </div>
            </form>
        </div>
    @endif

    <!-- Template creation form -->
    @if($isTemplateFormOpen)
        <div class="p-6 rounded-2xl bg-white/5 border border-white/10 max-w-xl mx-auto space-y-4">
            <h3 class="text-xs font-bold text-white uppercase tracking-wider">{{ $template_id ? 'Modify' : 'Create' }} Message Template</h3>
            <form wire:submit.prevent="saveTemplate" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] text-slate-400 mb-1">Category</label>
                        <select wire:model.defer="selectedCategoryId" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2.5 px-3 text-xs">
                            <option value="">Select Category...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] text-slate-400 mb-1">Template Name</label>
                        <input type="text" wire:model.defer="temp_name" required placeholder="e.g. Student Report Card Card" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2.5 px-3 text-xs focus:border-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] text-slate-400 mb-1">Primary Channel</label>
                        <select wire:model.defer="temp_channel" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2.5 px-3 text-xs">
                            <option value="in_app">In-App Alert</option>
                            <option value="email">Email</option>
                            <option value="sms">SMS</option>
                            <option value="push">Mobile Push</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] text-slate-400 mb-1">Target Audience</label>
                        <select wire:model.defer="temp_audience" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2.5 px-3 text-xs">
                            <option value="all">Everyone</option>
                            <option value="students">Students Only</option>
                            <option value="teachers">Teachers Only</option>
                            <option value="parents">Parents Only</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] text-slate-400 mb-1">Template Subject</label>
                        <input type="text" wire:model.defer="temp_subject" required placeholder="Subject / Title" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2.5 px-3 text-xs focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] text-slate-400 mb-1">Email / Alert HTML Body (Supports variable rendering e.g. &#123;&#123;student_name&#125;&#125;)</label>
                    <textarea wire:model.defer="temp_body" rows="4" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2.5 px-3 text-xs focus:border-blue-500"></textarea>
                </div>

                <div>
                    <label class="block text-[10px] text-slate-400 mb-1">Fallback SMS Body (Optional)</label>
                    <textarea wire:model.defer="temp_body_sms" rows="2" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2.5 px-3 text-xs focus:border-blue-500" placeholder="Keep short under 160 characters..."></textarea>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="submit" class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow-sm">Save Template</button>
                    <button type="button" wire:click="$set('isTemplateFormOpen', false)" class="px-5 py-2.5 bg-slate-800 text-slate-300 rounded-xl text-xs">Cancel</button>
                </div>
            </form>
        </div>
    @endif

    <!-- Scheduler Form -->
    @if($isSchedulerFormOpen)
        <div class="p-6 rounded-2xl bg-white/5 border border-white/10 max-w-xl mx-auto space-y-4">
            <h3 class="text-xs font-bold text-white uppercase tracking-wider">Schedule Notification Campaign</h3>
            <form wire:submit.prevent="saveScheduledNotification" class="space-y-4">
                <div>
                    <label class="block text-[10px] text-slate-400 mb-1">Choose Base Template (Optional)</label>
                    <select wire:model.defer="sched_template_id" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2.5 px-3 text-xs">
                        <option value="">Select Template...</option>
                        @foreach($templates as $tmp)
                            <option value="{{ $tmp->id }}">{{ $tmp->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] text-slate-400 mb-1">Campaign Subject</label>
                        <input type="text" wire:model.defer="sched_subject" required placeholder="Subject / Alert Title" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2.5 px-3 text-xs focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-[10px] text-slate-400 mb-1">Campaign Channel</label>
                        <select wire:model.defer="sched_channel" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2.5 px-3 text-xs">
                            <option value="in_app">In-App Banner</option>
                            <option value="email">Email Campaign</option>
                            <option value="sms">SMS Broadcaster</option>
                            <option value="push">Push Notification</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] text-slate-400 mb-1">Target Audience</label>
                        <select wire:model.defer="sched_audience" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2.5 px-3 text-xs">
                            <option value="all">Everyone</option>
                            <option value="students">Students Only</option>
                            <option value="teachers">Instructors Only</option>
                            <option value="parents">Parents Only</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] text-slate-400 mb-1">Scheduled Date & Time</label>
                        <input type="datetime-local" wire:model.defer="sched_at" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2.5 px-3 text-xs">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] text-slate-400 mb-1">Custom Message Body</label>
                    <textarea wire:model.defer="sched_body" rows="4" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2.5 px-3 text-xs focus:border-blue-500"></textarea>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="submit" class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow-sm">Save Campaign</button>
                    <button type="button" wire:click="$set('isSchedulerFormOpen', false)" class="px-5 py-2.5 bg-slate-800 text-slate-350 rounded-xl text-xs">Cancel</button>
                </div>
            </form>
        </div>
    @endif

    <!-- Content Sections based on tab -->
    <div class="p-6 rounded-2xl bg-white/5 border border-white/5 backdrop-blur-md">
        @if($activeTab === 'templates')
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead>
                        <tr class="border-b border-white/5 text-xs uppercase tracking-wider font-semibold text-slate-400">
                            <th class="py-3 px-4">Template Name</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Subject</th>
                            <th class="py-3 px-4">Channel</th>
                            <th class="py-3 px-4">Audience</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($templates as $tmp)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="py-4 px-4 font-bold text-white flex items-center gap-2">
                                    {{ $tmp->name }}
                                    @if(!$tmp->is_active)
                                        <span class="px-2 py-0.5 rounded bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[9px]">DISABLED</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-xs font-semibold text-slate-400">
                                    {{ $tmp->category->icon ?? '🔔' }} {{ $tmp->category->name ?? 'General' }}
                                </td>
                                <td class="py-4 px-4 text-xs text-slate-350">{{ $tmp->subject }}</td>
                                <td class="py-4 px-4 text-xs font-bold text-blue-400">{{ strtoupper($tmp->channel) }}</td>
                                <td class="py-4 px-4 text-xs font-semibold uppercase text-slate-400">{{ $tmp->audience }}</td>
                                <td class="py-4 px-4 text-right space-x-1">
                                    <button wire:click="toggleTemplate('{{ $tmp->id }}')" class="text-xs bg-slate-900 hover:bg-slate-850 px-2 py-1 rounded text-slate-300">
                                        {{ $tmp->is_active ? 'Disable' : 'Enable' }}
                                    </button>
                                    <button wire:click="editTemplate('{{ $tmp->id }}')" class="text-xs text-blue-400 hover:text-blue-300 px-2 py-1">Edit</button>
                                    <button onclick="confirm('Delete this template?') || event.stopImmediatePropagation()" wire:click="deleteTemplate('{{ $tmp->id }}')" class="text-xs text-rose-450 hover:text-rose-350 px-2 py-1">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500">No message templates configured.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @elseif($activeTab === 'scheduler')
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-bold text-white">Scheduled Notifications & Alerts</h3>
                <button wire:click="$set('isSchedulerFormOpen', true)" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-xs font-bold transition-all">Schedule campaign</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead>
                        <tr class="border-b border-white/5 text-xs uppercase tracking-wider font-semibold text-slate-400">
                            <th class="py-3 px-4">Campaign Title</th>
                            <th class="py-3 px-4">Scheduled Date</th>
                            <th class="py-3 px-4">Audience</th>
                            <th class="py-3 px-4">Channel</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($scheduledList as $sched)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="py-4 px-4 font-bold text-white">{{ $sched->subject }}</td>
                                <td class="py-4 px-4 text-xs font-semibold text-slate-400">{{ $sched->scheduled_at->format('M d, Y H:i') }}</td>
                                <td class="py-4 px-4 text-xs uppercase text-slate-300">{{ $sched->audience }}</td>
                                <td class="py-4 px-4 text-xs font-bold text-blue-400">{{ strtoupper($sched->channel) }}</td>
                                <td class="py-4 px-4 text-xs">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border 
                                        {{ $sched->status === 'SENT' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : '' }}
                                        {{ $sched->status === 'PENDING' ? 'bg-amber-500/10 text-amber-400 border-amber-500/20' : '' }}
                                    ">
                                        {{ $sched->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    @if($sched->status === 'PENDING')
                                        <button onclick="confirm('Cancel this campaign?') || event.stopImmediatePropagation()" wire:click="deleteScheduled('{{ $sched->id }}')" class="text-xs text-rose-450 hover:text-rose-350">Cancel</button>
                                    @else
                                        <span class="text-slate-500">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500">No scheduled notification campaigns.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @elseif($activeTab === 'logs')
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead>
                        <tr class="border-b border-white/5 text-xs uppercase tracking-wider font-semibold text-slate-400">
                            <th class="py-3 px-4">Recipient</th>
                            <th class="py-3 px-4">Template/Subject</th>
                            <th class="py-3 px-4">Channel</th>
                            <th class="py-3 px-4">Dispatch Date</th>
                            <th class="py-3 px-4">Delivery State</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($deliveryLogs as $log)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="py-4 px-4 font-bold text-white">
                                    {{ $log->user->first_name ?? 'Guest' }} {{ $log->user->last_name ?? '' }}
                                    <div class="text-[9px] text-slate-500 mt-0.5">{{ $log->user->email ?? 'N/A' }}</div>
                                </td>
                                <td class="py-4 px-4 text-xs font-semibold text-slate-350">{{ $log->template->name ?? $log->subject }}</td>
                                <td class="py-4 px-4 text-xs font-bold text-blue-450">{{ strtoupper($log->channel) }}</td>
                                <td class="py-4 px-4 text-xs text-slate-400">{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                                <td class="py-4 px-4 text-xs">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold border 
                                        {{ $log->status === 'SENT' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : '' }}
                                        {{ $log->status === 'FAILED' ? 'bg-rose-500/10 text-rose-400 border-rose-500/20' : '' }}
                                    ">
                                        {{ $log->status }}
                                    </span>
                                    @if($log->error_message)
                                        <div class="text-[9px] text-rose-400 mt-1 italic leading-snug">{{ $log->error_message }}</div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-500">No delivery logs registered.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @elseif($activeTab === 'categories')
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead>
                        <tr class="border-b border-white/5 text-xs uppercase tracking-wider font-semibold text-slate-400">
                            <th class="py-3 px-4">Icon</th>
                            <th class="py-3 px-4">Category Name</th>
                            <th class="py-3 px-4">Color Tag</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($categories as $cat)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="py-4 px-4 text-lg">{{ $cat->icon }}</td>
                                <td class="py-4 px-4 font-bold text-white">{{ $cat->name }}</td>
                                <td class="py-4 px-4 text-xs">
                                    <span class="inline-block w-4 h-4 rounded" style="background-color: {{ $cat->color }}"></span>
                                    <span class="ml-2 font-mono text-slate-400">{{ $cat->color }}</span>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <button onclick="confirm('Delete this category?') || event.stopImmediatePropagation()" wire:click="deleteItem('NotificationCategory', '{{ $cat->id }}')" class="text-xs text-rose-450 hover:text-rose-350">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-slate-500">No categories configured.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
