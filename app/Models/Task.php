<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $guarded = [];

    protected $casts = [
        'completed' => 'boolean'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function path()
    {
        return "projects/{$this->project->id}/tasks/{$this->id}";
    }

    public function complete()
    {
        $this->update(['completed' => true]);

        $this->recordActivity('completed_task');
    }

    public function incomplete()
    {
        $this->update(['completed' => false]);

        $this->recordActivity('incompleted_task');
    }

    public function activity()
    {
        return $this->morphMany(Activity::class, 'subject');
    }
    
    public function recordActivity($description)
    {
        $this->activity()->create([
            'project_id' => $this->project_id,
            'description' => $description
        ]);
    }

    protected static function boot()
    {
        parent::boot();
        // static::updated(function($task) {
        //     $task->project->recordActivity('updated_task');
        // });
    }
}
