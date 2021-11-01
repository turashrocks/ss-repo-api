<?php

namespace App\Jobs;

use Image;
use File;
use App\Models\Movie;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UploadImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $movie;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Movie $movie)
    {
        $this->movie = $movie;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $disk = $this->movie->disk;
        $filename = $this->movie->image;
        $original_file = storage_path() . '/uploads/original/'. $filename;

        try{
            // create the Large Image and save to tmp disk
            Image::make($original_file)
                ->fit(800, 600, function($constraint){
                    $constraint->aspectRatio();
                })
                ->save($large = storage_path('uploads/large/'. $filename));

            // Create the thumbnail image
            Image::make($original_file)
                ->fit(250, 200, function($constraint){
                    $constraint->aspectRatio();
                })
                ->save($thumbnail = storage_path('uploads/thumbnail/'. $filename));
            
            // store images to permanent disk
            // original image
            if(Storage::disk($disk)
                ->put('uploads/movies/original/'.$filename, fopen($original_file, 'r+'))){
                    File::delete($original_file);
                }

            // large images
            if(Storage::disk($disk)
                ->put('uploads/movies/large/'.$filename, fopen($large, 'r+'))){
                    File::delete($large);
                }

            // thumbnail images
            if(Storage::disk($disk)
                ->put('uploads/movies/thumbnail/'.$filename, fopen($thumbnail, 'r+'))){
                    File::delete($thumbnail);
                }
            
            // Update the database record with success flag
            $this->movie->update([
                'upload_successful' => true
            ]);

        } catch(\Exception $e){
            \Log::error($e->getMessage());
        }

    }
}
