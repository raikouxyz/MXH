@extends('layouts.app')

@section('title', 'Bảng tin')

{{-- Thêm CSS tùy chỉnh --}}
@push('styles')
<style>
    /* Style cho card bài viết */
    .post-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
        background: #fff;
        overflow: hidden;
    }

    .post-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.12);
    }

    /* Style cho tiêu đề trang */
    .page-title {
        font-size: 2rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #eee;
    }

    /* Style cho tiêu đề bài viết */
    .post-title {
        color: #2c3e50;
        font-weight: 600;
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
        transition: color 0.2s ease;
    }

    .post-title:hover {
        color: #3498db;
        text-decoration: none;
    }

    /* Style cho meta info */
    .post-meta {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .post-meta i {
        margin-right: 0.25rem;
    }

    /* Style cho nội dung bài viết */
    .post-excerpt {
        color: #505965;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 1rem;
    }

    /* Style cho nút xem chi tiết */
    .btn-view-post {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        background-color: #3498db;
        border-color: #3498db;
        color: white;
    }

    .btn-view-post:hover {
        background-color: #2980b9;
        border-color: #2980b9;
        transform: translateX(5px);
    }

    .btn-view-post i {
        margin-right: 0.5rem;
        transition: transform 0.3s ease;
    }

    .btn-view-post:hover i {
        transform: translateX(3px);
    }

    /* Style cho nút like */
    .like-button {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        color: #6c757d;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .like-button:hover {
        background-color: #e9ecef;
        transform: translateY(-2px);
    }

    .like-button i {
        font-size: 1.1rem;
        transition: all 0.3s ease;
    }

    .like-button:hover i {
        transform: scale(1.1);
    }

    .like-button.liked {
        background-color: #fff5f5;
        border-color: #ffcdd2;
        color: #e53935;
    }

    .like-button.liked i {
        color: #e53935;
    }

    .likes-count {
        font-weight: 500;
        min-width: 1.5rem;
        text-align: center;
    }

    /* Style cho phân trang */
    .pagination {
        margin-top: 2rem;
    }

    .page-link {
        border-radius: 5px;
        margin: 0 3px;
        color: #3498db;
        border: 1px solid #e9ecef;
    }

    .page-item.active .page-link {
        background-color: #3498db;
        border-color: #3498db;
    }

    /* Style cho alert */
    .custom-alert {
        border-radius: 10px;
        border-left: 4px solid #3498db;
        background-color: #f8f9fa;
        padding: 1rem;
    }

    .custom-alert a {
        color: #3498db;
        font-weight: 500;
        text-decoration: none;
    }

    .custom-alert a:hover {
        text-decoration: underline;
    }

    /* Thêm animation cho cards */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .post-card {
        animation: fadeInUp 0.5s ease forwards;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="page-title mb-0">Bảng tin</h2>
                
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @forelse($posts as $post)
                <div class="card post-card">
                    <div class="card-body">
                        <h5 class="post-title">
                            <a href="{{ route('posts.show', $post) }}" class="text-decoration-none">
                                {{ $post->title }}
                            </a>
                        </h5>
                        <div class="post-meta">
                            <span><i class="fas fa-user"></i> {{ $post->user->name }}</span>
                            <span><i class="fas fa-clock"></i> {{ $post->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <p class="post-excerpt">{{ Str::limit($post->content, 200) }}</p>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('posts.show', $post) }}" class="btn btn-view-post">
                                <i class="fas fa-eye"></i> Xem chi tiết
                            </a>
                            
                            <button class="like-button {{ $post->isLikedBy(auth()->user()) ? 'liked' : '' }}" 
                                    data-post-id="{{ $post->id }}" 
                                    data-liked="{{ $post->isLikedBy(auth()->user()) ? 'true' : 'false' }}">
                                <i class="fas fa-heart"></i>
                                <span class="likes-count">{{ $post->likes_count }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert custom-alert">
                    Chưa có bài viết nào. <a href="{{ route('posts.create') }}">Hãy là người đầu tiên đăng bài</a>
                </div>
            @endforelse

            <div class="d-flex justify-content-center">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const likeButtons = document.querySelectorAll('.like-button');
    
    likeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const icon = this.querySelector('i');
            const countSpan = this.querySelector('.likes-count');
            
            fetch(`/posts/${postId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.liked) {
                    this.classList.add('liked');
                } else {
                    this.classList.remove('liked');
                }
                countSpan.textContent = data.likes_count;
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
</script>
@endpush 
