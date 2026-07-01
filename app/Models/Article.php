<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
     protected $fillable = [
        'title',
        'url',
        'content',
        'summary',
        'key_points',
        'status',
        'failure_reason',
    ];

    protected $casts = [
        'key_points' => 'array',
    ];

    public function user()
{
    return $this->belongsTo(User::class);
}
}
