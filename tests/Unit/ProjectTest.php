<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */   
    public function it_has_a_path()
    {
        $project = factory('App\Models\Project')->make();
        
        $this->assertEquals("/projects/{$project->id}", $project->path());
    }

    /** @test */
    public function it_belongs_to_an_owner()
    {
        $project = factory('App\Models\Project')->make();

        $this->assertInstanceOf('App\User', $project->owner);
    }

    /** @test */
    public function it_can_add_task()
    {
        $project = factory('App\Models\Project')->create();

        $task = $project->addTask('Test Task');

        $this->assertCount(1, $project->tasks);
        $this->assertDatabaseHas('tasks', $project->tasks->toArray());
        $this->assertTrue($project->tasks->contains($task));
    }
}
