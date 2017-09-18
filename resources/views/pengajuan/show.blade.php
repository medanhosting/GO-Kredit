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
						<a href="#" class="btn btn-link d-block text-left">Overview</a>
						<a href="#" class="btn btn-link d-block text-left">Kerabat/Keluarga</a>
						<a href="#" class="btn btn-link d-block text-left">Survei</a>
						<a href="#" class="btn btn-link d-block text-left">Analisa</a>
						<a href="#" class="btn btn-link d-block text-left">Keputusan</a>
						<a href="#" class="btn btn-link d-block text-left">Realisasi</a>
					</div>
				</div>
				{{-- <div class="card" style="width: 20rem;">
					<ul class="list-group list-group-flush">
						<li class="list-group-item">Cras justo odio</li>
						<li class="list-group-item">Dapibus ac facilisis in</li>
						<li class="list-group-item">Vestibulum at eros</li>
					</ul>
				</div> --}}
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