<?php

namespace Tests\Feature\Auth;

use Facades\App\Services\JwtAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InvalidateTokenTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function token_is_required()
    {
        $this->json('POST', '/api/jwt/invalidate')
            ->assertJson([
                'errors' => ['token' => [__('validation.required', ['attribute' => 'token'])]]
            ])->assertStatus(422);
    }

    /** @test */
    public function token_invalid_so_no_blacklisted()
    {
        $this->json('POST', '/api/jwt/invalidate', ['token' => 'whatever'])
            ->assertJson(['message' => 'The code was invalidated.'])
            ->assertStatus(200);

        $this->assertFalse(JwtAuth::isBlacklisted('whatever'));
    }

    /** @test */
    public function token_expired_so_no_blacklisted()
    {
        $token = JwtAuth::createToken(1, 0);

        $this->json('POST', '/api/jwt/invalidate', ['token' => $token])
            ->assertJson(['message' => 'The code was invalidated.'])
            ->assertStatus(200);

        $this->assertFalse(JwtAuth::isBlacklisted($token));
    }

    /** @test */
    public function token_already_blacklisted()
    {
        $token = JwtAuth::createToken(1, 60);
        JwtAuth::blacklist($token);

        $this->json('POST', '/api/jwt/invalidate', ['token' => $token])
            ->assertJson(['message' => 'The code was invalidated.'])
            ->assertStatus(200);

        $this->assertTrue(JwtAuth::isBlacklisted($token));
    }

    /** @test */
    public function token_blacklisted()
    {
        $token = JwtAuth::createToken(1, 60);

        $this->json('POST', '/api/jwt/invalidate', ['token' => $token])
            ->assertJson(['message' => 'The code was invalidated.'])
            ->assertStatus(200);

        $this->assertTrue(JwtAuth::isBlacklisted($token));
    }
}
