@extends('layouts.frontend')

@section('title', 'Partners - Leveler')

@section('content')
<section class="page-header">
    <div class="container">
        <h1>Partners</h1>
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
                <h2>Our Partners</h2>
                <p>We are proud to collaborate with leading organizations and institutions to deliver exceptional training and development services.</p>
                <p>Our partners information will be displayed here.</p>
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

