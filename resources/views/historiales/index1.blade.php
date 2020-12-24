@extends('layouts.sidebar')

@section('content')

<div class="row">
    <div class="col-md-10">
        <div class="panel panel-default">
            <div class="panel-heading">@lang('global.app_dashboard')</div>

            <div class="panel-body">
                <ul>
                    @forelse ($audits as $audit)
                    <li>
                        @lang('article.updated.metadata', $audit->getMetadata())

                        @foreach ($audit->getModified() as $attribute => $modified)

                        <ul>
                            <li>@lang('article.'.$audit->event.'.modified.'.$attribute, $modified)</li>
                        </ul>
                        @endforeach
                        </li>
                        @empty
                        <p>@lang('article.unavailable_audits')</p>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

