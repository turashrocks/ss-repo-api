<?php

namespace App\Models;

use App\Models\Traits\Likeable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Cviebrock\EloquentTaggable\Taggable;

class Movie extends Model
{
    use Taggable, Likeable;
    
    protected $fillable=[
        'user_id',
        'studio_id',
        'image',
        'title',
        'description',
        'slug',
        'close_to_comment',
        'is_live',
        'upload_successful',
        'disk'
    ];

    protected $casts=[
        'is_live' => 'boolean',
        'upload_successful' => 'boolean',
        'close_to_comments' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }
    
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')
                ->orderBy('created_at', 'asc');
    }

    public function getImagesAttribute()
    {
        
        return [
            'thumbnail' => $this->getImagePath('thumbnail'),
            'large' => $this->getImagePath('large'),
            'original' => $this->getImagePath('original'),
        ];
    }

    protected function getImagePath($size)
    {
        return Storage::disk($this->disk)
                        ->url("uploads/movies/{$size}/".$this->image);
    }

    

}
