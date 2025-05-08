<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    /**
     * Các trường có thể gán hàng loạt
     */
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'is_public'
    ];

    /**
     * Định nghĩa quan hệ với User
     * Mỗi bài viết thuộc về một user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Một số truy vấn phổ biến có thể sử dụng
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Lấy số lượng like của bài viết
     */
    public function getLikesCount()
    {
        return $this->likes()->where('type', 'like')->count();
    }

    /**
     * Lấy số lượng dislike của bài viết
     */
    public function getDislikesCount()
    {
        return $this->likes()->where('type', 'dislike')->count();
    }

    /**
     * Kiểm tra xem bài viết có được like bởi một người dùng cụ thể không
     */
    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->where('type', 'like')->exists();
    }

    /**
     * Kiểm tra xem bài viết có bị dislike bởi một người dùng cụ thể không
     */
    public function isDislikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->where('type', 'dislike')->exists();
    }
} 