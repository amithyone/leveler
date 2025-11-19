@extends('layouts.frontend')

@section('title', $page->title . ' - Leveler')

@section('content')
<section class="page-header">
    <div class="container">
        <h1>{{ $page->title }}</h1>
    </div>
</section>

<section class="page-content">
    <div class="container">
        <div class="page-body">
            {!! nl2br(e($page->content)) !!}
        </div>
    </div>
</section>

<style>
.page-body {
    max-width: 900px;
    margin: 0 auto;
    line-height: 1.8;
    color: #333;
    font-size: 16px;
}
</style>
@endsection

