<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Facades\Tests\Setup\ProjectFactory;
use Tests\TestCase;

class ManageProjectsTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function guests_cannot_manage_a_projects()
    {
        // $project = factory('App\Models\Project')->create();
        $project = ProjectFactory::create();

        $this->get('/projects')->assertRedirect('login');
        $this->get($project->path())->assertRedirect('login');
        $this->post('/projects', $project->toArray())->assertRedirect('login');
    }

    /** @test */
    public function a_user_can_create_project()
    {
        $this->signIn();

        $this->withoutExceptionHandling();

        $this->get('/projects/create')->assertStatus(200);

        $attributes = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
        ];

        $this->post('/projects', $attributes)->assertRedirect('/projects');    
        $this->assertDatabaseHas('projects', $attributes);
        $this->get('projects')->assertSee($attributes['title']);
    }

    /** @test */
    function a_user_can_see_all_projects_they_have_been_invited_to_on_their_dashboard()
    {
        $john = $this->signIn();

        $project = factory('App\Models\Project')->create();
        $project->invite($john);

        $this->get('/projects')->assertSee($project->title);
    }

    /** @test */
    public function unauthorized_user_cannot_delete_a_project()
    {
        $project = factory('App\Models\Project')->create();

        $this->delete($project->path())
            ->assertRedirect('/login');

        $this->signIn();

        $this->delete($project->path())
            ->assertStatus(403);
    }

    /** @test */
    public function a_user_can_delete_a_project()
    {
        $this->signIn();

        $this->withoutExceptionHandling();

        $project = factory('App\Models\Project')->create();

        $this->actingAs($project->owner)
            ->delete($project->path())
            ->assertRedirect('projects');

        $this->assertDatabaseMissing('projects', $project->only('id'));
    }

    /** @test */
    public function a_projects_requeires_a_title()
    {
        $this->signIn();

        $attributes = factory('App\Models\Project')->raw(['title' => '']);

        $this->post('/projects', $attributes)->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_projects_requeires_a_description()
    {
        $this->signIn();

        $attributes = factory('App\Models\Project')->raw(['description' => '']);

        $this->post('/projects', $attributes)->assertSessionHasErrors('description');
    }

    /** @test */
    public function a_user_can_view_project()
    {
        // $project = factory('App\Models\Project')->create(['owner_id' => auth()->id()]);
        $project = ProjectFactory::ownedBy($this->signIn())->create();

        $this->actingAs($project->owner)
            ->get($project->path())
            ->assertSee($project->title)
            ->assertSee($project->description);
    }
    
    /** @test */
    public function authenticated_user_cannot_view_project_of_others()
    {
        $this->signIn();

        $project = factory('App\Models\Project')->create();

        $this->get($project->path())->assertStatus(403);
    }

    /** @test */
    public function a_user_can_update_projects()
    {
        $this->withoutExceptionHandling();
        $project = ProjectFactory::ownedBy($this->signIn())->create();

        $this->actingAs($project->owner)
            ->patch($project->path(), $attribute = [
                    'title' => 'Project Title',
                    'description' => 'Project Description',
                    'notes' => 'Project notes'
                ])
            ->assertRedirect($project->path());

        $this->assertDatabaseHas('projects', $attribute);

        $this->get($project->path())->assertSee('Project Title');
    }

    /** @test */
    public function a_user_can_update_general_notes()
    {
        $this->withoutExceptionHandling();
        $project = ProjectFactory::ownedBy($this->signIn())->create();

        $this->actingAs($project->owner)
            ->patch($project->path(), $attribute = [
                    'notes' => 'Project notes'
                ])
            ->assertRedirect($project->path());

        $this->assertDatabaseHas('projects', $attribute);
    }

    /** @test */
    public function a_user_can_update_projects_notes()
    {
        // $this->signIn();
        // $project = factory('App\Models\Project')->create(['owner_id' => auth()->id()]);
        $project = ProjectFactory::ownedBy($this->signIn())->create();

        $this->actingAs($project->owner)
            ->patch($project->path(), $notes = ['notes' => 'Add notes'])
            ->assertRedirect($project->path());

        $this->assertDatabaseHas('projects', $notes);

        $this->get($project->path())->assertSee('Add notes');
    }

    /** @test */
    public function only_owner_of_a_project_may_update_project_note()
    {
        $this->signIn();

        $project = factory('App\Models\Project')->create();
        
        $this->patch($project->path(), ['notes' => 'Add notes'])
            ->assertStatus(403);
    }
}
