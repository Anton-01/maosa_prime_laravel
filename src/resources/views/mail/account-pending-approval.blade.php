<x-mail::message>
    # ¡Hola, {{ $user->name }}!

    Gracias por registrarte en nuestra plataforma. Tu cuenta ha sido creada exitosamente.

    Un administrador revisará tus datos y, una vez que sean validados, recibirás una notificación y podrás acceder a la plataforma.

    Si tienes alguna pregunta, no dudes en contactarnos.

    Saludos,
    {{ config('app.name') }}
</x-mail::message>
