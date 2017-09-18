<h6 class="text-secondary ml-3"><strong><u>Jaminan Kendaraan</u></strong></h6>
<div class="col-auto col-md-4">
	{!! Form::bsSelect('Tipe', 'tipe', array_merge(['' => 'pilih'], $jenis_kendaraan), null, ['class' => 'custom-select form-control']) !!}
</div>
<div class="col-auto col-md-6">
	{!! Form::bsText('Merk', 'merk', null, ['class' => 'form-control']) !!}
</div>
<div class="col-auto col-md-5">
	{!! Form::bsText('Jenis', 'jenis', null, ['class' => 'form-control']) !!}
</div>
<div class="col-auto col-md-4">
	{!! Form::bsText('tahun', 'tahun', null, ['class' => 'form-control']) !!}
</div>
<div class="col-auto col-md-4">
	{!! Form::bsText('No. BPKB', 'nomor_bpkb', null, ['class' => 'form-control']) !!}
</div>
<div class="col-auto col-md-8">
	{!! Form::bsText('Atas Nama', 'atas_nama', null, ['class' => 'form-control']) !!}
</div>
<div class="col-auto col-md-6">
	{!! Form::bsText('Nilai Jaminan', 'nilai_jaminan', null, ['class' => 'form-control']) !!}
</div>
<div class="col-auto col-md-4">
	{!! Form::bsText('Tahun Perolehan', 'tahun_perolehan', null, ['class' => 'form-control']) !!}
</div>