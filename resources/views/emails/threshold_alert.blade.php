@component('mail::message')
# Threshold Alerts

<p>Please check the voucher level under the following products as they currently running low:</p>

@foreach ($products as $product)
## Threshold Alert for Product: {{ $product->product_name }}
## Order amount: {{ $product->order_amount }}
- **Threshold Level**: {{ $product->threshold_alert }}
- **Available Vouchers Left**: {{ $product->available_voucher_count }}
@endforeach

<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td align="center">
            <a href="{{ env('ALERT_EMAIL_URL') }}" target="_blank">
                <button type="button" name="linkBtn" style="color: #ffffff; background-color: #1a1b41; font-size: 16px; border: 0px solid #2d63c8; border-radius: 7px; padding: 10px 10px; cursor: pointer;">Check Vouchers</button>
            </a>
        </td>
    </tr>
</table>

@component('mail::subcopy')
This is an auto generated email. Please do not reply to this email.
@endcomponent

@endcomponent