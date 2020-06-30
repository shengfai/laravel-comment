<?php

namespace Shengfai\LaravelComment\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelLike\Traits\Likeable;
use Shengfai\LaravelComment\Events\Commented;
use Shengfai\LaravelComment\Events\CommentApproved;
use Shengfai\LaravelComment\Events\CommentComplained;

/**
 * 评论模型
 * Class Comment
 *
 * @package \Shengfai\LaravelComment\Models
 * @author ShengFai <shengfai@qq.com>
 * @version 2020年3月19日
 */
class Comment extends Model
{
    use SoftDeletes, Likeable;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'commentable_id',
        'commentable_type',
        'user_id',
        'parent_id',
        'message',
        'complaints_count',
        'approved'
    ];
    
    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => Commented::class
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::updated(function ($comment) {
            
            if ($comment->wasChanged('complaints_count')) {
                event(new CommentComplained($comment));
            }
            
            if ($comment->wasChanged('approved')) {
                event(new CommentApproved($comment));
            }
        });
    }

    /**
     * 可评论对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function commentable()
    {
        return $this->morphTo('commentable');
    }

    /**
     * 作者
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 父评论
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function parent()
    {
        return $this->hasOne(Comment::class, 'id', 'parent_id');
    }

    /**
     * 子评论
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Comment::class, 'parent_id', 'id');
    }
}