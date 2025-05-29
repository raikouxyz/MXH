@extends('layouts.app')

@section('title', 'Tin nhắn')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Danh sách người dùng và nhóm -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Trò chuyện</h5>
                    <button type="button" class="btn btn-sm btn-primary" onclick="showCreateGroupModal()">
                        <i class="fas fa-users"></i> Tạo nhóm
                    </button>
                </div>
                <div class="card-body p-0">
                    <!-- Tab navigation -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#friends-tab">
                                Bạn bè
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#groups-tab">
                                Nhóm chat
                            </a>
                        </li>
                    </ul>

                    <!-- Tab content -->
                    <div class="tab-content">
                        

                        <!-- Danh sách bạn bè -->
                        <div class="tab-pane fade" id="friends-tab">
                            <div class="list-group list-group-flush" id="friends-list">
                                @forelse(auth()->user()->friends as $friend)
                                    @php
                                        // Lấy số tin nhắn chưa đọc từ người dùng này
                                        $unreadCount = auth()->user()->getUnreadMessagesFrom($friend->id);
                                        // Lấy tin nhắn cuối cùng
                                        $lastMessage = auth()->user()->getLastMessageWith($friend->id);
                                    @endphp

                                    <a href="{{ route('messages.show', $friend->id) }}" 
                                       class="list-group-item list-group-item-action user-chat-item {{ $selectedUser && $selectedUser->id == $friend->id ? 'active' : '' }}"
                                       data-user-id="{{ $friend->id }}">
                                        <div class="d-flex align-items-center">
                                            <!-- Avatar người dùng -->
                                            <div class="position-relative">
                                                <img src="{{ $friend->avatar_url }}" 
                                                     class="rounded-circle me-2" 
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                                @if($unreadCount > 0)
                                                    <!-- Badge hiển thị số tin nhắn chưa đọc -->
                                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                        {{ $unreadCount }}
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <!-- Thông tin người dùng và tin nhắn -->
                                            <div class="flex-grow-1 min-width-0">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0">{{ $friend->name }}</h6>
                                                    @if($lastMessage)
                                                        <small class="text-muted">
                                                            {{ $lastMessage->created_at->diffForHumans(null, true) }}
                                                        </small>
                                                    @endif
                                                </div>
                                                @if($lastMessage)
                                                    <p class="mb-0 small text-truncate {{ !$lastMessage->is_read && $lastMessage->receiver_id === auth()->id() ? 'fw-bold' : 'text-muted' }}">
                                                        {{ $lastMessage->sender_id === auth()->id() ? 'Bạn: ' : '' }}
                                                        {{ Str::limit($lastMessage->content, 30) }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="list-group-item text-center text-muted">
                                        <p class="mb-2">Bạn chưa có bạn bè nào</p>
                                        <a href="{{ route('users.index') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-user-plus"></i> Tìm bạn mới
                                        </a>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Danh sách nhóm -->
                        <div class="tab-pane fade" id="groups-tab">
                            <div class="list-group list-group-flush" id="groups-list">
                                @forelse(auth()->user()->groups as $group)
                                    <a href="{{ route('chat-groups.show', $group) }}" 
                                       class="list-group-item list-group-item-action {{ request()->is('chat-groups/'.$group->id) ? 'active' : '' }}">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                @if($group->avatar)
                                                    <img src="{{ asset('storage/' . $group->avatar) }}" 
                                                         class="rounded-circle" 
                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                                         style="width: 40px; height: 40px;">
                                                        {{ substr($group->name, 0, 1) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-0">{{ $group->name }}</h6>
                                                <small class="text-muted">
                                                    {{ $group->members->count() }} thành viên
                                                    @if($group->messages->isNotEmpty())
                                                        · {{ $group->messages->first()->created_at->diffForHumans() }}
                                                    @endif
                                                </small>
                                                @if($group->messages->isNotEmpty())
                                                    <p class="mb-0 small text-truncate text-muted">
                                                        {{ $group->messages->first()->sender->name }}: 
                                                        {{ $group->messages->first()->content }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="text-center p-3 text-muted">
                                        <p>Bạn chưa tham gia nhóm chat nào</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Khung chat -->
        <div class="col-md-9">
            @if($selectedUser)
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <img src="{{ $selectedUser->avatar_url }}" 
                                 class="rounded-circle me-2" 
                                 style="width: 40px; height: 40px; object-fit: cover;">
                            <h5 class="mb-0">{{ $selectedUser->name }}</h5>
                        </div>
                    </div>
                    <div class="card-body" style="height: 400px; overflow-y: auto;" id="message-container">
                        @include('messages.partials.message-list')
                    </div>
                    <div class="card-footer">
                        <div class="message-input-wrapper">
                            <div class="message-actions mb-2">
                                <button type="button" class="btn btn-light btn-sm" onclick="toggleEmojiPicker()">
                                    <i class="far fa-smile"></i>
                                </button>
                                <button type="button" class="btn btn-light btn-sm" onclick="toggleStickerPicker()">
                                    <i class="far fa-sticky-note"></i>
                                </button>
                            </div>

                            <!-- Emoji Picker -->
                            <div id="emoji-picker" class="emoji-picker" style="display: none;">
                                <!-- Tab navigation -->
                                <ul class="nav nav-tabs nav-fill mb-2">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#smileys">😊</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#gestures">👋</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#love">❤️</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#activities">⚽</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#food">🍔</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#animals">🐶</a>
                                    </li>
                                </ul>

                                <!-- Tab content -->
                                <div class="tab-content">
                                    <!-- Mặt cười -->
                                    <div class="tab-pane fade show active" id="smileys">
                                        <div class="emoji-list">
                                            <span onclick="insertEmoji('😀')">😀</span>
                                            <span onclick="insertEmoji('😃')">😃</span>
                                            <span onclick="insertEmoji('😄')">😄</span>
                                            <span onclick="insertEmoji('😁')">😁</span>
                                            <span onclick="insertEmoji('😅')">😅</span>
                                            <span onclick="insertEmoji('😂')">😂</span>
                                            <span onclick="insertEmoji('🤣')">🤣</span>
                                            <span onclick="insertEmoji('😊')">😊</span>
                                            <span onclick="insertEmoji('😇')">😇</span>
                                            <span onclick="insertEmoji('🙂')">🙂</span>
                                            <span onclick="insertEmoji('😉')">😉</span>
                                            <span onclick="insertEmoji('😌')">😌</span>
                                            <span onclick="insertEmoji('😍')">😍</span>
                                            <span onclick="insertEmoji('🥰')">🥰</span>
                                            <span onclick="insertEmoji('😘')">😘</span>
                                            <span onclick="insertEmoji('😋')">😋</span>
                                            <span onclick="insertEmoji('😎')">😎</span>
                                            <span onclick="insertEmoji('🤩')">🤩</span>
                                            <span onclick="insertEmoji('🥳')">🥳</span>
                                            <span onclick="insertEmoji('😏')">😏</span>
                                        </div>
                                    </div>

                                    <!-- Cử chỉ -->
                                    <div class="tab-pane fade" id="gestures">
                                        <div class="emoji-list">
                                            <span onclick="insertEmoji('👋')">👋</span>
                                            <span onclick="insertEmoji('🤚')">🤚</span>
                                            <span onclick="insertEmoji('🖐')">🖐</span>
                                            <span onclick="insertEmoji('✋')">✋</span>
                                            <span onclick="insertEmoji('🖖')">🖖</span>
                                            <span onclick="insertEmoji('👌')">👌</span>
                                            <span onclick="insertEmoji('🤌')">🤌</span>
                                            <span onclick="insertEmoji('🤏')">🤏</span>
                                            <span onclick="insertEmoji('✌️')">✌️</span>
                                            <span onclick="insertEmoji('🤞')">🤞</span>
                                            <span onclick="insertEmoji('🤟')">🤟</span>
                                            <span onclick="insertEmoji('🤘')">🤘</span>
                                            <span onclick="insertEmoji('👍')">👍</span>
                                            <span onclick="insertEmoji('👎')">👎</span>
                                            <span onclick="insertEmoji('👊')">👊</span>
                                        </div>
                                    </div>

                                    <!-- Tình yêu -->
                                    <div class="tab-pane fade" id="love">
                                        <div class="emoji-list">
                                            <span onclick="insertEmoji('❤️')">❤️</span>
                                            <span onclick="insertEmoji('🧡')">🧡</span>
                                            <span onclick="insertEmoji('💛')">💛</span>
                                            <span onclick="insertEmoji('💚')">💚</span>
                                            <span onclick="insertEmoji('💙')">💙</span>
                                            <span onclick="insertEmoji('💜')">💜</span>
                                            <span onclick="insertEmoji('🤎')">🤎</span>
                                            <span onclick="insertEmoji('🖤')">🖤</span>
                                            <span onclick="insertEmoji('🤍')">🤍</span>
                                            <span onclick="insertEmoji('💯')">💯</span>
                                            <span onclick="insertEmoji('💢')">💢</span>
                                            <span onclick="insertEmoji('💥')">💥</span>
                                            <span onclick="insertEmoji('💫')">💫</span>
                                            <span onclick="insertEmoji('💝')">💝</span>
                                            <span onclick="insertEmoji('💞')">💞</span>
                                        </div>
                                    </div>

                                    <!-- Hoạt động -->
                                    <div class="tab-pane fade" id="activities">
                                        <div class="emoji-list">
                                            <span onclick="insertEmoji('⚽')">⚽</span>
                                            <span onclick="insertEmoji('🏀')">🏀</span>
                                            <span onclick="insertEmoji('🏈')">🏈</span>
                                            <span onclick="insertEmoji('⚾')">⚾</span>
                                            <span onclick="insertEmoji('🎾')">🎾</span>
                                            <span onclick="insertEmoji('🏐')">🏐</span>
                                            <span onclick="insertEmoji('🎮')">🎮</span>
                                            <span onclick="insertEmoji('🎲')">🎲</span>
                                            <span onclick="insertEmoji('🎭')">🎭</span>
                                            <span onclick="insertEmoji('🎨')">🎨</span>
                                            <span onclick="insertEmoji('🎬')">🎬</span>
                                            <span onclick="insertEmoji('🎤')">🎤</span>
                                            <span onclick="insertEmoji('🎧')">🎧</span>
                                            <span onclick="insertEmoji('🎸')">🎸</span>
                                            <span onclick="insertEmoji('🎹')">🎹</span>
                                        </div>
                                    </div>

                                    <!-- Đồ ăn -->
                                    <div class="tab-pane fade" id="food">
                                        <div class="emoji-list">
                                            <span onclick="insertEmoji('🍕')">🍕</span>
                                            <span onclick="insertEmoji('🍔')">🍔</span>
                                            <span onclick="insertEmoji('🍟')">🍟</span>
                                            <span onclick="insertEmoji('🌭')">🌭</span>
                                            <span onclick="insertEmoji('🍿')">🍿</span>
                                            <span onclick="insertEmoji('🧂')">🧂</span>
                                            <span onclick="insertEmoji('🥓')">🥓</span>
                                            <span onclick="insertEmoji('🥚')">🥚</span>
                                            <span onclick="insertEmoji('🍳')">🍳</span>
                                            <span onclick="insertEmoji('🧇')">🧇</span>
                                            <span onclick="insertEmoji('🥞')">🥞</span>
                                            <span onclick="insertEmoji('🧈')">🧈</span>
                                            <span onclick="insertEmoji('🍞')">🍞</span>
                                            <span onclick="insertEmoji('🥐')">🥐</span>
                                            <span onclick="insertEmoji('🥨')">🥨</span>
                                        </div>
                                    </div>

                                    <!-- Động vật -->
                                    <div class="tab-pane fade" id="animals">
                                        <div class="emoji-list">
                                            <span onclick="insertEmoji('🐶')">🐶</span>
                                            <span onclick="insertEmoji('🐱')">🐱</span>
                                            <span onclick="insertEmoji('🐭')">🐭</span>
                                            <span onclick="insertEmoji('🐹')">🐹</span>
                                            <span onclick="insertEmoji('🐰')">🐰</span>
                                            <span onclick="insertEmoji('🦊')">🦊</span>
                                            <span onclick="insertEmoji('🐻')">🐻</span>
                                            <span onclick="insertEmoji('🐼')">🐼</span>
                                            <span onclick="insertEmoji('🐨')">🐨</span>
                                            <span onclick="insertEmoji('🐯')">🐯</span>
                                            <span onclick="insertEmoji('🦁')">🦁</span>
                                            <span onclick="insertEmoji('🐮')">🐮</span>
                                            <span onclick="insertEmoji('🐷')">🐷</span>
                                            <span onclick="insertEmoji('🐸')">🐸</span>
                                            <span onclick="insertEmoji('🐵')">🐵</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sticker Picker -->
                            <div id="sticker-picker" class="sticker-picker" style="display: none;">
                                <div class="sticker-list">
                                    <!-- Thêm danh sách sticker -->
                                    <img src="/stickers/1.png" onclick="selectSticker('1.png')" class="sticker-thumb">
                                    <img src="/stickers/2.png" onclick="selectSticker('2.png')" class="sticker-thumb">
                                    <!-- Thêm các sticker khác -->
                                    <img src="/stickers/3.png" onclick="selectSticker('3.png')" class="sticker-thumb">
                                    <img src="/stickers/4.png" onclick="selectSticker('4.png')" class="sticker-thumb">
                                    <img src="/stickers/5.png" onclick="selectSticker('5.png')" class="sticker-thumb">
                                    <img src="/stickers/6.png" onclick="selectSticker('6.png')" class="sticker-thumb">
                                    <img src="/stickers/7.png" onclick="selectSticker('7.png')" class="sticker-thumb">
                                    <img src="/stickers/8.png" onclick="selectSticker('8.png')" class="sticker-thumb">
                                    <img src="/stickers/9.png" onclick="selectSticker('9.png')" class="sticker-thumb">
                                    <img src="/stickers/10.png" onclick="selectSticker('10.png')" class="sticker-thumb">
                                    <img src="/stickers/11.png" onclick="selectSticker('11.png')" class="sticker-thumb">
                                    <img src="/stickers/12.png" onclick="selectSticker('12.png')" class="sticker-thumb">
                                </div>
                            </div>

                            <form id="message-form" action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="receiver_id" value="{{ $selectedUser->id }}">
                                <input type="hidden" name="sticker" id="selected-sticker">
                                
                                <!-- Preview hình ảnh -->
                                <div id="image-preview" class="mb-2" style="display: none;">
                                    <div class="position-relative d-inline-block">
                                        <img src="" alt="Preview" style="max-height: 150px; max-width: 200px; object-fit: contain;" class="rounded">
                                        <button type="button" class="btn-close position-absolute top-0 end-0 m-1" 
                                                style="background-color: white; padding: 5px; border-radius: 50%; box-shadow: 0 0 5px rgba(0,0,0,0.2);"
                                                onclick="removeImage()"></button>
                                    </div>
                                </div>

                                <div class="input-group">
                                    <input type="text" name="content" class="form-control" placeholder="Nhập tin nhắn...">
                                    <label class="btn btn-outline-secondary" for="image-upload">
                                        <i class="fas fa-image"></i>
                                    </label>
                                    <input type="file" id="image-upload" name="image" accept="image/*" style="display: none;" onchange="previewImage(this)">
                                    <button type="submit" class="btn btn-primary">Gửi</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    Chọn một người dùng để bắt đầu cuộc trò chuyện
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal tạo nhóm chat -->
<div class="modal fade" id="createGroupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tạo nhóm chat mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createGroupForm" action="{{ route('chat-groups.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <!-- Tên nhóm -->
                    <div class="mb-3">
                        <label class="form-label">Tên nhóm</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>

                    <!-- Mô tả -->
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" name="description" rows="2"></textarea>
                    </div>

                    <!-- Chọn thành viên -->
                    <div class="mb-3">
                        <label class="form-label">Chọn thành viên (tối thiểu 2 người)</label>
                        <div class="border p-2 rounded" style="max-height: 200px; overflow-y: auto;">
                            @foreach($users as $user)
                                <div class="form-check">
                                    <input class="form-check-input member-checkbox" 
                                           type="checkbox" 
                                           name="members[]" 
                                           value="{{ $user->id }}" 
                                           id="user-{{ $user->id }}">
                                    <label class="form-check-label" for="user-{{ $user->id }}">
                                        {{ $user->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Avatar nhóm -->
                    <div class="mb-3">
                        <label class="form-label">Avatar nhóm (tùy chọn)</label>
                        <input type="file" class="form-control" name="avatar" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="createGroupBtn" disabled>Tạo nhóm</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* CSS cho danh sách người dùng */
.user-chat-item {
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.user-chat-item:hover {
    background-color: rgba(0,0,0,0.05);
}

.user-chat-item.active {
    border-left-color: #0d6efd;
    background-color: rgba(13,110,253,0.1);
}

.user-chat-item.has-unread {
    background-color: rgba(13,110,253,0.05);
}

/* Sửa lại phần scroll */
.card-body {
    height: calc(100vh - 200px); /* Điều chỉnh chiều cao phù hợp */
    overflow: hidden; /* Thay đổi từ auto thành hidden */
}

.tab-content {
    height: calc(100% - 40px); /* Trừ đi chiều cao của tab navigation */
    overflow: hidden;
}

.tab-pane {
    height: 100%;
    overflow-y: auto;
}

.list-group {
    height: 100%;
    overflow-y: auto;
    margin-bottom: 0; /* Loại bỏ margin bottom */
}

/* Giữ nguyên các style khác */
.min-width-0 {
    min-width: 0;
}

.emoji-picker {
    width: 300px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 10px;
    position: absolute;
    bottom: 100%;
    left: 0;
    z-index: 1000;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.nav-tabs .nav-link {
    padding: 5px;
    font-size: 1.2em;
}

.emoji-list {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 5px;
    padding: 10px;
}

.emoji-list span {
    cursor: pointer;
    font-size: 1.5em;
    text-align: center;
    padding: 5px;
    border-radius: 5px;
    transition: background-color 0.2s;
}

.emoji-list span:hover {
    background-color: #f0f0f0;
}

.sticker-picker {
    position: absolute;
    bottom: 100%;
    left: 0;
    background: white;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 10px;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
}

.sticker-thumb {
    width: 60px;
    height: 60px;
    object-fit: contain;
}

.message-input-wrapper {
    position: relative;
}

.nav-tabs .nav-link {
    border: none;
    color: #6c757d;
    padding: 1rem;
}

.nav-tabs .nav-link.active {
    color: #0d6efd;
    border-bottom: 2px solid #0d6efd;
}

.list-group-item-action {
    border-left: 3px solid transparent;
}

.list-group-item-action.active {
    border-left-color: #0d6efd;
    background-color: rgba(13, 110, 253, 0.1);
}

.list-group-item-action:hover {
    border-left-color: #0d6efd;
}

/* Style cho preview hình ảnh */
#image-preview {
    background-color: #f8f9fa;
    border-radius: 5px;
    padding: 10px;
    margin-bottom: 10px;
}

#image-preview img {
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-close {
    transition: all 0.2s ease;
}

.btn-close:hover {
    background-color: #dc3545 !important;
    opacity: 1;
}

/* Style cho input file button */
.btn-outline-secondary {
    border-color: #ced4da;
}

.btn-outline-secondary:hover {
    background-color: #f8f9fa;
    border-color: #0d6efd;
    color: #0d6efd;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageContainer = document.getElementById('message-container');
    const messageForm = document.getElementById('message-form');
    
    // Tự động chọn tab bạn bè khi load trang
    const friendsTab = document.querySelector('a[href="#friends-tab"]');
    if (friendsTab) {
        const tab = new bootstrap.Tab(friendsTab);
        tab.show();
    }
    
    // Cuộn xuống cuối cùng
    function scrollToBottom() {
        messageContainer.scrollTop = messageContainer.scrollHeight;
    }
    
    scrollToBottom();
    
    // Xử lý gửi tin nhắn bằng Ajax
    if (messageForm) {
        messageForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            
            try {
                // Disable nút gửi để tránh gửi nhiều lần
                submitButton.disabled = true;
                
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();
                
                if (response.ok) {
                    // Thêm tin nhắn mới vào container
                    messageContainer.insertAdjacentHTML('beforeend', data.message);
                    scrollToBottom();
                    
                    // Reset form và preview
                    this.reset();
                    document.getElementById('image-preview').style.display = 'none';
                    document.getElementById('selected-sticker').value = '';
                    
                    // Ẩn các picker
                    document.getElementById('emoji-picker').style.display = 'none';
                    document.getElementById('sticker-picker').style.display = 'none';
                } else {
                    // Hiển thị lỗi nếu có
                    alert(data.error || 'Có lỗi xảy ra khi gửi tin nhắn');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi gửi tin nhắn');
            } finally {
                // Enable lại nút gửi
                submitButton.disabled = false;
            }
        });
    }
    
    // Polling để kiểm tra tin nhắn mới
    if (messageContainer) {
        setInterval(() => {
            const receiverId = document.querySelector('input[name="receiver_id"]').value;
            fetch(`/messages/${receiverId}/new`)
                .then(response => response.json())
                .then(data => {
                    if (data.messages) {
                        messageContainer.insertAdjacentHTML('beforeend', data.messages);
                        scrollToBottom();
                    }
                });
        }, 5000);
    }
});

function toggleEmojiPicker() {
    const picker = document.getElementById('emoji-picker');
    picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
    document.getElementById('sticker-picker').style.display = 'none';
}

function toggleStickerPicker() {
    const picker = document.getElementById('sticker-picker');
    picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
    document.getElementById('emoji-picker').style.display = 'none';
}

function insertEmoji(emoji) {
    const input = document.querySelector('input[name="content"]');
    input.value += emoji;
}

function selectSticker(stickerId) {
    // Lấy các element cần thiết
    const messageForm = document.getElementById('message-form');
    const messageContainer = document.getElementById('message-container');
    const stickerPicker = document.getElementById('sticker-picker');
    
    // Cập nhật giá trị sticker được chọn
    document.getElementById('selected-sticker').value = stickerId;
    
    // Tạo FormData từ form
    const formData = new FormData(messageForm);
    
    // Gửi request bằng fetch API
    fetch(messageForm.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            // Thêm tin nhắn sticker vào container
            messageContainer.insertAdjacentHTML('beforeend', data.message);
            
            // Cuộn xuống cuối cùng
            messageContainer.scrollTop = messageContainer.scrollHeight;
            
            // Reset form và đóng sticker picker
            messageForm.reset();
            document.getElementById('selected-sticker').value = '';
            stickerPicker.style.display = 'none';
        } else if (data.error) {
            // Hiển thị lỗi nếu có
            console.error('Lỗi:', data.error);
        }
    })
    .catch(error => {
        // Xử lý lỗi network hoặc lỗi khác
        console.error('Lỗi khi gửi sticker:', error);
    });
}

// Thêm function scrollToBottom vào scope global để có thể sử dụng ở nhiều nơi
function scrollToBottom() {
    const messageContainer = document.getElementById('message-container');
    if (messageContainer) {
        messageContainer.scrollTop = messageContainer.scrollHeight;
    }
}

// Hiển thị modal tạo nhóm
function showCreateGroupModal() {
    const modal = new bootstrap.Modal(document.getElementById('createGroupModal'));
    modal.show();
}

// Xử lý form tạo nhóm
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('createGroupForm');
    const checkboxes = document.querySelectorAll('.member-checkbox');
    const createGroupBtn = document.getElementById('createGroupBtn');

    // Kiểm tra số lượng thành viên được chọn
    function updateCreateButton() {
        const checkedCount = document.querySelectorAll('.member-checkbox:checked').length;
        createGroupBtn.disabled = checkedCount < 2;
    }

    // Thêm sự kiện cho các checkbox
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateCreateButton);
    });

    // Xử lý submit form
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Thêm nhóm mới vào danh sách
                const groupsList = document.getElementById('groups-list');
                groupsList.insertAdjacentHTML('afterbegin', data.groupHtml);
                
                // Đóng modal và reset form
                bootstrap.Modal.getInstance(document.getElementById('createGroupModal')).hide();
                form.reset();
                
                // Chuyển tab sang danh sách nhóm
                const groupsTab = document.querySelector('a[href="#groups-tab"]');
                bootstrap.Tab.getOrCreateInstance(groupsTab).show();
            } else {
                alert(data.error || 'Có lỗi xảy ra khi tạo nhóm');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi tạo nhóm');
        });
    });
});

// Thêm các hàm xử lý preview hình ảnh
function previewImage(input) {
    const preview = document.getElementById('image-preview');
    const previewImg = preview.querySelector('img');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage() {
    const input = document.getElementById('image-upload');
    const preview = document.getElementById('image-preview');
    
    input.value = ''; // Xóa file đã chọn
    preview.style.display = 'none'; // Ẩn preview
    preview.querySelector('img').src = ''; // Xóa source của ảnh
}

// Thêm event listener cho input file
document.getElementById('image-upload').addEventListener('change', function() {
    previewImage(this);
});
</script>
@endpush 