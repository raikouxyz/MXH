@extends('layouts.app')

<<<<<<< Updated upstream
@section('title', 'Bài viết của tôi')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Bài viết của tôi</h4>
        <a href="{{ route('posts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tạo bài viết mới
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if($posts->count() > 0)
    <div class="row">
        @foreach($posts as $post)
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h5 class="card-title">{{ $post->title }}</h5>
                    </div>
                    <p class="card-text text-muted small">
                        Đăng ngày {{ $post->created_at->format('d/m/Y H:i') }}
                    </p>
                    <p class="card-text">{{ Str::limit($post->content, 200) }}</p>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-sm btn-outline-danger me-2 like-button" 
=======
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Bài viết của tôi</h4>
                <a href="{{ route('posts.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tạo bài viết mới
                </a>
            </div>

            @foreach($posts as $post)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <img src="{{ $post->user->avatar_url }}" alt="{{ $post->user->name }}" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                        <div>
                            <h6 class="mb-0">{{ $post->user->name }}</h6>
                            <small class="text-muted">{{ $post->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-link text-dark p-0" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('posts.edit', $post) }}">Sửa</a></li>
                            <li>
                                <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $post->title }}</h5>
                    <p class="card-text">{{ $post->content }}</p>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-link text-decoration-none p-0 me-3 like-button" 
>>>>>>> Stashed changes
                                data-post-id="{{ $post->id }}"
                                data-liked="{{ $post->isLikedBy(auth()->id()) ? 'true' : 'false' }}">
                            <i class="fas fa-heart {{ $post->isLikedBy(auth()->id()) ? 'text-danger' : 'text-muted' }}"></i>
                            <span class="like-count ms-1">{{ $post->likes()->count() }}</span>
                        </button>
<<<<<<< Updated upstream
                        <a href="{{ route('posts.show', $post) }}" class="btn btn-sm btn-info me-2">
                            <i class="fas fa-eye"></i> Xem
                        </a>

                        <a href="{{ route('posts.edit', $post) }}" class="btn btn-sm btn-warning me-2">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                        <form action="{{ route('posts.destroy', $post) }}" method="POST"
                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Hiển thị phân trang -->
    <div class="d-flex justify-content-center mt-4">
        {{ $posts->links() }}
    </div>
    @else
    <div class="alert alert-info">
        Bạn chưa có bài viết nào. <a href="{{ route('posts.create') }}">Tạo bài viết đầu tiên</a>
    </div>
    @endif
=======
                        <a href="{{ route('posts.show', $post) }}" class="btn btn-link text-decoration-none p-0 text-muted">
                            <i class="fas fa-eye"></i>
                            <span class="ms-1">Xem chi tiết</span>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach

            <div class="d-flex justify-content-center">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
>>>>>>> Stashed changes
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const likeButtons = document.querySelectorAll('.like-button');
    
    likeButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const postId = this.dataset.postId;
            const icon = this.querySelector('i');
            const countSpan = this.querySelector('.like-count');
            
            try {
                const response = await fetch(`/posts/${postId}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    // Update like count
                    countSpan.textContent = data.likeCount;
                    
                    // Update icon color
                    if (data.liked) {
                        icon.classList.remove('text-muted');
                        icon.classList.add('text-danger');
                    } else {
                        icon.classList.remove('text-danger');
                        icon.classList.add('text-muted');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    });
});
</script>
@endpush
<<<<<<< Updated upstream
@endsection
=======
@endsection
>>>>>>> Stashed changes
