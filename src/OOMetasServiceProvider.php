<?php

declare(strict_types=1);

namespace OnaOnbir\OOMetas;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\Repository as CacheRepository;
use OnaOnbir\OOMetas\Contracts\MetaRepositoryInterface;
use OnaOnbir\OOMetas\Contracts\MetaCacheInterface;
use OnaOnbir\OOMetas\Contracts\MetaServiceInterface;
use OnaOnbir\OOMetas\Repositories\MetaRepository;
use OnaOnbir\OOMetas\Repositories\CachedMetaRepository;
use OnaOnbir\OOMetas\Services\MetaCacheService;
use OnaOnbir\OOMetas\Services\MetaService;

class OOMetasServiceProvider extends ServiceProvider
{
    private string $packageName = 'oo-metas';

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        
        $this->bootPublishing();
    }

    public function register(): void
    {
        $this->registerConfig();
        $this->registerServices();
    }

    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/'.$this->packageName.'.php',
            $this->packageName
        );
    }

    protected function registerServices(): void
    {
        // Register Cache Service
        $this->app->singleton(MetaCacheInterface::class, function ($app) {
            return new MetaCacheService(
                $app->make(CacheRepository::class)
            );
        });

        // Register Repository
        $this->app->singleton(MetaRepositoryInterface::class, function ($app) {
            $baseRepository = new MetaRepository();
            
            if (config('oo-metas.cache.enabled', true)) {
                return new CachedMetaRepository(
                    $baseRepository,
                    $app->make(MetaCacheInterface::class)
                );
            }
            
            return $baseRepository;
        });

        // Register Service
        $this->app->singleton(MetaServiceInterface::class, function ($app) {
            return new MetaService(
                $app->make(MetaRepositoryInterface::class)
            );
        });
    }

    protected function bootPublishing(): void
    {
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], $this->packageName.'-migrations');

        $this->publishes([
            __DIR__.'/../config/'.$this->packageName.'.php' => config_path($this->packageName.'.php'),
        ], $this->packageName.'-config');
    }
}
