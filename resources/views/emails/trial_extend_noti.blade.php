@extends('emails.mailtemplate')
@section('title','Trial Extend Notification')
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
                                            Hello , <br/>
                                            &nbsp;&nbsp;&nbsp; Trial extended request for {{$user['get_user']->name or ''}} is pending yet. You want to approve or decline request click <a href="{{url('admin/user/'.$user['get_user']->id)}}">here</a>.
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
