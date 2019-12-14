<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Project extends Model
{
    protected $guarded = [];

    public $old = [];

    public function path()
    {
        return "/projects/{$this->id}";
    }

    public function owner()
    {
        return $this->belongsTo(\App\User::class, 'owner_id'); 
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function activity()
    {
        return $this->hasMany(Activity::class)->latest();
    }

    public function addTask($body = null)
    {
        return $this->tasks()->create(compact('body'));
    }
    
    public function recordActivity($description)
    {
        $this->activity()->create([
            'description' => $description,
            'changes' => $this->activityChanges()
        ]);
    }

    public function activityChanges()
    {
        if($this->wasChanged()) {
            return [
                'before' => Arr::except(array_diff($this->old, $this->toArray()), 'updated_at'),
                'after' => Arr::except($this->getChanges(), 'updated_at')
            ];
        }
    }

}
