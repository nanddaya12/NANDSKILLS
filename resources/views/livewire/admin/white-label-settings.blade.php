<div class="space-y-8 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center bg-slate-950/40 p-6 rounded-2xl border border-white/5">
        <div>
            <h2 class="text-xl font-bold text-white">White Label & Branding Engine</h2>
            <p class="text-slate-400 text-sm mt-1">Customize your academy portal, upload logos, select colors, choose typography, and configure custom hostnames.</p>
        </div>
    </div>

    @if ($successMessage)
        <div class="p-4 rounded-xl bg-emerald-500/15 border border-emerald-500/20 text-emerald-300 text-sm flex items-center gap-3">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ $successMessage }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Live Branding Preview Panel -->
        <div class="p-6 rounded-2xl bg-white/5 border border-white/5 backdrop-blur-md space-y-6">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Visual Portal Preview</h3>

            <!-- Simulated Card Container -->
            <div class="rounded-xl border border-white/5 bg-slate-950/80 overflow-hidden text-xs shadow-xl">
                <!-- Header -->
                <div class="h-8 bg-slate-900 border-b border-white/5 flex items-center justify-between px-3">
                    <span class="font-bold" style="color: {{ $primary_color }}">■ {{ $name ?: 'Academy Logo' }}</span>
                    <span class="h-3 w-3 rounded-full" style="background-color: {{ $primary_color }}"></span>
                </div>
                <!-- Content -->
                <div class="p-4 space-y-3">
                    <div class="h-3 rounded" style="background-color: {{ $primary_color }}; width: 60%"></div>
                    <div class="space-y-1">
                        <div class="h-2 bg-slate-800 rounded w-full"></div>
                        <div class="h-2 bg-slate-800 rounded w-4/5"></div>
                    </div>
                    <!-- Action -->
                    <button class="w-full py-1.5 rounded font-bold text-[10px] text-white transition-all" style="background-color: {{ $primary_color }}">
                        Enroll Now
                    </button>
                </div>
            </div>
            <p class="text-[10px] text-slate-500 leading-normal">Changes made in the branding controls will instantly update the dynamic styles across all public and internal dashboards.</p>
        </div>

        <!-- Branding Controls Form -->
        <div class="md:col-span-2 p-8 rounded-2xl bg-white/5 border border-white/5 backdrop-blur-md space-y-6">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Branding Parameters</h3>

            <form wire:submit.prevent="saveBranding" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Academy Portal Name</label>
                        <input type="text" wire:model.lazy="name" required
                               class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                        @error('name') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Custom Domain Name</label>
                        <input type="text" wire:model.defer="custom_domain"
                               class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm"
                               placeholder="academy.mydomain.com">
                        @error('custom_domain') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Primary Color Hex</label>
                        <div class="flex gap-2">
                            <input type="color" wire:model.lazy="primary_color" class="h-11 w-11 shrink-0 rounded-xl bg-transparent border-0 cursor-pointer">
                            <input type="text" wire:model.lazy="primary_color" required
                                   class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                        </div>
                        @error('primary_color') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Secondary Color Hex</label>
                        <div class="flex gap-2">
                            <input type="color" wire:model.lazy="secondary_color" class="h-11 w-11 shrink-0 rounded-xl bg-transparent border-0 cursor-pointer">
                            <input type="text" wire:model.lazy="secondary_color" required
                                   class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                        </div>
                        @error('secondary_color') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Typography Font</label>
                        <select wire:model.defer="typography" required
                                class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                            <option value="Inter">Inter</option>
                            <option value="Outfit">Outfit</option>
                            <option value="Roboto">Roboto</option>
                            <option value="Poppins">Poppins</option>
                            <option value="Montserrat">Montserrat</option>
                        </select>
                        @error('typography') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Dashboard Theme</label>
                        <select wire:model.defer="dashboard_theme" required
                                class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                            <option value="light">Light Mode</option>
                            <option value="dark">Dark Mode</option>
                        </select>
                        @error('dashboard_theme') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Branding Logo URL</label>
                        <input type="url" wire:model.defer="logo_url"
                               class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm"
                               placeholder="https://my-bucket.s3.amazonaws.com/logo.png">
                        @error('logo_url') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Branding Favicon URL</label>
                        <input type="url" wire:model.defer="favicon_url"
                               class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm"
                               placeholder="https://my-bucket.s3.amazonaws.com/favicon.ico">
                        @error('favicon_url') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="border-t border-white/5 pt-6 space-y-5">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider">Advanced Styling & Email Builder</h4>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Custom CSS Overrides</label>
                        <textarea wire:model.defer="custom_css" rows="4"
                                  class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm font-mono"
                                  placeholder="/* Add your custom CSS rules here */&#10;.custom-btn { background: red; }"></textarea>
                        @error('custom_css') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Email Template Header (HTML)</label>
                            <textarea wire:model.defer="email_header" rows="4"
                                      class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm font-mono"
                                      placeholder="<div style='background:#f4f4f4; padding:20px;'><img src='logo.png' /></div>"></textarea>
                            @error('email_header') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Email Template Footer (HTML)</label>
                            <textarea wire:model.defer="email_footer" rows="4"
                                      class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm font-mono"
                                      placeholder="<div style='text-align:center;'>&copy; NANDSKILLS Education. All rights reserved.</div>"></textarea>
                            @error('email_footer') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-semibold text-sm transition-colors shadow-md shadow-blue-600/10">
                    Apply Branding Customizations
                </button>
            </form>
        </div>
    </div>
</div>
