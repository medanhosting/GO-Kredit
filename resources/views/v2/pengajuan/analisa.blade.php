<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" href="#rknasabah" role="tab">
			Riwayat Kredit Nasabah 
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" href="#fanalisa" role="tab">
			Form Analisa @if(!$checker['analisa']) <span class="text-danger">&nbsp;<i class="fa fa-exclamation"></i></span> @endif
		</a>
	</li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
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

	<div class="tab-pane" id="fanalisa" role="tabpanel">
		<div class="clearfix">&nbsp;</div>
		<div class="clearfix">&nbsp;</div>
		{!! Form::open(['url' => route('pengajuan.analisa.update', ['id' => $permohonan['id'], 'kantor_aktif_id' => $kantor_aktif_id]), 'method' => 'PATCH']) !!}
			<div class="row">
				<div class="col">
					{!! Form::vText('Tanggal Analisa', 'tanggal', $analisa['tanggal'], ['class' => 'form-control mask-date inline-edit text-info', 'placeholder' => 'dd/mm/yyyy hh:mm'], true) !!}
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