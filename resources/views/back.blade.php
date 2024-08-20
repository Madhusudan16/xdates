<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <title>XDates-Administrator | {{ $page_title or ''}}</title>
        <meta name="description" content="">
        <link rel="shortcut icon" href="{{url('/assets/images/favicon.ico')}}" type="image/x-icon">
        <link rel="icon" href="{{url('/assets/images/favicon.ico')}}" type="image/x-icon">
         <!-- Bootstrap -->
        {!! HTML::style(asset('assets/css/select2.min.css')) !!}
        {!! HTML::style(asset('assets/css/bootstrap-datetimepicker.min.css')) !!}
        {!! HTML::style(asset('assets/css/bootstrap.min.css')) !!}
        {!! HTML::style('https://fonts.googleapis.com/css?family=Arimo:400,700,700italic,400italic|Roboto:400,400italic,500,500italic,700,700italic|Roboto+Condensed:400,400italic,700,700italic') !!}
        {!! HTML::style(asset('assets/css/jquery-ui.css')) !!}
        {!! HTML::style(asset('assets/css/scrollbar.css')) !!}
        {!! HTML::style(asset('assets/css/styles.css')) !!}
        {!! HTML::style(asset('assets/css/styles_1.css')) !!}

        {!! HTML::script(asset('assets/js/jquery/jquery-2.2.4.min.js')) !!}
        {!! HTML::script(asset('assets/js/jquery/jquery-ui.js')) !!}
        {!! HTML::script(asset('assets/js/jquery/jquery-migrate-1.4.1.min.js')) !!}
        {!! HTML::script(asset('assets/js/modernizr-2.8.3.min.js')) !!}
        {!! HTML::script(asset('assets/js/tablesorter.js')) !!}
		<script type="text/javascript">
			var site_url = '{{url('/admin/')}}';
            var base_url = '{{url('/')}}';
		</script>
        @yield('head')

        @yield('headscripts')
        <style>
            body.modal-open {
                bottom: 0;
                position: fixed;
                overflow-y: scroll;
                overflow-x: hidden;
                top: 0;
                width: 100%;
            }
        </style>
    </head>

  <body class="administrator">

    @if(isset($user))
    <header class="main-header">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="logo">
                        <a href="{{ url('/admin') }}">
                            <img src="{{asset('assets/images/logo.png')}}" alt="" />
                        </a>
                    </div>
                    <div class="search-section border-shadow">
                        <form action="#" method="get">
                            <input type="text" autocomplete = "off" class="form-control" placeholder="Search.." id="searchString">
                            <button type="submit" class="search-btn">
                                <img src="{{url('assets/images/search_icon.png')}}" alt="" />
                            </button>
                        </form>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="login-container dropdown">
                        <button class="dropdown-toggle border-shadow" type="button" id="login-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                           @if(!empty($user->profile_image))
                                 <img  src={{asset(config('constants.FILEUPLOAD').$user->profile_image)}}  width="42" height="42" alt="" class="profile-image"/>
                            @else
                                 <img src={{asset(config('constants.FILEUPLOAD').''.config('constants.DEFAULT_PROFILE')) }} width="42" height="42" alt="" class="profile-image"/>
                            @endif
                            <span class="usr-name">{{ $user->name }}</span>
                            <span class="drop-icon">
                                <img src="{{asset('assets/images/dropdown-icon-dark-open.jpg')}}" alt="" />
                            </span>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="login-dropdown">
                            <li><a href="{{url('admin/myprofile')}}">Settings</a></li>
                            <li><a href="{{url('admin/logout')}}">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>
 	@else
 	 <header class="main-header logout">
        <div class="container">
            <div class="logo">
                <a href="{{ url('/admin') }}">
                    <img src="{{asset('assets/images/logo.png')}}" alt="" />
                </a>
            </div>
        </div>
    </header>
 	@endif

    <section class="main-content {{isset($page_section_class) ? $page_section_class : 'admin-home' }}">
        <div class="container">

            @yield('main')

        </div>
    </section>

    <footer class="main-footer main-footer-bak">
        <div class="container footer-container-bak">
            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="text-center copy-text link-top">
                        © X-Dates {{date("Y")}}
                    </div>

                  <!--   <div class="col-md-6 col-sm-6 col-xs-6">
                    <div class="links link-top">
                        <a href="#">Learn More</a>
                    </div>
                    <div class="links">
                        <ul class="list-unstyled">
                            <li>
                                <a href="#">Plans & Pricing</a>
                            </li>
                            <li>
                                <a href="#">About</a>
                            </li>

                        </ul>
                    </div>
                </div> -->
               <!--  <div class="col-md-6 col-sm-6">
                   <div class="social-menu-footer text-center">
                        <ul class="list-inline">
                            <li>
                                <img src="{{asset('assets/images/social_footer_01.png')}}" alt="" />
                            </li>
                            <li>
                                <img src="{{asset('assets/images/social_footer_02.png')}}" alt="" />
                            </li>
                            <li>
                                <img src="{{asset('assets/images/social_footer_03.png')}}" alt="" />
                            </li>
                            <li>
                                <img src="{{asset('assets/images/social_footer_04.png')}}" alt="" />
                            </li>
                        </ul>
                    </div>
                </div>  -->
                <!-- <div class="col-md-6 col-sm-6 col-xs-6">

                    <div class="pull-right">

                    <div class="links">
                        <ul class="list-unstyled">
                            <li>
                                <a href="mailto:info@xdates.net" class="link-red link-underline">info@xdates.net</a>
                            </li>
                            <li>
                                <a href="#">Terms of Services</a>
                            </li>
                            <li>
                                <a href="#">Privacy Policy</a>
                            </li>
                        </ul>
                    </div>
                    </div>
                </div>
                </div> -->
            </div>
        </div>
    </footer>
	@include('front.partials.footerscript')
    {!! HTML::script(asset('assets/js/admin-common.js')) !!}
    @yield('footerscripts')

  </body>
</html>
