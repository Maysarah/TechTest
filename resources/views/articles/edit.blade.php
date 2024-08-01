@extends('layout')

@section('content')
    <div class="mt-5">
        <h1>Edit Article</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('articles.update', $article['id']) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" class="form-control" name="title" id="title" value="{{ old('title', $article['title']) }}">
            </div>
            <div class="form-group">
                <label for="content">Content:</label>
                <textarea class="form-control" name="content" id="content">{{ old('content', $article['content']) }}</textarea>
            </div>
            <div class="form-group">
                <label for="images">Upload New Images:</label>
                <input type="file" class="form-control" name="images[]" id="images" multiple>
            </div>

            <div class="form-group mt-4">
                <label>Existing Images:</label>
                @if (isset($article['images']) && count($article['images']) > 0)
                    <div class="row">
                        @foreach($article['images'] as $image)
                            <div class="col-md-3 mb-3">
                                <div class="card">
                                    <img src="{{ Storage::disk('s3')->url($image['path']) }}" class="card-img-top" alt="Article Image">
                                    <div class="card-body">
                                        <input type="checkbox" name="images_to_delete[]" value="{{ $image['id'] }}">
                                        <label for="images_to_delete[]"> Delete this image</label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p>No images uploaded yet.</p>
                @endif
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
@endsection
