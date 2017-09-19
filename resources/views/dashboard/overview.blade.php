@push('main')
	<div class="container bg-white p-4 bg-shadow">
		<div class="row">
			<div class="col">
				<h4 class='mb-4 text-style text-uppercase text-secondary'>
					Pengajuan Kredit
				</h4>
			</div>
		</div>
		<div class="row align-items-center">
			<div class="col">
				<div class="card text-center border-0" style="height: 100px;">
					<div class="card-body p-2">
						<a href="{{ route('pengajuan.pengajuan.index', ['status' => 'permohonan', 'kantor_aktif_id' => $kantor_aktif_id]) }}" class="card-link text-style">
							<i class="d-block fa fa-gear fa-2x"></i>
							Permohonan
						</a>
					</div>
				</div>
			</div>
			<div class="arrow"><i class="fa fa-arrow-right fa-2x"></i></div>
			<div class="col">
				<div class="card text-center border-0" style="height: 100px;">
					<div class="card-body p-2">
						<a href="{{ route('pengajuan.pengajuan.index', ['status' => 'survei']) }}" class="card-link text-style">
							<i class="d-block fa fa-home fa-2x"></i>
							Survei
						</a>
					</div>
				</div>
			</div>
			<div class="arrow"><i class="fa fa-arrow-right fa-2x"></i></div>
			<div class="col">
				<div class="card text-center border-0" style="height: 100px;">
					<div class="card-body p-2">
						<a href="{{ route('pengajuan.pengajuan.index', ['status' => 'analisa']) }}" class="card-link text-style">
							<i class="d-block fa fa-home fa-2x"></i>
							Analisa
						</a>
					</div>
				</div>
			</div>
			<div class="arrow"><i class="fa fa-arrow-right fa-2x"></i></div>
			<div class="col">
				<div class="card text-center border-0" style="height: 100px;">
					<div class="card-body p-2">
						<a href="{{ route('pengajuan.pengajuan.index', ['status' => 'analisa']) }}" class="card-link text-style">
							<i class="d-block fa fa-home fa-2x"></i>
							Keputusan
						</a>
					</div>
				</div>
			</div>
			<div class="col">
				<div class="row">
					<div class="arrow text-gray-dark"><i class="fa fa-arrow-right fa-2x"></i></div>
					<div class="col-12">
						<div class="card text-center border-0" style="height: 100px;">
							<div class="card-body p-2">
								<a href="{{ route('pengajuan.pengajuan.index', ['status' => 'setuju']) }}" class="card-link text-style">
									<i class="d-block fa fa-home fa-2x"></i>
									Setujui
								</a>
							</div>
						</div>
					</div>
					<div class="arrow"><i class="fa fa-arrow-right fa-2x"></i></div>
					<div class="col-12">
						<div class="card text-center border-0" style="height: 100px;">
							<div class="card-body p-2">
								<a href="{{ route('pengajuan.pengajuan.index', ['status' => 'tolak']) }}" class="card-link text-style">
									<i class="d-block fa fa-home fa-2x"></i>
									Tolak
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col">
				<div class="row align-items-start">
					<div class="arrow"><i class="fa fa-arrow-right fa-2x"></i></div>
					<div class="col-12">
						<div class="card text-center border-0" style="height: 100px;">
							<div class="card-body p-2">
								<a href="{{ route('pengajuan.pengajuan.index', ['status' => 'realisasi']) }}" class="card-link text-style">
									<i class="d-block fa fa-home fa-2x"></i>
									Realisasi
								</a>
							</div>
						</div>
					</div>
					<div class="arrow"><i class="fa fa-arrow-right fa-2x"></i></div>
					<div class="col-12">
						<div class="card text-center border-0" style="height: 100px;">
							<div class="card-body p-2">
								<a href="{{ route('pengajuan.pengajuan.index', ['status' => 'expired']) }}" class="card-link text-style">
									<i class="d-block fa fa-home fa-2x"></i></h4>
									Expired
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	@if($is_holder)
	<div class="container bg-white p-4 bg-shadow">
		<div class="row">
			<div class="col">
				<h4 class='mb-4 text-style text-uppercase text-secondary'>
					Manajemen Kantor
				</h4>
			</div>
		</div>
		<div class="row align-items-center">
			<div class="col">
				<div class="card text-center border-0" style="height: 100px;">
					<div class="card-body p-2">
						<a href="{{ route('manajemen.kantor.create', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="card-link text-style">
							<i class="d-block fa fa-building-o fa-2x"></i>
							Kantor Baru
						</a>
					</div>
				</div>
			</div>
			<div class="arrow"><i class="fa fa-arrow-right fa-2x"></i></div>
			<div class="col">
				<div class="card text-center border-0" style="height: 100px;">
					<div class="card-body p-2">
						<a href="{{ route('manajemen.kantor.index', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="card-link text-style">
							<i class="d-block fa fa-building fa-2x"></i>
							Semua Kantor
						</a>
					</div>
				</div>
			</div>
			<div class="arrow"><i class="fa fa-arrow-right fa-2x"></i></div>
			<div class="col">
				<div class="card text-center border-0" style="height: 100px;">
					<div class="card-body p-2">
						<a href="{{ route('manajemen.karyawan.create', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="card-link text-style">
							<i class="d-block fa fa-user-o fa-2x"></i>
							Karyawan Baru
						</a>
					</div>
				</div>
			</div>
			<div class="arrow"><i class="fa fa-arrow-right fa-2x"></i></div>
			<div class="col">
				<div class="card text-center border-0" style="height: 100px;">
					<div class="card-body p-2">
						<a href="{{ route('manajemen.karyawan.index', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="card-link text-style">
							<i class="d-block fa fa-users fa-2x"></i>
							Semua Karyawan
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	@endif
@endpush