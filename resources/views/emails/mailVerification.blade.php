@extends('emails.mailtemplate')
@section('title','X-Date Confirmation mail')
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
                                    <td align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding"><p>You requested this email address to be added to your X-Dates account.</p><p>Please click the activation link below to confirm this action:</p>
                                    <p><a href='{{ url("notification/confirm/{$token}") }}'>{{ url("notification/confirm/{$token}") }}</a></p>
                                    <p>
                                        If the above URL does not work, try copying and pasting the URL into you browser.
                                    </p>
                                    <p>
                                        The X-dates Team
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
