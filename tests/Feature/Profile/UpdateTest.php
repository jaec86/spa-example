<?php

namespace Tests\Feature\Profile;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory(\App\User::class)->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function name_is_required()
    {
        $this->json('PUT', '/api/profile')
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => ['first_name' => [__('validation.required', ['attribute' => 'first name'])]]
            ])->assertStatus(422);
    }

    /** @test */
    public function lastname_is_required()
    {
        $this->json('PUT', '/api/profile')
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => ['last_name' => [__('validation.required', ['attribute' => 'last name'])]]
            ])->assertStatus(422);
    }

    /** @test */
    public function email_is_required()
    {
        $this->json('PUT', '/api/profile')
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => ['email' => [__('validation.required', ['attribute' => 'email'])]]
            ])->assertStatus(422);
    }

    /** @test */
    public function email_invalid()
    {
        $this->json('PUT', '/api/profile', ['email' => str_random(10)])
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => ['email' => [__('validation.email', ['attribute' => 'email'])]]
            ])->assertStatus(422);
    }

    /** @test */
    public function email_is_taken()
    {
        $user = factory(\App\User::class)->create();

        $this->json('PUT', '/api/profile', ['email' => $user->email])
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => ['email' => [__('validation.unique', ['attribute' => 'email'])]]
            ])->assertStatus(422);
    }

    /** @test */
    public function user_is_registered()
    {
        $user = factory(\App\User::class)->make();

        $this->json('PUT', '/api/profile', [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $this->user->email,
        ])->assertJson([
            'message' => 'Your profile was updated.'
        ])->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $this->user->email,
        ]);
    }
}
