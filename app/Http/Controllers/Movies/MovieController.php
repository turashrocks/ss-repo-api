<?php

namespace App\Http\Controllers\Movies;

use App\Models\Movie;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\MovieResource;
use App\Repositories\Contracts\IMovie;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Eloquent\Criteria\{
    IsLive,
    LatestFirst,
    ForUser,
    EagerLoad
};

class MovieController extends Controller
{
    protected $movies;
    
    public function __construct(IMovie $movies)
    {
        $this->movies = $movies;
    }

    public function index()
    {
        $movies = $this->movies->withCriteria([
            new LatestFirst(),
            new IsLive(),
            new ForUser(2),
            new EagerLoad(['user', 'comments'])
        ])->all();
        return MovieResource::collection($movies);
    }

    public function findMovie($id)
    {
        $movie = $this->movies->find($id);
        return new MovieResource($movie);
    }

    public function update(Request $request, $id)
    {

        $movie = $this->movies->find($id);

        $this->authorize('update', $movie);
        $this->validate($request, [
            'title' => ['required', 'unique:movies,title,'. $id],
            'description' => ['required', 'string', 'min:20', 'max:140'],
            'tags' => ['required'],
            'studio' => ['required_if:assign_to_studio,true']
        ]);
        

        $movie = $this->movies->update($id, [
            'studio_id' => $request->studio,
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title), 
            'is_live' => ! $movie->upload_successful ? false : $request->is_live
        ]);

        // apply the tags
        $this->movies->applyTags($id, $request->tags);
        
        return new MovieResource($movie);
    }

    public function destroy($id)
    {
        $movie = $this->movies->find($id);
        $this->authorize('delete', $movie);
        // delete the files associated to the record
        foreach(['thumbnail', 'large', 'original'] as $size){
            // check if the file exists in the database
            if(Storage::disk($movie->disk)->exists("uploads/movies/{$size}/".$movie->image)){
                Storage::disk($movie->disk)->delete("uploads/movies/{$size}/".$movie->image);
            }
        }
        $this->movies->delete($id);
        return response()->json(['message' => 'Record deleted'], 200);

    }

    public function like($id)
    {
        $total = $this->movies->like($id);
        return response()->json([
            'message' => 'Successful',
            'total' => $total
        ], 200);
    }

    public function checkIfUserHasLiked($movieId)
    {
        $isLiked = $this->movies->isLikedByUser($movieId);
        return response()->json(['liked' => $isLiked], 200);
    }

    public function search(Request $request)
    {
        $movies = $this->movies->search($request);
        return MovieResource::collection($movies);
    }

    public function findBySlug($slug)
    {
        $movie = $this->movies->withCriteria([
                new IsLive(), 
                new EagerLoad(['user', 'comments'])
            ])->findWhereFirst('slug', $slug);
        return new MovieResource($movie);
    }

    public function getForStudio($studioId)
    {
        $movies = $this->movies
                        ->withCriteria([new IsLive()])
                        ->findWhere('studio_id', $studioId);
        return MovieResource::collection($movies);
    }

    public function getForUser($userId)
    {
        $movies = $this->movies
                        //->withCriteria([new IsLive()])
                        ->findWhere('user_id', $userId);
        return MovieResource::collection($movies);
    }

    public function userOwnsMovie($id)
    {
        $movie = $this->movies->withCriteria(
            [ new ForUser(auth()->id())]
        )->findWhereFirst('id', $id);

        return new MovieResource($movie);
    }

    
}
