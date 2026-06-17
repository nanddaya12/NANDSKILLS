<div class="bg-slate-50 text-slate-800 min-h-screen font-sans selection:bg-blue-600 selection:text-white">
    <!-- Header Navbar -->
    <header class="sticky top-0 z-50 backdrop-blur-md bg-white/80 border-b border-slate-200 px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <a href="/" class="flex items-center gap-3">
                @if(isset($currentTenant) && $currentTenant->logo_url)
                    <img src="{{ $currentTenant->logo_url }}" alt="{{ $currentTenant->name }}" class="h-8">
                @else
                    <span class="text-xl font-extrabold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                        {{ $currentTenant->name ?? 'NANDSKILLS' }}
                    </span>
                @endif
            </a>

            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="#about" class="hover:text-blue-600 transition-colors">About</a>
                <a href="#courses" class="hover:text-blue-600 transition-colors">Courses</a>
                <a href="#blogs" class="hover:text-blue-600 transition-colors">Insights</a>
                <a href="#contact" class="hover:text-blue-600 transition-colors">Contact</a>
            </nav>

            <div class="flex items-center gap-4">
                <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition-colors">Sign In</a>
                <a href="{{ route('auth.register') }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-sm font-semibold text-white transition-all shadow-md shadow-blue-500/10">
                    Get Started
                </a>
            </div>
        </div>
    </header>

    <!-- Dynamic Sections Parser -->
    @foreach ($sections as $section)
        @php
            $type = $section['type'];
            $settings = $section['settings'] ?? [];
        @endphp

        <!-- Notice Bar -->
        @if ($type === 'notice_bar')
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 py-2.5 px-4 text-center text-xs font-semibold tracking-wide text-white flex items-center justify-center gap-3">
                <span>{{ $settings['text'] ?? '' }}</span>
                @if (isset($settings['cta_text']))
                    <a href="{{ $settings['cta_url'] ?? '#' }}" class="underline hover:text-slate-200 ml-2 font-bold">
                        {{ $settings['cta_text'] }} →
                    </a>
                @endif
            </div>
        @endif

        <!-- Hero Section -->
        @if ($type === 'hero')
            <section class="relative py-20 px-6 overflow-hidden border-b border-slate-200 bg-white">
                <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-500/5 rounded-full blur-[120px] pointer-events-none"></div>
                <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-indigo-500/5 rounded-full blur-[120px] pointer-events-none"></div>

                <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center relative z-10">
                    <div class="space-y-6">
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight text-slate-900">
                            {{ $settings['title'] ?? '' }}
                        </h1>
                        <p class="text-slate-500 text-lg md:text-xl max-w-xl font-normal leading-relaxed">
                            {{ $settings['subtitle'] ?? '' }}
                        </p>
                        <div class="pt-4 flex flex-wrap gap-4">
                            <a href="{{ $settings['cta_url'] ?? '#' }}" class="px-6 py-3.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-505 font-bold text-sm text-white shadow-lg shadow-blue-500/10 transition-all">
                                {{ $settings['cta_text'] ?? 'Explore Courses' }}
                            </a>
                        </div>
                    </div>
                    <div class="flex justify-center">
                        @if (isset($settings['image_url']))
                            <img src="{{ $settings['image_url'] }}" alt="Hero Illustration" class="w-full max-w-lg hover:scale-105 transition-transform duration-500">
                        @endif
                    </div>
                </div>
            </section>
        @endif

        <!-- Stats Section -->
        @if ($type === 'stats')
            <section class="py-12 border-b border-slate-200 bg-slate-100/40">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                        @foreach ($settings['items'] ?? [] as $item)
                            <div class="text-center">
                                <p class="text-3xl md:text-4xl font-extrabold text-blue-600">{{ $item['value'] }}</p>
                                <p class="text-slate-500 text-xs mt-2 font-bold tracking-wide uppercase">{{ $item['label'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <!-- About Section -->
        @if ($type === 'about')
            <section id="about" class="py-20 px-6 border-b border-slate-200 relative bg-white">
                <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <div class="space-y-6">
                        <div class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-xs font-bold text-blue-600 border border-blue-100">
                            Who We Are
                        </div>
                        <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">{{ $settings['title'] ?? '' }}</h2>
                        <p class="text-slate-500 text-base leading-relaxed">{{ $settings['description'] ?? '' }}</p>
                    </div>
                    <div class="p-8 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-6">
                        <h3 class="text-xl font-bold text-blue-600">Our Mission</h3>
                        <p class="text-slate-650 text-sm leading-relaxed">{{ $settings['mission'] ?? '' }}</p>
                    </div>
                </div>
            </section>
        @endif

        <!-- Course Grid Section -->
        @if ($type === 'courses')
            <section id="courses" class="py-20 px-6 border-b border-slate-200 bg-slate-50">
                <div class="max-w-7xl mx-auto space-y-12">
                    <div class="text-center space-y-4">
                        <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">{{ $settings['title'] ?? '' }}</h2>
                        <p class="text-slate-500 text-base max-w-xl mx-auto">{{ $settings['subtitle'] ?? '' }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @forelse ($settings['items'] ?? [] as $course)
                            <div class="group rounded-2xl bg-white border border-slate-200 overflow-hidden hover:shadow-lg transition-all">
                                <div class="h-48 bg-slate-100 relative overflow-hidden">
                                    @if (isset($course['cover_image_url']))
                                        <img src="{{ $course['cover_image_url'] }}" alt="{{ $course['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-400 text-xs font-semibold">
                                            Cover Image
                                        </div>
                                    @endif
                                    <div class="absolute top-4 right-4 px-3 py-1 rounded-full bg-white/90 backdrop-blur-sm text-xs font-semibold text-blue-600 border border-slate-200">
                                        {{ $course['language'] ?? 'English' }}
                                    </div>
                                </div>
                                <div class="p-6 space-y-4">
                                    <h3 class="text-lg font-bold text-slate-900 group-hover:text-blue-600 transition-colors">{{ $course['title'] }}</h3>
                                    <p class="text-slate-500 text-xs line-clamp-2 leading-relaxed">{{ $course['short_description'] ?? 'No description provided.' }}</p>
                                    <div class="flex justify-between items-center pt-4 border-t border-slate-100">
                                        <span class="text-xl font-extrabold text-blue-600">
                                            {{ $course['price'] > 0 ? '$' . number_format($course['price'], 2) : 'Free' }}
                                        </span>
                                        <a href="/courses/{{ $course['slug'] }}" class="text-xs font-bold text-blue-600 hover:text-blue-700">
                                            Enroll Now →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-12 text-center text-slate-400 text-sm bg-white border border-slate-200 rounded-xl">
                                No featured courses available. Check back soon!
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        @endif

        <!-- Blog Grid Section -->
        @if ($type === 'blogs')
            <section id="blogs" class="py-20 px-6 border-b border-slate-200 bg-white">
                <div class="max-w-7xl mx-auto space-y-12">
                    <div class="text-center space-y-4">
                        <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">{{ $settings['title'] ?? '' }}</h2>
                        <p class="text-slate-500 text-base max-w-xl mx-auto">{{ $settings['subtitle'] ?? '' }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        @forelse ($settings['items'] ?? [] as $post)
                            <div class="group bg-slate-50 border border-slate-200 rounded-2xl overflow-hidden hover:shadow-md transition-all">
                                <div class="h-44 bg-slate-200 overflow-hidden">
                                    @if(isset($post['featured_image']))
                                        <img src="{{ $post['featured_image'] }}" alt="{{ $post['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @endif
                                </div>
                                <div class="p-6 space-y-3">
                                    <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">ARTICLE</span>
                                    <h3 class="text-base font-bold text-slate-900 group-hover:text-blue-600 transition-colors">{{ $post['title'] }}</h3>
                                    <p class="text-slate-500 text-xs line-clamp-2 leading-relaxed">{{ $post['summary'] }}</p>
                                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                                        <span>{{ \Carbon\Carbon::parse($post['created_at'])->format('M d, Y') }}</span>
                                        <a href="/insights/{{ $post['slug'] }}" class="text-blue-600 font-bold hover:underline">Read →</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-12 text-center text-slate-400 text-sm bg-slate-50 border border-slate-200 rounded-xl">
                                No insights posts published.
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        @endif

        <!-- Contact Section -->
        @if ($type === 'contact')
            <section id="contact" class="py-20 px-6 bg-slate-50">
                <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <div class="space-y-6">
                        <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">{{ $settings['title'] ?? 'Get in Touch' }}</h2>
                        <p class="text-slate-500 leading-relaxed">Have questions about registration, courses, or custom plans? Contact our support staff for help.</p>

                        <div class="space-y-4 text-sm">
                            <div class="flex items-center gap-4 text-slate-600">
                                <span class="h-10 w-10 rounded-xl bg-white flex items-center justify-center border border-slate-200 text-blue-600">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </span>
                                <span>{{ $settings['phone'] ?? '' }}</span>
                            </div>
                            <div class="flex items-center gap-4 text-slate-600">
                                <span class="h-10 w-10 rounded-xl bg-white flex items-center justify-center border border-slate-200 text-blue-600">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </span>
                                <span>{{ $settings['email'] ?? '' }}</span>
                            </div>
                            <div class="flex items-center gap-4 text-slate-600">
                                <span class="h-10 w-10 rounded-xl bg-white flex items-center justify-center border border-slate-200 text-blue-600">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </span>
                                <span>{{ $settings['address'] ?? '' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-8 rounded-2xl bg-white border border-slate-200 shadow-sm">
                        <form class="space-y-5">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Your Name</label>
                                <input type="text" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl py-3 px-4 focus:outline-none focus:bg-white focus:border-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Email Address</label>
                                <input type="email" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl py-3 px-4 focus:outline-none focus:bg-white focus:border-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Your Message</label>
                                <textarea required rows="4" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl py-3 px-4 focus:outline-none focus:bg-white focus:border-blue-500 text-sm"></textarea>
                            </div>
                            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-750 text-white rounded-xl font-semibold text-sm transition-colors">
                                Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </section>
        @endif
    @endforeach

    <!-- Footer -->
    <footer class="py-12 border-t border-slate-200 text-center text-sm text-slate-500 bg-white">
        <p>© 2026 {{ $currentTenant->name ?? 'NANDSKILLS' }}. All rights reserved.</p>
        <p class="mt-2 text-xs text-slate-400">Powered by NANDSKILLS Multi-Tenant SaaS Platform.</p>
    </footer>
</div>
