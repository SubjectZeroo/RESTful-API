{{-- Hola {{ $user->name }}
Gracias por crear una cuenta. Por favor verifica tu cuenta email con esta link:
{{ route('verify', $user->verification_token) }} --}}

{{-- {{ url("api/users/verify/{$user->verification_token}") }} --}}

@component('mail::message')
# Hola {{ $user->name }}

Gracias por crear una cuenta. Por favor verifica tu cuenta email con esta link:

@component('mail::button', ['url' => {{ route('verify', $user->verification_token) }}])
Verificar Cuenta
@endcomponent

Gracias,<br>
{{ confing(app.name) }}

@endcomponent
