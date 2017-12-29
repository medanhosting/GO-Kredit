<div class="clearfix">&nbsp;</div>

{{--  <div class="row">
	<div class="col-sm-4 text-right">
		<p class="text-secondary mb-1">
			Nomor Kredit
		</p>
	</div>
	<div class="col-sm-8 text-left">
		<p class="mb-1">
			{{$aktif['nomor_kredit']}}
		</p>
	</div>
</div>
<div class="row">
	<div class="col-sm-4 text-right">
		<p class="text-secondary mb-1">
			Jenis Pinjaman
		</p>
	</div>
	<div class="col-sm-8 text-left">
		<p class="mb-1">
			{{strtoupper($aktif['jenis_pinjaman'])}}
		</p>
	</div>
</div>
<div class="row">
	<div class="col-sm-4 text-right">
		<p class="text-secondary mb-1">
			Pokok Pinjaman
		</p>
	</div>
	<div class="col-sm-8 text-left">
		<p class="mb-1">
			{{strtoupper($aktif['plafon_pinjaman'])}}
		</p>
	</div>
</div>
<div class="row">
	<div class="col-sm-4 text-right">
		<p class="text-secondary mb-1">
			Suku Bunga
		</p>
	</div>
	<div class="col-sm-8 text-left">
		<p class="mb-1">
			{{strtoupper($aktif['suku_bunga'])}} %
		</p>
	</div>
</div>
<div class="row">
	<div class="col-sm-4 text-right">
		<p class="text-secondary mb-1">
			Tanggal
		</p>
	</div>
	<div class="col-sm-8 text-left">
		<p class="mb-1">
			{{strtoupper($aktif['tanggal'])}}
		</p>
	</div>
</div>
<div class="row justify-content-center">
	<div class="col">
		<p class="text-secondary">NASABAH</p>
	</div>
</div>
<div class="row">
	<div class="col-sm-4 text-right">
		<p class="text-secondary mb-1">
			Nama
		</p>
	</div>
	<div class="col-sm-8 text-left">
		<p class="mb-1">
			{{$aktif['nasabah']['nama']}}
		</p>
	</div>
</div>
<div class="row">
	<div class="col-sm-4 text-right">
		<p class="text-secondary mb-1">
			Jenis Kelamin
		</p>
	</div>
	<div class="col-sm-8 text-left">
		<p class="mb-1">
			{{$aktif['nasabah']['jenis_kelamin']}}
		</p>
	</div>
</div>
<div class="row">
	<div class="col-sm-4 text-right">
		<p class="text-secondary mb-1">
			Telepon
		</p>
	</div>
	<div class="col-sm-8 text-left">
		<p class="mb-1">
			{{$aktif['nasabah']['telepon']}}
		</p>
	</div>
</div>
<div class="row">
	<div class="col-sm-4 text-right">
		<p class="text-secondary mb-1">
			Alamat
		</p>
	</div>
	<div class="col-sm-8 text-left">
		<p class="mb-1">
			{!!implode(', ', $aktif['nasabah']['alamat'])!!}
		</p>
	</div>
</div>  --}}
<div class="clearfix">&nbsp;</div>
<table class="table w-100">
	<tbody>
		<tr class="mb-1">
			<td class="text-secondary w-25">Nomor Kredit</td>
			<td class="">{{$aktif['nomor_kredit']}}</td>
		</tr>
		<tr class="mb-1">
			<td class="text-secondary w-25">Jenis Pinjaman</td>
			<td class="">{{strtoupper($aktif['jenis_pinjaman'])}}</td>
		</tr>
		<tr class="mb-1">
			<td class="text-secondary w-25">Pokok Pinjaman</td>
			<td class="">{{strtoupper($aktif['plafon_pinjaman'])}}</td>
		</tr>
		<tr class="mb-1">
			<td class="text-secondary w-25">Suku Bunga</td>
			<td class="">{{strtoupper($aktif['suku_bunga'])}} %</td>
		</tr>
		<tr class="mb-1">
			<td class="text-secondary w-25">Tanggal</td>
			<td class="">{{strtoupper($aktif['tanggal'])}}</td>
		</tr>
		<tr>
			<th class=" bg-light text-secondary" colspan="2">NASABAH</th>
		</tr>
		<tr class="mb-1">
			<td class="text-secondary w-25">Nama</td>
			<td class="">{{$aktif['nasabah']['nama']}}</td>
		</tr>
		<tr class="mb-1">
			<td class="text-secondary w-25">Jenis Kelamin</td>
			<td class="text-capitalize">{{$aktif['nasabah']['jenis_kelamin']}}</td>
		</tr>
		<tr class="mb-1">
			<td class="text-secondary w-25">Telepon</td>
			<td class="">{{$aktif['nasabah']['telepon']}}</td>
		</tr>
		<tr class="mb-1">
			<td class="text-secondary w-25">Alamat</td>
			<td class="w-50 text-capitalize">{!! ucfirst(strtolower(implode(', ', $aktif['nasabah']['alamat']))) !!}</td>
		</tr>
	</tbody>
</table>