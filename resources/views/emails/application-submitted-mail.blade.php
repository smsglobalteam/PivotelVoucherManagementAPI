@component("mail::message")

# Greetings {{$name}},

Your application has been sent successfully with the email address '{{$email}}'. Thank you for trusting our business and we hope for a prosperous relationship with you. We will contact you as soon as possible.

Thank you and best regards, <br>
<b>Pivotel Team</b>

@component('mail::subcopy')
This is an auto generated email. Please do not reply to this email.
@endcomponent

@endcomponent