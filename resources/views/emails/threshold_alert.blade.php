@component('mail::message')
# Threshold Alerts

<p>Please check the vouchers under the following products as they are running low on available vouchers.</p>

@foreach ($products as $product)
## Threshold Alert for Product: {{ $product->product_name }}
- **Available Vouchers Left**: {{ $product->available_voucher_count }}
- **Threshold Alert**: {{ $product->threshold_alert }}
@endforeach

@component('mail::subcopy')
This is an auto generated email. Please do not reply to this email.
@endcomponent

@endcomponent
