<div class="row">
	<div class="col-6 text-left">
		<h3>{{strtoupper($kantor_aktif['nama'])}}</h3>
		<p class="p-1 m-0">{{implode(' ', $kantor_aktif['alamat'])}}</p>
		<p class="p-1 m-0">Telp : {{$kantor_aktif['telepon']}}</p>
	</div>
	<div class="col-6 text-right">
		<h6 class="p-1 m-0">
			Nomor : {{$kantor_aktif['id']}} / {{$putusan['pengajuan_id']}}
		</h6>
		<h6 class="p-1 m-0">
			Tanggal : {{$putusan['pengajuan']['status_realisasi']['tanggal']}}
		</h6>
	</div>
</div>
<div class="row">
	<div class="col text-center">
		<h1>BUKTI REALISASI KREDIT</h1>
	</div>
</div>
<hr/>

<div class="row">
	<div class="col">
		{!! Form::open(['url' => route('realisasi.update', ['id' => $putusan['pengajuan_id'], 'kantor_aktif_id' => $kantor_aktif_id]), 'method' => 'PATCH']) !!}
			<div class="row">
				<div class="col">
					{!! Form::vText('Tanggal Realisasi', 'tanggal_pencairan', $putusan['tanggal'], ['class' => 'form-control inline-edit mask-date-time', 'placeholder' => 'dd/mm/yyyy hh:mm'], true) !!}
				</div>
			</div>
		{!! Form::bsSubmit('Simpan', ['class' => 'btn btn-primary float-right mr-3']) !!}
		{!! Form::close() !!}
	</div>
</div>
<div>
