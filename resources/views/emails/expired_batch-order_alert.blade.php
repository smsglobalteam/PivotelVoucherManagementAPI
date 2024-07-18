@component('mail::message')
# Expiring Batch Orders

<p>Please check the vouchers under the following batch orders:</p>

@foreach ($batchOrders as $batchOrder)
- **Batch ID**: {{ $batchOrder->batch_id }}
- **Batch Count**: {{ $batchOrder->batch_count }}
- **Expiry Date**: {{ $batchOrder->expiry_date }}
<br><br>

---
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
