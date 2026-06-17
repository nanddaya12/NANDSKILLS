<div class="space-y-8">
    <!-- Top Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-slate-900/60 p-6 rounded-2xl border border-white/5 backdrop-blur-md gap-4">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight">Career & Placement Center</h2>
            <p class="text-slate-400 text-sm mt-1">Discover job listings, submit professional resumes, schedule interviews, and monitor application progress.</p>
        </div>
        <div class="flex bg-slate-950 p-1 rounded-xl border border-white/5">
            <button wire:click="$set('activeTab', 'jobs')"
                    class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $activeTab === 'jobs' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white' }}">
                💼 Job Board
            </button>
            <button wire:click="$set('activeTab', 'applications')"
                    class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $activeTab === 'applications' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white' }}">
                📁 Applications Log
            </button>
            @if($isAdmin)
                <button wire:click="$set('activeTab', 'profile')"
                        class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $activeTab === 'profile' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white' }}">
                    🏢 Company Profiles
                </button>
            @endif
        </div>
    </div>

    @if (session()->has('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 rounded-xl bg-rose-500/10 text-rose-400 border border-rose-500/20 text-sm font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        @if($activeTab === 'jobs')
            <!-- Job creation form for Admin, list for all -->
            @if($isAdmin)
                <div class="lg:col-span-4 p-6 rounded-2xl bg-slate-900/40 border border-white/5 backdrop-blur-md space-y-6 self-start">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Create Job Listing</h3>
                    
                    <form wire:submit.prevent="postJob" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Select Employer</label>
                            <select wire:model.defer="selectedEmployer" required
                                    class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                                <option value="">Choose Company...</option>
                                @foreach($employers as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->company_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Job Title</label>
                            <input type="text" wire:model.defer="jobTitle" required
                                   class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Type</label>
                                <select wire:model.defer="jobType" required
                                        class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                                    <option value="FULL_TIME">Full Time</option>
                                    <option value="PART_TIME">Part Time</option>
                                    <option value="INTERNSHIP">Internship</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Location</label>
                                <input type="text" wire:model.defer="jobLocation" placeholder="Onsite, Remote, Hybrid"
                                       class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Salary Estimate</label>
                            <input type="text" wire:model.defer="salaryRange" placeholder="$45k - $60k / year"
                                   class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Job Description</label>
                            <textarea wire:model.defer="jobDescription" rows="4" required
                                      class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Skill Requirements</label>
                            <textarea wire:model.defer="jobRequirements" rows="3" placeholder="React, Node.js, SQL, git..."
                                      class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm"></textarea>
                        </div>

                        <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-sm tracking-wide transition-all shadow-md shadow-blue-600/10">
                            Post Job Listing
                        </button>
                    </form>
                </div>
            @endif

            <!-- Job Board Listings List -->
            <div class="{{ $isAdmin ? 'lg:col-span-8' : 'lg:col-span-12' }} space-y-6">
                <div class="p-6 rounded-2xl bg-slate-900/40 border border-white/5 backdrop-blur-md">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-6">Active Career Opportunities</h3>
                    
                    <div class="space-y-4">
                        @forelse($jobs as $job)
                            <div class="p-6 rounded-2xl border border-white/5 bg-slate-950/20 hover:border-blue-500/30 transition-all flex flex-col md:flex-row justify-between gap-6">
                                <div class="space-y-3">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-wider border border-blue-500/20">{{ str_replace('_', ' ', $job->type) }}</span>
                                        @if($job->location)
                                            <span class="px-2.5 py-0.5 rounded-full bg-slate-800 text-slate-400 text-[10px] font-bold border border-white/5">{{ $job->location }}</span>
                                        @endif
                                        @if($job->salary_range)
                                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-bold border border-emerald-500/20">{{ $job->salary_range }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-extrabold text-white">{{ $job->title }}</h4>
                                        <p class="text-xs text-blue-400 mt-1 font-semibold">{{ $job->employer->company_name }}</p>
                                    </div>
                                    <p class="text-sm text-slate-300 leading-normal">{{ $job->description }}</p>
                                    @if($job->requirements)
                                        <div class="text-xs text-slate-400 leading-normal">
                                            <strong>Key Requirements:</strong> {{ $job->requirements }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center md:items-start shrink-0">
                                    @if(!$isAdmin)
                                        <button wire:click="selectJobToApply('{{ $job->id }}')" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-xs tracking-wider transition-colors uppercase">
                                            Apply Now
                                        </button>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded bg-white/5 text-slate-500 text-[10px] font-bold uppercase tracking-wider">Active Listing</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12 text-slate-500 text-sm">No career opportunities listed.</div>
                        @endforelse
                    </div>
                </div>
            </div>

        @elseif($activeTab === 'applications')
            <!-- Applications Log List -->
            <div class="lg:col-span-12 p-6 rounded-2xl bg-slate-900/40 border border-white/5 backdrop-blur-md">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-6">Job Application History Logs</h3>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead>
                            <tr class="border-b border-white/5 text-xs uppercase tracking-wider font-bold text-slate-400">
                                <th class="py-3 px-4">Position</th>
                                <th class="py-3 px-4">Employer</th>
                                @if($isAdmin)
                                    <th class="py-3 px-4">Applicant</th>
                                @endif
                                <th class="py-3 px-4">Submitted On</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Interview Schedule</th>
                                @if($isAdmin)
                                    <th class="py-3 px-4 text-right">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($applications as $app)
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="py-4 px-4 font-bold text-white">{{ $app->job->title }}</td>
                                    <td class="py-4 px-4 font-semibold text-slate-300">{{ $app->job->employer->company_name }}</td>
                                    @if($isAdmin)
                                        <td class="py-4 px-4 font-semibold text-slate-200">{{ $app->student->user->first_name }} {{ $app->student->user->last_name }}</td>
                                    @endif
                                    <td class="py-4 px-4 text-xs text-slate-400">{{ $app->created_at->format('M d, Y') }}</td>
                                    <td class="py-4 px-4">
                                        @if($app->status === 'ACCEPTED')
                                            <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-bold border border-emerald-500/20">ACCEPTED</span>
                                        @elseif($app->status === 'SHORTLISTED')
                                            <span class="px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-bold border border-blue-500/20">SHORTLISTED</span>
                                        @elseif($app->status === 'REJECTED')
                                            <span class="px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-400 text-[10px] font-bold border border-rose-500/20">REJECTED</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 text-[10px] font-bold border border-amber-500/20">PENDING</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-xs font-bold text-slate-300">
                                        {{ $app->interview_scheduled_at ? $app->interview_scheduled_at->format('M d, Y at g:i A') : 'Not Scheduled' }}
                                    </td>
                                    @if($isAdmin)
                                        <td class="py-4 px-4 text-right">
                                            <button wire:click="selectApplicationToReview('{{ $app->id }}')" class="text-xs text-blue-400 hover:text-blue-300 font-bold bg-blue-500/10 hover:bg-blue-500/20 px-3 py-1.5 rounded-lg border border-blue-500/20">
                                                Review
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-500">No applications registered in this cycle.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        @elseif($activeTab === 'profile' && $isAdmin)
            <!-- Employer Profile Builder Panel -->
            <div class="lg:col-span-5 p-6 rounded-2xl bg-slate-900/40 border border-white/5 backdrop-blur-md space-y-6 self-start">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Register Corporate Partner</h3>
                
                <form wire:submit.prevent="registerEmployer" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Company Legal Name</label>
                        <input type="text" wire:model.defer="companyName" required
                               class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Industry Sector</label>
                            <input type="text" wire:model.defer="industry" placeholder="SaaS, Robotics, Health"
                                   class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Company Website</label>
                            <input type="url" wire:model.defer="website" placeholder="https://"
                                   class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Recruitment Inbox Email</label>
                        <input type="email" wire:model.defer="contactEmail" required
                               class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Corporate Description</label>
                        <textarea wire:model.defer="companyDescription" rows="4"
                                  class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm"></textarea>
                    </div>

                    <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-sm tracking-wide transition-all shadow-md shadow-blue-600/10">
                        Register Partner
                    </button>
                </form>
            </div>

            <!-- List of Registered Employers -->
            <div class="lg:col-span-7 p-6 rounded-2xl bg-slate-900/40 border border-white/5 backdrop-blur-md">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-6">Corporate Placement Network</h3>
                
                <div class="space-y-4">
                    @foreach($employers as $emp)
                        <div class="p-5 rounded-xl border border-white/5 bg-slate-950/20 space-y-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-base font-extrabold text-white">{{ $emp->company_name }}</h4>
                                    <span class="text-xs text-blue-400 font-semibold">{{ $emp->industry ?: 'General' }}</span>
                                </div>
                                @if($emp->website)
                                    <a href="{{ $emp->website }}" target="_blank" class="text-xs text-slate-400 hover:text-white font-medium hover:underline">Visit Site ↗</a>
                                @endif
                            </div>
                            <p class="text-xs text-slate-300 leading-relaxed">{{ $emp->description }}</p>
                            <div class="text-[10px] text-slate-500">
                                Contact Inbox: <strong class="text-slate-400">{{ $emp->contact_email }}</strong>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Apply Job Modal Overlay -->
    @if($showApplyModal && $applyingJobId)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="w-full max-w-lg bg-slate-900 border border-white/10 rounded-2xl p-6 shadow-2xl space-y-6">
                <div>
                    <h3 class="text-lg font-black text-white">Placement Resume Compiler</h3>
                    <p class="text-xs text-slate-400 mt-1">Provide your academic skills, certifications, and work history to apply.</p>
                </div>

                <form wire:submit.prevent="applyToJob" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Resume Details & Portfolio Links</label>
                        <textarea wire:model.defer="resumeText" rows="10" required
                                  class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm font-mono"
                                  placeholder="Full Name: Dayanand Nand&#10;Skills: PHP, Laravel, Livewire, JavaScript&#10;Experience: Senior SaaS Architect...&#10;Portfolio Links: github.com/..."></textarea>
                        @error('resumeText') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex gap-4">
                        <button type="submit" class="flex-1 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-sm tracking-wide transition-colors">
                            Submit Application
                        </button>
                        <button type="button" wire:click="$set('showApplyModal', false)" class="px-5 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl font-semibold text-sm transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Review Application Modal Overlay -->
    @if($showReviewModal && $selectedApplicationId)
        @php
            $selectedApp = collect($applications)->firstWhere('id', $selectedApplicationId);
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="w-full max-w-xl bg-slate-900 border border-white/10 rounded-2xl p-6 shadow-2xl space-y-6">
                <div>
                    <h3 class="text-lg font-black text-white">Review Candidate Credentials</h3>
                    <p class="text-xs text-slate-400 mt-1">Analyze candidate resume information and schedule recruitment interviews.</p>
                </div>

                <div class="space-y-4">
                    <div class="bg-slate-950 p-4 rounded-xl border border-white/5 space-y-2 text-sm">
                        <div>
                            <span class="text-slate-500 font-bold uppercase text-[10px]">Candidate:</span>
                            <span class="text-white font-extrabold block">{{ $selectedApp->student->user->first_name }} {{ $selectedApp->student->user->last_name }}</span>
                        </div>
                        <div class="pt-2">
                            <span class="text-slate-500 font-bold uppercase text-[10px]">Submitted Resume:</span>
                            <pre class="bg-slate-900 p-3 rounded-lg border border-white/5 text-slate-300 font-mono text-xs whitespace-pre-wrap max-h-40 overflow-y-auto leading-relaxed">{{ $selectedApp->resume_text }}</pre>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Schedule Interview (Optional for shortlisting)</label>
                        <input type="datetime-local" wire:model.defer="interviewDate"
                               class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 pt-2">
                    <button wire:click="updateApplicationStatus('SHORTLISTED')" class="flex-1 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-sm tracking-wide transition-colors">
                        Shortlist Candidate
                    </button>
                    <button wire:click="updateApplicationStatus('ACCEPTED')" class="flex-1 py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold text-sm tracking-wide transition-colors">
                        Accept & Hire
                    </button>
                    <button wire:click="updateApplicationStatus('REJECTED')" class="flex-1 py-3 bg-rose-600 hover:bg-rose-500 text-white rounded-xl font-bold text-sm tracking-wide transition-colors">
                        Reject
                    </button>
                    <button type="button" wire:click="$set('showReviewModal', false)" class="px-5 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl font-semibold text-sm transition-colors w-full md:w-auto">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
