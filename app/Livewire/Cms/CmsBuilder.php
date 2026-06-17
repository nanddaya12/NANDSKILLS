<?php

namespace App\Livewire\Cms;

use Livewire\Component;
use App\Models\WebsitePage;
use App\Models\Course;
use App\Models\BlogPost;

class CmsBuilder extends Component
{
    public string $slug = 'home';
    public array $sections = [];
    public ?string $editingSectionIndex = null;
    public array $editingSectionSettings = [];
    public string $statusMessage = '';

    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin'])) {
            abort(403, 'Unauthorized action. CMS management is restricted to Administrators.');
        }
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
                $this->sections = $page->content['sections'] ?? [];
                return;
            }
        }

        // Fallback default structure
        $this->sections = $this->getDefaultLayout();
    }

    protected function getDefaultLayout(): array
    {
        $courses = Course::limit(3)->get()->toArray();
        $posts = BlogPost::where('status', 'PUBLISHED')->limit(3)->get()->toArray();

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

    public function selectSection($index)
    {
        $this->editingSectionIndex = (string) $index;
        $this->editingSectionSettings = $this->sections[$index]['settings'] ?? [];
        $this->statusMessage = '';
    }

    public function updateSectionSettings()
    {
        if ($this->editingSectionIndex !== null) {
            $index = (int) $this->editingSectionIndex;
            $this->sections[$index]['settings'] = $this->editingSectionSettings;
            $this->editingSectionIndex = null;
            $this->statusMessage = 'Section layout updated locally. Save changes to publish.';
        }
    }

    public function moveUp($index)
    {
        if ($index > 0) {
            $temp = $this->sections[$index];
            $this->sections[$index] = $this->sections[$index - 1];
            $this->sections[$index - 1] = $temp;
            $this->statusMessage = 'Section order updated locally.';
        }
    }

    public function moveDown($index)
    {
        if ($index < count($this->sections) - 1) {
            $temp = $this->sections[$index];
            $this->sections[$index] = $this->sections[$index + 1];
            $this->sections[$index + 1] = $temp;
            $this->statusMessage = 'Section order updated locally.';
        }
    }

    public function deleteSection($index)
    {
        array_splice($this->sections, $index, 1);
        $this->editingSectionIndex = null;
        $this->statusMessage = 'Section removed locally.';
    }

    public function addSection($type)
    {
        $newSection = [
            'type' => $type,
            'settings' => []
        ];

        switch ($type) {
            case 'notice_bar':
                $newSection['settings'] = [
                    'text' => 'New alert notification message!',
                    'cta_text' => 'Learn More',
                    'cta_url' => '#'
                ];
                break;
            case 'hero':
                $newSection['settings'] = [
                    'title' => 'New Awesome Header Title',
                    'subtitle' => 'This is a subtitle detail.',
                    'cta_text' => 'Click Here',
                    'cta_url' => '#',
                    'image_url' => 'https://illustrations.popsy.co/blue/studying.svg'
                ];
                break;
            case 'about':
                $newSection['settings'] = [
                    'title' => 'About Us',
                    'description' => 'Write details here...',
                    'mission' => 'Write mission here...'
                ];
                break;
            case 'contact':
                $newSection['settings'] = [
                    'title' => 'Contact Us',
                    'phone' => '+00-000-0000000',
                    'email' => 'contact@example.com',
                    'address' => '123 Main Street'
                ];
                break;
        }

        $this->sections[] = $newSection;
        $this->statusMessage = 'New section block appended.';
    }

    public function savePage()
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        if (!$tenantId) {
            session()->flash('error', 'Must be in tenant context to save.');
            return;
        }

        WebsitePage::updateOrCreate(
            ['tenant_id' => $tenantId, 'slug' => $this->slug],
            [
                'title' => 'Home Landing Page',
                'content' => ['sections' => $this->sections],
                'status' => 'PUBLISHED'
            ]
        );

        $this->statusMessage = 'Website layout changes published successfully!';
    }

    public function render()
    {
        return view('livewire.cms.cms-builder')
            ->layout('layouts.app');
    }
}
