<?php

namespace Shengfai\LaravelComment\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Shengfai\LaravelComment\Models\Comment;

/**
 * 评论 Traits
 * trait HasComments
 *
 * @package \Shengfai\LaravelComment\Traits
 * @author ShengFai <shengfai@qq.com>
 * @version 2020年3月19日
 */
trait HasComments
{

    /**
     * 模型关联的评论
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}