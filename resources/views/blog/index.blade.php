<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Neo Assurances Blog</title>
</head>
<body>
    <h1>Blog</h1>
    @if (!empty($posts))
        @foreach ($posts as $post)
            <h2>{{ $post['title']['rendered'] }}</h2>
            <div>{!! $post['content']['rendered'] !!}</div>
            <hr>
        @endforeach
    @else
        <p>No posts found. Please add posts in WordPress.</p>
    @endif
</body>
</html>
