@extends('adminlte::page')

@section('title', 'User Requests')

@section('content_header')
    <h1>User Requests</h1>
@stop

@section('content')
    <div class="row">
	<div class="col-lg-12">
	<div class="box box-success">
	<div class="box-body">
	<table id='displayTable' class='table table-striped table-bordered hover dataTable' cellspacing='0' role='grid' width='100%'>
		<thead><tr><th>User Email</th><th>Store/Brand</th><th>Hide</th><th>User</th><th>Date</th><th>IP Address</th></tr></thead>
		<tbody></tbody>
	</table>
	</div>
	</div>
	</div>
	</div>
@stop

@section('css')
    <style>
	.dt-right{text-align:right;}
	th.dt-right{text-align:left !important;}
	</style>
@stop

@section('js')
	<script>
	var totalCount = 0;
	var multiSessionCount = 0;
	$(document).ready(function() {		
		$('#displayTable').DataTable({
			'paging': true,
			'dom': '<\"col-sm-6\"l><\"col-sm-6\"f><\"col-sm-3\"i><\"col-sm-9\"p>rt<\"col-sm-12\"Bp><\"clearfix\">',
			'order': [[4,'desc']],
			'lengthMenu': [[20,50,100,-1], [20,50,100,'All']],
			'iDisplayLength': 50,
			'searching': true,
			'ordering': true,
			'info': true,
			'responsive': true,
			'scrollX': false,
			'autoWidth': true,
			'ajax': {
				'url': '{{ url(config('adminlte.dashboard_url', '')) }}/data/agg_requests'
			},
			'columns': [
				{ data: 'email' },				
				{ data: 'retailer' },
				{ data: null },
				{ data: 'name' },
				{ data: 'date' },
				{ data: 'ip' }
			],
			'createdRow': function ( row, data, index ) {
				$('td', row).eq(2).html('<a href="{{ url(config('adminlte.dashboard_url', '')) }}/fix/request/'+data['DT_RowId']+'">Mark as Read</a>');
			}
		});
	} );
	</script>
@stop

