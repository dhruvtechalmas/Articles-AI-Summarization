<x-app-layout>

<div class="container mt-4">

    <div class="card">

        <div class="card-header">

            <h4>Edit Article</h4>

        </div>

        <div class="card-body">

            <form
                action="{{ route('articles.update',$article) }}"
                method="POST">

                @csrf

                @method('PUT')

                <div class="mb-3">

                    <label>Title</label>

                    <input
                        type="text"
                        name="title"
                        class="form-control"
                        value="{{ old('title',$article->title) }}">

                </div>

                <div class="mb-3">

                    <label>URL</label>

                    <input
                        type="url"
                        name="url"
                        class="form-control"
                        value="{{ old('url',$article->url) }}">

                </div>

                <button class="btn btn-primary">

                    Update

                </button>

                <a
                    href="{{ route('articles.index') }}"
                    class="btn btn-secondary">

                    Cancel

                </a>

            </form>

        </div>

    </div>

</div>

</x-app-layout>