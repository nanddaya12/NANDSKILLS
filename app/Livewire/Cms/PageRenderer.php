<?php

namespace App\Livewire\Cms;

use Livewire\Component;
use App\Models\WebsitePage;
use App\Models\Course;
use App\Models\BlogPost;

class PageRenderer extends Component
{
    public string $slug = 'home';
    public ?array $pageData = null;
    public array $sections = [];

    public function mount(string $slug = 'home')
    {
        $this->slug = $slug;
        $this->loadPage();
    }

    public function loadPage()
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        if ($tenantId) {
            $page = WebsitePage::where('tenant_id', $tenantId)
                ->where('slug', $this->slug)
                ->first();

            if ($page) {
                $this->pageData = $page->toArray();
                $this->sections = $page->content['sections'] ?? [];
                return;
            }
        }

        // Fallback default layout configuration
        $this->sections = $this->getDefaultLayout();
    }

    protected function getDefaultLayout(): array
    {
        $courses = [];
        $posts = [];
        try {
            $courses = Course::limit(3)->get()->toArray();
            $posts = BlogPost::where('status', 'PUBLISHED')->limit(3)->get()->toArray();
        } catch (\Throwable $e) {
            // Gracefully ignore database errors if connection/migration is not set up yet
        }

        return [
            [
                'type' => 'notice_bar',
                'settings' => [
                    'text' => 'Admissions are now open for the 2026 Session. Apply before the deadline!',
                    'cta_text' => 'APPLY NOW',
                    'cta_url' => '/register',
                ]
            ],
            [
                'type' => 'hero',
                'settings' => [
                    'title' => 'Advance Your Future with Professional Training',
                    'subtitle' => 'Join premium institutional courses designed for high-demand skills, certifications, and career growth.',
                    'cta_text' => 'Explore Courses',
                    'cta_url' => '#courses',
                    'image_url' => 'https://illustrations.popsy.co/blue/studying.svg'
                ]
            ],
            [
                'type' => 'stats',
                'settings' => [
                    'items' => [
                        ['label' => 'Active Students', 'value' => '5,000+'],
                        ['label' => 'Certified Courses', 'value' => '50+'],
                        ['label' => 'Placement Rate', 'value' => '94%'],
                        ['label' => 'Expert Instructors', 'value' => '120+'],
                    ]
                ]
            ],
            [
                'type' => 'about',
                'settings' => [
                    'title' => 'Pioneering Excellence in Education',
                    'description' => 'We are committed to providing premium vocational, professional, and academic certifications to empower the next generation of industry experts.',
                    'mission' => 'To make career-focused education accessible, interactive, and outcome-oriented.',
                ]
            ],
            [
                'type' => 'courses',
                'settings' => [
                    'title' => 'Our Featured Programs',
                    'subtitle' => 'Choose from our high-demand certification courses.',
                    'items' => $courses
                ]
            ],
            [
                'type' => 'blogs',
                'settings' => [
                    'title' => 'Latest Insights & Articles',
                    'subtitle' => 'Stay updated with education trends and platform updates.',
                    'items' => $posts
                ]
            ],
            [
                'type' => 'contact',
                'settings' => [
                    'title' => 'Get in Touch',
                    'phone' => '+92-300-1234567',
                    'email' => 'info@nandskills.com',
                    'address' => 'Jamshoro Phase 1, Sindh'
                ]
            ]
        ];
    }

    public function render()
    {
        return view('livewire.cms.page-renderer')
            ->layout('layouts.guest');
    }
}
