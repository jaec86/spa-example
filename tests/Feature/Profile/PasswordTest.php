<?php

namespace Tests\Feature\Profile;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory(\App\User::class)->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function old_password_is_required()
    {
        $this->json('PUT', '/api/profile/password')
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => ['old_password' => [__('validation.required', ['attribute' => 'old password'])]]
            ])->assertStatus(422);   
    }

    /** @test */
    public function old_password_invalid()
    {
        $this->json('PUT', '/api/profile/password', ['old_password' => str_random(10)])
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => ['old_password' => ['Your old password is invalid.']]
            ])->assertStatus(422);   
    }

    /** @test */
    public function new_password_is_required()
    {
        $this->json('PUT', '/api/profile/password')
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => ['new_password' => [__('validation.required', ['attribute' => 'new password'])]]
            ])->assertStatus(422);   
    }

    /** @test */
    public function new_password_invalid()
    {
        $this->json('PUT', '/api/profile/password', ['new_password' => '12345'])
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => ['new_password' => [__('validation.min.string', ['attribute' => 'new password', 'min' => 6])]]
            ])->assertStatus(422);   
    }

    /** @test */
    public function new_password_not_confirmed()
    {
        $this->json('PUT', '/api/profile/password', ['new_password' => '123456'])
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => ['new_password' => [__('validation.confirmed', ['attribute' => 'new password'])]]
            ])->assertStatus(422);   
    }

    /** @test */
    public function new_password_saved()
    {
        $this->json('PUT', '/api/profile/password',[
            'old_password' => 'secret',
            'new_password' => '123456',
            'new_password_confirmation' => '123456',
        ])->assertJson([
            'message' => 'Your new password was saved.',
        ])->assertStatus(200);

        $user = \App\User::find($this->user->id);
        $this->assertFalse($this->user->uuuid === $user->uuid);
        $this->assertTrue(Hash::check('123456', $user->password));   
    }
}
