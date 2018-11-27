@extends('adminlte::page')

@section('title', 'Popular Likes')

@section('content_header')
    <h1>Popular Likes</h1>
@stop

@section('content')
    <div class="row">
	<div class="col-lg-12">
	<div class="box box-success">
	<div class="box-body">
		<div class="row">
			<div class="col-lg-3">
				<h3>Image</h3>
				<div id="itemImg"></div>
			</div>			
			<div class="col-lg-9">
				<h3>Most Popular</h3>		
				<table id='displayTable' class='table table-striped table-bordered hover dataTable' cellspacing='0' role='grid' width='100%'>
					<thead><tr><th>Item #</th><th>Item Name</th><th>Item Category</th><th>Retailer</th><th>Price</th><th>View Item</th><th>Likes</th></tr></thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
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
	select2-selection select2-selection--single{height:34px !important;}
	</style>
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css">
@stop

@section('js')
	<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
	<script>
	var dtTable;
	$(document).ready(function() {
		dtTable = $('#displayTable').DataTable({
			'paging': true,
			'dom': '<\"col-sm-6\"l><\"col-sm-6\"f><\"col-sm-3\"i><\"col-sm-9\"p>rt<\"col-sm-12\"Bp><\"clearfix\">',
			'order': [[6,'desc']],
			'lengthMenu': [[20,50,100,-1], [20,50,100,'All']],
			'iDisplayLength': 50,
			'searching': true,
			'ordering': true,
			'info': true,
			'responsive': true,
			'scrollX': false,
			'autoWidth': true,
			'ajax': {
				'url': '{{ url(config('adminlte.dashboard_url', '')) }}/data/agg_likes'
			},
			'columns': [
				{ data: 'item_id' },
				{ data: 'item_name' },
				{ data: 'item_category' },
				{ data: 'retailer' },
				{ data: 'price' },
				{ data: 'url' },
				{ data: 'likes' }
			],
			'select': {
				style: 'single'
			},
			'createdRow': function ( row, data, index ) {
				// url setup
				$('td', row).eq(5).html('<a href="'+data['url']+'" target="_blank">View Item</a>');
			}
		});
		
		dtTable.on('select', function(e, dt, type, indexes){
			if ( type === 'row' ) {
				var data = dtTable.rows( indexes ).data().pluck('image');
				$("#itemImg").html("<img width='100%' src='https://resources.justmystyleapp.com/img/"+data[0]+"'>");
			}
		});
		
		
	} );
	</script>
@stop

