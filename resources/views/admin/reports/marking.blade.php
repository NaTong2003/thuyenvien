@extends('layouts.app')

@section('title', 'Chấm điểm bài thi - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-graduation-cap me-2"></i> Chấm điểm bài thi
        </h1>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Chấm điểm bài thi</li>
        </ol>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng bài cần chấm</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $attempts->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Đã chấm gần đây</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ session('recently_marked_count', 0) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách bài thi cần chấm điểm</h6>
            <div class="card-tools">
                <form action="{{ route('admin.reports.marking') }}" method="GET" class="d-none d-sm-inline-block form-inline">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control bg-light border-0 small" placeholder="Tìm theo tên hoặc ID..." 
                               value="{{ request('search') }}" aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body">
            @if($attempts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Thuyền viên</th>
                                <th>Bài kiểm tra</th>
                                <th>Thời gian nộp</th>
                                <th>Điểm hiện tại</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attempts as $attempt)
                                <tr>
                                    <td>{{ $attempt->id }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $attempt->user_id) }}" class="text-decoration-none">
                                            {{ $attempt->user->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.tests.show', $attempt->test_id) }}" class="text-decoration-none">
                                            {{ $attempt->test->title }}
                                        </a>
                                    </td>
                                    <td>{{ $attempt->end_time ? $attempt->end_time->format('d/m/Y H:i') : 'Chưa hoàn thành' }}</td>
                                    <td>{{ $attempt->score }}/100</td>
                                    <td>
                                        @if($attempt->is_marked)
                                            <span class="badge bg-success">Đã chấm</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Chưa chấm</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.reports.mark.attempt', $attempt->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-pen me-1"></i> Chấm điểm
                                        </a>
                                        <a href="{{ route('admin.reports.attempt', $attempt->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye me-1"></i> Xem chi tiết
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $attempts->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i> Không có bài thi nào cần chấm điểm.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
 