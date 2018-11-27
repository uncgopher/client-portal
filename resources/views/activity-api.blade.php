@extends('adminlte::page')

@section('title', 'API Activity')

@section('content_header')
    <h1>API Activity</h1>
@stop

@section('content')
    <div class="row">
	<div class="col-lg-12">
	<div class="box box-success" style='max-width:900px;'>
	<div class="box-body">
		<div style='max-height:400px;max-width:100%;position:relative;'><canvas id="myChart" style='max-height:400px;'></canvas></div>
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
	<script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.min.js'></script>
	<script>
	var chartData;var tester = [0.1, 0.5, 1.0, 2.0, 1.5, 0];
	$.get('{{ url(config('adminlte.dashboard_url', '')) }}/data/activity-api', function(tempData){
		var ctx = document.getElementById("myChart").getContext('2d');
		var myChart = new Chart(ctx, {
			type: 'bar',
			data: {
				datasets: [{
					data: tempData.users,
					label: 'Users',
					borderColor: 'rgba(0,0,0,.5)',
					yAxisID: 'left-y-axis',
					type: 'line',
					fill: false
				}, {
					data: tempData.matches,
					label: 'Matches',
					backgroundColor: 'rgba(32,178,170,.5)',
					borderColor: 'rgb(34, 139, 34)',
					yAxisID: 'right-y-axis',
				}],
				labels: tempData.dataDate,
			},
			options: {
				scales: {
					yAxes: [{
						id: 'left-y-axis',
						type: 'linear',
						position: 'left',
						gridLines: {
							display: false
						}
					}, {
						id: 'right-y-axis',
						type: 'linear',
						position: 'right'
					}],
										
				}
			}
		});
	}, 'json');	
	</script>
@stop

