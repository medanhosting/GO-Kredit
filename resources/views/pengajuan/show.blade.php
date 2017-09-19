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
						@if(count($breadcrumb)-1 ==$k)
							<li class="breadcrumb-item active">{{ucwords($v['title'])}}</li>
						@else
							<li class="breadcrumb-item"><a href="{{$v['route']}}">{{ucwords($v['title'])}}</a></li>
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
						<h4 class="card-title">Menu Permohonan</h4>
						<nav class="nav flex-column" role="tablist">
							<a href="#overview" class="nav-link active" role="tab" data-toggle="tab" arial-controls="overview" aria-expanded="true">Overview</a>
							<a href="#keluarga" class="nav-link" role="tab" data-toggle="tab" arial-controls="keluarga">Kerabat/Keluarga</a>
							<a href="#survei" class="nav-link" role="tab" data-toggle="tab" arial-controls="survei">Survei</a>
							<a href="#analisa" class="nav-link" role="tab" data-toggle="tab" arial-controls="analisa">Analisa</a>
							<a href="#keputusan" class="nav-link" role="tab" data-toggle="tab" arial-controls="keputusan">Keputusan</a>
							<a href="#realisasi" class="nav-link" role="tab" data-toggle="tab" arial-controls="realisasi">Realisasi</a>
						</nav>
					</div>
				</div>
			</div>
			<div class="col">
				<div class="row mt-4">
					<div class="col">
						<div class="tab-content">
							<div class="tab-pane fade show active" id="overview" role="tabpanel">
								@include ('pengajuan.permohonan.show.overview')
							</div>
							<div class="tab-pane fade" id="keluarga" role="tabpanel">
								@include ('pengajuan.permohonan.show.keluarga')
							</div>
							<div class="tab-pane fade" id="survei" role="tabpanel">
								@include ('pengajuan.survei.show')
							</div>
							<div class="tab-pane fade" id="analisa" role="tabpanel">
								@include ('pengajuan.permohonan.show.analisa')
							</div>
							<div class="tab-pane fade" id="keputusan" role="tabpanel">
								@include ('pengajuan.permohonan.show.keputusan')
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix">&nbsp;</div>
	</div>

	@component ('bootstrap.modal', ['id' => 'delete'])
		{!! Form::open() !!}
		@slot ('title')
			Hapus Data
		@endslot

		@slot ('body')
			<p>Untuk menghapus data ini, silahkan masukkan password dibawah!</p>
			{!! Form::bsPassword(null, 'password', ['placeholder' => 'Password']) !!}
		@endslot

		@slot ('footer')
			<a href="#" data-dismiss="modal" class="btn btn-link text-secondary">Batal</a>
			<a href="#" class="btn btn-danger btn-outline">Tambahkan</a>
		@endslot
		{!! Form::close() !!}
	@endcomponent
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
@endpush