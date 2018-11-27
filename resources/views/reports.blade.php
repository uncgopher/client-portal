@extends('adminlte::page')

@section('title', 'User Reports')

@section('content_header')
    <h1>User Reports</h1>
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
				<h3 style='margin-top:50px;'>Change Category</h3>
				<form method='POST' action='{{ url(config('adminlte.dashboard_url', '')) }}/fix/category'>
					{{ csrf_field() }}
					<input id='hidden_item_id' type='hidden' name='item_id' value='0'>
					<select name='category_code' class='pickItem form-control'></select><br/><br/>
					<button type='submit' class='btn btn-info'>Change Category</button>
				</form>
			</div>			
			<div class="col-lg-9">
				<h3>Reported Items</h3>		
				<table id='displayTable' class='table table-striped table-bordered hover dataTable' cellspacing='0' role='grid' width='100%'>
					<thead><tr><th>Reason</th><th>Report Count</th><th>Item Category</th><th>Item Name</th><th>URL</th><th>Status</th></tr></thead>
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
	var totalCount = 0;
	var multiSessionCount = 0;
	var dtTable;
	var categoryList = [];
	$(document).ready(function() {
		dtTable = $('#displayTable').DataTable({
			'paging': true,
			'dom': '<\"col-sm-6\"l><\"col-sm-6\"f><\"col-sm-3\"i><\"col-sm-9\"p>rt<\"col-sm-12\"Bp><\"clearfix\">',
			'order': [[3,'asc']],
			'lengthMenu': [[20,50,100,-1], [20,50,100,'All']],
			'iDisplayLength': 50,
			'searching': true,
			'ordering': true,
			'info': true,
			'responsive': true,
			'scrollX': false,
			'autoWidth': true,
			'ajax': {
				'url': '{{ url(config('adminlte.dashboard_url', '')) }}/data/agg_reports'
			},
			'columns': [
				{ data: 'reason' },
				{ data: 'numReports' },
				{ data: 'item_category' },
				{ data: 'item_name' },
				{ data: 'url' },
				{ data: 'status' }
			],
			'select': {
				style: 'single'
			},
			'createdRow': function ( row, data, index ) {
				// url setup
				$('td', row).eq(4).html('<a href="'+data['url']+'" target="_blank">View Item</a>');
				
				// options
				if(data['status'] == 0){
					$('td', row).eq(5).html('<a href="{{ url(config('adminlte.dashboard_url', '')) }}/fix/valid-report/'+data['DT_RowId']+'" class="btn btn-success">Valid Report</a> <a href="{{ url(config('adminlte.dashboard_url', '')) }}/fix/revert-report/'+data['DT_RowId']+'" class="btn btn-danger">Restore Item</a>');
				}else{
					$('td', row).eq(5).html('<a href="{{ url(config('adminlte.dashboard_url', '')) }}/fix/valid-report/'+data['DT_RowId']+'" class="btn btn-success">Valid Report</a>');
				}
			}
		});
		
		dtTable.on('select', function(e, dt, type, indexes){
			if ( type === 'row' ) {
				var data = dtTable.rows( indexes ).data().pluck('image');
				$("#itemImg").html("<img width='100%' src='https://resources.justmystyleapp.com/img/"+data[0]+"'>");
				var itemID = dtTable.rows( indexes ).data().pluck('DT_RowId');
				$("#hidden_item_id").val(itemID[0]);
			}
		});
		
		
	} );
	
	$.get('{{ url(config('adminlte.dashboard_url', '')) }}/data/categories', function(tempData){$(".pickItem").select2({data: tempData});}, 'json');
	
	function showImg(imgName){
		$("#itemImg").html("<img width='100%' src='https://resources.justmystyleapp.com/img/"+imgName+"'>");
	}
	</script>
@stop

