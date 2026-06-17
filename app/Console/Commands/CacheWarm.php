<?php

namespace App\Console\Commands;

use App\Services\PerformanceOptimizer;
use Illuminate\Console\Command;

class CacheWarm extends Command
{
    protected $signature   = 'app:cache-warm';
    protected $description = 'Warm up application caches for all active tenants';

    public function handle(): int
    {
        $this->info('🔥 Warming caches for all active tenants…');
        PerformanceOptimizer::warmAll();
        $this->info('✅ Cache warm-up complete.');
        return self::SUCCESS;
    }
}
