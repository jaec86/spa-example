@component('mail::message')
# Reset Password

Hi {{ $user->name }},<br>
It looks like you forgot your password. Please click in the button bellow to reset your password.

@component('mail::button', ['url' => $url])
Reset Password
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
