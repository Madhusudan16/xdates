@extends('front')
@section('main')
<meta name="_token" content="{!! csrf_token() !!}"/>
    <div class="row">
        @include('front.partials.setting-menu')
        <div class="col-lg-9 col-sm-12 col-md-9 account-right">
        <div class="right-profile">
            <div class="success-card-mas @if(!isset($success_message)) hide @endif">{{$success_message or 'card saved successfully.'}}</div>
            <div class="error-card-mas @if(!isset($error_message)) hide @endif">{{$error_message or 'card did not updated.'}}</div>
            <div class="row">

                <div class="padding-cont clearfix">
                    <div class="col-md-8 col-sm-8 padding-right-none form-sec">
                        <h3 class="heding">Billing info</h3>
                            <div class="{{(!empty($invalidCard) AND isset($invalidCard))?'invalid-card':''}}">
                                {{$invalidCard or ''}}
                            </div>
                            <div class="fild-sec">
                                <form class="form-inline" id="billingInfo" action={{url('/planbill/card')}} method="post">
                                    <div class="form-group f-name">
                                        {{ csrf_field() }} 
                                        <span class="border-span">
                                            <input type="text" name="billing_first_name" class="form-control" id="cardFirstName" placeholder="First Name" value="{{$cardData->billing_first_name or ''}}">
                                        </span>
                                    </div>
                                    <div class="form-group l-name">
                                        <span class="border-span">
                                            <input type="text" name="billing_last_name" class="form-control" id="cardLastName" placeholder="Last Name" value="{{$cardData->billing_last_name or ''}}">
                                        </span>
                                    </div>
                                    <div class="form-group card_number" >
                                        <span class="border-span">
                                            <input type="text" name="card_no" class="form-control" id="cardNumber" placeholder="Credit card number" value="{{$cardData->card_no or ''}}" autocomplete="off">
                                        </span>
                                    </div>
                                    <div class="form-group card-cvc">
                                        <span class="border-span">
                                            <input type="password" class="form-control" name="card_cvv" id="cardCvvNumber" placeholder="CVV" value="{{$cardData->card_cvv or ''}}" autocomplete="off" >
                                        </span>
                                    </div>
                                    <div class="form-group month">
                                        <label>Expires</label>&nbsp;
                                        <span class="border-span">
                                            <select class="selectpicker" id="cardExpireMonth" name="expires_month" >
                                                <option value="">Select Month</option>
                                                @foreach($months as $key=>$month)
                                                    <option value="{{$key}}" {{(isset($cardData->expires_month)&& $cardData->expires_month == $key)?'selected':''}}>{{$key}}-{{$month}}</option>
                                                @endforeach
                                            </select>
                                        </span>
                                    </div>
                                    <div class="form-group year">
                                        <span class="border-span">
                                            <select class="selectpicker" id="cardExpireYear" name="expires_year">
                                                <option value="">Select Year</option>
                                                @for($i=date('Y');$i<=2040;$i++)
                                                    <option value="{{$i}}" {{(isset($cardData->expires_month)&&$cardData->expires_year == $i)?'selected':''}}>{{$i}}</option>
                                                @endfor
                                                
                                            </select>
                                        </span>
                                    </div>
                                    <div class="form-group add_1">
                                        <span class="border-span">
                                            <input type="text" class="form-control" id="billingAddress1" placeholder="Billing Address line 1" name="address_line_1" value="{{$cardData->address_line_1 or ''}}">
                                        </span>
                                    </div>
                                    <div class="form-group add_2">
                                        <span class="border-span">
                                            <input type="text" class="form-control" id="billingAddress1" placeholder="Billing Address line 2" name="address_line_2" value="{{$cardData->address_line_2 or ''}}">
                                        </span>
                                    </div>
                                    <div class="form-group city">
                                        <span class="border-span">
                                            <input type="text" class="form-control" id="addressCity" placeholder="City" name="city" value="{{$cardData->city or ''}}">
                                        </span>
                                    </div>
                                    <div class="form-group state">
                                        <span class="border-span">
                                            @if(isset($cardData->state) )
                                                <select class="selectpicker" id="addressState" name="state" >
                                                    @foreach($states as $state) 
                                                        <option value="{{$state->id}}" {{($state->id == $cardData->state)?'selected':''}}>{{$state->state_name}}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <select class="selectpicker" id="addressState" name="state">
                                                    @foreach($states as $state) 
                                                        @if($state->country_id == 1)  <option value="{{$state->id}}">{{$state->state_name}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="form-group zip-code">
                                        <span class="border-span">
                                            <input type="text" class="form-control" id="addressZipcode" placeholder="Zip Code" name="zip_code" value="{{$cardData->zip_code or ''}}">
                                        </span>
                                    </div>
                                    <div class="form-group state">
                                        <span class="border-span">
                                            <select class="selectpicker" id="addressCountry" name="country">
                                                @foreach($countries as $country)
                                                    <option value="{{$country->id}}" {{(isset($cardData->country) && $country->id == $cardData->country)?'selected':''}}>{{$country->name}}</option>
                                                @endforeach
                                            </select>
                                        </span>
                                    </div>
                                    <div class="text-right button-right">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal" id="updateCard">update</button>
                                    </div>

                                </form>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4 padding-left-none">
                            <div class="cart_info">
                                <h3 class="heding">Ways to Pay</h3>
                                    <img src={{url('assets/images/cart_images.png')}}>
                                    <p>We accept MasterCard, VISA, American Express and Discover.</p>
                            </div>
                            <div class="cart_text">
                                <h3 class="heding">
                                    This page is secure
                                </h3>
                                <p>Your connection to the X-Dates service is always secure. We use HTTPS protocol and a valid, trusted security certificate.</p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
 
@endsection


@section('footerscripts')  
   {!! HTML::script(asset('vendor/jsvalidation/js/jsvalidation.js')) !!}
   {!! $validator or '' !!}
   <script>
            
            
        $(document).ready(function(){
            $('#cardExpireMonth').change(function(){
                $('#cardExpireYear').removeData('previousValue');
                jsFormValidation.element('#cardExpireYear');
            });

            $.ajaxSetup({
                headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
            });

            var card_valid = 0;
            $("#cardNumber").change();
            $("#cardNumber").validateCreditCard(function(result){
                if(result.card_type != null && result.valid) {
                    console.log(result.valid);
                    card_valid = 1;
                     $(".card-error").hide();
                    $("#updateCard").prop('disabled',false);
                }
                else {
                    card_valid = 0;
                    console.log(result.valid);
                    //$("#cardNumber-error").css('color','red');
                   // $("#cardNumber-error").html('Invalid card number!');

                    //$("#updateCard").prop('disabled',true);
                }
            });
            $("#updateCard").click(function(){
                    if($("#billingInfo").valid()) {
                        if(card_valid == 1) {
                            $("#billingInfo").submit();
                        } else {
                            $("#cardNumber-error").show();
                            $("#cardNumber-error").closest('.form-group').addClass('has-error');
                            $("#cardNumber-error").html('Invalid card number!');
                        }
                    }
             });
            $("#billingInfo").submit(function(){
            
            });
            $("#addressCountry").change(function(){
                  var country_id = $(this).val();
                  $.get(site_url+"/planbill/getCountry/"+country_id, function(data,status){
                    $("#addressState").html(data);
                     console.log(data);
                  });
            });
        });
   </script>
@endsection