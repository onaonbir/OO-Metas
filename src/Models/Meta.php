<?php

namespace OnaOnbir\OOMetas\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use OnaOnbir\OOMetas\Models\Traits\JsonCast;

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

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function connected(): MorphTo
    {
        return $this->morphTo();
    }
}
