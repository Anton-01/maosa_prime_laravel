<x-mail::message>
    Has recibido un mensaje de contacto de **{{ $name }}**.

    {!! $contactMessage !!}

    Saludos,
    {{ config('app.name') }}
</x-mail::message>
