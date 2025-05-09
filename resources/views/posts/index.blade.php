@extends('layouts.app')

<<<<<<< Updated upstream
@section('title', 'Bảng tin')

@section('content')
<div class="container py-4">
    <h4 class="mb-4">Bảng tin</h4>
    
    @if($posts->count() > 0)
        <div class="row">
            @foreach($posts as $post)
                <div class="col-md-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $post->title }}</h5>
                            <p class="card-text text-muted small">
                                Đăng bởi: {{ $post->user->name }} - 
                                {{ $post->created_at->format('d/m/Y H:i') }}
                            </p>
                            <p class="card-text">{{ Str::limit($post->content, 200) }}</p>
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-sm btn-outline-danger me-2 like-button {{ $post->isLikedBy(auth()->id()) ? 'active' : '' }}" 
                                        data-post-id="{{ $post->id }}">
                                    <i class="fas fa-heart {{ $post->isLikedBy(auth()->id()) ? 'text-white' : 'text-muted' }}"></i>
                                    <span class="like-count ms-1">{{ $post->getLikesCount() }}</span>
                                </button>
                                <a href="{{ route('posts.show', $post) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> Xem chi tiết
                                </a>
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
            Chưa có bài viết nào được đăng. <a href="{{ route('posts.create') }}">Hãy là người đầu tiên đăng bài</a>
        </div>
    @endif
</div>
@endsection
=======
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
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
                    @if(auth()->id() === $post->user_id)
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
                    @endif
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $post->title }}</h5>
                    <p class="card-text">{{ $post->content }}</p>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-link text-decoration-none p-0 me-3 like-button" 
                                data-post-id="{{ $post->id }}"
                                data-liked="{{ $post->isLikedBy(auth()->id()) ? 'true' : 'false' }}">
                            <i class="fas fa-heart {{ $post->isLikedBy(auth()->id()) ? 'text-danger' : 'text-muted' }}"></i>
                            <span class="like-count ms-1">{{ $post->likes()->count() }}</span>
                        </button>
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
</div>
>>>>>>> Stashed changes

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const likeButtons = document.querySelectorAll('.like-button');
<<<<<<< Updated upstream
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
=======
>>>>>>> Stashed changes
    
    likeButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const postId = this.dataset.postId;
<<<<<<< Updated upstream
=======
            const icon = this.querySelector('i');
            const countSpan = this.querySelector('.like-count');
>>>>>>> Stashed changes
            
            try {
                const response = await fetch(`/posts/${postId}/like`, {
                    method: 'POST',
                    headers: {
<<<<<<< Updated upstream
                        'X-CSRF-TOKEN': csrfToken,
=======
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
>>>>>>> Stashed changes
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
<<<<<<< Updated upstream
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                // Update like count
                const likeCount = this.querySelector('.like-count');
                likeCount.textContent = data.likesCount;
                
                // Update button state
                if (data.reaction === 'like') {
                    this.classList.add('active');
                    this.querySelector('i').classList.remove('text-muted');
                    this.querySelector('i').classList.add('text-white');
                } else {
                    this.classList.remove('active');
                    this.querySelector('i').classList.remove('text-white');
                    this.querySelector('i').classList.add('text-muted');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi thực hiện thao tác like!');
=======
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
>>>>>>> Stashed changes
            }
        });
    });
});
</script>
<<<<<<< Updated upstream
@endpush 
=======
@endpush
@endsection
>>>>>>> Stashed changes
