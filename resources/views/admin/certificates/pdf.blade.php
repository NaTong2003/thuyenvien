<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chứng chỉ - {{ $certificate->certificate_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }
        
        .certificate-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 50px;
            box-sizing: border-box;
            position: relative;
            border: 10px solid #1a73e8;
        }
        
        .certificate-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #1a73e8;
            padding-bottom: 20px;
        }
        
        .logo {
            width: 100px;
            height: auto;
            margin-bottom: 20px;
        }
        
        .certificate-title {
            font-size: 30px;
            font-weight: bold;
            color: #1a73e8;
            margin-bottom: 10px;
        }
        
        .certificate-subtitle {
            font-size: 18px;
            color: #555;
        }
        
        .certificate-number {
            font-size: 14px;
            color: #777;
            margin-top: 5px;
        }
        
        .certificate-body {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .issued-to-label {
            font-size: 16px;
            color: #555;
            margin-bottom: 10px;
        }
        
        .seafarer-name {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }
        
        .certificate-description {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 30px;
            text-align: justify;
        }
        
        .certificate-details {
            margin-bottom: 40px;
            width: 100%;
        }
        
        .certificate-details table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .certificate-details th {
            text-align: left;
            padding: 10px;
            font-weight: bold;
            color: #555;
            width: 40%;
            border-bottom: 1px solid #ddd;
        }
        
        .certificate-details td {
            text-align: left;
            padding: 10px;
            color: #333;
            border-bottom: 1px solid #ddd;
        }
        
        .certificate-footer {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        
        .signature-container {
            text-align: center;
            width: 200px;
        }
        
        .signature-line {
            border-top: 1px solid #555;
            margin-bottom: 10px;
        }
        
        .signature-title {
            font-size: 16px;
            color: #555;
        }
        
        .stamp-container {
            text-align: center;
            width: 100px;
        }
        
        .stamp {
            width: 100px;
            height: 100px;
            border: 2px dashed #e74a3b;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            color: #e74a3b;
        }
        
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.05;
            font-size: 100px;
            color: #1a73e8;
            z-index: -1;
            transform: translate(-50%, -50%) rotate(-45deg);
        }
        
        .status-revoked {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            color: #e74a3b;
            font-size: 80px;
            font-weight: bold;
            opacity: 0.2;
            z-index: 10;
            border: 10px solid #e74a3b;
            padding: 10px 20px;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        @if($certificate->status == 'revoked')
            <div class="status-revoked">ĐÃ THU HỒI</div>
        @elseif($certificate->isExpired())
            <div class="status-revoked">HẾT HẠN</div>
        @endif
        
        <div class="watermark">CHỨNG CHỈ</div>
        
        <div class="certificate-header">
            <div class="certificate-title">{{ $certificate->title }}</div>
            <div class="certificate-subtitle">Hệ thống Đánh giá Năng lực Thuyền viên</div>
            <div class="certificate-number">Số: {{ $certificate->certificate_number }}</div>
        </div>
        
        <div class="certificate-body">
            <div class="issued-to-label">Được cấp cho:</div>
            <div class="seafarer-name">{{ $certificate->user->name }}</div>
            <div class="certificate-description">{{ $certificate->description }}</div>
        </div>
        
        <div class="certificate-details">
            <table>
                <tr>
                    <th>Ngày cấp:</th>
                    <td>{{ $certificate->issue_date->format('d/m/Y') }}</td>
                </tr>
                @if($certificate->expiry_date)
                    <tr>
                        <th>Ngày hết hạn:</th>
                        <td>{{ $certificate->expiry_date->format('d/m/Y') }}</td>
                    </tr>
                @endif
                @if($certificate->test)
                    <tr>
                        <th>Bài kiểm tra:</th>
                        <td>{{ $certificate->test->title }}</td>
                    </tr>
                @endif
                @if($certificate->testAttempt)
                    <tr>
                        <th>Điểm số:</th>
                        <td>{{ $certificate->testAttempt->score }}/100</td>
                    </tr>
                @endif
                <tr>
                    <th>Trạng thái:</th>
                    <td>
                        @if($certificate->status == 'active')
                            Hoạt động
                        @elseif($certificate->status == 'expired')
                            Hết hạn
                        @elseif($certificate->status == 'revoked')
                            Đã thu hồi
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="certificate-footer">
            <div class="signature-container">
                <div class="signature-line"></div>
                <div class="signature-title">Chữ ký người cấp</div>
                <div>{{ $certificate->issuer?->name ?? '' }}</div>
            </div>
            
            <div class="stamp-container">
                <div class="stamp">Dấu</div>
            </div>
        </div>
    </div>
</body>
</html> 