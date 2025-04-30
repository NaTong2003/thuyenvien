@extends('layouts.app')

@section('title', 'Đăng nhập - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h5 class="mb-0 text-center">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Đăng nhập
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                    id="email" name="email" value="{{ old('email') }}" required autofocus>
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                    id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-1"></i> Đăng nhập
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer py-3 border-0 text-center">
                    <div>Chưa có tài khoản? <a href="{{ route('register.form') }}" class="text-primary">Đăng ký ngay</a></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 