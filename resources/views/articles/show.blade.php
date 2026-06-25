<x-app-layout>

<div class="container mt-4">

    <div class="card">

        <div class="card-header">

            <h4>

                Article Details

            </h4>

        </div>

        <div class="card-body">

            <table class="table">

                <tr>

                    <th width="180">

                        Title

                    </th>

                    <td>

                        {{ $article->title }}

                    </td>

                </tr>

                <tr>

                    <th>

                        URL

                    </th>

                    <td>

                        <a href="{{ $article->url }}"
                            target="_blank">

                            {{ $article->url }}

                        </a>

                    </td>

                </tr>

                <tr>

                    <th>

                        Status

                    </th>

                    <td>

                        {{ ucfirst($article->status) }}

                    </td>

                </tr>

                <tr>

                    <th>

                        Content

                    </th>

                    <td>

                        {!! nl2br(e($article->content ?? 'Not Generated Yet')) !!}

                    </td>

                </tr>

                <tr>

                    <th>

                        Summary

                    </th>

                    <td>

                        {!! nl2br(e($article->summary ?? 'Not Generated Yet')) !!}

                    </td>

                </tr>

                <tr>

                    <th>

                        Key Points

                    </th>

                    <td>

                        @if($article->key_points)

                            <ul>

                                @foreach($article->key_points as $point)

                                    <li>

                                        {{ $point }}

                                    </li>

                                @endforeach

                            </ul>

                        @else

                            Not Generated Yet

                        @endif

                    </td>

                </tr>

            </table>

            <a
                href="{{ route('articles.index') }}"
                class="btn btn-secondary">

                Back

            </a>

        </div>

    </div>

</div>

</x-app-layout>