<?php

namespace Tests\Feature\Auth;

use Facades\App\Services\JwtAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ActivateAccountTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function token_invalid()
    {
        $this->json('POST', '/api/activate/whatever')
            ->assertJson([
                'message' => 'The activation code is invalid.',
            ])->assertStatus(401);
    }

    /** @test */
    public function user_not_found()
    {
        $this->json('POST', '/api/activate/' . JwtAuth::createActivationToken(100))
            ->assertJson([
                'message' => 'The user does not exist.',
            ])->assertStatus(401);
    }

    /** @test */
    public function account_activated()
    {
        $user = factory(\App\User::class)->create(['activated_at' => null]);
        $token = JwtAuth::createActivationToken($user->uuid);

        $response = $this->json('POST', '/api/activate/' . $token)
            ->assertJson([
                'message' => 'Welcome!',
                'user' => [
                    'email' => $user->email,
                ]
            ])->assertJsonStructure(['access_token', 'refresh_token'])
            ->assertStatus(200);

        $data = json_decode($response->getContent());
        $this->assertTrue(JwtAuth::verify($data->access_token));
        $this->assertTrue(JwtAuth::verify($data->refresh_token));

        $user = \App\User::find($user->id);
        $this->assertTrue(Hash::check('secret', $user->password));
    }
}
