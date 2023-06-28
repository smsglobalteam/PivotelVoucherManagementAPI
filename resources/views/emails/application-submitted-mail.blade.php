@component("mail::message")

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

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
<b style="font-size:20px;color:#87179d;margin-top:25px;">Your information</b>
<p style="color:#87179d">
    If you’re an individual or sole trader you can skip the fields that are not applicable.
</p>

<div style="border:none; height:4px; background-color:#87179d;"></div>

<div class="row">
<div class="col">

<div class="mb-3">
<label class="form-label">Tax ID</label>
<input class="form-control" value="{{$tax_id}}" readonly onclick="selectAllText(this)">
</div>
<div class="mb-3">
<label class="form-label">Date of Birth</label>
<input class="form-control" value="{{$birthdate}}" readonly onclick="selectAllText(this)">
</div>
<div class="mb-3">
<label class="form-label">Trading name</label>
<input class="form-control" value="{{$trading_name}}" readonly onclick="selectAllText(this)">
</div>
<div class="mb-3">
<label class="form-label">Telephone Number (Daytime)</label>
<input class="form-control" value="{{$telephone_number}}" readonly onclick="selectAllText(this)">
</div>
</div>
<div class="col">
<div class="mb-3">
<label class="form-label">Primary Contact (Full Name)</label>
<input class="form-control" value="{{$full_name}}" readonly onclick="selectAllText(this)">
</div>
<div class="mb-3">
<label class="form-label">Company Name</label>
<input class="form-control" value="{{$company_name}}" readonly onclick="selectAllText(this)">
</div>
<div class="mb-3">
<label class="form-label">Industry</label>
<input class="form-control" value="{{$industry}}" readonly onclick="selectAllText(this)">
</div>
<div class="mb-3">
<label class="form-label">Mobile</label>
<input class="form-control" value="{{$mobile}}" readonly onclick="selectAllText(this)">
</div>
</div>
</div>

<b style="font-size:20px;color:#87179d;margin-top:35px;" class="form-header-style">Billing Address</b>
<div style="border:none; height:4px; background-color:#87179d;"></div>

<div class="row">
<div class="col">
<div class="mb-3">
<label class="form-label">Street Address</label>
<input class="form-control" value="{{$primary_street_address}}" readonly onclick="selectAllText(this)">
</div>

<!--  two part column -->
<div class="row">
<div class="col">
<div class="mb-3">
<label class="form-label">City</label>
<input class="form-control" value="{{$primary_city}}" readonly onclick="selectAllText(this)">
</div>
</div>
<div class="col">
<div class="mb-3">
<label class="form-label">State</label>
<input class="form-control" value="{{$primary_state}}" readonly onclick="selectAllText(this)">
</div>
</div>
</div>

  <!--  two part column -->
<div class="row">
<div class="col">
<div class="mb-3">
<label class="form-label">ZIP Code</label>
<input class="form-control" value="{{$primary_zip_code}}" readonly onclick="selectAllText(this)">
</div>
</div>
<div class="col">
<div class="mb-3">
<label class="form-label">Country</label>
<input class="form-control" value="{{$primary_country}}" readonly onclick="selectAllText(this)">
</div>
</div>
</div>
  
<div class="mb-3">
<label class="form-label">Email Address (Primary Contact)</label>
<input class="form-control" value="{{$primary_email}}" readonly onclick="selectAllText(this)">
</div>
</div>
</div>

<b style="font-size:20px;color:#87179d;margin-top:35px;" class="form-header-style">Shipping Address</b>
<div style="border:none; height:4px; background-color:#87179d;"></div>

<div class="row">
<div class="col">
<div class="mb-3">
<label class="form-label">Street Address</label>
<input class="form-control" value="{{$shipping_street_address}}" readonly onclick="selectAllText(this)">
</div>

<!--  two part column -->
<div class="row">
<div class="col">
<div class="mb-3">
<label class="form-label">City</label>
<input class="form-control" value="{{$shipping_city}}" readonly onclick="selectAllText(this)">
</div>
</div>
<div class="col">
<div class="mb-3">
<label class="form-label">State</label>
<input class="form-control" value="{{$shipping_state}}" readonly onclick="selectAllText(this)">
</div>
</div>
</div>

  <!--  two part column -->
<div class="row">
<div class="col">
<div class="mb-3">
<label class="form-label">ZIP Code</label>
<input class="form-control" value="{{$shipping_zip_code}}" readonly onclick="selectAllText(this)">
</div>
</div>
<div class="col">
<div class="mb-3">
<label class="form-label">Country</label>
<input class="form-control" value="{{$shipping_country}}" readonly onclick="selectAllText(this)">
</div>
</div>
</div>
  
<div class="mb-3">
<label class="form-label">Email Address (Primary Contact)</label>
<input class="form-control" value="{{$shipping_email}}" readonly onclick="selectAllText(this)">
</div>
</div>
  
</div>

{{-- EMERGENCY CONTACT --}}
<b style="font-size:20px;color:#87179d;margin-top:35px;" class="form-header-style">Emergency Contact</b>
<div style="border:none; height:4px; background-color:#87179d;"></div>

<div class="row">
<div class="col">

<div class="mb-3">
<label class="form-label">Emergency Contact (Full Name)</label>
<input class="form-control" value="{{$emergency_contact}}" readonly onclick="selectAllText(this)">
</div>

<!--  two part column -->
<div class="row">
<div class="col">
<div class="mb-3">
<label class="form-label">Emergency Telephone</label>
<input class="form-control" value="{{$emergency_telephone}}" readonly onclick="selectAllText(this)">
</div>
</div>
<div class="col">
<div class="mb-3">
<label class="form-label">Emergency Mobile</label>
<input class="form-control" value="{{$emergency_mobile}}" readonly onclick="selectAllText(this)">
</div>
</div>
</div>

<div class="mb-3">
<label class="form-label">Email Address (Emergency Contact)</label>
<input class="form-control" value="{{$emergency_email}}" readonly onclick="selectAllText(this)">
</div>

<div class="mb-3">
<label class="form-label">Relationship</label>
<input class="form-control" value="{{$emergency_relationship}}" readonly onclick="selectAllText(this)">
</div>

</div>
</div>

{{-- YOUR IDENTIFICATION --}}
<b style="font-size:20px;color:#87179d;margin-top:35px;" class="form-header-style">Your Identification</b>
<p style="color:#87179d">
  At least one form of ID is required. Don’t forget to choose an enquiry password so we can identify you when you make account enquiries.
</p>
<div style="border:none; height:4px; background-color:#87179d;"></div>

<div class="row">
<div class="col">
<div class="mb-3">
<label class="form-label">ID Type</label>
<input class="form-control" value="{{$id_type}}" readonly onclick="selectAllText(this)">
</div>

<div class="mb-3">
<label class="form-label">Social Security Number</label>
<input class="form-control" value="{{$social_security_no}}" readonly onclick="selectAllText(this)">
</div>
</div>

<div class="col">
<div class="mb-3">
<label class="form-label">Expiry Date</label>
<input class="form-control" value="{{$id_expiry}}" readonly onclick="selectAllText(this)">
</div>

<div class="mb-3">
<label class="form-label">Inquiry Password</label>
<input class="form-control" value="{{$inquiry_password}}" readonly onclick="selectAllText(this)">
</div>
</div>
</div>

{{-- CREDIT CARD --}}
<b style="font-size:20px;color:#87179d;margin-top:35px;" class="form-header-style">Credit Card</b>
<p style="color:#87179d">
  Your credit card will be charged on the due date shown on your invoice for monthly services, or at the time of voucher recharge.
</p>
<div style="border:none; height:4px; background-color:#87179d;"></div>

<div class="row">
<div class="col">

<div class="mb-3">
<label class="form-label">Card Type</label>
<input class="form-control" value="{{$card_type}}" readonly onclick="selectAllText(this)">
</div>

<div class="mb-3">
<label class="form-label">Card Holder Name</label>
<input class="form-control" value="{{$card_holder_name}}" readonly onclick="selectAllText(this)">
</div>

<div class="mb-3">
<label class="form-label">Card Number</label>
<input class="form-control" value="{{$card_number}}" readonly onclick="selectAllText(this)">
</div>

<!--  two part column -->
<div class="row">
<div class="col">
<div class="mb-3">
<label class="form-label">Card Expiry Date</label>
<input class="form-control" value="{{$card_expiry_date}}" readonly onclick="selectAllText(this)">
</div>
</div>
<div class="col">
<div class="mb-3">
<label class="form-label">Card CVV</label>
<input class="form-control" value="{{$card_ccv}}" readonly onclick="selectAllText(this)">
</div>
</div>
</div>

</div>
</div>

{{-- YOUR IDENTIFICATION --}}
<b style="font-size:20px;color:#87179d;margin-top:35px;" class="form-header-style">Satellite Service & Equipment</b>
<p style="color:#87179d">
  Information about the service and equipment you’re applying for.
</p>
<div style="border:none; height:4px; background-color:#87179d;"></div>

<div class="row">

<div class="col">

<div class="mb-3">
<label class="form-label">Satellite Network</label>
<input class="form-control" value="{{$satellite_network}}" readonly onclick="selectAllText(this)">
</div>

<div class="mb-3">
<label class="form-label">Service Plan</label>
<input class="form-control" value="{{$service_plan}}" readonly onclick="selectAllText(this)">
</div>

<div class="mb-3">
<label class="form-label">Equipment Provider</label>
<input class="form-control" value="{{$equipment_provider}}" readonly onclick="selectAllText(this)">
</div>

<div class="mb-3">
<label class="form-label">IMEI/ESN</label>
<input class="form-control" value="{{$imei_esn}}" readonly onclick="selectAllText(this)">
</div>

<div class="mb-3">
<label class="form-label">Requested Activation Date</label>
<input class="form-control" value="{{$requested_activation_date}}" readonly onclick="selectAllText(this)">
</div>

</div>

<div class="col">

<div class="mb-3">
<label class="form-label">Service Type</label>
<input class="form-control" value="{{$service_type}}" readonly onclick="selectAllText(this)">
</div>

<div class="mb-3">
<label class="form-label">SIM Number</label>
<input class="form-control" value="{{$sim_number}}" readonly onclick="selectAllText(this)">
</div>

<div class="mb-3">
<label class="form-label">Hardware Model</label>
<input class="form-control" value="{{$hardware_model}}" readonly onclick="selectAllText(this)">
</div>

<div class="mb-3">
<label class="form-label">Vessel/Narrative</label>
<input class="form-control" value="{{$vessel_narrative}}" readonly onclick="selectAllText(this)">
</div>

<div class="mb-3">
<label class="form-label">Cost Center</label>
<input class="form-control" value="{{$cost_center}}" readonly onclick="selectAllText(this)">
</div>

</div>

</div>

{{-- END OF FORMS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

@component('mail::subcopy')
This is an auto generated email. Please do not reply to this email.
@endcomponent

@endcomponent