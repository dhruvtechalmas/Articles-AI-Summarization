@extends('layouts.master')

@section('content')

<div class="row justify-content-center">

    <div class="col-lg-8">

        <div class="card shadow">

            <div class="card-header bg-primary text-white">

                <h4 class="mb-0">
                    Create New Article
                </h4>

            </div>

            <div class="card-body">

                <form action="{{ route('articles.store') }}" method="POST">

                    @csrf

                    <div class="mb-3">

                        <label class="form-label fw-bold">

                            Article Title

                        </label>

                        <input
                            type="text"
                            name="title"
                            class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title') }}">

                        @error('title')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="mb-3">

                        <label class="form-label fw-bold">

                            Article URL

                        </label>

                        <input
                            type="url"
                            name="url"
                            class="form-control @error('url') is-invalid @enderror"
                            value="{{ old('url') }}"
                            placeholder="https://en.wikipedia.org/wiki/Laravel">

                        @error('url')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="text-end">

                        <a href="{{ route('articles.index') }}" class="btn btn-secondary">

                            Cancel

                        </a>

                        <button class="btn btn-primary">

                            Save Article

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection