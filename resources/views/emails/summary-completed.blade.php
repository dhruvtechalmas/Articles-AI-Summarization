<h2>Article Summary Completed</h2>

<p><strong>Title:</strong> {{ $article->title }}</p>

<p><strong>Summary:</strong></p>

<p>{{ $article->summary }}</p>

<h3>Key Points</h3>

<ul>
    @foreach ($article->key_points as $point)
        <li>{{ $point }}</li>
    @endforeach
</ul>

<p>Thank you.</p>