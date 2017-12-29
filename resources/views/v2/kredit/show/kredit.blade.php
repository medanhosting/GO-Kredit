<div class="clearfix">&nbsp;</div>
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