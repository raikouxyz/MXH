@extends('layouts.app')

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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const likeButtons = document.querySelectorAll('.like-button');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    likeButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const postId = this.dataset.postId;
            
            try {
                const response = await fetch(`/posts/${postId}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
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
            }
        });
    });
});
</script>
@endpush 