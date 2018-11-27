@extends('adminlte::page')

@section('title', 'User Overview')

@section('content_header')
    <h1>User Overview</h1>
@stop

@section('content')
    <div class="row">
	<div class="col-lg-12">
	<div class="box box-success">
	<div class="box-body">
	<strong>Users with multiple sessions:</strong> <span id='multiSession'></span><br><br>
	<p>Excludes Josh's, Becky's, and Erin's accounts along with any account with the word 'test' in the name or email.</p>
	<br>
	<table id='displayTable' class='table table-striped table-bordered hover dataTable' cellspacing='0' role='grid' width='100%'>
		<thead><tr><th>User</th><th>User Email</th><th>Sessions</th><th>API Calls</th><th>Date Created</th><th>Latest Session</th><th>Zip Code</th><th>Gender</th></tr></thead>
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
			'order': [[5,'desc']],
			'lengthMenu': [[20,50,100,-1], [20,50,100,'All']],
			'iDisplayLength': 50,
			'searching': true,
			'ordering': true,
			'info': true,
			'responsive': true,
			'scrollX': false,
			'autoWidth': true,
			'columnDefs': [
				{ className: 'dt-right', 'targets': [ 2,3,4,5,6,7 ] } 
			],
			'ajax': {
				'url': '{{ url(config('adminlte.dashboard_url', '')) }}/data/agg_users'
			},
			'columns': [
				{ data: 'name' },
				{ data: 'email' },
				{ data: 'sessions', type: 'num' },
				{ data: 'calls', type: 'num' },
				{ data: 'created' },
				{ data: 'latest' },
				{ data: 'zip' },
				{ data: 'gender' }
			],
			'createdRow': function ( row, data, index ) {
				// highlight based on gender 
				if ( data['gender'] == "Female" ) {
					$('td', row).eq(7).addClass('text-green');
				}else{
					$('td', row).eq(7).addClass('text-red');
				}

				// multiple session counting
				totalCount++;
				if( data['sessions'] > 1 ) multiSessionCount++;
			},
			initComplete: function () {
				$('#multiSession').html(((multiSessionCount/totalCount) * 100).toFixed(1) + '%');
			}
		});
	} );
	</script>
@stop

