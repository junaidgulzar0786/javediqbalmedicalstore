@extends('app')
@section('content')
{!! Html::script('js/angular.min.js', array('type' => 'text/javascript')) !!}
{!! Html::script('js/app.js', array('type' => 'text/javascript')) !!}
<style>
table td {
    border-top: none !important;
}
</style>
<div class="container-fluid">
   <div class="row">
        <div class="col-md-12" style="text-align:center">
            Javed Iqbal Medical Store - Point of Sale           
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {{trans('sale.customer')}}: {{ $sales->customer->name}}<br />
            {{trans('sale.sale_id')}}: SALE{{$saleItemsData->sale_id}}<br />
            {{trans('sale.employee')}}: {{$sales->user->name}}<br />
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
           <table class="table">
                <tr>
                    <td>{{trans('sale.item')}}</td>
                    <td>{{trans('sale.item_c')}}</td>
                    <td>{{trans('sale.price')}}</td>
                    <td>{{trans('sale.qty')}}</td>
                    <td>{{trans('sale.dis')}}</td>
                    <td>{{trans('sale.total')}}</td>
                </tr>
                @foreach($saleItems as $value)
                <tr>
                    <td>{{$value->item->item_name}}</td>
                    <td>{{$value->item->upc_ean_isbn}}</td>
                    <td>{{$value->selling_price}}</td>
                    <td>{{$value->quantity}}</td>
                    <td>{{$value->discount}}</td>
                    <td>{{$value->total_selling}}</td>
                </tr>
                @endforeach
            </table>
        </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            {{trans('sale.payment_type')}}: {{$sales->payment_type}}
        </div>
        <div class="col-md-6">
            <?php $complete_grand_total = 0; ?>
           
            @foreach ($saleItems as $sale)
                <?php $complete_grand_total += $sale->total_selling; ?>
            @endforeach
            
            {{trans('sale.grand_total')}} : {{ number_format($complete_grand_total,3) }}
        </div>
    </div>
    <hr class="hidden-print"/>
    <div class="row">
        <div class="col-md-8">
            &nbsp;
        </div>
        <div class="col-md-2">
            <button type="button" onclick="printInvoice()" class="btn btn-info pull-right hidden-print">{{trans('sale.print')}}</button> 
        </div>
        <div class="col-md-2">
            <a href="{{ url('/sales') }}" type="button" class="btn btn-info pull-right hidden-print">{{trans('sale.new_sale')}}</a>
        </div>
    </div>
</div>
<script>
function printInvoice() {
    window.print();
}
</script>
@endsection