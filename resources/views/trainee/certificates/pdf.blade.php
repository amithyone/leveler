<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate - {{ $course->title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            background: #f5f5f5;
        }
        
        .certificate {
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            background: white;
            padding: 40mm;
            position: relative;
        }
        
        .certificate-border {
            border: 8mm solid #6B46C1;
            height: 100%;
            padding: 20mm;
            position: relative;
        }
        
        .inner-border {
            border: 2mm solid #9333EA;
            height: 100%;
            padding: 15mm;
        }
        
        .certificate-header {
            text-align: center;
            margin-bottom: 30mm;
        }
        
        .cert-logo {
            font-size: 60pt;
            color: #6B46C1;
            margin-bottom: 10mm;
        }
        
        .certificate-header h1 {
            font-size: 36pt;
            color: #333;
            font-weight: bold;
            letter-spacing: 3pt;
            margin-bottom: 5mm;
        }
        
        .cert-divider {
            width: 80mm;
            height: 2mm;
            background: #6B46C1;
            margin: 0 auto;
        }
        
        .certificate-body {
            text-align: center;
            margin: 30mm 0;
        }
        
        .cert-presented {
            font-size: 16pt;
            margin-bottom: 10mm;
            color: #666;
        }
        
        .cert-name {
            font-size: 32pt;
            font-weight: bold;
            color: #6B46C1;
            margin: 15mm 0;
            text-transform: uppercase;
        }
        
        .cert-completed {
            font-size: 16pt;
            margin-bottom: 10mm;
            color: #666;
        }
        
        .cert-course {
            font-size: 24pt;
            font-weight: bold;
            color: #333;
            margin: 15mm 0 5mm 0;
        }
        
        .cert-code {
            font-size: 14pt;
            color: #666;
            margin-bottom: 10mm;
        }
        
        .cert-score {
            font-size: 16pt;
            margin-top: 10mm;
            color: #333;
        }
        
        .certificate-footer {
            display: flex;
            justify-content: space-between;
            margin-top: 40mm;
            padding-top: 10mm;
            border-top: 1mm solid #ddd;
        }
        
        .cert-date {
            font-size: 12pt;
            color: #666;
        }
        
        .cert-signature {
            text-align: center;
        }
        
        .signature-line {
            width: 60mm;
            height: 1mm;
            background: #333;
            margin: 0 auto 5mm;
        }
        
        .cert-signature p {
            font-size: 12pt;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="certificate-border">
            <div class="inner-border">
                <div class="certificate-header">
                    <div class="cert-logo">🎓</div>
                    <h1>CERTIFICATE OF COMPLETION</h1>
                    <div class="cert-divider"></div>
                </div>
                
                <div class="certificate-body">
                    <p class="cert-presented">This is to certify that</p>
                    <h2 class="cert-name">{{ $trainee->full_name }}</h2>
                    <p class="cert-completed">has successfully completed the course</p>
                    <h3 class="cert-course">{{ $course->title }}</h3>
                    <p class="cert-code">Course Code: {{ $course->code }}</p>
                    <div class="cert-score">
                        <p>With a score of <strong>{{ number_format($result->percentage, 1) }}%</strong></p>
                    </div>
                </div>
                
                <div class="certificate-footer">
                    <div class="cert-date">
                        <p>Date: {{ $date }}</p>
                    </div>
                    <div class="cert-signature">
                        <div class="signature-line"></div>
                        <p>Authorized Signature</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

