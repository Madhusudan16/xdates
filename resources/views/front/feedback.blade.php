@extends('front')

@section('main')
	<div class="row">
		@include('front.partials.setting-menu')
		<div class="col-lg-9 col-md-9 col-sx-9 account-right">
            <div class="right-profile">
            	@if(!$saveFeedback)
                    <h2 class="feedback_title">Submit Feedback</h2>
                    <div class="feed_sec"><h4>Have a question? Is there some missing functionality you'd like to see us add? Tell us what you think.</h4>
                        <form method="post" action="feedback"  id="feedbackForm">
                           	{{ csrf_field() }}
                            <div class="form-group">
                            	<textarea class="form-control" rows="9" id="comment" placeholder="Your feedback message here....." name="feedback"></textarea>
                            </div>
                            <div class="text-right">
                                <button type="submit"  class="btn feedback-btn send_feedback">Send Feedback</button>
                            </div>
                        </form>
                    </div>
                @else 	
                	<h2 class="feedback_title">Thanks for your Feedback!</h2>
                    <p class="thankyou_meassag">We'll do our best to get back with you in a timely manner.</p>
                @endif
            </div>
        </div>
	</div>
 
@endsection


@section('footerscripts')  
   {!! HTML::script(asset('vendor/jsvalidation/js/jsvalidation.js')) !!}
   {!! $validator or '' !!}
   <script>
   		$(document).ready(function(){
   			$(".feedback-btn").prop('disabled',true);
   			$("#comment").on('keyup',function(){
   				if($(this).val().length >=4) {
   					$(".feedback-btn").prop('disabled',false);
   					$(".feedback-btn").addClass('feedback-success');
   				} else {
   					$(".feedback-btn").prop('disabled',true);
   					$(".feedback-btn").removeClass('feedback-success');
   				}
   			});
        $(".send_feedback").click(function(){
            setTimeout(function(){
              $(this).prop('disabled',true);
            },1);
        });
   		});
   </script>
@endsection