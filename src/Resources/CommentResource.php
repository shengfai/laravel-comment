<?php

namespace Shengfai\LaravelComment\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 评论资源
 * Class CommentResource
 *
 * @package \App\Http\Resources
 * @author ShengFai <shengfai@qq.com>
 */
class CommentResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'author' => [
                'id' => $this->author->id,
                'name' => $this->author->name,
                'avatar' => $this->author->avatar ?? ''
            ],
            'parent' => $this->when($this->relationLoaded('parent'), function () {
                return new CommentResource($this->parent);
            }),
            'message' => $this->message,
            'created_at' => $this->created_at_format
        ];
    }
}
