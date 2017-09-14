<h6 class="text-secondary ml-3"><strong><u>Jaminan Tanah &amp; Bangunan</u></strong></h6>
<div class="col-auto col-md-4">
	{!! Form::bsSelect('Jenis', 'jenis', ['' => 'pilih', 'tanah' => 'Tanah', 'tanah_bangunan' => 'Tanah &amp; Bangunan'], null, ['class' => 'custom-select form-control']) !!}
</div>
<div class="col-auto col-md-6">
	{!! Form::bsSelect('Sertifikat', 'jenis_sertifikat', array_merge(['' => 'pilih'], $jenis_sertifikat), null, ['class' => 'custom-select form-control']) !!}
</div>
<div class="col-auto col-md-5">
	{!! Form::bsText('No. Sertifikat', 'no_sertifikat', null, ['class' => 'form-control']) !!}
</div>
<div class="row ml-0 mr-0">
	<div class="col-auto col-md-4 tanah">
		{!! Form::bsText('luas tanah', 'luas_tanah', null, ['class' => 'form-control'], true, null, 'M<sup>2</sup>') !!}
	</div>
	<div class="col-auto col-md-4 tanah_bangunan">
		{!! Form::bsText('luas bangunan', 'luas_bangunan', null, ['class' => 'form-control'], true, null, 'M<sup>2</sup>') !!}
	</div>
</div>
<div class="col-auto col-md-6">
	{!! Form::bsText('Atas Nama', 'atas_nama', null, ['class' => 'form-control']) !!}
</div>
<div class="col-auto col-md-4">
	{!! Form::bsText('Harga Jaminan', 'harga_jaminan', null, ['class' => 'form-control']) !!}
</div>