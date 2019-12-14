<?php

namespace Tests\Setup;

class ProjectFactory {

    protected $taskCount = 0;

    protected $user = null;

    public function withTasks($count)
    {
        $this->taskCount = $count;
        
        return $this;
    }

    public function ownedBy($user = [])
    {
        $this->user = $user;

        return $this;
    }

    public function create()
    {
        $project = factory('App\Models\Project')->create([
            'owner_id' => $this->user ?? factory('App\User')->create()
        ]);
        
        factory('App\Models\Task', $this->taskCount)->create([
            'project_id' => $project->id
        ]);

        return $project;
    }
    
}