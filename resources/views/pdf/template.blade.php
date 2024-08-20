<!DOCTYPE html>
<html>
<head>
    <title>@yield('title')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <style>
        
        .owner-address {
            float: left;
        }
        body {

            width:100%;
            height:792px;
        }
        .title {
            padding:0 0 8px;
            color:#808080;
            width: 90px;
            display: inline-block;
        }
        .bill-details {
            float: right;
        }
        .bill-address {
            float: left;
        }
        .paid-header {
            height: 48px;
        }
        .bill-money {
            height: 48px;
        }
        .paid {
            background-color: #3c9b01;
              width: 100px;
            color: #FFF;
            font-weight: normal;
        }
        table.bill-table {
          margin-top: 10px;
          width: 248px;
        }
        .customer-details {
            margin-top: 30px;
        }
        td.bill-money {
            height: 48px;
        }
        tr.invoice-tr {
            height: 48px;
            padding: 8px;
        }
        .total {
            float: right;
            padding-top: 30px;
        }
        .total-p {
            height: 62px;
            width: 310px;
            
        }
       .total-p:nth-child(even) {background: #F2F2F2}

        /* .invoice-table{
          border-collapse: collapse;
          width: 100%;
        }

        .invoice-content,.invoice-header {
           padding: 8px;
          text-align: left;
          border-bottom: 1px solid #ddd;
        }*/
        .invoice-tr:nth-child(odd) {background-color: #f2f2f2}
        .billed-on {
            font-size:20px;
            padding: 0 0 10px 0;
        }
        .total-title {
            width: 150px;
            margin-right: 15px;
            display: inline-block;
            text-align: right;
            font-weight: bold;
            padding-top: 22px;
            
            
        }
        .total-value{
            width: 391px;
            text-align: right;
            font-weight: bold
            
        }
        .full-total{
            font-size: 18px;
            padding-top: 20px;
        }
        
        .owner_address {
            padding-top: 20px;
        }
        span.amount {
           font-size: 30px;
        }
        table.invoice-table {
            margin-top: 100px;
        }
        .bill {
           /*padding-bottom: 5px;*/
           white-space: nowrap;
        }
        .on-date {
            font-weight: normal !important;
        }
        table{
            border-color: #372f2c;
            text-align: left;
        }
        .bill-title{
            color: #808082;
            font-size: 20px;
        }
        .note {
            color:#B6BAB6;
            font-size: 13px;
            white-space: nowrap;
            text-align: center;
           margin-right: 10px;
            font-weight: normal;
            
        }
    </style>
</head>
<body style="margin: 0 !important; padding: 0 !important;" >
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td >
                <div align="left" class="pdf logo">
                    <img src="http://xdates.xyz1.us/assets/images/pdf-logo.png" alt="">
                </div>
                <div  class="owner_address">
                    <div class="owner-address">
                        @if(!empty($invoices['from_address']))
                            <div class="bill">
                                <?php echo nl2br($invoices['from_address']); ?>
                            </div>
                        @endif
                    </div>
                    <div class="bill-details" >
                        <div class="bill">
                            <span class="title billed-on">Billed on </span>
                            <span class="value billed-on">{{date('M d,Y',strtotime($invoices['bill_date']))}}</span>
                        </div>
                        <div class="bill">
                            <span class="title">Due on</span>
                            <span class="value">{{date('M d,Y',strtotime($invoices['bill_date']))}}</span>
                        </div> 
                        <div class="bill">
                            <span class="title">Terms</span>
                            <span class"value">On-Receipt</span>
                        </div>
                        <div class="bill">
                            <span class="title">Invoice # </span>
                            <span class="value">{{$invoices['id']}}</span>
                        </div>
                        
                    </div>
                </div>
               
            </td>
        </tr>
        <tr>
            <td>
                <div class="customer-details">
                    <div class="bill-address">
                        
                            <div class="bill bill-title"><strong >Billed To:</strong></div>
                           @if(!empty($invoices['to_address']))
                                <div class="bill">
                                    {{$invoices['to_address']['first_name']}} {{$invoices['to_address']['last_name']}}
                                </div>
                                <div class="bill">
                                    {{$invoices['to_address']['address']}}
                                </div>
                                <div class="bill">
                                    {{$invoices['to_address']['state']}} {{$invoices['to_address']['zip_code']}}
                                </div>
                                <div class="bill">
                                    {{$invoices['to_address']['country']}}
                                </div>
                                
                            @endif
                        
                    </div>

                    <div class="bill-details">
                        <table border="1" cellspacing="0" cellpadding="5" class="bill-table">
                            <tr class="paid-header">
                                <th align="center" class="paid">PAID</th>
                                <th align="center" class="on-date">on {{date('M d,Y',strtotime($invoices['bill_date']))}}</th>

                            </tr>
                            <tr class="bill-money">
                                <td style="padding-left:35px;" colspan="2" align="left"><span class="amount">${{$cost['totalPaidAmount']}}</span> <small>USD</small></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </td>
        </tr>
        <tr> 
            
                <table align="left" width="100%" border="2" cellpadding="10" cellspacing="0" rules="ROWS" frame="BOX" class="invoice-table">
                    <tr class="invoice-tr">
                        <th class="invoice-header">Date</th>
                        <th class="invoice-header">Description</th>
                        <th class="invoice-header">Qty</th>
                        <th class="invoice-header">Price</th>
                        <th class="invoice-header">Subtotal</th>
                    </tr>
                    @foreach($items as $key=>$item)
                       
                        <?php 
                            if($key == 0 )
                                $subTotal = $item['paid_amount'];
                            else 
                                $subTotal += $item['paid_amount'];
                        ?>
                        <tr class="invoice-tr">
                            <td class="invoice-content">{{date('M d',strtotime($item['from_date']))}} - {{date('M d,Y',strtotime($item['to_date']))}}</td>
                            <td class="invoice-content">{{$item['plan_name']}}</td>
                            <td class="invoice-content">1</td>
                            <td class="invoice-content">${{$item['plan_amount']}}</td>

                            <td align="left" class="invoice-content">${{$item['paid_amount']}}</td>
                        </tr>
                    @endforeach
                </table>
            
        </tr>
        <tr>
            <div class="total">
                <div class="total-p"><span class="total-title ">Subtotal</span><span class="total-value">${{$cost['totalPaidAmount']}}</span></div>
                <div class="total-p"><span class="total-title ">Total</span><span class="total-value">${{$cost['totalPaidAmount']}}</span></div>
                <div class="total-p"><span class="total-title ">Amount Paid</span><span class="total-value">${{$cost['totalPaidAmount']}}</span></div>
                <div class="total-p"><span class="total-title full-total">Amount Due</span><span class="total-value full-total">${{$cost['due_payment']}}</span></div>
                <div class="total-p"><span class="total-title full-total note"><i>All amounts in United States Dollars (USD)</i></span></div>
            </div>
        </tr>
    </table>
</body>
</html>
