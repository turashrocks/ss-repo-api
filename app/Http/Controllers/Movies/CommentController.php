<?php

namespace App\Http\Controllers\Movies;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Repositories\Contracts\IMovie;
use App\Repositories\Contracts\IComment;

class CommentController extends Controller
{
    
    protected $comments;
    protected $movies;

    public function __construct(IComment $comments, IMovie $movies)
    {
        $this->comments = $comments;
        $this->movies = $movies;
    }

    public function store(Request $request, $movieId)
    {
        $this->validate($request, [
            'body' => ['required']
        ]);
        
        $comment = $this->movies->addComment($movieId, [
            'body' => $request->body,
            'user_id' => auth()->id()
        ]);

        return new CommentResource($comment);
    }

    public function update(Request $request, $id)
    {
        $comment = $this->comments->find($id);
        $this->authorize('update', $comment);

        $this->validate($request, [
            'body' => ['required']
        ]);
        $comment = $this->comments->update($id, [
            'body' => $request->body
        ]);
        return new CommentResource($comment);
    }

    public function destroy($id)
    {
        $comment = $this->comments->find($id);
        $this->authorize('delete', $comment);
        $this->comments->delete($id);
        return response()->json(['message' => 'Item deleted'], 200);
    }

    
}
