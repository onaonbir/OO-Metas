<?php

declare(strict_types=1);

namespace OnaOnbir\OOMetas\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;
use OnaOnbir\OOMetas\Models\Traits\JsonCast;
use OnaOnbir\OOMetas\ValueObjects\MetaIdentifier;
use OnaOnbir\OOMetas\ValueObjects\MetaKey;

class Meta extends Model
{
    protected $table;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('oo-metas.table_names.oo_metas', 'oo_metas');
    }

    protected $fillable = [
        'connected_id',
        'connected_type',
        'model_id',
        'model_type',
        'key',
        'value',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'value' => JsonCast::class,
    ];

    // Relationships
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function connected(): MorphTo
    {
        return $this->morphTo();
    }

    // Query Scopes
    public function scopeForIdentifier(Builder $query, MetaIdentifier $identifier): Builder
    {
        $query->where('model_type', $identifier->modelType)
              ->where('model_id', $identifier->modelId);

        if ($identifier->hasFullConnection()) {
            $query->where('connected_type', $identifier->connectedType)
                  ->where('connected_id', $identifier->connectedId);
        } elseif ($identifier->hasTypeOnlyConnection()) {
            $query->where('connected_type', $identifier->connectedType)
                  ->whereNull('connected_id');
        } else {
            $query->whereNull('connected_type')
                  ->whereNull('connected_id');
        }

        return $query;
    }

    public function scopeForKey(Builder $query, MetaKey $key): Builder
    {
        return $query->where('key', $key->getMainKey());
    }

    public function scopeForModel(Builder $query, Model $model): Builder
    {
        return $query->where('model_type', get_class($model))
                     ->where('model_id', (string) $model->getKey());
    }

    public function scopeWithoutConnection(Builder $query): Builder
    {
        return $query->whereNull('connected_type')
                     ->whereNull('connected_id');
    }

    public function scopeWithConnection(Builder $query, Model $connected): Builder
    {
        return $query->where('connected_type', get_class($connected))
                     ->where('connected_id', (string) $connected->getKey());
    }

    public function scopeWithConnectionType(Builder $query, string $connectedType): Builder
    {
        return $query->where('connected_type', $connectedType)
                     ->whereNull('connected_id');
    }

    // Helper methods
    public function getNestedValue(string $nestedKey, mixed $default = null): mixed
    {
        return data_get($this->value, $nestedKey, $default);
    }

    public function setNestedValue(string $nestedKey, mixed $value): void
    {
        $currentValue = $this->value ?? [];
        
        if (!is_array($currentValue)) {
            $currentValue = [];
        }
        
        data_set($currentValue, $nestedKey, $value);
        $this->value = $currentValue;
    }

    public function hasNestedValue(string $nestedKey): bool
    {
        return data_get($this->value, $nestedKey) !== null;
    }

    public function getIdentifier(): MetaIdentifier
    {
        return new MetaIdentifier(
            modelType: $this->model_type,
            modelId: $this->model_id,
            connectedType: $this->connected_type,
            connectedId: $this->connected_id
        );
    }
}
