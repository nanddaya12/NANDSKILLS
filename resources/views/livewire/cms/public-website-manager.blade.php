<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">🌐 Public Website & CMS</h1>
            <p class="text-sm text-gray-500 mt-1">Manage news articles, events and SEO settings for your public website.</p>
        </div>
        <button wire:click="$set('isArticleFormOpen', true)"
            class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 font-medium transition">
            + New Article
        </button>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-2 border-b border-gray-200 dark:border-gray-700">
        @foreach(['articles' => '📰 Articles', 'events' => '📅 Events', 'seo' => '🔎 SEO'] as $tab => $label)
        <button wire:click="setTab('{{ $tab }}')"
            class="px-4 py-2 text-sm font-medium border-b-2 transition {{ $activeTab === $tab ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    @if($activeTab === 'articles')

    {{-- Article Form --}}
    @if($isArticleFormOpen)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-blue-200 dark:border-blue-800">
        <h3 class="font-semibold text-gray-800 dark:text-white mb-4">{{ $article_id ? 'Edit' : 'New' }} Article</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Title *</label>
                <input wire:model="art_title" type="text" placeholder="Article title…"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Status</label>
                <select wire:model="art_status"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="DRAFT">Draft</option>
                    <option value="PUBLISHED">Published</option>
                    <option value="ARCHIVED">Archived</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Featured Image</label>
                <input wire:model="art_image" type="file" accept="image/*"
                    class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Excerpt</label>
                <textarea wire:model="art_excerpt" rows="2" placeholder="Brief summary shown in listings…"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Content *</label>
                <textarea wire:model="art_content" rows="8" placeholder="Full article content (HTML supported)…"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white font-mono"></textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">SEO Title</label>
                <input wire:model="art_meta_title" type="text" placeholder="Meta title (60 chars max)"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">SEO Description</label>
                <input wire:model="art_meta_desc" type="text" placeholder="Meta description (160 chars max)"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
        </div>
        <div class="flex gap-3 mt-4">
            <button wire:click="saveArticle" class="px-5 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 font-medium transition">
                {{ $article_id ? 'Update Article' : 'Publish Article' }}
            </button>
            <button wire:click="$set('isArticleFormOpen', false)" class="px-5 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm rounded-lg font-medium transition">
                Cancel
            </button>
        </div>
    </div>
    @endif

    {{-- Filter Bar --}}
    <div class="flex gap-3 flex-wrap">
        <input wire:model.live.debounce.400ms="searchQuery" type="text" placeholder="🔍 Search articles…"
            class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white w-64">
        <select wire:model.live="filterStatus"
            class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            <option value="">All Status</option>
            <option value="DRAFT">Draft</option>
            <option value="PUBLISHED">Published</option>
            <option value="ARCHIVED">Archived</option>
        </select>
    </div>

    {{-- Articles Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($articles as $article)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden hover:shadow-md transition border border-gray-100 dark:border-gray-700">
            @if($article->featured_image)
            <img src="{{ asset('storage/'.$article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-36 object-cover">
            @else
            <div class="w-full h-36 bg-gradient-to-br from-blue-400 to-indigo-600 flex items-center justify-center">
                <span class="text-white text-4xl">📰</span>
            </div>
            @endif
            <div class="p-4">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm line-clamp-2">{{ $article->title }}</h3>
                    <span class="flex-shrink-0 px-2 py-0.5 rounded-full text-xs font-medium {{ $article->status === 'PUBLISHED' ? 'bg-green-100 text-green-700' : ($article->status === 'ARCHIVED' ? 'bg-gray-100 text-gray-600' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ $article->status }}
                    </span>
                </div>
                @if($article->excerpt)
                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mb-3">{{ $article->excerpt }}</p>
                @endif
                <div class="flex gap-2">
                    <button wire:click="editArticle('{{ $article->id }}')"
                        class="flex-1 text-xs py-1.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 rounded-lg hover:bg-blue-100 font-medium">
                        ✏️ Edit
                    </button>
                    @if($article->status !== 'PUBLISHED')
                    <button wire:click="publishArticle('{{ $article->id }}')"
                        class="flex-1 text-xs py-1.5 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 font-medium">
                        🚀 Publish
                    </button>
                    @else
                    <button wire:click="unpublishArticle('{{ $article->id }}')"
                        class="flex-1 text-xs py-1.5 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 font-medium">
                        📥 Unpublish
                    </button>
                    @endif
                    <button wire:click="deleteArticle('{{ $article->id }}')" onclick="return confirm('Delete this article?')"
                        class="text-xs py-1.5 px-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-100">
                        🗑️
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center text-gray-400 py-12">
            <div class="text-5xl mb-3">📰</div>
            <p>No articles yet. Create your first article to get started.</p>
        </div>
        @endforelse
    </div>
    <div>{{ $articles->links() }}</div>

    @elseif($activeTab === 'events')
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-8 text-center text-gray-400">
        <div class="text-5xl mb-3">📅</div>
        <p class="text-lg font-medium mb-2">Events Calendar coming soon</p>
        <p class="text-sm">Public events management with registration & reminders.</p>
    </div>

    @elseif($activeTab === 'seo')
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-8 text-center text-gray-400">
        <div class="text-5xl mb-3">🔎</div>
        <p class="text-lg font-medium mb-2">SEO Configuration</p>
        <p class="text-sm">Configure sitemap.xml, robots.txt, and global meta tags.</p>
    </div>
    @endif

</div>
