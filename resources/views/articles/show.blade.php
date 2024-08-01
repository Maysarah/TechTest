@extends('layout')

@section('content')
    <div class="mt-5">
        <h1>{{ $article['title'] }}</h1>
        <p>{{ $article['content'] }}</p>

        @if(!empty($article['images']))
            <div class="mt-4">
                <h2>Images:</h2>
                <div class="row">
                    @foreach($article['images'] as $image)
                        <div class="col-md-3 mb-3">
                            <img src="{{ Storage::disk('s3')->url($image['path']) }}" class="img-fluid" alt="Article Image">
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <a href="{{ route('articles.index') }}" class="btn btn-secondary">Back to Articles</a>
        <a href="{{ route('articles.edit', $article['id']) }}" class="btn btn-warning">Edit</a>
    </div>
@endsection
