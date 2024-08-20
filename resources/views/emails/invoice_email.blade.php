@extends('emails.mailtemplate')
@section('title','X-Dates - Invoice notification')
@section('content')
	<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;" class="responsive-table">
        <tr>
            <td>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td>
                            <!-- COPY -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding">
                                   
                                        <p>
                                            Hello {{$user->first_name}}, <br>
                                            &nbsp;&nbsp;&nbsp; Your invoice for this month has been  generated <a href="{{url('generate-pdf')}}/{{$invoice_no}}">click here</a> to see invoice.
                                        </p>
                                      
                                    </td>

                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
@endsection
