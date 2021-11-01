<?php
namespace App\Repositories\Eloquent;
use App\Models\Movie;
use Illuminate\Http\Request;
use App\Repositories\Contracts\IMovie;
use App\Repositories\Eloquent\BaseRepository;

class MovieRepository extends BaseRepository implements IMovie
{
    
    public function model()
    {
        return Movie::class; 
    }


    public function applyTags($id, array $data)
    {
        $movie = $this->find($id);
        $movie->retag($data);
    }

    public function addComment($movieId, array $data)
    {
        // get the movie for which we want to create a comment
        $movie = $this->find($movieId);

        // create the comment for the movie
        $comment = $movie->comments()->create($data);

        return $comment;
    }

    public function like($id)
    {
        $movie = $this->model->findOrFail($id);
        if($movie->isLikedByUser(auth()->id())){
            $movie->unlike();
        } else {
            $movie->like();
        }

        return $movie->likes()->count();
    }

    public function isLikedByUser($id)
    {
        $movie = $this->model->findOrFail($id);
        return $movie->isLikedByUser(auth()->id());
    }

    public function search(Request $request)
    {
        $query = (new $this->model)->newQuery();
        $query->where('is_live', true);

        // return only movies with comments
        if($request->has_comments){
            $query->has('comments');
        }

        // return only movies assigned to studios
        if($request->has_studio){
            $query->has('studio');
        }

        // search title and description for provided string
        if($request->q){
            $query->where(function($q) use ($request){
                $q->where('title', 'like', '%'.$request->q.'%')
                    ->orWhere('description', 'like', '%'.$request->q.'%');
            });
        }

        // order the query by likes or latest first
        if($request->orderBy=='likes'){
            $query->withCount('likes') // likes_count
                ->orderByDesc('likes_count');
        } else {
            $query->latest();
        }

        return $query->with('user')->get();
    }
    

}