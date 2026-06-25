@extends('layouts.master')

@section('content')

<div class="card shadow">

    <div class="card-header d-flex justify-content-between align-items-center">

        <h4 class="mb-0">

            Article List

        </h4>

        <a href="{{ route('articles.create') }}" class="btn btn-success">

            + Add Article

        </a>

    </div>

    <div class="card-body">

        @if(session('success'))

            <div class="alert alert-success">

                {{ session('success') }}

            </div>

        @endif

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-dark">

                <tr>

                    <th width="60">#</th>

                    <th>Title</th>

                    <th width="120">Status</th>

                    <th width="150">Created</th>

                    <th width="220" class="text-center">

                        Action

                    </th>

                </tr>

                </thead>

                <tbody>

                @forelse($articles as $article)

                    <tr>

                        <td>{{ $loop->iteration }}</td>

                        <td>

                            <strong>

                                {{ $article->title }}

                            </strong>

                            <br>

                            <small>

                                <a href="{{ $article->url }}" target="_blank">

                                    {{ Str::limit($article->url,60) }}

                                </a>

                            </small>

                        </td>

                        <td>

                            @switch($article->status)

                                @case('pending')

                                <span class="badge bg-warning">

                                    Pending

                                </span>

                                @break

                                @case('processing')

                                <span class="badge bg-info">

                                    Processing

                                </span>

                                @break

                                @case('completed')

                                <span class="badge bg-success">

                                    Completed

                                </span>

                                @break

                                @default

                                <span class="badge bg-danger">

                                    Failed

                                </span>

                            @endswitch

                        </td>

                        <td>

                            {{ $article->created_at->format('d M Y') }}

                        </td>

                        <td class="text-center">

                            <a href="{{ route('articles.show',$article) }}" class="btn btn-info btn-sm">

                                View

                            </a>

                            <a href="{{ route('articles.edit',$article) }}" class="btn btn-warning btn-sm">

                                Edit

                            </a>

                            <form action="{{ route('articles.destroy',$article) }}"
                                  method="POST"
                                  class="d-inline">

                                @csrf
                                @method('DELETE')

                                <button
                                    onclick="return confirm('Delete this article?')"
                                    class="btn btn-danger btn-sm">

                                    Delete

                                </button>

                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="5" class="text-center">

                            No Articles Found

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

        <div class="mt-3">

            {{ $articles->links() }}

        </div>

    </div>

</div>

@endsection