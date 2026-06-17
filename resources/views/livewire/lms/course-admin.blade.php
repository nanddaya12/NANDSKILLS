<div class="space-y-8">

    {{-- Page Header --}}
    <div class="flex justify-between items-center bg-white border border-slate-200 shadow-sm p-6 rounded-2xl">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Course Administration</h2>
            <p class="text-slate-500 text-sm mt-1">Manage learning programs, create chapters, edit lessons content, and deploy certifications.</p>
        </div>
        <button wire:click="$toggle('isCreating')"
                class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm transition-all shadow-sm">
            {{ $isCreating ? '← View Directory' : '+ Create New Course' }}
        </button>
    </div>

    @if (session()->has('status'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-5 py-3 text-sm font-medium">
            ✓ {{ session('status') }}
        </div>
    @endif

    @if($isCreating)
        {{-- Create / Edit Form --}}
        <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-8 max-w-2xl mx-auto">
            <h3 class="text-lg font-bold text-slate-800 mb-6">
                {{ $editCourseId ? '✏️ Modify Course Details' : '📚 Create New Course Program' }}
            </h3>

            <form wire:submit.prevent="{{ $editCourseId ? 'updateCourse' : 'createCourse' }}" class="space-y-5">

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Course Title</label>
                    <input type="text" wire:model.defer="title" required
                           class="w-full bg-white border border-slate-300 text-slate-800 rounded-xl py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm placeholder-slate-400 transition"
                           placeholder="e.g. Introduction to Web Development">
                    @error('title') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Description</label>
                    <textarea wire:model.defer="description" rows="4"
                              class="w-full bg-white border border-slate-300 text-slate-800 rounded-xl py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm placeholder-slate-400 transition"
                              placeholder="Describe what students will learn..."></textarea>
                    @error('description') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Price ($)</label>
                        <input type="number" step="0.01" wire:model.defer="price" required
                               class="w-full bg-white border border-slate-300 text-slate-800 rounded-xl py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition"
                               placeholder="0.00">
                        @error('price') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Language</label>
                        <input type="text" wire:model.defer="language" required
                               class="w-full bg-white border border-slate-300 text-slate-800 rounded-xl py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition"
                               placeholder="English">
                        @error('language') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Cover Image URL</label>
                    <input type="url" wire:model.defer="cover_image_url"
                           class="w-full bg-white border border-slate-300 text-slate-800 rounded-xl py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm placeholder-slate-400 transition"
                           placeholder="https://images.unsplash.com/...">
                    @error('cover_image_url') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4 flex gap-4">
                    <button type="submit"
                            class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold text-sm transition-colors shadow-sm">
                        {{ $editCourseId ? '💾 Save Modifications' : '🚀 Publish Program' }}
                    </button>
                    <button type="button" wire:click="$set('isCreating', false)"
                            class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold text-sm transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>

    @else
        {{-- Course Table --}}
        <div class="bg-white border border-slate-200 shadow-sm rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Course Catalog</h3>
                <span class="text-xs text-slate-400">{{ $courses->count() }} program(s)</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr class="text-xs uppercase tracking-wider font-semibold text-slate-500">
                            <th class="py-3 px-5">Title</th>
                            <th class="py-3 px-5">Slug</th>
                            <th class="py-3 px-5">Price</th>
                            <th class="py-3 px-5">Chapters</th>
                            <th class="py-3 px-5">Language</th>
                            <th class="py-3 px-5">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($courses as $course)
                            <tr class="hover:bg-blue-50/50 transition-colors">
                                <td class="py-4 px-5 font-semibold text-slate-800">{{ $course->title }}</td>
                                <td class="py-4 px-5 text-xs text-slate-400 font-mono">{{ $course->slug }}</td>
                                <td class="py-4 px-5 font-bold text-blue-600">
                                    {{ $course->price > 0 ? '$' . number_format($course->price, 2) : 'Free' }}
                                </td>
                                <td class="py-4 px-5">
                                    <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-600 rounded-full px-2.5 py-0.5 text-xs font-semibold">
                                        {{ $course->chapters_count ?? 0 }} Chapters
                                    </span>
                                </td>
                                <td class="py-4 px-5 text-xs text-slate-600">{{ $course->language }}</td>
                                <td class="py-4 px-5 flex gap-3 text-xs font-semibold">
                                    <button wire:click="editCourse('{{ $course->id }}')"
                                            class="text-blue-600 hover:text-blue-800 hover:underline">Edit</button>
                                    <button onclick="confirm('Are you sure you want to delete this course?') || event.stopImmediatePropagation()"
                                            wire:click="deleteCourse('{{ $course->id }}')"
                                            class="text-rose-500 hover:text-rose-700 hover:underline">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-16 text-center">
                                    <div class="text-4xl mb-3">📚</div>
                                    <p class="text-slate-400 text-sm">No courses defined in the catalog yet.</p>
                                    <button wire:click="$set('isCreating', true)"
                                            class="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition">
                                        Create your first course
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
