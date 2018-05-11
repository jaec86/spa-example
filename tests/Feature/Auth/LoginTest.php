<?php

namespace Tests\Feature\Auth;

use Facades\App\Services\JwtAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function email_is_required()
    {
        $this->json('POST', '/api/login')
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => ['email' => [__('validation.required', ['attribute' => 'email'])]]
            ])->assertStatus(422);
    }

    /** @test */
    public function password_is_required()
    {
        $this->json('POST', '/api/login')
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => ['password' => [__('validation.required', ['attribute' => 'password'])]]
            ])->assertStatus(422);
    }

    /** @test */
    public function email_not_registered()
    {
        $this->json('POST', '/api/login', [
            'email' => 'johndoe@email.com',
            'password' => 'secret'
        ])->assertJson([
            'message' => __('auth.failed')
        ])->assertStatus(422);
    }

    /** @test */
    public function block_login_after_five_failed_attempts()
    {
        for ($i = 0; $i < 6; $i++) {
            $response = $this->json('POST', '/api/login', [
                    'email' => 'jestupinan@zeerbyte.com',
                    'password' => 'secret'
            ]);
        }

        $response->assertStatus(423)
            ->assertJson([
                'message' => __('auth.throttle', ['seconds' => 60])
            ]);
    }

    /** @test */
    public function user_not_active()
    {
        $user = factory(\App\User::class)->create(['activated_at' => null]);

        $this->json('POST', '/api/login', [
            'email' => $user->email,
            'password' => 'secret'
        ])->assertJson(['message' => 'The account has not been activated.'])
            ->assertStatus(403);
    }

    /** @test */
    public function user_logged_in()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->json('POST', '/api/login', [
            'email' => $user->email,
            'password' => 'secret',
        ])->assertJson([
            'message' => 'Welcome!',
            'user' => [
                'email' => $user->email,
            ]
        ])->assertJsonStructure(['access_token', 'refresh_token'])
            ->assertStatus(200);

        $data = json_decode($response->getContent());
        $this->assertTrue(JwtAuth::verify($data->access_token));
        $this->assertTrue(JwtAuth::verify($data->refresh_token));
    }
}
