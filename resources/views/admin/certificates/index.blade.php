@extends('layouts.app')

@section('title', 'Quản lý Chứng chỉ - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-certificate me-2"></i> Quản lý Chứng chỉ
        </h1>
        <a href="{{ route('admin.certificates.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Tạo chứng chỉ mới
        </a>
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

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách chứng chỉ</h6>
            <div>
                <form action="{{ route('admin.certificates.index') }}" method="GET" class="d-flex">
                    <div class="input-group me-2">
                        <input type="text" class="form-control" name="search" placeholder="Tìm kiếm..." value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <select name="status" class="form-select me-2" onchange="this.form.submit()">
                        <option value="">-- Trạng thái --</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Hết hạn</option>
                        <option value="revoked" {{ request('status') == 'revoked' ? 'selected' : '' }}>Đã thu hồi</option>
                    </select>
                    <select name="test_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Bài kiểm tra --</option>
                        @foreach($tests as $test)
                            <option value="{{ $test->id }}" {{ request('test_id') == $test->id ? 'selected' : '' }}>
                                {{ $test->title }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Mã chứng chỉ</th>
                            <th>Tên chứng chỉ</th>
                            <th>Thuyền viên</th>
                            <th>Bài kiểm tra</th>
                            <th>Ngày cấp</th>
                            <th>Hạn sử dụng</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($certificates->count() > 0)
                            @foreach($certificates as $certificate)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.certificates.show', $certificate->id) }}" class="fw-bold text-primary">
                                            {{ $certificate->certificate_number }}
                                        </a>
                                    </td>
                                    <td>{{ $certificate->title }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $certificate->user_id) }}">
                                            {{ $certificate->user->name }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($certificate->test)
                                            <a href="{{ route('admin.tests.show', $certificate->test_id) }}">
                                                {{ $certificate->test->title }}
                                            </a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ $certificate->issue_date->format('d/m/Y') }}</td>
                                    <td>
                                        @if($certificate->expiry_date)
                                            {{ $certificate->expiry_date->format('d/m/Y') }}
                                            @if($certificate->isExpired())
                                                <span class="badge bg-danger">Hết hạn</span>
                                            @elseif($certificate->expiry_date->diffInDays(now()) < 30)
                                                <span class="badge bg-warning text-dark">Sắp hết hạn</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Không có hạn</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($certificate->status == 'active')
                                            <span class="badge bg-success">Hoạt động</span>
                                        @elseif($certificate->status == 'expired')
                                            <span class="badge bg-secondary">Hết hạn</span>
                                        @elseif($certificate->status == 'revoked')
                                            <span class="badge bg-danger">Đã thu hồi</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.certificates.show', $certificate->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.certificates.edit', $certificate->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.certificates.pdf', $certificate->id) }}" class="btn btn-sm btn-secondary">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                            <form action="{{ route('admin.certificates.destroy', $certificate->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa chứng chỉ này?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center">Không có dữ liệu</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-end mt-3">
                {{ $certificates->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 