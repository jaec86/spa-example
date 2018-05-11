<?php

namespace App\Services;

use App\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class JwtAuth
{
    public function createAccessToken($sub, $options = [])
    {
        return $this->createToken($sub, config('settings.jwt.exp.access'), $options);
    }

    public function createRefreshToken($sub, $options = [])
    {
        return $this->createToken($sub, config('settings.jwt.exp.refresh'), $options);
    }

    public function createResetToken($sub, $options = [])
    {
        return $this->createToken($sub, config('settings.jwt.exp.reset'), $options);
    }

    public function createActivationToken($sub, $options = [])
    {
        return $this->createToken($sub, 0, $options);
    }

    public function createToken($sub, $exp, $options = [])
    {
        $header = $this->encode(config('settings.jwt.header'));

        $payload = $this->encode(array_merge([
            'sub' => $sub,
            'exp' => $exp === 0 ? null : time() + ($exp * 60),
            'jti' => $sub . str_random(10),
        ], $options));

        $signature = $this->sign($header, $payload);
        
        return "$header.$payload.$signature";
    }

    protected function encode($data)
    {
        return $this->base64url_encode(json_encode($data));
    }

    protected function decode($data)
    {
        return json_decode($this->base64url_decode($data));
    }

    protected function base64url_encode($data) { 
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
    }

    protected function base64url_decode($data) { 
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
    }

    protected function sign($header, $payload)
    {
        return $this->base64url_encode(hash_hmac('SHA256', "$header.$payload", config('settings.jwt.key'), true));
    }

    protected function getClaims($token)
    {
        $payload = explode('.', $token);

        if (count($payload) !== 3) return null;

        $claims = $this->decode($payload[1]);

        return $claims;
    }

    public function verify($token)
    {
        $segments = explode('.', $token);

        if (count($segments) !== 3) return false;

        $expected = $this->base64url_encode(hash_hmac('sha256', "$segments[0].$segments[1]", config('settings.jwt.key'), true));

        return hash_equals($expected, $segments[2]);
    }

    public function isExpired($token)
    {
        $claims = $this->getClaims($token);

        if (is_null($claims->exp)) {
            return false;
        }

        return Carbon::createFromTimestamp($claims->exp)->isPast();
    }

    public function getUser($token)
    {
        $claims = $this->getClaims($token);
        return User::where('uuid', $claims->sub)->first();
    }

    public function blacklist($token)
    {
        if (! $this->verify($token)) return;

        if ($this->isExpired($token)) return;

        if ($this->isBlacklisted($token)) return;

        $claims = $this->getClaims($token);

        if (is_null($claims->exp)) return;

        Cache::put($claims->jti, $claims->sub, ($claims->exp - now()->timestamp) / 60);
    }

    public function isBlacklisted($token)
    {
        $claims = $this->getClaims($token);

        return is_null($claims) ? false : Cache::has($claims->jti);
    }

    public function check($token, $type = 'access')
    {
        if (! $token) {
            throw new AuthenticationException("We could not find the $type code.");
        }

        if (! $this->verify($token)) {
            throw new AuthenticationException("The $type code is invalid.");
        }

        if ($this->isBlacklisted($token)) {
            throw new AuthenticationException("The $type code is in blacklist.");
        }

        if ($this->isExpired($token)) {
            throw new AuthenticationException("The $type code expired.");
        }

        if (! $user = $this->getUser($token)) {
            throw new AuthenticationException('The user does not exist.');
        }

        if (! $user->activated_at) {
            throw new AuthorizationException('The account has not been activated.');
        }

        return $user;
    }
}