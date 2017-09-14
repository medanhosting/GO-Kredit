<h6 class="text-secondary ml-3"><strong><u>Jaminan Kendaraan</u></strong></h6>
<div class="col-auto col-md-4">
	{!! Form::bsSelect('Jenis', 'jenis', array_merge(['' => 'pilih'], $jenis_kendaraan), null, ['class' => 'custom-select form-control']) !!}
</div>
<div class="col-auto col-md-6">
	{!! Form::bsText('Merk', 'merk', null, ['class' => 'form-control']) !!}
</div>
<div class="col-auto col-md-5">
	{!! Form::bsText('tipe', 'tipe', null, ['class' => 'form-control']) !!}
</div>
<div class="col-auto col-md-3">
	{!! Form::bsText('tahun', 'tahun', null, ['class' => 'form-control']) !!}
</div>
<div class="col-auto col-md-4">
	{!! Form::bsText('No. BPKB', 'no_bpkb', null, ['class' => 'form-control']) !!}
</div>
<div class="col-auto col-md-6">
	{!! Form::bsText('Harga Jaminan', 'harga_jaminan', null, ['class' => 'form-control']) !!}
</div>