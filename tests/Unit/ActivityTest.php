<?php

namespace Tests\Unit;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityTest extends TestCase
{

    use RefreshDatabase;
    
    /** @test */
    function it_has_a_user()
    {
        $this->signIn();

        $project = factory('App\Models\Project')->create();

        $this->assertInstanceOf(User::class, $project->activity->first()->user);
    }
}
