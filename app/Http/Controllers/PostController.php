<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class PostController extends Controller
{
    protected $user;
 
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index()
    {
        $this->authorize('read_post');

        $posts = $this->user->posts()->get(['id', 'title', 'body'])->toArray();

        return response()->json([
            'posts' => $posts
        ], 200);
    }

    public function store(Request $request)
    {
        $this->authorize('create_post');

        $data = $request->only('title', 'body',);

        $validator = Validator::make($data, [
            'title' => 'required|string|min:2|max:255',
            'body' => 'required|string|min:2|max:255',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $validated = $validator->validate();

        if($validated == true){
            $post = Post::create([
                'user_id' => Auth::user()->id,
                'title' => $validated['title'],
                'body' => $validated['body'],
            ]);

            return response()->json([
                'message' => 'Post create successfully',
                'user' => new PostResource($post)
            ], 201);
        }

        return response()->json([
            'message' => 'Post create failed',
        ], 400);
    }
    
    public function show(Post $post, $id)
    {
        $this->authorize('view_post');

        $post = $this->user->posts()->find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
            ], 400);
        }

        return response()->json([
            'post' => $post
        ], 200);
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('edit_post', $post);

        $data = $request->only('id','title', 'body',);

        $validator = Validator::make($data, [
            'id' => 'required|integer',
            'title' => 'required|string|min:2|max:255',
            'body' => 'required|string|min:2|max:255',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $validated = $validator->validate();

        if($validated == true){
            $post = Post::find($validated['id']);

            if($post == null){
                return response()->json([
                    'message' => 'Post not found',
                ], 400);
            }

            if($post->user_id != Auth::user()->id){
                return response()->json([
                    'message' => 'You can only edit your own posts',
                ], 400);
            }

            $post->title = $validated['title'];
            $post->body = $validated['body'];
            $post->save();

            return response()->json([
                'message' => 'Post updated successfully',
                'user' => new PostResource($post)
            ], 201);
        }

        return response()->json([
            'message' => 'Post create failed',
        ], 400);
    }

    public function destroy(Post $post, $id)
    {
        $this->authorize('delete_post', $post);
        
        $post = $this->user->posts()->find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
            ], 400);
        }

        if($post->delete()) {
            return response()->json([
                'message' => 'Post deleted successfully',
            ], 200);
        }

        return response()->json([
            'message' => 'Post could not be deleted',
        ], 500);
    }
}
