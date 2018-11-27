@extends('adminlte::page')

@section('title', 'Send Emails')

@section('content_header')
    <h1>Send Emails</h1>
@stop

@section('content')	
	<div class="row">
	<div class="col-lg-12">
	<div class="box box-info">
	<div class="box-body">	
		<h3>Send an Email</h3>
		<form id='email_form' action="{{ url(config('adminlte.dashboard_url', '')) }}/emails/new" method="POST">
			{{ csrf_field() }}
			<div class='row'>
				<div class='col-md-2 col-sm-12'>
					<label>Item ID</label><input type='number' name='item_id' id='item_id_email' step='1' min='0' class='form-control' required>
					<br/><button type='button' class='btn btn-info' onclick='fetchUsers()'>Show Users</button>
				</div>
				<div class='col-md-1 col-sm-12'>
					<label>Test Email <br/><input type='checkbox' value='1' name='test' id='test_check' onchange='testEmail()'></label>
				</div>
				<div class='col-md-3 col-sm-12'><label>Email Title</label><input type='text' name='email_title' class='form-control' required></div>
				<div class='col-md-6 col-sm-12'><label>Email Body</label><textarea name='email_body' rows='5' class='form-control' required></textarea></div>
			</div>
			<br/><br/>
			<div class='row'>			
				<div id='user_list' class='col-sm-12'></div>
			</div>
			<br/>
			@php
				if(isset($_SESSION['emails_sent']) and $_SESSION['emails_sent'] <> ''){
					print $_SESSION['emails_sent'].'<br/>';
					$_SESSION['emails_sent'] = '';
				}
			@endphp
			<button id='submit_btn' type='submit' onclick='confirmEmail(event)' class='btn btn-success'>Submit</button> 
			<button id='test_btn' type='button' class='btn btn-danger' onclick='sendTestEmail()'>Send Test Email</button>
		</form>
	</div>
	</div>
	</div>
	</div>
	
	<div class="row">
	<div class="col-lg-12">
	<div class="box box-info">
	<div class="box-body">	
		<h3>Manually Refresh Item</h3>
		<form id='refresh_item' action="{{ url(config('adminlte.dashboard_url', '')) }}/refresh/item" method="GET">
			{{ csrf_field() }}
			<div class='row'>
				<div class='col-sm-12'>
					<label>Item ID</label><input type='number' style='max-width:200px;' name='item_id' id='item_id' step='1' min='0' class='form-control'>
				</div>
				<div class='col-sm-12'>
					<br/><br/><button type='submit' class='btn btn-success'>Update</button> 
				</div>
			</div>
		</form>
	</div>
	</div>
	</div>
	</div>
	
	<div class="row">
	<div class="col-lg-12">
	<div class="box box-success">
	<div class="box-body">
		<h3>Sent Emails</h3>		
		<table id='displayTable' class='table table-striped table-bordered hover dataTable' cellspacing='0' role='grid' width='100%'>
			<thead><tr><th>Date</th><th>User</th><th>Email</th><th>[ID] Item Name</th><th>Title</th></tr></thead>
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
	#test_btn{display:none;}
	</style>
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css">
@stop

@section('js')
	<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
	<script>
	function fetchUsers(){
		$.get("{{ url(config('adminlte.dashboard_url', '')) }}/emails/item/"+$('#item_id_email').val(), function(data){
			$('#user_list').html(data['matches']);
		});
	}
	function testEmail(){
		if($('#test_check').prop('checked')){
			$('#test_btn').show();
		}else{
			$('#test_btn').hide();
		}
	}
	function sendTestEmail(){
		if($('#test_check').prop('checked')){
			$.post("{{ url(config('adminlte.dashboard_url', '')) }}/emails/new", $('#email_form').serialize(), function(data){
				alert('Test Email Sent');
			});
		}
	}
	function confirmEmail(event){
		if(!confirm("Are you sure you want to send the email?")){
			event.preventDefault();
		}else{
			// parameters cannot be empty
			if($('#item_id_email').val() == '' || $('#item_id_email').val() == '' || $('#item_id_email').val() == ''){
				alert('Missing Values');
				event.preventDefault();
			}else{
				var tth = $('#submit_btn');
				if( !tth.hasClass( 'pendingClick' ) ) {
					tth.addClass( 'pendingClick btn-warning' );
					tth.removeClass('btn-success');
					tth.html('Processing...');
					$('#email_form').submit();
				}
			}			
		}
	}
	
	var dtTable;
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
				'url': '{{ url(config('adminlte.dashboard_url', '')) }}/data/emails'
			},
			'columns': [
				{ data: 'date' },
				{ data: 'user' },
				{ data: 'email' },
				{ data: 'item' },
				{ data: 'title' }
			]
		});		
	} );
	</script>
@stop

