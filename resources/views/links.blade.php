@extends('adminlte::page')

@section('title', 'Link Tracking')

@section('content_header')
    <h1>Link Tracking</h1>
@stop

@section('content')
    <div class="row">
	<div class="col-lg-12">
	<div class="box box-success">
	<div class="box-body">	
		<table id='displayTable' class='table table-striped table-bordered hover dataTable' cellspacing='0' role='grid' width='100%'>
			<thead><tr><th>Name</th><th>Count</th><th>Date Created</th><th>Comment</th><th>ID</th></tr></thead>
			<tbody></tbody>
		</table>
	</div>
	</div>
	</div>
	</div>
	
	<div class="row">
	<div class="col-lg-12">
	<div class="box box-info">
	<div class="box-body">	
		<h3>Create Tracking Link</h3>
		<form action="{{ url(config('adminlte.dashboard_url', '')) }}/links/add" method="POST">
			{{ csrf_field() }}
			<div class='row'>
				<div class='col-md-3 col-sm-12'><label>Tracking Link Name</label><input type='text' name='short_link_name' class='form-control'></div>
				<div class='col-md-3 col-sm-12'><label>Destination URL</label><input type='text' name='short_link_url' class='form-control' value='https://www.justmystyleapp.com'></div>
				<div class='col-md-6 col-sm-12'><label>Comment</label><input type='text' name='short_link_comment' class='form-control'></div>
			</div>
			<br/>
			<button type='submit' class='btn btn-success'>Submit</button>
		</form>
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
			'order': [[2,'desc']],
			'lengthMenu': [[20,50,100,-1], [20,50,100,'All']],
			'iDisplayLength': 50,
			'searching': true,
			'ordering': true,
			'info': true,
			'responsive': true,
			'scrollX': false,
			'autoWidth': true,
			'ajax': {
				'url': '{{ url(config('adminlte.dashboard_url', '')) }}/data/agg_links'
			},
			'columns': [
				{ data: 'name' },
				{ data: 'count' },
				{ data: 'date' },
				{ data: 'comment' },
				{ data: 'DT_RowId' }
			]
		});		
	} );
	</script>
@stop

