@php
	// dd($permohonan);
@endphp
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
			<div class="col bg-gray mb-4" style="background-color: #efefef;">&nbsp;</div>
		</div>
		<div class="row">
			<div class="col-3">
				@stack('menu_sidebar')
				<div class="card text-left">
					<div class="card-body">
						<h4 class="card-title">Menu Permohonan</h4>
						<nav class="nav flex-column" role="tablist">
							<a href="#overview" id="overview-tab" class="nav-link active" role="tab" data-toggle="tab" arial-controls="overview" aria-expanded="true">Overview</a>
							<a href="#keluarga" id="keluarga-tab" class="nav-link" role="tab" data-toggle="tab" arial-controls="keluarga" aria-expanded="true">Kerabata/Keluarga</a>
							<a href="#survei" id="survei-tab" class="nav-link" role="tab" data-toggle="tab" arial-controls="survei" aria-expanded="true">Survei</a>
							<a href="#analisa" id="analisa-tab" class="nav-link" role="tab" data-toggle="tab" arial-controls="analisa" aria-expanded="true">Analisa</a>
							<a href="#keputusan" id="keputusan-tab" class="nav-link" role="tab" data-toggle="tab" arial-controls="keputusan" aria-expanded="true">Keputusan</a>
							<a href="#realisasi" id="realisasi-tab" class="nav-link" role="tab" data-toggle="tab" arial-controls="realisasi" aria-expanded="true">Realisasi</a>
						</nav>
					</div>
				</div>
			</div>
			<div class="col">
				@include('pengajuan.permohonan.kredit.detail')
				@stack('content')
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
					<a href="{{ route('home') }}" class="nav-link text-secondary">Menu Utama</a>
					<a href="#" class="nav-link text-secondary">Simulasi Kredit</a>
				</nav>
			</div>
		</div>
	</div>
@endpush