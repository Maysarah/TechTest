@extends('layout')

@section('content')
    <div class="mt-5">
        <h1>Articles</h1>
        <a href="{{ route('articles.create') }}" class="btn btn-primary mb-3">Create New Article</a>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($articles->isEmpty())
            <div class="alert alert-info">
                No articles found.
            </div>
        @else
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($articles as $article)
                    <tr>
                        <td>{{ $article->id }}</td>
                        <td>{{ $article->title }}</td>
                        <td>{{ Str::limit($article->content, 50) }}</td> <!-- Limit content length -->
                        <td>
                            <a href="{{ route('articles.show', $article->id) }}" class="btn btn-info">View</a>
                            <a href="{{ route('articles.edit', $article->id) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('articles.destroy', $article->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this article?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
