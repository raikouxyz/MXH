@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- Thông tin người dùng -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <img src="{{ asset('images/default-avatar.png') }}" alt="{{ $user->name }}" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    @endif
                    
                    <h4>{{ $user->name }}</h4>
                    <p class="text-muted">{{ $user->email }}</p>
                    
                    @if($user->bio)
                        <p>{{ $user->bio }}</p>
                    @endif
                    
                    <div class="d-flex justify-content-around mb-3">
                        <div>
                            <strong>{{ $postsCount }}</strong>
                            <div>Bài viết</div>
                        </div>
                        <div>
                            <strong>{{ $friendsCount }}</strong>
                            <div>Bạn bè</div>
                        </div>
                    </div>

                    @if(auth()->id() !== $user->id)
                        <div class="d-flex justify-content-center gap-2">
                            @php
                                $isFriend = auth()->user()->isFriendWith($user);
                                $hasSentRequest = auth()->user()->hasSentFriendRequestTo($user);
                                $hasReceivedRequest = auth()->user()->hasReceivedFriendRequestFrom($user);
                            @endphp

                            @if($isFriend)
                                <form action="{{ route('users.remove-friend', $user) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Hủy kết bạn</button>
                                </form>
                            @elseif($hasSentRequest)
                                <button class="btn btn-secondary" disabled>Đã gửi lời mời</button>
                            @elseif($hasReceivedRequest)
                                <div class="d-flex gap-2">
                                    <form action="{{ route('users.accept-friend-request', $user) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success">Chấp nhận</button>
                                    </form>
                                    <form action="{{ route('users.reject-friend-request', $user) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">Từ chối</button>
                                    </form>
                                </div>
                            @else
                                <form action="{{ route('users.add-friend', $user) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">Kết bạn</button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Bài viết của người dùng -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Bài viết</h5>
                </div>
                <div class="card-body">
                    @forelse($posts as $post)
                        <div class="post mb-4">
                            <div class="d-flex align-items-center mb-3">
                                @if($post->user->avatar)
                                    <img src="{{ Storage::url($post->user->avatar) }}" alt="{{ $post->user->name }}" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    <img src="{{ asset('images/default-avatar.png') }}" alt="{{ $post->user->name }}" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                @endif
                                <div>
                                    <h6 class="mb-0">{{ $post->user->name }}</h6>
                                    <small class="text-muted">{{ $post->created_at->diffForHumans() }}</small>
                                </div>
                            </div>

                            <p>{{ $post->content }}</p>

                            @if($post->image)
                                <img src="{{ Storage::url($post->image) }}" alt="Post image" class="img-fluid mb-3">
                            @endif

                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="me-3">
                                        <i class="fas fa-heart"></i> {{ $post->likes->count() }}
                                    </span>
                                    <span>
                                        <i class="fas fa-comment"></i> {{ $post->comments->count() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center">Chưa có bài viết nào.</p>
                    @endforelse

                    {{ $posts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 