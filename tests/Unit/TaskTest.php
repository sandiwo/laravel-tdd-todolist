<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function it_belongs_to_project()
    {
        $task = factory('App\Models\Task')->create();

        $this->assertInstanceOf('App\Models\Project', $task->project);
    }

    /** @test */
    public function it_has_a_path()
    {
        $project = factory('App\Models\Project')->create();
        $task = $project->addTask('add new task');
        $this->assertEquals("projects/{$project->id}/tasks/{$task->id}", $task->path());
    }

    /** @test */
    function it_can_be_completed()
    {
        $task = factory('App\Models\Task')->create();
        $this->assertFalse($task->completed);

        $task->complete();

        $this->assertTrue($task->fresh()->completed);
        $this->assertDatabaseHas('tasks', ['completed' => true]);
    }

    /** @test */
    function it_can_be_marked_as_incomplete()
    {
        $task = factory('App\Models\Task')->create(['completed' => true]);
        $this->assertTrue($task->completed);

        $task->incomplete();

        $this->assertFalse($task->fresh()->completed);
        $this->assertDatabaseHas('tasks', ['completed' => false]);
    }
}
