<?php

namespace Tests\Feature\Auth;

use App\Mail\ResetPassword;
use Facades\App\Services\JwtAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function email_is_required()
    {
        $this->json('POST', '/api/register')
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => ['email' => [__('validation.required', ['attribute' => 'email'])]]
            ])->assertStatus(422);
    }

    /** @test */
    public function email_invalid()
    {
        $this->json('POST', '/api/password/forgot', ['email' => str_random(10)])
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => ['email' => [__('validation.email', ['attribute' => 'email'])]]
            ])->assertStatus(422);
    }

    /** @test */
    public function email_not_registered()
    {
        $this->json('POST', '/api/password/forgot', ['email' => $this->faker->email()])
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => ['email' => [__('validation.exists', ['attribute' => 'email'])]]
            ])->assertStatus(422);
    }

    /** @test */
    public function account_not_active()
    {
        $user = factory(\App\User::class)->create(['activated_at' => null]);
        $this->json('POST', '/api/password/forgot', ['email' => $user->email])
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => ['email' => [__('validation.exists', ['attribute' => 'email'])]]
            ])->assertStatus(422);
    }    

    /** @test */
    public function reset_link_sent()
    {
        $user = factory(\App\User::class)->create();

        $this->json('POST', '/api/password/forgot', ['email' => $user->email])
            ->assertJson([
                'message' => 'We have send you an email with the reset password link.',
            ])->assertStatus(200);

        Mail::assertSent(ResetPassword::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email)
                && $mail->user->email === $user->email
                && JwtAuth::verify($mail->token);
        });
    }
}
