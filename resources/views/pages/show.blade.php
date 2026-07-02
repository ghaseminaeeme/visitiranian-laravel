@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <x-seo.breadcrumbs :items="$seo->breadcrumbs" />

        <article class="card-elevated overflow-hidden">
            <header class="border-b border-slate-100 bg-gradient-to-l from-primary-50/60 to-white px-6 py-8 md:px-10">
                <h1 class="section-title">{{ $page->title }}</h1>
            </header>
            <div class="prose-fa px-6 py-8 md:px-10">{!! $page->body !!}</div>
        </article>
    </div>
@endsection
