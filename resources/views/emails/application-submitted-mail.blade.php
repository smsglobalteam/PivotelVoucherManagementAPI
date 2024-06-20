@component("mail::message")

<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous"> -->

<style>
/* Row container */
.row {
  display: flex;
  flex-wrap: wrap;
}

/* Column */
.col {
  flex: 1;
  padding: 10px; /* Adjust as needed */
}

/* Example media query for responsiveness */
@media screen and (max-width: 768px) {
  .col {
    flex-basis: 100%; /* Set column to full width on small screens */
  }
}

/* Label Styles */
.form-label {
  display: inline-block;
  margin-bottom: 0.5rem;
  margin-top:10px;
  color:black;
}

/* Input Styles */
.form-control {
  display: block;
  width: 100%;
  padding: 0.375rem 0.75rem;
  font-size: 1rem;
  line-height: 1.5;
  color: #495057;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid #ced4da;
  border-radius: 0.25rem;
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control:focus {
  color: #495057;
  background-color: #fff;
  border-color: #80bdff;
  outline: 0;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.form-control::placeholder {
  color: #6c757d;
  opacity: 1;
}

.form-control:disabled,
.form-control[readonly] {
  background-color: #e9ecef;
  opacity: 1;
}

.form-header-style{
  margin-top:30px;
}


</style>
<div style="width:100%;">
<center><b style="font-size:25px;color:black;margin-bottom:15px;text-align:center">Application Form<b></center>
</div>
<br>
<br>
<b style="font-size:20px;color:#1780E7;margin-top:25px;">Your information</b>
<p style="color:#1780E7">
    If you’re an individual or sole trader you can skip the fields that are not applicable.
</p>

<div style="border:none; height:4px; background-color:#1780E7;"></div>


<table style="width:100%;">
<tr>
<td style="padding:10px;width:50%;">
<div class="mb-3">
<label class="form-label">Account Number: </label>
<input class="form-control" value="{{$value->account_number}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">First Name: </label>
<input class="form-control" value="{{$value->first_name}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Birthdate: </label>
<input class="form-control" value="{{$value->birthdate}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Trading Name: </label>
<input class="form-control" value="{{$value->trading_name}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Telephone Number: </label>
<input class="form-control" value="{{$value->telephone_number}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Sign Up For Marketing: </label>
<input class="form-control" value="{{$value->sign_up_marketing}}" readonly onclick="selectAllText(this)">
</div>
<hr>
</td>
<td style="padding:10px;width:50%;">
<div class="mb-3">
<label class="form-label">Title: </label>
<input class="form-control" value="{{$value->title}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Last Name: </label>
<input class="form-control" value="{{$value->last_name}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Company Name: </label>
<input class="form-control" value="{{$value->company_name}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Email: </label>
<input class="form-control" value="{{$value->email}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Mobile: </label>
<input class="form-control" value="{{$value->mobile}}" readonly onclick="selectAllText(this)">
</div>
<hr>
</td>
</tr>
</table>

<br>
<b style="font-size:20px;color:#1780E7;margin-top:35px;" class="form-header-style">Billing Information</b>
<div style="border:none; height:4px; background-color:#1780E7;"></div>

<table style="width:100%;">
<tr>
<td style="padding:10px;width:50%;">
<div class="mb-3">
<label class="form-label">Email: </label>
<input class="form-control" value="{{$value->billing_email}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">City: </label>
<input class="form-control" value="{{$value->billing_city}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Country: </label>
<input class="form-control" value="{{$value->billing_country}}" readonly onclick="selectAllText(this)">
</div>
<hr>
</td>
<td style="padding:10px;width:50%;">
<div class="mb-3">
<label class="form-label">Street Address: </label>
<input class="form-control" value="{{$value->billing_street_address}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">ZIP Code: </label>
<input class="form-control" value="{{$value->billing_zip_code}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">State: </label>
<input class="form-control" value="{{$value->billing_state}}" readonly onclick="selectAllText(this)">
</div>
<hr>
</td>
</tr>
</table>

<br>
<b style="font-size:20px;color:#1780E7;margin-top:35px;" class="form-header-style">Shipping Address</b>
<div style="border:none; height:4px; background-color:#1780E7;"></div>

<table style="width:100%;">
<tr>
<td style="padding:10px;width:50%;">
<div class="mb-3">
<label class="form-label">Street Address: </label>
<input class="form-control" value="{{$value->shipping_street_address}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">ZIP Code: </label>
<input class="form-control" value="{{$value->shipping_zip_code}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">State: </label>
<input class="form-control" value="{{$value->shipping_state}}" readonly onclick="selectAllText(this)">
</div>
<hr>
</td>
<td style="padding:10px;width:50%;">
<div class="mb-3">
<label class="form-label">City: </label>
<input class="form-control" value="{{$value->shipping_city}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Country: </label>
<input class="form-control" value="{{$value->shipping_country}}" readonly onclick="selectAllText(this)">
</div>
<hr>
</td>
</tr>
</table>

{{-- CREDIT CARD --}}
<br>
<b style="font-size:20px;color:#1780E7;margin-top:35px;" class="form-header-style">Credit Card</b>
<p style="color:#1780E7">
  Your credit card will be charged on the due date shown on your invoice for monthly services, or at the time of voucher recharge.
</p>
<div style="border:none; height:4px; background-color:#1780E7;"></div>

<table style="width:100%;">
<tr>
<td style="padding:10px;width:50%;">
<div class="mb-3">
<label class="form-label">Card Type: </label>
<input class="form-control" value="{{$value->card_type}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Card Holder Name: </label>
<input class="form-control" value="{{$value->card_holder_name}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Card Number: </label>
<input class="form-control" value="{{ substr($value->card_number, 0, 4) . str_repeat('*', strlen($value->card_number) - 8) . substr($value->card_number, -4) }}" readonly onclick="selectAllText(this)">
</div>
<hr>
</td>
<td style="padding:10px;width:50%;">
<div class="mb-3">
<label class="form-label">Card Expiry Date: </label>
<input class="form-control" value="{{$value->card_expiry_date}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Card CVV: </label>
<input class="form-control" value="{{ str_repeat('*', strlen($value->card_ccv) - 1) . substr($value->card_ccv, -1) }}" readonly onclick="selectAllText(this)">
</div>
<hr>
</td>
</tr>
</table>

{{-- YOUR IDENTIFICATION --}}
<br>
<b style="font-size:20px;color:#1780E7;margin-top:35px;" class="form-header-style">Plan Type</b>
<div style="border:none; height:4px; background-color:#1780E7;"></div>

<table style="width:100%;">
<tr>
<td style="padding:10px;width:50%;">
<div class="mb-3">
<label class="form-label">Satellite Network: </label>
<input class="form-control" value="{{$value->satellite_network}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Plan Family: </label>
<input class="form-control" value="{{$value->plan_family}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">IMEI/ESN: </label>
<input class="form-control" value="{{$value->imei_esn}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Requested Activation Date: </label>
<input class="form-control" value="{{$value->requested_activation_date}}" readonly onclick="selectAllText(this)">
</div>
<hr>
</td>
<td style="padding:10px;width:50%;">
<div class="mb-3">
<label class="form-label">Hardware Type: </label>
<input class="form-control" value="{{$value->hardware_type}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">SIM Number: </label>
<input class="form-control" value="{{$value->sim_number}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Vessel/Narrative: </label>
<input class="form-control" value="{{$value->vessel_narrative}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">For Maritime: </label>
<input class="form-control" value="{{ $value->is_for_maritime ? 'Yes' : 'No' }}" readonly onclick="selectAllText(this)">
</div>
<hr>
</td>
</tr>
</table>

{{-- YOUR IDENTIFICATION --}}
<br>
<b style="font-size:20px;color:#1780E7;margin-top:35px;" class="form-header-style">Vessel Information</b>
<div style="border:none; height:4px; background-color:#1780E7;"></div>

<table style="width:100%;">
<tr>
  <td style="padding:10px;width:50%;">
<div class="mb-3">
<label class="form-label">Vessel Name: </label>
<input class="form-control" value="{{$value->vessel_name}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Country of Registry: </label>
<input class="form-control" value="{{$value->country_of_registry}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Home Port: </label>
<input class="form-control" value="{{$value->home_port}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Vessel Type: </label>
<input class="form-control" value="{{$value->vessel_type}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Self Propelled Flag: </label>
<input class="form-control" value="{{$value->self_propelled_flag}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Tonnage of Vessel: </label>
<input class="form-control" value="{{$value->tonnage_of_vessel}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">IMO Number: </label>
<input class="form-control" value="{{$value->imo_number}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">AAIC: </label>
<input class="form-control" value="{{$value->aaic}}" readonly onclick="selectAllText(this)">
</div>
<hr>
</td>
<td style="padding:10px;width:50%;">
<div class="mb-3">
<label class="form-label">Fleet ID: </label>
<input class="form-control" value="{{$value->fleet_id}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Number of Persons Onboard: </label>
<input class="form-control" value="{{$value->number_of_persons_onboard}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Port of Registry: </label>
<input class="form-control" value="{{$value->port_of_registry}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Sea Going Flag: </label>
<input class="form-control" value="{{$value->sea_going_flag}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Over 100 GT Flag: </label>
<input class="form-control" value="{{$value->over_100_gt_flag}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Year of Build: </label>
<input class="form-control" value="{{$value->year_of_build}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Call Sign: </label>
<input class="form-control" value="{{$value->call_sign}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">MMSI: </label>
<input class="form-control" value="{{$value->mmsi}}" readonly onclick="selectAllText(this)">
</div>
<hr>
</td>
</tr>
</table>

{{-- EMERGENCY CONTACT --}}
<br>
<b style="font-size:20px;color:#1780E7;margin-top:35px;" class="form-header-style">Vessel Emergency Information</b>
<div style="border:none; height:4px; background-color:#1780E7;"></div>

<table style="width:100%;">
<tr>
<td style="padding:10px;width:50%;">
<div class="mb-3">
<label class="form-label">Contact Name: </label>
<input class="form-control" value="{{$value->vessel_emergency_contact_name}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Street Address: </label>
<input class="form-control" value="{{$value->vessel_emergency_street_address}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">ZIP Code: </label>
<input class="form-control" value="{{$value->vessel_emergency_zip_code}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Emergency State: </label>
<input class="form-control" value="{{$value->vessel_emergency_state}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Contact Email: </label>
<input class="form-control" value="{{$value->vessel_emergency_contact_email}}" readonly onclick="selectAllText(this)">
</div>
<hr>

</td>
<td style="padding:10px;width:50%;">
<div class="mb-3">
<label class="form-label">Contact Address: </label>
<input class="form-control" value="{{$value->vessel_emergency_contact_address}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Emergency City: </label>
<input class="form-control" value="{{$value->vessel_emergency_city}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Emergency Country: </label>
<input class="form-control" value="{{$value->vessel_emergency_country}}" readonly onclick="selectAllText(this)">
</div>
<hr>
<div class="mb-3">
<label class="form-label">Contact Mobile: </label>
<input class="form-control" value="{{$value->vessel_emergency_contact_mobile}}" readonly onclick="selectAllText(this)">
</div>
<hr>
</td>
</tr>
</table>

<br>
{{-- END OF FORMS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

@component('mail::subcopy')
This is an auto generated email. Please do not reply to this email.
@endcomponent

@endcomponent