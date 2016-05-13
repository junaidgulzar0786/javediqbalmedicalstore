@extends('app')

@section('content')
{!! Html::script('js/angular.min.js', array('type' => 'text/javascript')) !!}
{!! Html::script('js/create.item.js', array('type' => 'text/javascript')) !!}
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">{{trans('item.inventory_data_tracking')}}</div>

				<div class="panel-body" ng-controller="InventoryItemCtrl">
					@if (Session::has('message'))
						<div class="alert alert-info">{{ Session::get('message') }}</div>
					@endif

					{!! Html::ul($errors->all()) !!}

					<table class="table table-bordered">
					<tr><td>UPC/EAN/ISBN</td><td>{{ $item->upc_ean_isbn }}</td></tr>
					<tr><td>{{trans('item.item_name')}}</td><td>{{ $item->item_name }}</td></tr>
					<tr><td>{{trans('item.total_quantity')}}</td><td>@{{ totalQuantity }}</td></tr>
					</table>
					<div>
						{!! Form::model($item->inventory, array('route' => array('inventory.update', $item->id), 'method' => 'PUT')) !!}
						
						<div data-ng-repeat="choice in choices">
							{!! Form::label('batch_no', trans('item.batch_no')) !!}
							{!! Form::text('batch_no', '@{{ choice.batch_no }}' , array('name'=>'batch_arr[batch_no][]')) !!}

							{!! Form::label('quantity', trans('item.quantity')) !!}
							{!! Form::text('quantity', '@{{ choice.in_out_qty }}', array('name'=>'batch_arr[quantity][]')) !!}
							
							{!! Form::label('Expiry Date', trans('item.expiry')) !!}
							{!! Form::date('Expiry Date', '@{{ choice.expiry_date }}', array('name'=>'batch_arr[expiry][]','class'=>'datepicker')) !!}

							{!! Form::label('remarks', trans('item.remarks')) !!}
							{!! Form::text('remarks', '@{{ choice.remarks }}', array('name'=>'batch_arr[remarks][]')) !!}

							{!! Form::hidden('Batch Id', '@{{ choice.id }}', array('name'=>'batch_arr[inventory_id][]')) !!}
							
							<button type="button" class="remove" ng-show="$last" ng-click="removeChoice(choice.id,choice.in_out_qty)">-</button>
						</div>

						<button type="button" class="addfields" ng-click="addNewChoice()">Add fields</button><br/><br/>

							<input type="hidden" id="get_item_id" value="{{ $item->id }}"/>
						{!! Form::submit(trans('item.submit'), array('class' => 'btn btn-primary')) !!}
						{!! Form::close() !!}
					</div>
					<table class="table table-striped table-bordered">
					    <thead>
					        <tr>
					            <td>{{trans('item.inventory_data_tracking')}}</td>
					            <td>{{trans('item.employee')}}</td>
					            <td>{{trans('item.in_out_qty')}}</td>
					            <td>{{trans('item.remarks')}}</td>
					        </tr>
					    </thead>
					    <tbody>
					    @foreach($item->inventory as $value)
					        <tr id="{{ $value->id }}" >
					            <td>{{ $value->created_at }}</td>
					            <td>{{ $value->user->name }}</td>
					            <td>{{ $value->in_out_qty }}</td>
					            <td>{{ $value->remarks }}</td>
					        </tr>
					    @endforeach
					    </tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection