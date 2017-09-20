@push('main')
	<div class="container bg-white bg-shadow p-4">
		<div class="row">
			<div class="col">
				<h4 class='mb-2 text-style text-secondary'>
					<span class="text-uppercase">Kredit {{ $permohonan['id'] }}</span> 
				</h4>
			</div>
		</div>
		<div class="row ml-0 mr-0">
			<div class="col p-0">
				<ol class="breadcrumb" style="border-radius:0;">
					@foreach($breadcrumb as $k => $v)
						@if ($loop->count - 1 == $k)
							<li class="breadcrumb-item active">{{ ucwords($v['title']) }}</li>
						@else
							<li class="breadcrumb-item"><a href="{{ $v['route'] }}">{{ ucwords($v['title']) }}</a></li>
						@endif
					@endforeach
				</ol>
			</div>
		</div>
		<div class="row">
			<div class="col-3">
				@stack('menu_sidebar')
				<div class="card text-left">
					<div class="card-body">
						<h4 class="card-title">Menu</h4>
						<nav class="nav flex-column" role="tablist">
							<a href="#" class="nav-link active" data-toggle="paneltab" data-target="#overview">Overview</a>
							<a href="#" class="nav-link" data-toggle="paneltab" data-target="#keluarga">Kerabat/Keluarga</a>
							<a href="#" class="nav-link" data-toggle="paneltab" data-target="#survei">Survei</a>
							<a href="#" class="nav-link" data-toggle="paneltab" data-target="#analisa">Analisa</a>
							<a href="#" class="nav-link" data-toggle="paneltab" data-target="#keputusan">Keputusan</a>
							<a href="#" class="nav-link" data-toggle="paneltab" data-target="#realisasi">Realisasi</a>
						</nav>
					</div>
				</div>
			</div>
			<div class="col">
				<div class="row mt-4">
					<div class="col">
						<div class="tab-content paneltab-content">
							<div class="tab-pane paneltab-pane show active" id="overview" role="tabpanel">
								@include ('pengajuan.permohonan.show.overview')
							</div>
							<div class="tab-pane paneltab-pane" id="keluarga" role="tabpanel">
								@include ('pengajuan.permohonan.show.keluarga')
							</div>
							<div class="tab-pane paneltab-pane" id="survei" role="tabpanel">
								@include ('pengajuan.survei.index')
							</div>
							<div class="tab-pane paneltab-pane" id="analisa" role="tabpanel">
								@include ('pengajuan.permohonan.show.analisa')
							</div>
							<div class="tab-pane paneltab-pane" id="keputusan" role="tabpanel">
								@include ('pengajuan.permohonan.show.keputusan')
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix">&nbsp;</div>
	</div>
@endpush

@push('submenu')
	<div class="container-fluid bg-light" style="background-color: #eee !important;">
		<div class="row">
			<div class="col">
				<nav class="nav">
					<a href="{{ route('home', ['kantor_aktif_id' => request()->get('kantor_aktif_id')]) }}" class="nav-link text-secondary">Menu Utama</a>
					<a href="#" class="nav-link text-secondary">Simulasi Kredit</a>
				</nav>
			</div>
		</div>
	</div>
@endpush

@push ('js')
	<script>
		function togglePanelTab (parent, target, className) {
			$(parent).find(className).hide();
			$(parent).find(target).fadeIn();
		}
		function toggleActiveClass (parent, target) {
			$(parent).find('.active').removeClass('active');
			$(parent).find('a[data-target="'+ target +'"]').addClass('active');
		}

		$(document).on('click', 'a[data-toggle="paneltab"]', function (event){
			event.preventDefault();

			selectorTarget = $(this).attr('data-target');
			elementParentNode = this.parentNode;
			targetParentNode = $(selectorTarget)[0].parentNode;

			toggleActiveClass (elementParentNode, selectorTarget);
			togglePanelTab (targetParentNode, selectorTarget, '.paneltab-pane');
		})

		$(document).on('click', 'a[data-toggle="panel-toggle"]', function (event) {
			event.preventDefault();

			selectorTarget = $(this).attr('data-target');
			targetParentNode = $(selectorTarget)[0].parentNode;

			togglePanelTab (targetParentNode, selectorTarget, '.panel-toggle-pane');
		})
	</script>
@endpush