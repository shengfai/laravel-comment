<?php

namespace Shengfai\LaravelComment\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Shengfai\LaravelComment\Models\Comment;
use Shengfai\LaravelComment\Resources\CommentResource;

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
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // 获取可评论对象
        $target = $this->getTargetOfCommentable($request);
        
        // 获取评论
        $per_page = min($request->get('per_page', 20), 100);
        
        $comments = $target->comments()->with([
            'author',
            'parent'
        ])->where('approved', true)->paginate($per_page);
        
        return CommentResource::collection($comments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // 获取可评论对象
        $target = $this->getTargetOfCommentable($request);
        
        // 创建评论
        $comment = $target->comments()->make($request->all());
        
        // 关联评论用户
        $comment->author()->associate($request->user());
        
        // 保存记录
        $comment->save();
        
        // 触发评论后事件
        if (method_exists($target, 'commented')) {
            $target->commented($request->user(), $comment);
        }
        
        return \response(null, 201);
    
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
        
        return \response()->noContent();
    }

    /**
     * 举报投诉
     *
     * @param Request $request
     * @param Comment $comment
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function complain(Request $request, Comment $comment)
    {
        $comment->increment('complaints_count');
        
        return \response(null, 201);
    }

    /**
     * 获取可评论对象
     *
     * @param Request $request
     * @return Illuminate\Database\Eloquent\Model
     * @throws Illuminate\Database\Eloquent\ModelNotFoundException
     */
    protected function getTargetOfCommentable(Request $request)
    {
        try {
            
            // 获取可评论对象
            $target = with($request, function ($model) {
                $class = '\App\Models\\' . ucfirst($model->target);
                return $class::findOrFail($model->id);
            });
            
            return $target;
        
        } catch (ModelNotFoundException $exception) {
            return \response([
                'error_code' => $exception->getCode(),
                'message' => $exception->getMessage()
            ], 404);
        }
    }
}