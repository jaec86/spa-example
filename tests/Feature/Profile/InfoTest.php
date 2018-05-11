<?php

namespace Tests\Feature\Profile;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InfoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function profile_info_retrived()
    {
        $user = factory(\App\User::class)->create();

        $this->actingAs($user)->json('GET', '/api/profile')
            ->assertJson([
                'message' => 'Profile info.',
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                ]
            ])->assertStatus(200);
    }
}
