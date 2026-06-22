@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <x-seo.breadcrumbs :items="$seo->breadcrumbs" />

        <article class="card p-6 md:p-10">
            <h1 class="section-title">{{ $page->title }}</h1>
            <div class="prose-fa mt-6">{!! $page->body !!}</div>
        </article>
    </div>
@endsection
