<?php

namespace App\Http\Controllers\Studios;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudioResource;
use App\Repositories\Contracts\IStudio;
use App\Repositories\Contracts\IUser;
use App\Repositories\Contracts\IInvitation;

class StudiosController extends Controller
{
    
    protected $studios;
    protected $users;
    protected $invitations;

    public function __construct(IStudio $studios, 
        IUser $users, IInvitation $invitations)
    {
        $this->studios = $studios;
        $this->users = $users;
        $this->invitations = $invitations;
    }

    /**
     * Get list of all studios (eg for Search)
     */
    public function index(Request $request)
    {
        
    }

    /**
     * Save studio to database
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:80', 'unique:studios,name']
        ]);
        
        // create studio in database
        $studio = $this->studios->create([
            'owner_id' => auth()->id(),
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);
        
        // current user is inserted as
        // studio member using boot method in Studio model

        return new StudioResource($studio);


    }

    /**
     * Update studio information
     */
    public function update(Request $request, $id)
    {
        $studio = $this->studios->find($id);
        $this->authorize('update', $studio);

        $this->validate($request, [
            'name' => ['required', 'string', 'max:80', 'unique:studios,name,'.$id]
        ]);

        $studio = $this->studios->update($id, [
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return new StudioResource($studio);
    }

    /**
     * Find a studio by its ID
     */
    public function findById($id)
    {
        $studio = $this->studios->find($id);
        return new StudioResource($studio);
    }

    /**
     * Get the studios that the current user belongs to
     */
    public function fetchUserStudios()
    {
        $studios = $this->studios->fetchUserStudios();
        return StudioResource::collection($studios);
    }

    /**
     * Get studio by slug for Public view
     */
    public function findBySlug($slug)
    {
        $studio = $this->studios->findWhereFirst('slug', $slug);
        return new StudioResource($studio);
    }

    /**
     * Destroy (delete) a studio
     */
    public function destroy($id)
    {
        $studio = $this->studios->find($id);
        $this->authorize('delete', $studio);

        $studio->delete();

        return response()->json(['message' => 'Deleted'], 200);
    }

    public function removeFromStudio($studioId, $userId)
    {
        // get the studio
        $studio = $this->studios->find($studioId);
        $user = $this->users->find($userId);

        // check that the user is not the owner
        if($user->isOwnerOfStudio($studio)){
            return response()->json([
                'message' => 'You are the studio owner'
            ], 401);
        }

        // check that the person sending the request
        // is either the owner of the studio or the person
        // who wants to leave the studio
        if(!auth()->user()->isOwnerOfStudio($studio) && 
            auth()->id() !== $user->id
        ){
            return response()->json([
                'message' => 'You cannot do this'
            ], 401);
        }

        $this->invitations->removeUserFromStudio($studio, $userId);

        return response()->json(['message' => 'Success'], 200);


    }
}
