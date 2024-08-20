@extends('emails.mailtemplate')
@section('title','X-Dates: x-date assigned to you')
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
                                     <td align="left" style="font-size: 25px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px;" class="padding">
                                            X-Dates: X-Date Assigned To You                     
                                     </td>
                                </tr>
                                <tr>

                                    <td align="left" style="padding: 20px 0 0 0; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #323232; font-size: 14px; " class="padding">
                                        Congratulations! The following x-date has been assigned to you:<br>
                                            <ul style="list-style: none;">
                                                <li>- {{$xname}}</li>
                                            </ul>
                                        
                                        <p>
                                            Good luck with this prospect!<br>
                                            ~ The X-Dates Team
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
