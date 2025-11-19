@extends('layouts.frontend')

@section('title', 'Tips & Updates - Leveler')

@section('content')
<section class="page-header">
    <div class="container">
        <h1>Tips & Updates</h1>
    </div>
</section>

<section class="page-content">
    <div class="container">
        @if($page && $page->content)
            <div class="page-body">
                {!! nl2br(e($page->content)) !!}
            </div>
        @else
            <div class="page-body">
                <h2>Tips & Updates</h2>
                <p>Stay updated with the latest tips, industry insights, and professional development advice from our experts.</p>
                <p>Latest tips and updates will be displayed here.</p>
            </div>
        @endif
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

