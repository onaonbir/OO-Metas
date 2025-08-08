<?php

declare(strict_types=1);

namespace OnaOnbir\OOMetas\Console;

use Illuminate\Console\Command;
use OnaOnbir\OOMetas\Contracts\MetaRepositoryInterface;
use OnaOnbir\OOMetas\Repositories\CachedMetaRepository;

class OOMetasStatusCommand extends Command
{
    protected $signature = 'oo-metas:status';

    protected $description = 'Show OOMetas configuration and cache status';

    public function handle(): int
    {
        $this->info('🧠 OOMetas Configuration Status');
        $this->line('');

        // Cache Configuration
        $this->line('📋 <info>Cache Configuration:</info>');
        $cacheEnabled = config('oo-metas.cache.enabled', true);
        $cacheTtl = config('oo-metas.cache.ttl', 3600);
        $cachePrefix = config('oo-metas.cache.prefix', 'oo_metas');
        $cacheDriver = config('cache.default', 'null');

        $this->line('   Enabled: '.($cacheEnabled ? '✅ Yes' : '❌ No'));
        $this->line("   TTL: {$cacheTtl} seconds (".gmdate('H:i:s', $cacheTtl).')');
        $this->line("   Prefix: {$cachePrefix}");
        $this->line("   Driver: {$cacheDriver}");
        $this->line('');

        // Repository Status
        $repository = app(MetaRepositoryInterface::class);
        $repositoryType = get_class($repository);
        $isCached = $repository instanceof CachedMetaRepository;

        $this->line('🏗️ <info>Repository Status:</info>');
        $this->line("   Type: {$repositoryType}");
        $this->line('   Caching: '.($isCached ? '✅ Active (CachedMetaRepository)' : '❌ Disabled (MetaRepository)'));
        $this->line('');

        // Performance Configuration
        $this->line('⚡ <info>Performance Configuration:</info>');
        $batchSize = config('oo-metas.performance.batch_size', 100);
        $queryTimeout = config('oo-metas.performance.query_timeout', 30);

        $this->line("   Batch Size: {$batchSize}");
        $this->line("   Query Timeout: {$queryTimeout} seconds");
        $this->line('');

        // Table Configuration
        $this->line('🗄️ <info>Database Configuration:</info>');
        $tableName = config('oo-metas.table_names.oo_metas', 'oo_metas');
        $this->line("   Table Name: {$tableName}");
        $this->line('');

        // Recommendations
        $this->line('💡 <info>Recommendations:</info>');

        if (! $cacheEnabled) {
            $this->warn('   • Enable caching for better performance: OO_METAS_CACHE_ENABLED=true');
        }

        if ($cacheDriver === 'null') {
            $this->warn('   • Consider using Redis or Memcached for production: CACHE_DRIVER=redis');
        }

        if ($cacheDriver === 'array') {
            $this->warn('   • Array cache is memory-only, consider persistent cache for production');
        }

        if ($isCached && $cacheEnabled) {
            $this->info('   ✅ Cache is properly configured and active');
        }

        return self::SUCCESS;
    }
}
