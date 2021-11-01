<?php

namespace App\Providers;

use App\Models\Studio;
use App\Models\Movie;
use App\Models\Comment;
use App\Models\Message;
use App\Models\Invitation;
use App\Policies\StudioPolicy;
use App\Policies\MoviePolicy;
use App\Policies\CommentPolicy;
use App\Policies\MessagePolicy;
use App\Policies\InvitationPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        Movie::class => MoviePolicy::class,
        Comment::class => CommentPolicy::class,
        Studio::class => StudioPolicy::class,
        Invitation::class => InvitationPolicy::class,
        Message::class => MessagePolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
