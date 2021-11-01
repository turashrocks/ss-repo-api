<?php
namespace App\Repositories\Eloquent;
use App\Models\Studio;
use App\Repositories\Contracts\IStudio;
use App\Repositories\Eloquent\BaseRepository;

class StudioRepository extends BaseRepository implements IStudio
{
    
    public function model()
    {
        return Studio::class; 
    }

    public function fetchUserStudios()
    {
        return auth()->user()->studios;
    }

}