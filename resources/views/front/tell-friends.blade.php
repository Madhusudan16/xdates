@extends('front')

@section('main')

<meta name="_token" content="{!! csrf_token() !!}"/>
<div class="row">
	@include('front.partials.setting-menu')
	<div class="col-lg-9 col-md-9 col-sx-9 account-right">
        <div class="right-profile">
            <div class="row"> 
                <div class="col-md-7 padding-rigth-none">
                    <div class="left_sec">
                        <div class="sher_info">
                            <h4>Tell a friend</h4>
                            <p>If a friend signs up after clicking on your link, they get an extra month of <strong>FREE</strong> trial and use of invaluable software. Should they start a paid subscription, you get a <strong>FREE</strong> year of X-Dates!</p>
                                <div class="sher_url">
                                    <div class="in_bg">
                                        <p>YOUR UNIQUE SHARE LINK</p>
                                        <a href="{{$short_url or ''}}">{{$short_url or ''}}</a>
                                    </div>
                                </div>

                                <div class="sher_social">
                                    <ul class="sociyal">
                                        <li class="fb"><a onclick="Gotofb()"><img src="{{asset('assets/images/sher_bf.png') }}" alt="facebook"></a></li>
                                        <input type="hidden" value="{{$short_url or ''}}" name="short_link">
                                        <li class="tw"><a target="_blank"  onclick="openTwitter('{{$short_url}}')"><img src="{{asset('/assets/images/sher_tw.png')}}" alt="twitter"></a></li>
                                        <li class="link-d"><a href="#" onclick="openLinkShare('{{$short_url}}')"><img src="{{asset('assets/images/sher_linkd.png')}}" alt="linkedin"></a></li>
                                    </ul>
                                </div>
                                </div>
                                <div class="shre_mass">
                                    <h4>Email your friends</h4>
                                    <form class="form-horizontal" method="post" action="tell-friends" id="mailToFriend">
                                      @if(!empty($emails))
                                        <p class="error">
                                            Invites sent to {{implode(', ',$emails)}}. Check back soon to see if they have joined yet.    
                                        </p>
                                      @endif
                                    	<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                        <div class="control-group">
                                            <div class="controls">
                                                  <input type="text" id="inputEmail" class="friendEmail" name="friendEmail" placeholder="Enter email addresses, separated by commas.">
                                            </div>
                                        </div>
                                        <div class="control-group" contenteditable="true">
                                          <?php $message = tell_friend_default_text($short_url); ?>
                                        <textarea rows="8" name="message" id="msg_textarea">{{$message['heading'] or 'Hey,'}}

{{$message['center_content'] or ''}}

{{$message['end_text'] or 'Be well,'}}
{{$user->name}}
                                            </textarea>
                                        </div>
                                        <div class="text-right ">
                                        	<button class="btn btn-small btn-primary" type="submit">Send invite</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                </div>
                <div class="col-md-5 padding-left-none">
                    <div class="shre_stats shre_stats_two">
                        <h4>Sharing Stats</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <td><span class="title">Total Credit Earned (Trial Days)</span></td>
                                    <td><span class="total_day">{{$total_credit_days or 0}} days</span></td>
                                </tr>
                                <tr>
                                    <td><span class="title">Total Credits Remaining (Trial Days)</span></td>
                                    <td><span class="total">{{$remaining_trial_days or 0}} days</span></td>
                                </tr>
                            </table>
                            <table class="table table-bordered">
                                <tr>
                                    <td><span class="title">Total Credit Earned (Amount)</span></td>
                                    <td><span class="total_day">${{$totalAmountCredit or 0}}</span></td>
                                </tr>
                                <tr>
                                    <td><span class="title">Total Credits Remaining (Amount)</span></td>
                                    <td><span class="total">${{$remaining_balance or 0}} </span></td>
                                </tr>
                            </table>
                            <ul>
                              @if(!empty($userInvitedFriends)) 
                                  @foreach($userInvitedFriends as $friendData)
                                    <li>
      	                                <p>
      		                                  <span class="name">friend:</span>
      		                                  <span class="value">{{$friendData['friend_email']}}</span>

      	                                </p>
      	                                <p>
      	                                    <span class="name">Status:</span>
      	                                    <span class="value">{{$inviteFriendStatus[$friendData['status']]}}</span>
      	                                </p>
                                        <p>
                                            <span class="name">Credits Earned::</span>
                                            @if($friendData['status'] == 2)
                                                <span class="value">${{$friendData['amount'] or 0}} </span>
                                            @else 
                                                <span class="value">{{$friendData['trial_days'] or 30}} days</span>
                                            @endif
                                        </p>
                                       <!--  <p>
                                            @if($friendData['status'] == 2)
                                                <span class="name">Credits Remaining:</span>
                                                <span class="value">${{$friendData['amount_left'] or 0}}</span>
                                            @else
                                                <span class="name">Credits Remaining:</span>
                                                <span class="value">{{$friendData['left_days'] or 0}} days</span>
                                            @endif
                                        </p> -->
                                    </li>
                                  @endforeach
                               @endif
                             </ul>
                    </div>
                            
                </div>
            </div>
        </div>
    </div>
</div>
 
@endsection


@section('footerscripts')  
{!! HTML::script(asset('vendor/jsvalidation/js/jsvalidation.js')) !!}
{!! $validator  or  ''  !!}
{!! HTML::script(asset('assets/js/profile-manage.js')) !!}
<script>
 var FB;
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '1071917356177353',
      xfbml      : true,
      version    : 'v2.6'
    });
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "http://connect.facebook.net/en_US/all.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
  function Gotofb(){
  	FB.ui({
  		method: 'send',
  		link: "<?php echo $short_url ; ?>",
  	});
    
  }
  $(document).ready(function(){
      $(".friendEmail").focus(function(){
          $(this).attr("placeholder","");
      });

      $(".friendEmail").blur(function(){
          $(this).attr("placeholder","Enter email addresses, separated by commas.");
      });
  })
</script>

@endsection