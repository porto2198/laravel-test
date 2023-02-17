<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends ApiController
{
    public function index()
    {
        $posts = Post::query()->oldest('title')->get();

        if ($posts->isEmpty()) {
            return response()->json([
                'error' => 'No se encontraron posts.',
                'code' => 400,
            ], 400);
        }

        return PostResource::collection($posts)->additional([
            'message' => 'Lista de posts',
            'status' => 200,
        ]);
    }

    public function store(StorePostRequest $request)
    {
        $post = Post::query()->create($request->validated());

        return response()->json([
            'data' => PostResource::make($post),
            'message' => 'Post creado satisfactoriamente.',
            'status' => 201,
        ], 201);
    }

    public function show(Post $post)
    {
        return PostResource::make($post)->additional([
            'message' => "Detalle del post con id: {$post->id}.",
            'status' => 200,
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $post->update($request->validated());

        return PostResource::make($post)->additional([
            'message' => "Post con id: {$post->id} actualizado satisfactoriamente.",
            'status' => 200,
        ]);
    }

    public function destroy(Post $post)
    {
        if (!isset($post)) {
            return response()->json([
                'error' => 'Post no encontrado',
                'status' => 400,
            ], 400);
        }

        $post->delete();

        return PostResource::make($post)->additional([
            'message' => "Post con id: {$post->id} eliminado satisfactoriamente.",
            'status' => 200,
        ]);
    }
}
