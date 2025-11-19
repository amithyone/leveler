@extends('layouts.frontend')

@section('title', 'Careers - Leveler')

@section('content')
<section class="page-header">
    <div class="container">
        <h1>{{ $page->title ?? 'Careers' }}</h1>
    </div>
</section>

<section class="page-content">
    <div class="container">
        @if($page && $page->content)
            <div class="page-body">
                {!! nl2br(e($page->content)) !!}
            </div>
        @else
            <div class="careers-content">
                <h2>Join Our Team</h2>
                <p>At Leveler, we are always looking for talented individuals who are passionate about human capacity development and business growth.</p>
                
                <div class="careers-section">
                    <h3>Why Work With Us?</h3>
                    <ul>
                        <li>Opportunity to make a real impact in people's professional development</li>
                        <li>Collaborative and supportive work environment</li>
                        <li>Continuous learning and professional growth opportunities</li>
                        <li>Competitive compensation packages</li>
                    </ul>
                </div>

                <div class="careers-section">
                    <h3>Current Openings</h3>
                    <p>We currently don't have any open positions, but we're always interested in connecting with talented professionals. Please send your resume to <a href="mailto:info@leveler.com">info@leveler.com</a> and we'll keep your information on file for future opportunities.</p>
                </div>

                <div class="careers-section">
                    <h3>How to Apply</h3>
                    <p>To apply for any position, please send your CV and cover letter to <a href="mailto:careers@leveler.com">careers@leveler.com</a> with the position title as the subject line.</p>
                </div>
            </div>
        @endif
    </div>
</section>

<style>
.careers-content {
    max-width: 800px;
    margin: 0 auto;
}

.careers-content h2 {
    color: #333;
    margin-bottom: 20px;
    font-size: 28px;
}

.careers-content > p {
    font-size: 18px;
    color: #666;
    margin-bottom: 30px;
    line-height: 1.8;
}

.careers-section {
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 25px;
    margin-bottom: 25px;
}

.careers-section h3 {
    color: #667eea;
    margin-bottom: 15px;
    font-size: 20px;
}

.careers-section ul {
    list-style: none;
    padding-left: 0;
}

.careers-section ul li {
    padding: 10px 0 10px 30px;
    position: relative;
    color: #666;
    line-height: 1.6;
}

.careers-section ul li:before {
    content: "✓";
    position: absolute;
    left: 0;
    color: #10b981;
    font-weight: bold;
    font-size: 18px;
}

.careers-section a {
    color: #667eea;
    text-decoration: none;
}

.careers-section a:hover {
    text-decoration: underline;
}

.page-body {
    max-width: 900px;
    margin: 0 auto;
    line-height: 1.8;
    color: #333;
}
</style>
@endsection

