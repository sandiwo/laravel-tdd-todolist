<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activities';

    protected $guarded = [];

    protected $casts = [
        'changes' => 'array'
    ];

    public function subject()
    {
        return $this->morphTo();
    }
}
