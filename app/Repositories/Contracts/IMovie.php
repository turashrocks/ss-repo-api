<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface IMovie
{
    public function applyTags($id, array $data);
    public function addComment($movieId, array $data);
    public function like($id);
    public function isLikedByUser($id);
    public function search(Request $request);
}