<div class="col-md-3 col-sx-3 account-left">
    <div class="left-menu">
        <ul class="nav nav-pills nav-stacked">
        	@if($allModAccess['10']['view'])
            <li class="{{ activeLink('myprofile') }} @if($user->is_expired == 1 && $check_access['allow_access'] == 1 )account-expired @endif"><a href="{{url('myprofile')}}">My Profile</a></li>
            @endif
            @if($allModAccess['2']['view'])
            <li class="{{ activeLink('user-manage') }} @if($user->is_expired == 1 && $check_access['allow_access'] == 1) account-expired @endif"><a href="{{url('user-manage')}}">Users</a></li>
            @endif
            @if($allModAccess['3']['view'])
            <li class="{{ activeLink('manage-customize-fields') }} @if($user->is_expired == 1 && $check_access['allow_access'] == 1) account-expired @endif"><a href="{{url('manage-customize-fields')}}">Customize Fields</a></li>
            @endif
            @if($allModAccess['11']['view']) 
            <li class="{{ dropdownOpenClass('planbill/*')}} openmenu">
                <a  class="{{ activeLink('planbill/*')}}" href="javascript:void(0)">Plans & Billing</a> 
                    <ul class="sub-menu">
                    	@if($allModAccess['4']['upgrade'])
                        <li class="{{ activeLink('planbill/change-plan') }}"><a href="{{url('planbill/change-plan')}}">Change plans</a></li>
                        @endif
                        @if($allModAccess['5']['view'])
                        <li class="{{ activeLink('planbill/invoice') }} @if($user->is_expired == 1 && $check_access['allow_access'] == 1) account-expired @endif"><a href="{{url('planbill/invoice')}}">invoices</a></li>
                        @endif
                        @if($allModAccess['6']['view'])
                        <li class="{{ activeLink('planbill/card') }}"><a href="{{url('planbill/card')}}">card</a></li>
                        @endif
                    </ul>
            </li>
            @endif
            @if($allModAccess['7']['view'])
            <li class="{{ activeLink('tell-friends') }} @if($user->is_expired == 1 && $check_access['allow_access'] == 1) account-expired @endif"><a href="{{url('tell-friends')}}">Tell a Friend</a></li>
            @endif
            @if($allModAccess['8']['view'])
            <li class="{{ activeLink('export') }} @if($user->is_expired == 1 && $check_access['allow_access'] == 1) account-expired @endif"><a href="{{url('export')}}">Export</a></li>
            @endif
            @if($allModAccess['8']['view'])
            <li class="{{ activeLink('notification') }} @if($user->is_expired == 1 && $check_access['allow_access'] == 1) account-expired @endif"><a href="{{url('notification')}}">notifications</a></li>
            @endif
        </ul>
    </div>
</div>
<script>
    $(".openmenu").click(function(){ $(this).toggleClass('open'); });
</script>