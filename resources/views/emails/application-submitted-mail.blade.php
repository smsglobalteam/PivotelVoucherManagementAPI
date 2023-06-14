@component("mail::message")

# Greetings {{$name}},

Your company has been registered in our database with the email '{{$email}}'. Thank you for trusting our business and we hope for a prosperous relation.

Regards, <br>
SEBIONE

@component('mail::subcopy')
This is a message from SEBIONE
@endcomponent

@endcomponent