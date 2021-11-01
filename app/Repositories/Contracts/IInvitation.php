<?php

namespace App\Repositories\Contracts;

interface IInvitation 
{

    public function addUserToStudio($studio, $user_id);
    public function removeUserFromStudio($studio, $user_id);
    
}