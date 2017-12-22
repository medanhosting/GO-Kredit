<div class="clearfix">&nbsp;</div>
<div class="row">
	<div class="col-sm-4 text-right">
		Nomor Kredit
	</div>
	<div class="col-sm-8 text-left">
		{{$aktif['nomor_kredit']}}
	</div>
</div>
<div class="row">
	<div class="col-sm-4 text-right">
		Jenis Pinjaman
	</div>
	<div class="col-sm-8 text-left">
		{{strtoupper($aktif['jenis_pinjaman'])}}
	</div>
</div>
<div class="row">
	<div class="col-sm-4 text-right">
		Pokok Pinjaman
	</div>
	<div class="col-sm-8 text-left">
		{{strtoupper($aktif['plafon_pinjaman'])}}
	</div>
</div>
<div class="row">
	<div class="col-sm-4 text-right">
		Suku Bunga
	</div>
	<div class="col-sm-8 text-left">
		{{strtoupper($aktif['suku_bunga'])}} %
	</div>
</div>
<div class="row">
	<div class="col-sm-4 text-right">
		Tanggal
	</div>
	<div class="col-sm-8 text-left">
		{{strtoupper($aktif['tanggal'])}}
	</div>
</div>
<h7 class="text-secondary">NASABAH</h7>
<div class="row">
	<div class="col-sm-4 text-right">
		Nama
	</div>
	<div class="col-sm-8 text-left">
		{{$aktif['nasabah']['nama']}}
	</div>
</div>
<div class="row">
	<div class="col-sm-4 text-right">
		Jenis Kelamin
	</div>
	<div class="col-sm-8 text-left">
		{{$aktif['nasabah']['jenis_kelamin']}}
	</div>
</div>
<div class="row">
	<div class="col-sm-4 text-right">
		Telepon
	</div>
	<div class="col-sm-8 text-left">
		{{$aktif['nasabah']['telepon']}}
	</div>
</div>
<div class="row">
	<div class="col-sm-4 text-right">
		Alamat
	</div>
	<div class="col-sm-8 text-left">
		{!!implode(', ', $aktif['nasabah']['alamat'])!!}
	</div>
</div>