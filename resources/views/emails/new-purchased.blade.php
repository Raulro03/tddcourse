@component('mail::message')
    # Thanks for pruchasing {{ $course->title }}

    If this is your first purchase on {{ config('app.name') }}, then a new account has been created for you, and you just need to reset your password to get started.
    Have fun with the new course.

    @component('mail::button', ['url' => url('login')])
        Login
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent

