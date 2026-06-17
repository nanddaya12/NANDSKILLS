<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ url('/admissions/apply') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    @foreach($articles ?? [] as $article)
    @if($article->status === 'PUBLISHED')
    <url>
        <loc>{{ url('/news/' . $article->slug) }}</loc>
        <lastmod>{{ optional($article->updated_at)->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    @endif
    @endforeach
</urlset>
