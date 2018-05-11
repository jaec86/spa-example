<?php

namespace Tests\Feature\Auth;

use Facades\App\Services\JwtAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory(\App\User::class)->create();
        $this->token = JwtAuth::createResetToken($this->user->uuid);
    }

    /** @test */
    public function token_invalid()
    {
        $this->json('POST', '/api/password/reset/whatever')
            ->assertJson([
                'message' => 'The reset code is invalid.',
            ])->assertStatus(401);
    }

    /** @test */
    public function token_expired()
    {
        $token = JwtAuth::createResetToken(1);
        $date = now()->addMinutes(config('settings.jwt.exp.reset') + 1);
        Carbon::setTestNow($date);

        $this->json('POST', '/api/password/reset/' . $token)
            ->assertJson([
                'message' => 'The reset code expired.',
            ])->assertStatus(401);
    }

    /** @test */
    public function reset_token_is_blacklisted()
    {
        $token = JwtAuth::createResetToken(1);
        JwtAuth::blacklist($token);
        
        $this->json('POST', '/api/password/reset/' . $token)
            ->assertExactJson([
                'message' => 'The reset code is in blacklist.'
            ])->assertStatus(401);
    }

    /** @test */
    public function user_not_found()
    {
        $this->json('POST', '/api/password/reset/' . JwtAuth::createToken(100, 60))
            ->assertJson([
                'message' => 'The user does not exist.',
            ])->assertStatus(401);
    }

    /** @test */
    public function password_is_required()
    {
        $this->json('POST', '/api/password/reset/' . $this->token)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => ['password' => [__('validation.required', ['attribute' => 'password'])]]
            ])->assertStatus(422);
    }

    /** @test */
    public function password_too_short()
    {
        $this->json('POST', '/api/password/reset/' . $this->token, ['password' => '12345'])
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => ['password' => [__('validation.min.string', ['attribute' => 'password', 'min' => 6])]]
            ])->assertStatus(422);
    }

    /** @test */
    public function password_not_confirmed()
    {
        $this->json('POST', '/api/password/reset/' . $this->token, ['password' => '123456'])
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => ['password' => [__('validation.confirmed', ['attribute' => 'password'])]]
            ])->assertStatus(422);
    }

    /** @test */
    public function new_password_saved()
    {
        $this->json('POST', '/api/password/reset/' . $this->token, [
            'password' => '123456',
            'password_confirmation' => '123456'
        ])->assertJson([
            'message' => 'Welcome!',
            'user' => [
                'email' => $this->user->email,
            ]
        ])->assertJsonStructure(['access_token', 'refresh_token'])
            ->assertStatus(200);

        $user = \App\User::find($this->user->id);
        $this->assertFalse($this->user->uuuid === $user->uuid);
        $this->assertTrue(Hash::check('123456', $user->password));
    }
}
