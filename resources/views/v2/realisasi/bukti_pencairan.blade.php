@inject('idr', 'App\Service\UI\IDRTranslater')
@inject('tanggal', 'App\Service\UI\TanggalTranslater')
@inject('carbon', 'Carbon\Carbon')

<div class="row">
	<div class="col-6 text-left">
		<h3 class="mb-2">{{strtoupper($kantor_aktif['nama'])}}</h3>
		<p class="mb-0"><i class="fa fa-building-o fa-fw"></i>&nbsp; {{implode(' ', $kantor_aktif['alamat'])}}</p>
		<p class="mb-0"><i class="fa fa-phone fa-fw"></i>&nbsp; {{$kantor_aktif['telepon']}}</p>
	</div>
	<div class="col-6 text-right">
		<div class="row justify-content-end">
			<div class="col-2">Nomor</div>
			<div class="col-6">{{$kantor_aktif['id']}} / {{$putusan['pengajuan_id']}}</div>
		</div>
		<div class="row justify-content-end">
			<div class="col-2">Tanggal</div>
			<div class="col-6">{{$putusan['pengajuan']['status_realisasi']['tanggal']}}</div>
		</div>
	</div>
</div>
<div class="clearfix">&nbsp;</div>
<div class="clearfix">&nbsp;</div>
<div class="row">
	<div class="col text-center">
		<h1 class="mb-1">BUKTI REALISASI KREDIT</h1>
	</div>
</div>
<hr/>
@php
	{{--  dd($putusan);  --}}
@endphp
<div class="clearfix">&nbsp;</div>
<div class="row justify-content-center">
	<div class="col-8">
		{!! Form::open(['url' => route('realisasi.update', ['id' => $putusan['pengajuan_id'], 'kantor_aktif_id' => $kantor_aktif_id]), 'method' => 'PATCH']) !!}
			{!! Form::vText('Tanggal Realisasi', 'tanggal_pencairan', $putusan['tanggal'], ['class' => 'form-control inline-edit border-input text-info  mask-date pb-1 w-50', 'placeholder' => 'dd/mm/yyyy hh:mm'], true) !!}
			<div class="clearfix">&nbsp;</div>
			<div class="row form-group">
				<div class="col offset-sm-4">
					<a href="#" class="btn btn-primary" data-toggle="modal" data-target="#nota-realisasi">Simpan</a>
				</div>
			</div>

			@component('bootstrap.modal', [
				'id' 	=> 'nota-realisasi',
				'size'	=> 'modal-xl'
			])
				@slot('title')
					Nota Bukti Realisasi
				@endslot

				@slot('body')
					<div class="row">
						<div class="col-6 text-left">
							<h3 class="mb-2">{{strtoupper($kantor_aktif['nama'])}}</h3>
							<p class="mb-0"><i class="fa fa-building-o fa-fw"></i>&nbsp; {{implode(' ', $kantor_aktif['alamat'])}}</p>
							<p class="mb-0"><i class="fa fa-phone fa-fw"></i>&nbsp; {{$kantor_aktif['telepon']}}</p>
						</div>
						<div class="col-6 text-right">
							<div class="row justify-content-end">
								<div class="col-2">Nomor</div>
								<div class="col-6">{{$kantor_aktif['id']}} / {{$putusan['pengajuan_id']}}</div>
							</div>
							<div class="row justify-content-end">
								<div class="col-2">Tanggal</div>
								<div class="col-6">{{$putusan['pengajuan']['status_realisasi']['tanggal']}}</div>
							</div>
						</div>
					</div>
					<div class="clearfix">&nbsp;</div>
					<div class="clearfix">&nbsp;</div>
					<div class="row">
						<div class="col text-center">
							<h4 class="mb-1">BUKTI REALISASI KREDIT</h4>
						</div>
					</div>
					<hr class="mb-1">
					<hr>

					<p>Sudah terima dari {{ strtoupper($kantor_aktif['nama']) }} sejumlah uang dibawah ini untuk direalisasi kredit:</p>

					<div class="row">
						<div class="col-6">
							<div class="row">
								<div class="col-3">
									<p class="mb-1 ml-2">Nomor Rekening</p>
								</div>
								<div class="col">
									<p class="mb-1">: </p>
								</div>
							</div>
							<div class="row">
								<div class="col-3">
									<p class="mb-1 ml-2">Nama</p>
								</div>
								<div class="col">
									<p class="mb-1">: {{ $putusan['pengajuan']['nasabah']['nama'] }}</p>
								</div>
							</div>
							<div class="row">
								<div class="col-3">
									<p class="mb-1 ml-2">Alamat</p>
								</div>
								<div class="col">
									<p class="mb-1">: {{ implode(' ', $putusan['pengajuan']['nasabah']['alamat']) }}</p>
								</div>
							</div>
							<div class="row">
								<div class="col-3">
									<p class="mb-1 ml-2">Jaminan</p>
								</div>
								<div class="col">
									<p class="mb-1">: </p>
								</div>
							</div>
						</div>
						<div class="col-6">
							<div class="row">
								<div class="col-3">
									<p class="mb-1 ml-2">No. SPK</p>
								</div>
								<div class="col">
									<p class="mb-1">: </p>
								</div>
							</div>
							<div class="row">
								<div class="col-3">
									<p class="mb-1 ml-2">Usaha</p>
								</div>
								<div class="col">
									<p class="mb-1">: </p>
								</div>
							</div>
							<div class="row">
								<div class="col-3">
									<p class="mb-1 ml-2">Jenis Pinjaman</p>
								</div>
								<div class="col">
									<p class="mb-1">: </p>
								</div>
							</div>
							<div class="row">
								<div class="col-3">
									<p class="mb-1 ml-2">Nama AO</p>
								</div>
								<div class="col">
									<p class="mb-1">: {{ $putusan['pengajuan']['ao']['nama'] }}</p>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-3">
							<p class="mb-1 ml-2">Nominal</p>
						</div>
						<div class="col">
							<p class="mb-1">: {{ $idr->formatMoneyTo($putusan['pengajuan']['pokok_pinjaman']) }}</p>
						</div>
					</div>
					<div class="row">
						<div class="col-3">
							<p class="mb-1 ml-2">Terbilang</p>
						</div>
						<div class="col">
							<p class="mb-1">: {{ $idr->terbilang($putusan['pengajuan']['pokok_pinjaman']) }}</p>
						</div>
					</div>
					<div class="row">
						<div class="col-6">
							<div class="row">
								<div class="col-3">
									<p class="mb-1 ml-2">Jangka Waktu</p>
								</div>
								<div class="col">
									<p class="mb-1">: {{ $putusan['jangka_waktu'] }}</p>
								</div>
							</div>
							<div class="row">
								<div class="col-3">
									<p class="mb-1 ml-2">Angsuran per Bulan</p>
								</div>
								<div class="col">
									<p class="mb-1">: {{ $putusan['kemampuan_angsur'] }}</p>
								</div>
							</div>
						</div>
						<div class="col-6">
							<div class="row">
								<div class="col-3">
									<p class="mb-1 ml-2">Suku Bunga</p>
								</div>
								<div class="col">
									<p class="mb-1">: {{ $putusan['suku_bunga'] }} %</p>
								</div>
							</div>
							<div class="row">
								<div class="col-3">
									<p class="mb-1 ml-2">Tgl Bayar</p>
								</div>
								<div class="col">
									<p class="mb-1">: </p>
								</div>
							</div>
						</div>
					</div>


					{!! Form::bsSubmit('Realisasikan', ['class' => 'btn btn-primary']) !!}
				@endslot
			@endcomponent
		{!! Form::close() !!}
	</div>
</div>