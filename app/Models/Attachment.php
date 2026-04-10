<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;


class Attachment extends Model
{
    protected $fillable = [
        'attachable_id',
        'attachable_type',
        'path',
        'mime_type',
        'file_name',
        'file_type',
        'file_size',
    ];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }
}
