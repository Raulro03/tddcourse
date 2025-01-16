<h2>{{ $course->title }}</h2>
<h3>{{ $course->tagline }}</h3>
<p>{{ $course->description }}</p>
<p>{{ $course->videos_count }} videos</p>
<ul>
    @foreach($course->learnings as $learning)
        <li>{{ $learning }}</li>
    @endforeach
</ul>
<img src="{{ asset("images/$course->image_name") }}" alt="Image of the course {{ $course->title }}">
<a href="#" class="paddle_button" data-theme="light"
   data-items='[
         {
              "priceId": "{{ $course->paddle_product_id }}",
              "quantity": 1
         }
   ]'
>
    Buy Now
</a>
<script src="https://cdn.paddle.com/paddle/v2/paddle.js"></script>
<script type="text/javascript">
    @env('local')
         Paddle.Environment.set('sandbox');
    @endenv
    Paddle.Initialize({ token: "{{ config('services.paddle.vendor-id') }}" });
</script>
