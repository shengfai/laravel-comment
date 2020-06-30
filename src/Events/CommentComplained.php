<?php

namespace Shengfai\LaravelComment\Events;

use Illuminate\Database\Eloquent\Model;

/**
 * 评论举报事件
 * Class CommentComplained
 *
 * @author ShengFai <shengfai@qq.com>
 * @version 2020年6月30日
 */
class CommentComplained
{
    /**
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $comment;

    /**
     * Event constructor.
     *
     * @param \Illuminate\Database\Eloquent\Model $comment
     */
    public function __construct(Model $comment)
    {
        $this->comment = $comment;
    }
}
