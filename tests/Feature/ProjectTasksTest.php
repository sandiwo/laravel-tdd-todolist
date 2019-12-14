<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Facades\Tests\Setup\ProjectFactory;
use Tests\TestCase;

class ProjectTasksTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function only_owner_of_a_project_may_add_tasks()
    {
        // $this->signIn();
        
        // $project = factory('App\Models\Project')->create();
        $project = ProjectFactory::create();

        $this->actingAs($this->signIn())
            ->post($project->path() . '/tasks', $task = ['body' => 'Add task'])
            ->assertStatus(403);

        $this->assertDatabaseMissing('tasks', $task);
    }

    /** @test */
    public function a_project_can_have_tasks()
    {
        $this->withoutExceptionHandling();
        $this->signIn();

        // $project = factory('App\Models\Project')->create(['owner_id' => auth()->id()]);
        // or 
        $project = auth()->user()->projects()->create(
            factory('App\Models\Project')->raw()
        );

        $this->post($project->path() . '/tasks', ['body' => 'Test task']);

        $this->get($project->path())
            ->assertSee('Test task');
    }

    /** @test */
    public function a_project_can_updated()
    {
        $this->withoutExceptionHandling(); 
        // $this->signIn();

        // $project = auth()->user()->projects()->create(
        //     factory('App\Models\Project')->raw()
        // );
        
        // $task = $project->addTask('test create task');

        $project = ProjectFactory::withTasks(1)->create();

        $attribute = [
            'body' => 'test update task',
            'completed' => true
        ];

        $this->actingAs($project->owner)
            ->patch($project->tasks[0]->path(), $attribute)
            ->assertRedirect($project->path());

        $this->assertDatabaseHas('tasks', $attribute);
    }

    /** @test */
    public function only_the_owner_a_project_may_update_a_task()
    {
    //     $this->signin();

    //     $project = factory('app\models\project')->create();

    //     $task = $project->addtask('task one');
        $project = ProjectFactory::withTasks(1)->create();
         
        $this->actingAs($this->signIn())
            ->patch($project->tasks[0]->path(), $task = ['body' => 'Task one updated'])
            ->assertStatus(403);

        $this->assertDatabaseMissing('tasks', $task);
    }

    /** @test */
    public function a_tasks_requeires_a_body()
    {
        // $this->signIn();

        // $project = factory('App\Models\Project')->create(['owner_id' => auth()->id()]);
        $project = ProjectFactory::create();

        $task = factory('App\Models\Task')->raw(['body' => '']);

        $this->actingAs($project->owner)
            ->post($project->path() . '/tasks', $task)
            ->assertSessionHasErrors('body');
    }

}
