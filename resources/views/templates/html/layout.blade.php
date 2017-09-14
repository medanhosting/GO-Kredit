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
								<i class="fa fa-building-o"></i>&nbsp; Cabang &nbsp;&nbsp;
							@endif
							<i class='fa fa-caret-down'></i>
						</a>
					</li>
				</ul>
				<ul class="navbar-nav ml-auto mt-lg-0">
					{{-- <li class="nav-item"><a class="nav-link" href="{{ route('social_media.index') }}"'><i class='fa fa-plus-circle'></i><span class='d-none d-md-'>Add Account</span></a></li> --}}
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<div class='d-none d-sm-inline'>
								<i class="fa fa-user-circle"></i>&nbsp; {{ $me->email }} &nbsp;&nbsp;&nbsp;
							</div>
							<span class='d-sm-none'><i class='fa fa-user'></i></span>
						</a>
						<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
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
						<h5 class="modal-title" id="exampleModalLabel">My Social Media</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="form-row">
							@foreach ($my_social_media as $x)
								<div class="col-6 col-sm-4 col-lg-3">
									@if ($x->is_instagram)
										<a href='{{ route('social_media.instagram', ['account_id' => $x->id]) }}' class='btn btn-block bg-{{ $x->type }}'>
											{!! Form::bsIcon($x->type, 'fa-4x') !!}
											<p class='mt-2 mb-0 pb-0'>{{ $x->name }}</p>
										</a>
									@endif
								</div>
							@endforeach
						</div>
					</div>
					<div class="modal-footer">
						<a href="{{ route('social_media.index') }}" class="btn btn-primary"><i class='fa fa-plus'></i> Add Account</a>
					</div>
				</div>
			</div>
		</div>

	</body>
</html>
