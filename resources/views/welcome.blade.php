@extends('layouts.app')

@section('title', 'Trang chủ - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('css')
<style>
    .hero-section {
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1596464716127-f2a82984de30?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 100px 0;
        margin-top: -16px;
    }
    
    .feature-box {
        padding: 30px;
        border-radius: 10px;
        height: 100%;
        transition: transform 0.3s ease;
    }
    
    .feature-box:hover {
        transform: translateY(-10px);
    }
    
    .feature-icon {
        font-size: 2.5rem;
        margin-bottom: 20px;
        color: var(--primary-color);
    }
    
    .section-title {
        position: relative;
        padding-bottom: 15px;
        margin-bottom: 30px;
    }
    
    .section-title:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 4px;
        background-color: var(--primary-color);
    }
    
    .cta-section {
        background-color: var(--primary-color);
        color: white;
        padding: 80px 0;
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-10">
                <h1 class="display-4 fw-bold mb-4">Hệ thống Đánh giá Năng lực Thuyền viên</h1>
                <p class="lead mb-5">Nâng cao chất lượng đội ngũ thuyền viên thông qua hệ thống kiểm tra năng lực toàn diện, hiện đại và khách quan</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('login.form') }}" class="btn btn-primary btn-lg px-4">
                        <i class="fas fa-sign-in-alt me-2"></i> Đăng nhập
                    </a>
                    <a href="{{ route('register.form') }}" class="btn btn-outline-light btn-lg px-4">
                        <i class="fas fa-user-plus me-2"></i> Đăng ký
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="container py-5">
    <div class="row text-center">
        <div class="col-12">
            <h2 class="section-title">Tính năng nổi bật</h2>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card feature-box shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-question-circle feature-icon"></i>
                    <h4 class="card-title">Đa dạng loại câu hỏi</h4>
                    <p class="card-text">Hệ thống hỗ trợ nhiều loại câu hỏi khác nhau: trắc nghiệm, tự luận, mô phỏng tình huống và bài kiểm tra thực hành.</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card feature-box shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-random feature-icon"></i>
                    <h4 class="card-title">Ngân hàng câu hỏi phong phú</h4>
                    <p class="card-text">Chọn ngẫu nhiên câu hỏi từ ngân hàng đề thi để đảm bảo tính khách quan và công bằng trong quá trình đánh giá.</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card feature-box shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line feature-icon"></i>
                    <h4 class="card-title">Báo cáo và phân tích chi tiết</h4>
                    <p class="card-text">Cung cấp báo cáo kết quả chi tiết, phân tích điểm mạnh/yếu và đề xuất hướng phát triển cho từng thuyền viên.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- About Section -->
<div class="bg-light py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="mb-4">Về Hệ thống Đánh giá Năng lực Thuyền viên</h2>
                <p class="lead">Hệ thống được phát triển nhằm nâng cao chất lượng đội ngũ thuyền viên, đảm bảo an toàn hàng hải và hiệu quả vận hành.</p>
                <p>Với giao diện thân thiện, dễ sử dụng và tính năng đa dạng, hệ thống giúp đánh giá toàn diện năng lực của thuyền viên theo từng vị trí công việc và loại tàu cụ thể.</p>
                <p>Hệ thống phù hợp với các cơ sở đào tạo, công ty vận tải biển và tổ chức quản lý thuyền viên, giúp đơn giản hóa quy trình đánh giá và nâng cao hiệu quả quản lý.</p>
            </div>
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1589085909615-c65162eea2a9?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80" 
                    alt="Đội ngũ thuyền viên" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</div>

<!-- Call to Action -->
<div class="cta-section">
    <div class="container text-center">
        <h2 class="display-6 mb-4">Sẵn sàng nâng cao năng lực đội ngũ thuyền viên?</h2>
        <p class="lead mb-5">Đăng ký ngay để trải nghiệm hệ thống đánh giá toàn diện và hiện đại</p>
        <a href="{{ route('register.form') }}" class="btn btn-light btn-lg px-5">
            <i class="fas fa-user-plus me-2"></i> Đăng ký ngay
        </a>
    </div>
</div>
@endsection 