<?php

namespace Tests\Feature\Auth;

use Facades\App\Services\JwtAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class RefreshTokenTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function refresh_token_not_found()
    {
        $this->json('POST', '/api/jwt/refresh')
            ->assertExactJson(['message' => 'We could not find the refresh code.'])
            ->assertStatus(401);
    }

    /** @test */
    public function refresh_token_not_valid()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer whatever'
        ])->json('POST', '/api/jwt/refresh')
            ->assertExactJson(['message' => 'The refresh code is invalid.'])
            ->assertStatus(401);
    }

    /** @test */
    public function refresh_token_is_expired()
    {
        $token = JwtAuth::createRefreshToken(1);
        $date = now()->addMinutes(config('settings.jwt.exp.refresh') + 1);
        Carbon::setTestNow($date);
        
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->json('POST', '/api/jwt/refresh')
            ->assertExactJson([
                'message' => 'The refresh code expired.'
            ])->assertStatus(401);
    }

    /** @test */
    public function refresh_token_is_blacklisted()
    {
        $token = JwtAuth::createAccessToken(1);
        JwtAuth::blacklist($token);
        
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->json('POST', '/api/jwt/refresh')
            ->assertExactJson([
                'message' => 'The refresh code is in blacklist.'
            ])->assertStatus(401);
    }

    /** @test */
    public function user_is_not_found()
    {
        $token = JWTAuth::createAccessToken(1);
        
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->json('POST', '/api/jwt/refresh')
            ->assertJson(['message' => 'The user does not exist.'])
            ->assertStatus(401);
    }

    /** @test */
    public function user_not_active()
    {
        $user = factory(\App\User::class)->create(['activated_at' => null]);
        $token = JwtAuth::createRefreshToken($user->uuid);

        $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->json('POST', '/api/jwt/refresh')
            ->assertJson(['message' => 'The account has not been activated.'])
            ->assertStatus(403);
    }

    /** @test */
    public function token_is_refreshed()
    {
        $user = factory(\App\User::class)->create();
        $token = JwtAuth::createRefreshToken($user->uuid);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->json('POST', '/api/jwt/refresh')
            ->assertJson([
                'message' => 'The access code was refreshed.',
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
