<?php

namespace Tests\Feature\Auth;

use App\Mail\ActivateAccount;
use Facades\App\Services\JwtAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function name_is_required()
    {
        $this->json('POST', '/api/register')
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => ['first_name' => [__('validation.required', ['attribute' => 'first name'])]]
            ])->assertStatus(422);
    }

    /** @test */
    public function lastname_is_required()
    {
        $this->json('POST', '/api/register')
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => ['last_name' => [__('validation.required', ['attribute' => 'last name'])]]
            ])->assertStatus(422);
    }

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
        $this->json('POST', '/api/register', ['email' => str_random(10)])
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => ['email' => [__('validation.email', ['attribute' => 'email'])]]
            ])->assertStatus(422);
    }

    /** @test */
    public function email_is_taken()
    {
        $user = factory(\App\User::class)->create();

        $this->json('POST', '/api/register', ['email' => $user->email])
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => ['email' => [__('validation.unique', ['attribute' => 'email'])]]
            ])->assertStatus(422);
    }

    /** @test */
    public function password_is_required()
    {
        $this->json('POST', '/api/register')
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => ['password' => [__('validation.required', ['attribute' => 'password'])]]
            ])->assertStatus(422);
    }

    /** @test */
    public function password_is_too_short()
    {
        $this->json('POST', '/api/register', ['password' => str_random(5)])
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => ['password' => [__('validation.min.string', ['attribute' => 'password', 'min' => 6])]]
            ])->assertStatus(422);
    }

    /** @test */
    public function password_not_confirmed()
    {
        $this->json('POST', '/api/register', ['password' => str_random(10)])
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => ['password' => [__('validation.confirmed', ['attribute' => 'password'])]]
            ])->assertStatus(422);
    }

    /** @test */
    public function user_is_registered()
    {
        $user = factory(\App\User::class)->make();

        $this->json('POST', '/api/register', [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'password' => '123456',
            'password_confirmation' => '123456',
        ])->assertJson([
            'message' => 'We have send you an email with the activation link.'
        ])->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
        ]);

        Mail::assertSent(ActivateAccount::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email)
                && $mail->user->email === $user->email
                && JwtAuth::verify($mail->token);
        });
    }
}
