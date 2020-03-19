<?php

namespace Shengfai\LaravelComment\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Shengfai\LaravelComment\Models\Comment;

/**
 * 评论资源
 * Class CommentsController
 *
 * @package \Shengfai\LaravelComment\Controllers
 * @author ShengFai <shengfai@qq.com>
 * @version 2020年3月19日
 */
class CommentsController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            
            // 获取可评论对象
            $commentable = with($request, function ($model) {
                $class = '\App\Models\\' . ucfirst($model->target);
                return $class::findOrFail($model->id);
            });
            
            // 创建评论
            $comment = $commentable->comments()->make($request->all());
            
            // 关联评论用户
            $comment->author()->associate($request->user());
            
            // 保存记录
            $comment->save();
            
            // 触发评论后事件
            if (method_exists($commentable, 'commented')) {
                $commentable->commented($request->user(), $comment);
            }
            
            return \response(null, 201);
        
        } catch (ModelNotFoundException $exception) {
            return \response([
                'error_code' => $exception->getCode(),
                'message' => $exception->getMessage()
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Comment $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Comment $comment)
    {
        // 非作者本人
        if (!$comment->author->is($request->user())) {
            abort(403, 'Unauthorized.');
        }
        
        $comment->delete();
        
        return response()->noContent();
    }
}