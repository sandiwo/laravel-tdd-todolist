<?php

namespace Tests\Unit;

use Facades\Tests\Setup\ProjectFactory;
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

    /** @test */
    function it_can_invite_user()
    {
        $project = factory('App\Models\Project')->create();
        $project->invite($newMember = factory('App\User')->create());

        $project->members->contains($newMember);
        $this->assertCount(1, $project->members);
    }

    /** @test */
    public function it_can_accessible_project()
    {
        $john = factory('App\User')->create();

        $project = ProjectFactory::ownedBy($john)->create();

        $this->assertCount(1, $john->accessibleProject());

        $sally = factory('App\User')->create();
        $nick = factory('App\User')->create();

        $sallyProject = ProjectFactory::ownedBy($sally)->create();
        $sallyProject->invite($nick);

        $this->assertCount(1, $john->accessibleProject());

        $sallyProject->invite($john);
        $this->assertCount(2, $john->accessibleProject());
    }
}
