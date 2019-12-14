<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Facades\Tests\Setup\ProjectFactory;
use Tests\TestCase;

class ActivityRecordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function creating_a_project()
    {
        $project = ProjectFactory::create();

        $this->assertCount(1, $project->activity);
        tap($project->activity->last(), function($activity) {
            $this->assertNull($activity->changes);
            $this->assertDatabaseHas('activities', ['description' => 'created_project']);
        });
    }

    /** @test */
    function updating_a_project()
    {
        $project = ProjectFactory::create();
        $projectTitle = $project->title;

        $project->update(['title' => 'project updated']);

        tap($project->activity->last(), function($activity) use ($projectTitle) {
            $this->assertDatabaseHas('activities', ['description' => 'updated_project']);
            
            $expected = [
                'before' => ['title' => $projectTitle],
                'after' => ['title' => 'project updated']
            ];

            $this->assertEquals($expected, $activity->changes);
        });
        $this->assertCount(2, $project->activity);
    }

    /** @test */
    function creating_a_task()
    {
        $this->withoutExceptionHandling();
        $project = ProjectFactory::create();
        $project->addTask('new task');

        tap($project->activity->last(), function($activity) {
            $this->assertDatabaseHas('activities', ['description' => 'created_task']);
            $this->assertInstanceOf('App\Models\Task', $activity->subject);
        });
    }

    /** @test */
    function updating_a_task()
    {
        $project = ProjectFactory::withTasks(1)->create();

        // $this->actingAs($project->owner)
        //     ->patch($project->tasks[0]->path(), [
        //         'body' => 'updated task body'
        //     ]);

        $project->tasks[0]->update([
            'body' => 'updated task body'
        ]);

        tap($project->activity->last(), function($activity) {
            $this->assertInstanceOf('App\Models\Task', $activity->subject);
            $this->assertDatabaseHas('activities', ['description' => $activity->description]);
        });
    }

    /** @test */
    function completed_a_task()
    {
        $this->withoutExceptionHandling();
        $project = ProjectFactory::withTasks(1)->create();

        $attribute = [
            'body' => 'test update task',
            'completed' => true
        ];

        $this->actingAs($project->owner)
            ->patch($project->tasks[0]->path(), $attribute);

        tap($project->activity->last(), function($activity) {
            $this->assertInstanceOf('App\Models\Task', $activity->subject);
            $this->assertDatabaseHas('activities', ['description' => $activity->description]);
            $this->assertDatabaseHas('activities', ['description' => 'completed_task']);
        });
    }

    /** @test */
    function a_task_can_be_marked_as_incomplete()
    {
        $project = ProjectFactory::withTasks(1)->create();

        $this->actingAs($project->owner)
            ->patch($project->tasks[0]->path(), [
                'body' => 'test update task',
            ]);

        $this->assertCount(3, $project->activity);

        $this->actingAs($project->owner)
            ->patch($project->tasks[0]->path(), [
                'completed' => false
            ]);

        $project = $project->refresh();

        $this->assertCount(3, $project->activity);

        $activity = $project->activity->last();
        $this->assertEquals('incompleted_task', $activity->description);
        $this->assertInstanceOf('App\Models\Task', $activity->subject);
    }

    /** @test */
    function a_task_can_be_deleted()
    {
        $project = ProjectFactory::withTasks(1)->create();
        
        $project->tasks[0]->delete();

        tap($project->activity->last(), function($activity) {
            // $this->assertInstanceOf('App\Models\Task', $activity->subject);
            $this->assertDatabaseHas('activities', ['description' => $activity->description]);
            $this->assertDatabaseHas('activities', ['description' => 'deleted_task']);
        });
    }
}
