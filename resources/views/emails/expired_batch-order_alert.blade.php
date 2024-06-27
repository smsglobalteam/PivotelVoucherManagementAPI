@component('mail::message')
# Expired Batch Orders

<p>Please check the vouchers under the following batch orders:</p>

@foreach ($products as $product)
## Expired Batch Orders for Product: {{ $product->product_name }}

@if ($product->batch_order && count($product->batch_order) > 0)
@foreach ($product->batch_order as $batch)
- **Batch ID**: {{ $batch->batch_id }}
- **Batch Count**: {{ $batch->batch_count }}
- **Expiry Date**: {{ $batch->expiry_date }}
<br>
<br>
@endforeach
@endif

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