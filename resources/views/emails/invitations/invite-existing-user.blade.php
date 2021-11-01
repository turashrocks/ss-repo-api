@component('mail::message')
# Hi,

You have been invited to join the studio 
**{{ $invitation->studio->name }}**. 
Because you are already registered to the platform, you just
need to accept or reject the invitation in your
[studio management console]({{ $url }}).

@component('mail::button', ['url' => $url])
Go to Dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
