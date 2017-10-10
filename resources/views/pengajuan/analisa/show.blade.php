@push('main')
	<div class="container bg-white bg-shadow p-4">
		<div class="row">
			<div class="col">
				<h4 class='mb-2 text-style text-secondary'>
					<span class="text-uppercase">ANALISA KREDIT</span> 
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
			<div class="col-6">
				<!-- Nav tabs -->
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" data-toggle="tab" href="#overview" role="tab">
							Overview
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#hasil_survei" role="tab">
							Laporan Survei
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#rknasabah" role="tab">
							Riwayat Kredit Nasabah 
						</a>
					</li>
				</ul>
				
				<!-- Tab panes -->
				<div class="tab-content">
					<div class="tab-pane active" id="overview" role="tabpanel">
						@include('pengajuan.analisa.permohonan_kredit')

						@if(!is_null($analisa['id']))
							<div class="row">
								<div class="col-sm-12 text-right">
									Analisa Sudah Lengkap.
									<a data-toggle="modal" data-target="#ajukan-putusan" data-action="{{route('pengajuan.pengajuan.assign_putusan', ['id' => $permohonan['id'], 'kantor_aktif_id' => $kantor_aktif['id'], 'status' => 'analisa'])}}" class="modal_putusan text-success">
										<i>Ajukan Ke Komite Kredit</i>
									</a> 
								</div>
							</div>
						@endif
					</div>

					<div class="tab-pane" id="hasil_survei" role="tabpanel">
						@include('pengajuan.analisa.survei_report')
						<div class="row">
							<div class="col-sm-12 text-right">
								<a href="{{route('pengajuan.pengajuan.print', ['id' => $survei['pengajuan_id'], 'mode' => 'survei_report', 'kantor_aktif_id' => $kantor_aktif['id']])}}" target="__blank">Tampilkan Penuh Layar</a>
							</div>
						</div>
					</div>

					<div class="tab-pane" id="rknasabah" role="tabpanel">
						<div class="clearfix">&nbsp;</div>
						<div class="row">
							<div class="col-sm-12">
								<table class="table table-sm table-bordered" style="">
									<thead class="thead-default">
										<tr>
											<th style="border:1px #aaa solid" class="text-center">#</th>
											<th style="border:1px #aaa solid" class="text-center">Tanggal Pengajuan</th>
											<th style="border:1px #aaa solid" class="text-center">Pokok Pinjaman</th>
											<th style="border:1px #aaa solid" class="text-center">Status Terakhir</th>
										</tr>
									</thead> 
									<tbody>
										@forelse ($r_nasabah as $k => $v)
										<tr>
											<td style="border:1px #aaa solid" class="text-center">{{ $v['id'] }}</td>
											<td style="border:1px #aaa solid" class="text-center">{{ $v['status_permohonan']['tanggal'] }}</td>
											<td style="border:1px #aaa solid" class="text-center">{{ $v['pokok_pinjaman'] }}</td>
											<td style="border:1px #aaa solid" class="text-center">{{ $v['status_terakhir']['status'] }}</td>
										</tr>
										@empty
											<tr>
												<td style="border:1px #aaa solid" colspan="4" class="text-center"><i class="text-secondary">tidak ada data</i></td>
											</tr>
										@endforelse
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-6">
				<div class="clearfix">&nbsp;</div>
				<div class="clearfix">&nbsp;</div>
				{!! Form::open(['url' => route('pengajuan.analisa.update', ['id' => $permohonan['id'], 'kantor_aktif_id' => $kantor_aktif_id]), 'method' => 'PATCH']) !!}
					<div class="row">
						<div class="col">
							{!! Form::vText('Tanggal Analisa', 'tanggal', $analisa['tanggal'], ['class' => 'form-control mask-date-time inline-edit text-info', 'placeholder' => 'dd/mm/yyyy hh:mm'], true) !!}
						</div>
					</div>
					<div class="row">
						<div class="col">
							{!! Form::vSelect('Character', 'character', ['sangat_baik' => 'Sangat Baik', 'baik' => 'Baik', 'cukup_baik' => 'Cukup Baik', 'tidak_baik' => 'Tidak Baik','buruk' => 'Buruk'], $analisa['character'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>
					<div class="row">
						<div class="col">
							{!! Form::vSelect('Condition', 'condition', ['sangat_baik' => 'Sangat Baik', 'baik' => 'Baik', 'cukup_baik' => 'Cukup Baik', 'tidak_baik' => 'Tidak Baik','buruk' => 'Buruk'], $analisa['condition'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>
					<div class="row">
						<div class="col">
							{!! Form::vSelect('Capacity', 'capacity', ['sangat_baik' => 'Sangat Baik', 'baik' => 'Baik', 'cukup_baik' => 'Cukup Baik', 'tidak_baik' => 'Tidak Baik','buruk' => 'Buruk'], $analisa['capacity'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>
					<div class="row">
						<div class="col">
							{!! Form::vSelect('Capital', 'capital', ['sangat_baik' => 'Sangat Baik', 'baik' => 'Baik', 'cukup_baik' => 'Cukup Baik', 'tidak_baik' => 'Tidak Baik','buruk' => 'Buruk'], $analisa['capital'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>
					<div class="row">
						<div class="col">
							{!! Form::vSelect('Collateral', 'collateral', ['sangat_baik' => 'Sangat Baik', 'baik' => 'Baik', 'cukup_baik' => 'Cukup Baik', 'tidak_baik' => 'Tidak Baik','buruk' => 'Buruk'], $analisa['collateral'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>
					<div class="row">
						<div class="col">
							{!! Form::vSelect('Jenis Pinjaman', 'jenis_pinjaman', ['pa' => 'PA', 'pt' => 'PT'], $analisa['jenis_pinjaman'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>
					<div class="row">
						<div class="col">
							{!! Form::vText('Suku Bunga', 'suku_bunga', $analisa['suku_bunga'], ['class' => 'form-control inline-edit text-info', 'placeholder' => '0.4'], true) !!}
						</div>
					</div>
					<div class="row">
						<div class="col">
							{!! Form::vText('Jangka Waktu', 'jangka_waktu', $analisa['jangka_waktu'], ['class' => 'form-control inline-edit text-info', 'placeholder' => '12'], true) !!}
						</div>
					</div>
					<!-- <div class="row">
						<div class="col">
							{!! Form::vText('Limit Angsuran', 'limit_angsuran', $analisa['limit_angsuran'], ['class' => 'form-control inline-edit text-info mask-money', 'placeholder' => 'Rp 6.000.000'], true) !!}
						</div>
					</div>
					<div class="row">
						<div class="col">
							{!! Form::vText('Limit Jangka Waktu', 'limit_jangka_waktu', $analisa['limit_jangka_waktu'], ['class' => 'form-control inline-edit text-info', 'placeholder' => '12'], true) !!}
						</div>
					</div> -->

					<div class="row">
						<div class="col">
							{!! Form::vText('Kredit Diusulkan', 'kredit_diusulkan', $analisa['kredit_diusulkan'], ['class' => 'form-control inline-edit text-info mask-money', 'placeholder' => 'Rp 6.000.000'], true) !!}
						</div>
					</div>

					<div class="row">
						<div class="col">
							{!! Form::vText('Angsuran Pokok', 'angsuran_pokok', $analisa['angsuran_pokok'], ['class' => 'form-control inline-edit text-info mask-money', 'placeholder' => 'Rp 500.000'], true) !!}
						</div>
					</div>

					<div class="row">
						<div class="col">
							{!! Form::vText('Angsuran Bunga', 'angsuran_bunga', $analisa['angsuran_bunga'], ['class' => 'form-control inline-edit text-info mask-money', 'placeholder' => 'Rp 30.000'], true) !!}
						</div>
					</div>
				{!! Form::bsSubmit('Simpan', ['class' => 'btn btn-primary float-right mr-3']) !!}
				{!! Form::close() !!}
				
			</div>
		</div>
		<div class="clearfix">&nbsp;</div>
	</div>
	@include('pengajuan.ajax.modal_putusan')
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush

@push('css')
@endpush
@push ('js')
@endpush