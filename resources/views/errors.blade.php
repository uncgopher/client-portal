@extends('adminlte::page')

@section('title', 'Errors - 7 Days')

@section('content_header')
    <h1>Errors - 7 Days</h1>
@stop

@section('content')
    <div class="row">
	<div class="col-lg-12">
	<div class="box box-success">
	<div class="box-body">	
		<table id='displayTable' class='table table-striped table-bordered hover dataTable' cellspacing='0' role='grid' width='100%'>
			<thead><tr><th>Date</th><th>API Call</th><th>User ID</th><th>Error Message</th></tr></thead>
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
	.btn-danger{margin-left:20px;}
	h3{margin:5px 0 30px 0;}
	</style>
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css">
@stop

@section('js')
	<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
	<script>
	$(document).ready(function() {
		dtTable = $('#displayTable').DataTable({
			'paging': true,
			'dom': '<\"col-sm-6\"l><\"col-sm-6\"f><\"col-sm-3\"i><\"col-sm-9\"p>rt<\"col-sm-12\"Bp><\"clearfix\">',
			'order': [[0,'desc']],
			'lengthMenu': [[20,50,100,-1], [20,50,100,'All']],
			'iDisplayLength': 50,
			'searching': true,
			'ordering': true,
			'info': true,
			'responsive': true,
			'scrollX': false,
			'autoWidth': true,
			'ajax': {
				'url': '{{ url(config('adminlte.dashboard_url', '')) }}/data/agg_errors'
			},
			'columns': [
				{ data: 'date' },
				{ data: 'url' },
				{ data: 'user_id' },
				{ data: 'msg' }
			]
		});		
	} );
	</script>
@stop

