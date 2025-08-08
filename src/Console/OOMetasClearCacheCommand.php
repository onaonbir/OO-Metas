<?php

declare(strict_types=1);

namespace OnaOnbir\OOMetas\Console;

use Illuminate\Console\Command;
use OnaOnbir\OOMetas\Contracts\MetaCacheInterface;

class OOMetasClearCacheCommand extends Command
{
    protected $signature = 'oo-metas:clear-cache {--prefix= : Clear only specific prefix}';
    protected $description = 'Clear OOMetas cache';

    public function handle(): int
    {
        $prefix = $this->option('prefix');
        
        try {
            $cache = app(MetaCacheInterface::class);
            
            if ($prefix) {
                // Clear specific prefix (would need implementation in cache service)
                $this->info("Clearing cache with prefix: {$prefix}");
                // cache()->forget() pattern for specific keys
                $this->warn('Prefix-specific clearing not yet implemented');
                return self::FAILURE;
            } else {
                // Clear all OOMetas cache
                $result = $cache->flush();
                
                if ($result) {
                    $this->info('✅ OOMetas cache cleared successfully');
                    return self::SUCCESS;
                } else {
                    $this->error('❌ Failed to clear OOMetas cache');
                    return self::FAILURE;
                }
            }
        } catch (\Exception $e) {
            $this->error("❌ Error clearing cache: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
