@extends('layouts.frontend')

@section('title', 'About Us - Leveler')

@section('content')
<section class="page-header" @if($page && $page->featured_image) style="background-image: url('{{ \Illuminate\Support\Facades\Storage::url($page->featured_image) }}'); background-size: cover; background-position: center; position: relative;" @endif>
    @if($page && $page->featured_image)
    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5);"></div>
    @endif
    <div class="container" style="position: relative; z-index: 1;">
        <h1>{{ $page->title ?? 'About Leveler' }}</h1>
    </div>
</section>

<section class="page-content">
    <div class="container">
        @if($page && $page->featured_image && !str_contains($page->featured_image, 'header'))
        <div class="featured-image-container" style="margin-bottom: 30px; text-align: center;">
            <img src="{{ \Illuminate\Support\Facades\Storage::url($page->featured_image) }}" alt="{{ $page->title }}" style="max-width: 100%; height: auto; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
        </div>
        @endif

        @if($page && $page->content)
            <div class="page-body">
                {!! nl2br(e($page->content)) !!}
            </div>
        @else
            <div class="page-body">
                <h2>Leveler</h2>
                <p>…Leveler means Competence</p>
                <p>For over 10 years, we have supported businesses to accelerate growth.</p>
                <p>Leveler is a Business & Management consulting company whose mandate is to aid growth and sustainability of businesses through strategy development.</p>
                <p>Our reputation is built on the foundation of providing business and management solutions that deliver growth, increase profit and boost efficiency.</p>
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

