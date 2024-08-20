<div class="col-md-3 col-sx-3 account-left">
    <div class="left-menu">
        <ul class="nav nav-pills nav-stacked">
        	@if($allModAccess['4']['view'])
            <li class="{{ activeLink('admin/myprofile') }}"><a href="{{url('/admin/myprofile')}}">My Profile</a></li>
            @endif
            @if($allModAccess['2']['view'])
            <li class="{{ activeLink('admin/user-manage') }}"><a href="{{url('/admin/user-manage')}}">Users</a></li>
            @endif
            @if($allModAccess['3']['view'])
            <li class="{{ activeLink('admin/manage-plans') }}"><a href="{{url('/admin/manage-plans')}}">Manage Plans</a></li>
            @endif
            @if($allModAccess['5']['view'])
            <li class="{{ activeLink('admin/manage-variables') }}"><a href="{{url('/admin/manage-variables')}}">Global Variables</a></li>
            @endif
            @if($allModAccess['6']['view'])
            <li class="{{ activeLink('admin/coupon-manage') }}"><a href="{{url('/admin/coupon-manage')}}">Coupon Manage</a></li>
            @endif
            @if($allModAccess['7']['view'])
            <li class="{{ activeLink('admin/trial-extend') }}"><a href="{{url('/admin/trial-extend')}}">Trial Extension Requests</a></li>
            @endif
        </ul>
    </div>
</div>
