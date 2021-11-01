<?php

namespace App\Http\Controllers\Movies;

use App\Jobs\UploadImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\IMovie;

class UploadController extends Controller
{
    protected $movies;

    public function __construct(IMovie $movies)
    {
        $this->movies = $movies;
    }

    public function upload(Request $request)
    {
        // validate the request
        $this->validate($request, [
            'image' => ['required', 'mimes:jpeg,gif,bmp,png', 'max:2048']
        ]); 

        // get the image
        $image = $request->file('image');
        $image_path = $image->getPathName();


        // get the original file name and replace any spaces with _
        // Business Cards.png = timestamp()_business_cards.png
        $filename = time()."_". preg_replace('/\s+/', '_', strtolower($image->getClientOriginalName()));
        
        // move the image to the temporary location (tmp)
        $tmp = $image->storeAs('uploads/original', $filename, 'tmp');

        // create the database record for the movie
        // $movie = auth()->user()->movies()->create([
        //     'image' => $filename,
        //     'disk' => config('site.upload_disk')
        // ]);

        $movie = $this->movies->create([
            'user_id' => auth()->id(),
            'image' => $filename,
            'disk' => config('site.upload_disk')
        ]);

        // dispatch a job to handle the image manipulation
        $this->dispatch(new UploadImage($movie));
        
        return response()->json($movie, 200);

    }
}
