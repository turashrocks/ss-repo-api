<?php

namespace App\Http\Controllers\Studios;

use Mail;
use App\Models\Studio;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\IStudio;
use App\Repositories\Contracts\IUser;
use App\Mail\SendInvitationToJoinStudio;
use App\Repositories\Contracts\IInvitation;


class InvitationsController extends Controller
{
    
    protected $invitations;
    protected $studios;
    protected $users;

    public function __construct(IInvitation $invitations, 
        IStudio $studios, IUser $users
    )
    {
        $this->invitations = $invitations;
        $this->studios = $studios;
        $this->users = $users;
    }

    public function invite(Request $request, $studioId)
    {
        // get the studio
        $studio = $this->studios->find($studioId);

        $this->validate($request, [
            'email' => ['required', 'email']
        ]);
        $user = auth()->user();
        // check if the user owns the studio
        if(! $user->isOwnerOfStudio($studio)){
            return response()->json([
                'email' => 'You are not the studio owner'
            ], 401);
        }

        // check if the email has a pending invitation
        if($studio->hasPendingInvite($request->email)){
            return response()->json([
                'email' => 'Email already has a pending invite'
            ], 422);
        }

        // get the recipient by email
        $recipient = $this->users->findByEmail($request->email);

        // if the recipient does not exist, send invitation to join the studio
        if(! $recipient){
            $this->createInvitation(false, $studio, $request->email);
            
            return response()->json([
                'message' => 'Invitation sent to user'
            ], 200);
        }

        // check if the studio already has the user
        if($studio->hasUser($recipient)){
            return response()->json([
                'email' => 'This user seems to be a studio member already'
            ], 422); 
        }

        // send the invitation to the user
        $this->createInvitation(true, $studio, $request->email);
        return response()->json([
            'message' => 'Invitation sent to user'
        ], 200);
    }

    public function resend($id)
    {
        $invitation = $this->invitations->find($id);

        $this->authorize('resend', $invitation);
        
        $recipient = $this->users
                        ->findByEmail($invitation->recipient_email);
        
        Mail::to($invitation->recipient_email)
            ->send(new SendInvitationToJoinStudio($invitation, !is_null($recipient)));

        return response()->json(['message' => 'Invitation resent'], 200);
    }

    public function respond(Request $request, $id)
    {
        $this->validate($request, [
            'token' => ['required'],
            'decision' => ['required']
        ]);
        
        $token = $request->token;
        $decision = $request->decision; // 'accept' or 'deny'
        $invitation = $this->invitations->find($id);

        // check if the invitation belongs to this user
        $this->authorize('respond', $invitation);
        

        // check to make sure that the tokens match
        if($invitation->token !== $token){
            return response()->json([
                'message' => 'Invalid Token'
            ], 401);
        }

        // check if accepted
        if($decision !== 'deny'){
            $this->invitations->addUserToStudio($invitation->studio, auth()->id());
        }

        $invitation->delete();

        return response()->json(['message' => 'Successful'], 200);

    }

    public function destroy($id)
    {
        $invitation = $this->invitations->find($id);
        $this->authorize('delete', $invitation);

        $invitation->delete();

        return response()->json(['message' => 'Deleted'], 200);
    }

    protected function createInvitation(bool $user_exists, Studio $studio, string $email)
    {

        $invitation = $this->invitations->create([
            'studio_id' => $studio->id,
            'sender_id' => auth()->id(),
            'recipient_email' => $email,
            'token' => md5(uniqid(microtime()))
        ]);

        Mail::to($email)
            ->send(new SendInvitationToJoinStudio($invitation, $user_exists));
    
    }




}
