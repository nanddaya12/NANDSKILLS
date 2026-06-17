<?php

namespace App\Livewire\Cms;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\NewsArticle;
use Illuminate\Support\Str;

class PublicWebsiteManager extends Component
{
    use WithFileUploads;

    public string $activeTab = 'articles'; // articles | events | seo

    // Article form
    public bool   $isArticleFormOpen = false;
    public string $article_id        = '';
    public string $art_title         = '';
    public string $art_excerpt       = '';
    public string $art_content       = '';
    public string $art_status        = 'DRAFT';
    public string $art_meta_title    = '';
    public string $art_meta_desc     = '';
    public $art_image;

    // Filter
    public string $filterStatus = '';
    public string $searchQuery  = '';

    public function mount(): void
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin'])) {
            abort(403, 'CMS Manager is restricted.');
        }
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->isArticleFormOpen = false;
    }

    public function saveArticle(): void
    {
        $this->validate([
            'art_title'   => 'required|string|max:200',
            'art_content' => 'required|string',
            'art_image'   => 'nullable|image|max:4096',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        $slug     = Str::slug($this->art_title);

        $imagePath = null;
        if ($this->art_image) {
            $imagePath = $this->art_image->store('news_images', 'public');
        }

        $data = [
            'tenant_id'        => $tenantId,
            'author_id'        => auth()->id(),
            'title'            => $this->art_title,
            'slug'             => $slug . '-' . now()->format('YmdHis'),
            'excerpt'          => $this->art_excerpt ?: Str::limit(strip_tags($this->art_content), 200),
            'content'          => $this->art_content,
            'status'           => $this->art_status,
            'meta_title'       => $this->art_meta_title ?: $this->art_title,
            'meta_description' => $this->art_meta_desc ?: Str::limit(strip_tags($this->art_content), 160),
            'published_at'     => $this->art_status === 'PUBLISHED' ? now() : null,
        ];

        if ($imagePath) {
            $data['featured_image'] = $imagePath;
        }

        if ($this->article_id) {
            NewsArticle::findOrFail($this->article_id)->update($data);
        } else {
            NewsArticle::create($data);
        }

        $this->isArticleFormOpen = false;
        $this->reset(['article_id', 'art_title', 'art_excerpt', 'art_content', 'art_image']);
    }

    public function editArticle(string $id): void
    {
        $a = NewsArticle::findOrFail($id);
        $this->article_id    = $a->id;
        $this->art_title     = $a->title;
        $this->art_excerpt   = $a->excerpt ?? '';
        $this->art_content   = $a->content ?? '';
        $this->art_status    = $a->status;
        $this->art_meta_title = $a->meta_title ?? '';
        $this->art_meta_desc  = $a->meta_description ?? '';
        $this->isArticleFormOpen = true;
        $this->activeTab = 'articles';
    }

    public function deleteArticle(string $id): void
    {
        NewsArticle::destroy($id);
    }

    public function publishArticle(string $id): void
    {
        NewsArticle::findOrFail($id)->update([
            'status'       => 'PUBLISHED',
            'published_at' => now(),
        ]);
    }

    public function unpublishArticle(string $id): void
    {
        NewsArticle::findOrFail($id)->update([
            'status'       => 'DRAFT',
            'published_at' => null,
        ]);
    }

    public function render()
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        $articles = NewsArticle::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->searchQuery, fn($q) => $q->where('title', 'like', '%' . $this->searchQuery . '%'))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.cms.public-website-manager', compact('articles'))
            ->layout('layouts.app');
    }
}
