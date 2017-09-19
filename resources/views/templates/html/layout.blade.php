<!doctype html>
<html lang="{{ app()->getLocale() }}">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>{{ $html['title'] }}</title>
		<link rel="stylesheet" href="{{ mix('css/app.css') }}">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
		@stack('css')
	</head>
	<body class=''>
		<nav class="navbar navbar-expand navbar-dark bg-success text-white main">
			<div class="collapse navbar-collapse" id="navbarTogglerDemo01">
				<a class="navbar-brand" href="{{ route('home') }}">{{ config('app.name') }}</a>
				<ul class="navbar-nav mr-auto mt-lg-0">
					<li class="nav-item btn btn-outline-primary {{ str_is('dashboard', $active_menu) ? 'active' : '' }}">
						<a class="nav-link" href="#" data-toggle='modal' data-target='#select_social_media'>
							@if ($active_account)
								{!! Form::bsIcon($active_account->type) !!} {{ $active_account->name }}
							@else
								<i class="fa fa-building-o"></i>&nbsp; {{$kantor_aktif['nama']}} &nbsp;&nbsp;
							@endif
							<i class='fa fa-caret-down'></i>
						</a>
					</li>
				</ul>
				<ul class="navbar-nav ml-auto mt-lg-0">
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<div class='d-none d-sm-inline'>
								<i class="fa fa-user-circle"></i>&nbsp; {{ $me->email }} &nbsp;&nbsp;&nbsp;
							</div>
							<span class='d-sm-none'><i class='fa fa-user'></i></span>
						</a>
						<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
							<a class="dropdown-item" href="{{ route('password.get', ['kantor_aktif_id' => $kantor_aktif['id']]) }}">Ganti Password</a>
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="{{ route('logout') }}">Logout</a>
						</div>
					</li>
				</ul>
			</div>
		</nav>

		@stack('submenu')
		@include('templates.alerts.alert')

		<div class="container-fluid mt-4 pb-5">
			<div class="row">
				<div class="col-12">
					@stack('main')
				</div>
			</div>
		</div>


		<footer class='pt-5 pb-3 align-items-end'>
			<div class="container">
				<div class="row">
					<div class="col text-center">
					  	{{ config('app.name') }}.com &copy; {{ date('Y') }} - Developed by <a href='http://thunderlab.id' target='_blank'>Thunderlab.id</a>
					</div>
				</div>
			</div>
		</footer>		

		<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.min.js"></script>
		<script src="{{ mix('js/app.js') }}"></script>

		<script>
			$(document).ready(function(){
				
				// Daterange picker
				$(".daterangepicker").flatpickr({
					dateFormat: "d-m-Y",
					mode: 'range'
				});

				// Chart
				var ctx = document.getElementsByClassName('chartjs');
				for (var i = 0; i < ctx.length; i++)
				{
					new Chart(ctx[i].getContext('2d'), {
															type: ctx[i].getAttribute('data-chart-type'),
															data: {
																labels: JSON.parse(ctx[i].getAttribute('data-chart-label')),
																datasets: JSON.parse(ctx[i].getAttribute('data-chart-dataset'))
															},
															options: {}
														}
					);
				}

			});
		</script>
		
		@stack('js')

		{{-- SHOW MY ACCOUNT --}}
		<div class="modal fade" id="select_social_media" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">BPR / Koperasi</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="form-row">
							@php
								$all = request()->all();
							@endphp
							@foreach ($kantor as $x)
								@if (in_array(strtolower($x['jenis']), ['bpr', 'koperasi']))
									@php
										$all['kantor_aktif_id'] = $x['id'];
									@endphp
									<div class="col-6 col-sm-4 col-lg-3">
										<a href="{{request()->url().'?'.http_build_query($all)}}" class="btn 

											@if($kantor_aktif['id']==$x['id']) btn-info disabled @else btn-primary @endif col-12">
											{{ $x['nama'] }}
										</a>
									</div>
								@else
									<div class="col-6 col-sm-4 col-lg-3">
										<a href="{{route('home', ['kantor_aktif_id' => $x['id']])}}" class="btn btn-primary col-12">
											{{ $x['nama'] }}
										</a>
									</div>
									@php $is_holder = true; @endphp
								@endif
							@endforeach
						</div>
					</div>
					<div class="modal-footer">
						<!-- @if($is_holder)
							<a href="{{ route('home') }}" class="btn btn-primary">
								<i class='fa fa-plus'></i> 
								Kantor Baru
							</a>
						@else
							<a href="#" class="btn btn-disabled">
								<i class='fa fa-plus'></i> 
								Kantor Baru
							</a>
						@endif -->
					</div>
				</div>
			</div>
		</div>

	</body>
</html>
