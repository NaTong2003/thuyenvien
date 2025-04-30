@extends('layouts.app')

@section('title', 'Theo dõi Chi tiết Bài thi - Hệ thống Đánh giá Năng lực Thuyền viên')

@section('css')
<style>
    .monitor-header {
        background-color: #f8f9fc;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border-left: 5px solid var(--primary-color);
    }
    
    .timer-display {
        font-family: 'Courier New', monospace;
        font-size: 1.8rem;
        font-weight: bold;
        color: #e74a3b;
        background-color: rgba(231, 74, 59, 0.1);
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        display: inline-block;
        vertical-align: middle;
    }
    
    .test-info {
        margin-bottom: 0.5rem;
    }
    
    .test-info span {
        font-weight: 500;
    }
    
    .status-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 10rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .status-badge.active {
        background-color: #1cc88a;
        color: white;
    }
    
    .status-badge.idle {
        background-color: #f6c23e;
        color: white;
    }
    
    .status-badge.warning {
        background-color: #e74a3b;
        color: white;
    }
    
    .status-badge.finished {
        background-color: #4e73df;
        color: white;
    }
    
    .candidate-card {
        border-radius: 0.5rem;
        overflow: hidden;
        transition: all 0.3s ease;
        margin-bottom: 1rem;
    }
    
    .candidate-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .candidate-header {
        background-color: #f8f9fc;
        padding: 1rem;
        border-bottom: 1px solid #e3e6f0;
        position: relative;
    }
    
    .candidate-header .name {
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .candidate-header .status {
        position: absolute;
        right: 1rem;
        top: 1rem;
    }
    
    .candidate-body {
        padding: 1rem;
    }
    
    .progress-label {
        font-weight: 500;
        margin-bottom: 0.25rem;
        display: flex;
        justify-content: space-between;
    }
    
    .progress {
        height: 1rem;
        margin-bottom: 1rem;
    }
    
    .warning-item {
        background-color: rgba(231, 74, 59, 0.1);
        border-left: 3px solid #e74a3b;
        padding: 0.5rem 0.75rem;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }
    
    .warning-item i {
        color: #e74a3b;
        margin-right: 0.5rem;
    }
    
    .warning-time {
        color: #858796;
        font-size: 0.75rem;
        margin-left: 1.7rem;
    }
    
    .btn-notification {
        background-color: rgba(78, 115, 223, 0.1);
        color: #4e73df;
        border: 1px solid #4e73df;
        transition: all 0.3s ease;
    }
    
    .btn-notification:hover {
        background-color: #4e73df;
        color: white;
    }
    
    .btn-terminate {
        background-color: rgba(231, 74, 59, 0.1);
        color: #e74a3b;
        border: 1px solid #e74a3b;
        transition: all 0.3s ease;
    }
    
    .btn-terminate:hover {
        background-color: #e74a3b;
        color: white;
    }
    
    .behavior-log {
        max-height: 200px;
        overflow-y: auto;
        font-size: 0.85rem;
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
        padding: 0.75rem;
        background-color: #f8f9fc;
    }
    
    .log-entry {
        margin-bottom: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px dashed #e3e6f0;
    }
    
    .log-time {
        color: #858796;
        font-size: 0.75rem;
        margin-right: 0.5rem;
    }
    
    .log-message.info {
        color: #4e73df;
    }
    
    .log-message.warning {
        color: #e74a3b;
    }
    
    .refresh-badge {
        display: inline-block;
        vertical-align: middle;
        margin-left: 0.5rem;
        font-size: 0.75rem;
        font-weight: normal;
        padding: 0.2rem 0.5rem;
        background-color: #4e73df;
        color: white;
        border-radius: 10rem;
    }
    
    .actions-container {
        display: flex;
        margin-top: 1rem;
    }
    
    .actions-container .btn {
        margin-right: 0.5rem;
    }
    
    .modal-notification textarea {
        resize: none;
        height: 120px;
    }
    
    .notification-recipients {
        margin-top: 1rem;
    }
    
    .ip-device-info {
        font-size: 0.85rem;
        color: #858796;
        margin-top: 0.5rem;
    }
    
    .question-progress {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 1rem;
    }
    
    .question-dot {
        width: 1.5rem;
        height: 1.5rem;
        border-radius: 50%;
        background-color: #e3e6f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        color: #5a5c69;
    }
    
    .question-dot.answered {
        background-color: #1cc88a;
        color: white;
    }
    
    .question-dot.current {
        background-color: #4e73df;
        color: white;
        animation: pulse 2s infinite;
    }
    
    .question-dot.flagged {
        background-color: #f6c23e;
        color: white;
    }
    
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(78, 115, 223, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(78, 115, 223, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(78, 115, 223, 0);
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-eye me-2"></i> Theo dõi Chi tiết Bài thi
        </h1>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.monitor.active') }}">Theo dõi Bài thi</a></li>
            <li class="breadcrumb-item active">Chi tiết</li>
        </ol>
    </div>

    <!-- Test Info Header -->
    <div class="monitor-header shadow-sm">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-3">{{ $test->title }}</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="test-info">
                            <i class="fas fa-tag me-1"></i> Loại: <span class="text-primary">{{ ucfirst($test->type) }}</span>
                        </div>
                        <div class="test-info">
                            <i class="fas fa-users me-1"></i> Thí sinh: <span>{{ $activeAttempts->count() }} đang làm bài</span>
                        </div>
                        <div class="test-info">
                            <i class="fas fa-clock me-1"></i> Thời gian: <span>{{ $test->duration }} phút</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="test-info">
                            <i class="fas fa-question-circle me-1"></i> Số câu hỏi: <span>{{ $test->questions->count() }}</span>
                        </div>
                        <div class="test-info">
                            <i class="fas fa-exclamation-triangle me-1"></i> Cảnh báo: <span class="text-danger">{{ $warningsCount }} cảnh báo</span>
                        </div>
                        <div class="test-info">
                            <i class="fas fa-eye me-1"></i> Cập nhật: <span>Tự động mỗi <span id="refreshCountdown">30</span>s</span>
                            <span class="refresh-badge">LIVE</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="d-flex flex-column justify-content-end align-items-md-end">
                    <div class="mb-2">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#notificationModal">
                            <i class="fas fa-bell me-1"></i> Gửi thông báo
                        </button>
                    </div>
                    <div class="mb-3">
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#terminateModal">
                            <i class="fas fa-power-off me-1"></i> Kết thúc bài thi
                        </button>
                    </div>
                    <div>
                        <button id="manualRefresh" class="btn btn-outline-primary">
                            <i class="fas fa-sync-alt me-1"></i> Làm mới ngay
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lọc thí sinh</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="statusFilter">Trạng thái</label>
                        <select class="form-select" id="statusFilter">
                            <option value="all">Tất cả</option>
                            <option value="active">Đang làm bài</option>
                            <option value="idle">Không hoạt động</option>
                            <option value="warning">Có cảnh báo</option>
                            <option value="finished">Đã hoàn thành</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="progressFilter">Tiến độ</label>
                        <select class="form-select" id="progressFilter">
                            <option value="all">Tất cả</option>
                            <option value="less25">Dưới 25%</option>
                            <option value="25to50">25% - 50%</option>
                            <option value="50to75">50% - 75%</option>
                            <option value="more75">Trên 75%</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="searchUser">Tìm kiếm</label>
                        <input type="text" class="form-control" id="searchUser" placeholder="Tên thí sinh...">
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100" id="applyFilters">
                        <i class="fas fa-filter me-1"></i> Áp dụng
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Candidate Cards -->
    <div class="row" id="candidatesContainer">
        @foreach($activeAttempts as $attempt)
        <div class="col-lg-6 candidate-item" 
            data-status="{{ $attempt->status }}" 
            data-progress="{{ $attempt->progress_percentage }}">
            <div class="card shadow candidate-card">
                <div class="candidate-header">
                    <div class="name">{{ $attempt->user->name }}</div>
                    <div class="status">
                        <span class="status-badge {{ $attempt->status }}">
                            @if($attempt->status == 'active')
                                <i class="fas fa-circle me-1"></i> Đang làm bài
                            @elseif($attempt->status == 'idle')
                                <i class="fas fa-pause-circle me-1"></i> Không hoạt động
                            @elseif($attempt->status == 'warning')
                                <i class="fas fa-exclamation-circle me-1"></i> Cảnh báo
                            @elseif($attempt->status == 'finished')
                                <i class="fas fa-check-circle me-1"></i> Hoàn thành
                            @endif
                        </span>
                    </div>
                </div>
                <div class="candidate-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="progress-label">
                                    <div>Tiến độ làm bài</div>
                                    <div>{{ $attempt->answered_count }}/{{ $test->questions->count() }}</div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $attempt->progress_percentage }}%" 
                                        aria-valuenow="{{ $attempt->progress_percentage }}" aria-valuemin="0" aria-valuemax="100">
                                        {{ $attempt->progress_percentage }}%
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="progress-label">
                                    <div>Thời gian còn lại</div>
                                    <div>{{ $attempt->remaining_time }}</div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $attempt->time_percentage }}%" 
                                        aria-valuenow="{{ $attempt->time_percentage }}" aria-valuemin="0" aria-valuemax="100">
                                        {{ $attempt->time_percentage }}%
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            @if(count($attempt->warnings) > 0)
                            <div class="mb-3">
                                <div class="font-weight-bold mb-2">Cảnh báo (<span class="text-danger">{{ count($attempt->warnings) }}</span>):</div>
                                <div class="warnings-list">
                                    @foreach($attempt->warnings as $warning)
                                    <div class="warning-item">
                                        <i class="fas fa-exclamation-triangle"></i> {{ $warning->message }}
                                        <div class="warning-time">{{ $warning->created_at->format('H:i:s') }}</div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            <div class="ip-device-info">
                                <div><i class="fas fa-desktop me-1"></i> {{ $attempt->device }}</div>
                                <div><i class="fas fa-map-marker-alt me-1"></i> {{ $attempt->ip_address }}</div>
                                <div><i class="fas fa-clock me-1"></i> Bắt đầu: {{ $attempt->start_time->format('H:i:s d/m/Y') }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="question-progress">
                        @for($i = 1; $i <= $test->questions->count(); $i++)
                            @php
                                $status = 'unanswered';
                                if(in_array($i, $attempt->answered_questions)) {
                                    $status = 'answered';
                                }
                                if($i == $attempt->current_question) {
                                    $status = 'current';
                                }
                                if(in_array($i, $attempt->flagged_questions)) {
                                    $status = 'flagged';
                                }
                            @endphp
                            <div class="question-dot {{ $status }}" title="Câu hỏi {{ $i }}">
                                {{ $i }}
                            </div>
                        @endfor
                    </div>
                    
                    <div class="actions-container">
                        <button class="btn btn-sm btn-notification send-notification" data-user="{{ $attempt->user->id }}">
                            <i class="fas fa-comment me-1"></i> Gửi thông báo
                        </button>
                        <button class="btn btn-sm btn-terminate terminate-attempt" data-attempt="{{ $attempt->id }}">
                            <i class="fas fa-ban me-1"></i> Dừng bài làm
                        </button>
                        <button class="btn btn-sm btn-info view-behavior" data-attempt="{{ $attempt->id }}">
                            <i class="fas fa-history me-1"></i> Lịch sử hành vi
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Send Notification Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">Gửi thông báo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="sendNotificationForm">
                    <div class="mb-3">
                        <label for="notificationMessage" class="form-label">Nội dung thông báo</label>
                        <textarea class="form-control" id="notificationMessage" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Người nhận</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="recipients" id="allRecipients" value="all" checked>
                            <label class="form-check-label" for="allRecipients">
                                Tất cả thí sinh
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="recipients" id="selectedRecipients" value="selected">
                            <label class="form-check-label" for="selectedRecipients">
                                Thí sinh đã chọn
                            </label>
                        </div>
                    </div>
                    <div class="notification-recipients d-none" id="recipientsList">
                        @foreach($activeAttempts as $attempt)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="{{ $attempt->user->id }}" id="user{{ $attempt->user->id }}">
                            <label class="form-check-label" for="user{{ $attempt->user->id }}">
                                {{ $attempt->user->name }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="sendNotificationBtn">Gửi</button>
            </div>
        </div>
    </div>
</div>

<!-- Terminate Test Modal -->
<div class="modal fade" id="terminateModal" tabindex="-1" aria-labelledby="terminateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="terminateModalLabel">Kết thúc bài thi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i> Cảnh báo: Hành động này sẽ kết thúc bài thi cho tất cả thí sinh hiện đang làm bài.
                </div>
                <p>Bạn có chắc chắn muốn kết thúc bài thi <strong>{{ $test->title }}</strong>?</p>
                <p>Tất cả <strong>{{ $activeAttempts->count() }}</strong> thí sinh đang làm bài sẽ bị buộc kết thúc và nộp bài.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form action="{{ route('admin.monitor.terminate', $test->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">Kết thúc bài thi</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View Behavior Modal -->
<div class="modal fade" id="behaviorModal" tabindex="-1" aria-labelledby="behaviorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="behaviorModalLabel">Lịch sử hành vi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="behavior-log" id="behaviorLog">
                    <!-- Behavior logs will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Auto refresh
        let refreshCountdown = 30;
        const refreshInterval = setInterval(function() {
            refreshCountdown--;
            $('#refreshCountdown').text(refreshCountdown);
            
            if (refreshCountdown <= 0) {
                refreshData();
                refreshCountdown = 30;
            }
        }, 1000);
        
        // Manual refresh
        $('#manualRefresh').click(function() {
            refreshData();
            refreshCountdown = 30;
        });
        
        function refreshData() {
            $.ajax({
                url: "{{ route('admin.monitor.test.refresh', $test->id) }}",
                type: "GET",
                success: function(data) {
                    // Update UI with new data
                    updateCandidateStatuses(data);
                },
                error: function(error) {
                    console.error("Lỗi khi cập nhật dữ liệu:", error);
                }
            });
        }
        
        function updateCandidateStatuses(data) {
            // Update logic would go here
            console.log("Cập nhật dữ liệu thành công");
        }
        
        // Show recipients list when selecting specific recipients
        $('input[name="recipients"]').change(function() {
            if ($(this).val() === 'selected') {
                $('#recipientsList').removeClass('d-none');
            } else {
                $('#recipientsList').addClass('d-none');
            }
        });
        
        // Send notification
        $('#sendNotificationBtn').click(function() {
            const message = $('#notificationMessage').val();
            const recipientType = $('input[name="recipients"]:checked').val();
            let recipients = [];
            
            if (recipientType === 'selected') {
                $('#recipientsList input:checked').each(function() {
                    recipients.push($(this).val());
                });
                
                if (recipients.length === 0) {
                    alert('Vui lòng chọn ít nhất một thí sinh để gửi thông báo.');
                    return;
                }
            }
            
            $.ajax({
                url: "{{ route('admin.monitor.sendNotification') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    test_id: "{{ $test->id }}",
                    message: message,
                    recipient_type: recipientType,
                    recipients: recipients
                },
                success: function(response) {
                    $('#notificationModal').modal('hide');
                    alert('Đã gửi thông báo thành công!');
                    $('#notificationMessage').val('');
                },
                error: function(error) {
                    console.error("Lỗi khi gửi thông báo:", error);
                    alert('Có lỗi xảy ra khi gửi thông báo. Vui lòng thử lại!');
                }
            });
        });
        
        // Individual notification
        $('.send-notification').click(function() {
            const userId = $(this).data('user');
            $('#selectedRecipients').prop('checked', true).trigger('change');
            $(`#user${userId}`).prop('checked', true);
            $('#notificationModal').modal('show');
        });
        
        // Terminate attempt
        $('.terminate-attempt').click(function() {
            const attemptId = $(this).data('attempt');
            if (confirm('Bạn có chắc chắn muốn dừng bài làm của thí sinh này?')) {
                $.ajax({
                    url: "{{ route('admin.monitor.terminateAttempt') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        attempt_id: attemptId
                    },
                    success: function(response) {
                        alert('Đã dừng bài làm thành công!');
                        refreshData();
                    },
                    error: function(error) {
                        console.error("Lỗi khi dừng bài làm:", error);
                        alert('Có lỗi xảy ra khi dừng bài làm. Vui lòng thử lại!');
                    }
                });
            }
        });
        
        // View behavior log
        $('.view-behavior').click(function() {
            const attemptId = $(this).data('attempt');
            
            $.ajax({
                url: "{{ route('admin.monitor.behaviorLog') }}",
                type: "GET",
                data: {
                    attempt_id: attemptId
                },
                success: function(data) {
                    // Clear existing logs
                    $('#behaviorLog').empty();
                    
                    // Add logs to modal
                    if (data.logs && data.logs.length > 0) {
                        data.logs.forEach(function(log) {
                            const logClass = log.type === 'warning' ? 'warning' : 'info';
                            const logEntry = `
                                <div class="log-entry">
                                    <span class="log-time">${log.time}</span>
                                    <span class="log-message ${logClass}">${log.message}</span>
                                </div>
                            `;
                            $('#behaviorLog').append(logEntry);
                        });
                    } else {
                        $('#behaviorLog').html('<p class="text-center text-muted">Không có dữ liệu hành vi.</p>');
                    }
                    
                    $('#behaviorModal').modal('show');
                },
                error: function(error) {
                    console.error("Lỗi khi lấy dữ liệu hành vi:", error);
                    alert('Có lỗi xảy ra khi lấy dữ liệu hành vi. Vui lòng thử lại!');
                }
            });
        });
        
        // Filter candidates
        $('#applyFilters').click(function() {
            const statusFilter = $('#statusFilter').val();
            const progressFilter = $('#progressFilter').val();
            const searchText = $('#searchUser').val().toLowerCase();
            
            $('.candidate-item').each(function() {
                let showItem = true;
                
                // Status filter
                if (statusFilter !== 'all') {
                    const status = $(this).data('status');
                    if (status !== statusFilter) {
                        showItem = false;
                    }
                }
                
                // Progress filter
                if (progressFilter !== 'all' && showItem) {
                    const progress = parseInt($(this).data('progress'));
                    
                    switch(progressFilter) {
                        case 'less25':
                            if (progress >= 25) showItem = false;
                            break;
                        case '25to50':
                            if (progress < 25 || progress > 50) showItem = false;
                            break;
                        case '50to75':
                            if (progress < 50 || progress > 75) showItem = false;
                            break;
                        case 'more75':
                            if (progress <= 75) showItem = false;
                            break;
                    }
                }
                
                // Search filter
                if (searchText && showItem) {
                    const name = $(this).find('.name').text().toLowerCase();
                    if (!name.includes(searchText)) {
                        showItem = false;
                    }
                }
                
                $(this).toggle(showItem);
            });
        });
    });
</script>
@endsection 