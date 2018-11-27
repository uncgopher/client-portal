@extends('adminlte::page')

@section('title', 'User Stats')

@section('content_header')
    <h1>User Stats</h1>
@stop

@section('content')
    <div class="row max1000">
		<div class="col-md-3 col-sm-6 col-xs-12">
		<div class="info-box">
			<span class="info-box-icon bg-green"><i class="fa fa-user"></i></span>
			<div class="info-box-content">
			  <h4 class="studyOptionHeader">New Today</h4>
			  <span id="new" class="info-box-number">Loading</span>
			</div>
		</div>
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12">
		<div class="info-box">
			<span class="info-box-icon bg-aqua"><i class="fa fa-user"></i></span>
			<div class="info-box-content">
			  <h4 class="studyOptionHeader">Active Today</h4>
			  <span id="active" class="info-box-number">Loading</span>
			</div>
		</div>
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12">
		<div class="info-box">
			<span class="info-box-icon bg-green"><i class="fa fa-users"></i></span>
			<div class="info-box-content">
			  <h4 class="studyOptionHeader">Weekly New</h4>
			  <span id="new7" class="info-box-number">Loading</span>
			</div>
		</div>
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12">
		<div class="info-box">
			<span class="info-box-icon bg-aqua"><i class="fa fa-users"></i></span>
			<div class="info-box-content">
			  <h4 class="studyOptionHeader">Weekly Active</h4>
			  <span id="active7" class="info-box-number">Loading</span>
			</div>
		</div>
		</div>
	</div>
	<div class="row max1000">
		<div class="col-md-4 col-sm-6 col-xs-12">
		<div class="info-box">
			<span class="info-box-icon bg-aqua"><i class="fa fa-calendar"></i></span>
			<div class="info-box-content">
			  <h4 class="studyOptionHeader">Yesterday's DAU</h4>
			  <span id="dau" class="info-box-number">Loading</span>
			</div>
		</div>
		</div>
		<div class="col-md-4 col-sm-6 col-xs-12">
		<div class="info-box">
			<span class="info-box-icon bg-aqua"><i class="fa fa-calendar"></i></span>
			<div class="info-box-content">
			  <h4 class="studyOptionHeader">Last 30 Days MAU</h4>
			  <span id="mau" class="info-box-number">Loading</span>
			</div>
		</div>
		</div>
		<div class="col-md-4 col-sm-6 col-xs-12">
		<div class="info-box">
			<span class="info-box-icon bg-aqua"><i class="fa fa-bolt"></i></span>
			<div class="info-box-content">
			  <h4 class="studyOptionHeader">Stickiness</h4>
			  <span id="sticky" class="info-box-number">Loading</span>
			</div>
		</div>
		</div>
	</div>
    <div class="row max1000">
		<div class="col-md-3 col-sm-6 col-xs-12">
		<div class="info-box">
			<span class="info-box-icon bg-yellow"><i class="fa fa-link"></i></span>
			<div class="info-box-content">
			  <h4 class="studyOptionHeader">iTunes</h4>
			  <span id="short1" class="info-box-number">Loading</span>
			</div>
		</div>
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12">
		<div class="info-box">
			<span class="info-box-icon bg-yellow"><i class="fa fa-link"></i></span>
			<div class="info-box-content">
			  <h4 class="studyOptionHeader">GooglePlay</h4>
			  <span id="short2" class="info-box-number">Loading</span>
			</div>
		</div>
		</div>
	</div>
@stop

@section('css')
    <style>
	.dt-right{text-align:right;}
	th.dt-right{text-align:left !important;}
	.max1000{max-width:1000px;}
	</style>
@stop

@section('js')
	<script>
	$(document).ready(function() {
		$.get('{{ url(config('adminlte.dashboard_url', '')) }}/data/stats', function(tempData){
			$('#new').html(tempData['new']);
			$('#new7').html(tempData['new7']);
			$('#active').html(tempData['active']);
			$('#active7').html(tempData['active7']);
			
			$('#dau').html(tempData['dau']);
			$('#mau').html(tempData['mau']);
			$('#sticky').html(tempData['sticky']+'%');
			
			$('#short1').html(tempData['short1']);
			$('#short2').html(tempData['short2']);
		}, 'json');
	});	
	</script>
@stop

