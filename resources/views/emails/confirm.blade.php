{{-- Hola {{ $user->name }}
Cambiaste tu correo, nesecitamos verificar tu nuevo correo. Por favor sigue este link
{{ route('verify', $user->verification_token) }}
 --}}


@component('mail::message')
# Hola {{ $user->name }}

Cambiaste tu correo, nesecitamos verificar tu nuevo correo. Por favor sigue este link

@component('mail::button', ['url' => {{ route('verify', $user->verification_token) }}])
Verificar Cuenta
@endcomponent

Gracias,<br>
{{ confing(app.name) }}

@endcomponent
