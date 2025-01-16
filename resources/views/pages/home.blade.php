@guest
    <a href="{{ route('login') }}">Login</a>
@else
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Log out</button>
    </form>
@endguest
@foreach($courses as $course)
    <a href="{{ route('pages.course-details', $course) }}">
        <h2>{{ $course->title }}</h2>
    </a>
    <p>{{ $course->description }}</p>
@endforeach
