@extends('emails.mailtemplate')
@section('title','X-Dates: deletion notification')
@section('content')
    <?php
        $types =  array(1=>'Lines Type','Industry Type','Personal policy','Commercial policy');
     ?>
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
                                        <p>Hello, {{$user->first_name or ''}}!</p>
                                        <p>As Owner of the <b>{{$user->com_name or ''}}</b> X-Dates account, we notify you of certain changes made to the account. <b>{{$policy->deleted_by or  ''}}</b> deleted the following custom field: 
                                        <ul>
                                        <li>{{$types[$policy->type]}} : {{$policy->name}}</li>
                                        </ul></p>
                                        <p>To undo this action <a href='{{ url("policy/confirm/{$policy->id}") }}'>click here</a>.</p>
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
