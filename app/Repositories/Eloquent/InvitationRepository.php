<?php
namespace App\Repositories\Eloquent;
use App\Models\Invitation;
use App\Repositories\Contracts\IInvitation;
use App\Repositories\Eloquent\BaseRepository;

class InvitationRepository extends BaseRepository implements IInvitation
{
    
    public function model()
    {
        return Invitation::class; 
    }

    public function addUserToStudio($studio, $user_id)
    {
        $studio->members()->attach($user_id);
    }

    public function removeUserFromStudio($studio, $user_id)
    {
        $studio->members()->detach($user_id);
    }


}