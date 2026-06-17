<div class="max-w-3xl mx-auto space-y-8">
    <div class="bg-slate-950/40 p-6 rounded-2xl border border-white/5 text-center">
        <h2 class="text-2xl font-bold text-white">Online Admission Application Portal</h2>
        <p class="text-slate-400 text-sm mt-1">Start your academic journey with us. Submit your details, qualification certificates, and track your application status.</p>
    </div>

    @if($isSubmitted)
        <div class="p-8 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-center space-y-4">
            <div class="text-4xl text-emerald-400">🎉</div>
            <h3 class="text-xl font-bold text-white">Application Submitted Successfully!</h3>
            <p class="text-slate-300 max-w-md mx-auto text-sm">Your application has been received and is currently under review. Please keep the following application number for tracking purposes:</p>
            <div class="text-2xl font-black text-emerald-400 bg-emerald-950/40 py-3 px-6 rounded-xl border border-emerald-500/20 w-fit mx-auto tracking-widest mt-2">
                {{ $trackingNumber }}
            </div>
            <div class="pt-4">
                <button onclick="window.location.reload()" class="px-6 py-2 bg-slate-800 hover:bg-slate-750 text-slate-300 rounded-xl text-sm font-semibold transition-colors">
                    Apply for Another Program
                </button>
            </div>
        </div>
    @else
        <div class="p-8 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md space-y-6">
            <form wire:submit.prevent="submitApplication" class="space-y-6">
                <!-- Select Form template -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Select Target Program</label>
                    <select wire:model="selectedFormId" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                        <option value="">Choose a Program...</option>
                        @foreach($activeForms as $f)
                            <option value="{{ $f->id }}">{{ $f->title }} ({{ $f->program->name ?? '' }})</option>
                        @endforeach
                    </select>
                </div>

                @if($formConfig)
                    <div class="border-t border-white/5 pt-6 space-y-5">
                        <h4 class="text-sm font-bold text-blue-400 uppercase tracking-wider">Applicant Personal Profile</h4>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Full Name</label>
                                <input type="text" wire:model.defer="applicant_name" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Email Address</label>
                                <input type="email" wire:model.defer="email" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Phone Number</label>
                                <input type="text" wire:model.defer="phone" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Date of Birth</label>
                                <input type="date" wire:model.defer="date_of_birth" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Gender</label>
                                <select wire:model.defer="gender" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                                    <option value="MALE">MALE</option>
                                    <option value="FEMALE">FEMALE</option>
                                    <option value="OTHER">OTHER</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Nationality</label>
                                <input type="text" wire:model.defer="nationality" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                            </div>
                        </div>

                        <!-- Qualifications -->
                        <div class="border-t border-white/5 pt-6 space-y-4">
                            <h4 class="text-sm font-bold text-blue-400 uppercase tracking-wider">Academic Qualifications</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Last Degree / School Qualification</label>
                                    <input type="text" wire:model.defer="previous_qualification" placeholder="e.g. High School Diploma, A-Levels" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Grade Percentage / GPA / Score</label>
                                    <input type="number" step="0.01" wire:model.defer="previous_grade" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic configs if present -->
                        @if(!empty($formConfig->fields_config))
                            <div class="border-t border-white/5 pt-6 space-y-4">
                                <h4 class="text-sm font-bold text-blue-400 uppercase tracking-wider">Additional Information</h4>
                                @foreach($formConfig->fields_config as $field)
                                    <div>
                                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">{{ $field['label'] }}</label>
                                        @if(($field['type'] ?? 'text') === 'textarea')
                                            <textarea wire:model.defer="dynamicResponses.{{ $field['name'] }}" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm"></textarea>
                                        @else
                                            <input type="text" wire:model.defer="dynamicResponses.{{ $field['name'] }}" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Document Uploads -->
                        <div class="border-t border-white/5 pt-6 space-y-4">
                            <h4 class="text-sm font-bold text-blue-400 uppercase tracking-wider font-semibold">Supporting Documents</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Transcripts & Certificates (PDF, JPG, PNG)</label>
                                    <input type="file" wire:model="transcriptFile" class="w-full text-slate-400 text-xs bg-slate-900 border border-white/10 p-2.5 rounded-xl">
                                    <div wire:loading wire:target="transcriptFile" class="text-[10px] text-blue-400 mt-1">Uploading...</div>
                                    @error('transcriptFile') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">ID Card / Passport Scan</label>
                                    <input type="file" wire:model="idCardFile" class="w-full text-slate-400 text-xs bg-slate-900 border border-white/10 p-2.5 rounded-xl">
                                    <div wire:loading wire:target="idCardFile" class="text-[10px] text-blue-400 mt-1">Uploading...</div>
                                    @error('idCardFile') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="pt-6">
                            <button type="submit" class="w-full py-3.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-sm transition-colors shadow-md shadow-blue-600/15">
                                Submit Application File
                            </button>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    @endif
</div>
