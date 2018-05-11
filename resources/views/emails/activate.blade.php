@component('mail::message')
# Account Activation

Hi {{ $user->name }},
In order to start using our app, you must activate your account first by clicking the button bellow.

@component('mail::button', ['url' => $url])
Activate Account
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
