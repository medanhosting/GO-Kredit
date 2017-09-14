<h6 class="text-secondary ml-3"><strong><u>Kendaraan</u></strong></h6>
<div class="col-auto">
	<table class="table table-sm table-responsive">
		<thead class="thead-default">
			<tr>
				<th>#</th>
				<th>Jenis</th>
				<th>Merk</th>
				<th>Tipe</th>
				<th>Tahun</th>
				<th>No. BPKB</th>
				<th>Harga Jaminan</th>
				<th>Tahun Perolehan</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="8" class="text-center">Belum ada jaminan kendaraan</td>
			</tr>
			<tr id="clone-kendaraan" style="display: none;">
				<td class="nomor text-capitalize"></td>
				<td class="jenis text-capitalize"></td>
				<td class="merk text-capitalize"></td>
				<td class="tipe text-capitalize"></td>
				<td class="tahun text-uppercase"></td>
				<td class="nomor_bpkb text-capitalize"></td>
				<td class="harga_jaminan text-capitalize"></td>
				<td class="tahun_perolehan"></td>
				<td class="action"></td>

				{!! Form::hidden('jaminan[dokumen_jaminan][bpkb][jenis]', 'roda_2') !!}
				{!! Form::hidden('jaminan[dokumen_jaminan][bpkb][merk]', 'yamaha') !!}
				{!! Form::hidden('jaminan[dokumen_jaminan][bpkb][tipe]', 'mio') !!}
				{!! Form::hidden('jaminan[dokumen_jaminan][bpkb][tahun]', '2009') !!}
				{!! Form::hidden('jaminan[dokumen_jaminan][bpkb][nomor_bpkb]', 'D 92139213') !!}
				{!! Form::hidden('jaminan[dokumen_jaminan][bpkb][atas_nama]', 'Agil M') !!}
				{!! Form::hidden('jaminan[dokumen_jaminan][bpkb][nilai_jaminan]', 'Rp. 8.000.000') !!}
				{!! Form::hidden('jaminan[dokumen_jaminan][bpkb][tahun_perolehan]', '2009') !!}
			</tr>
		</tbody>
	</table>
	<div class="clearfix">&nbsp;</div>
	<a href="#" class="btn btn-primary btn-sm btn-link" data-toggle="modal" data-target="#jaminan-kendaraan">Tambah Jaminan Kendaraan</a>
</div>
<div class="clearfix">&nbsp;</div>
<div class="clearfix">&nbsp;</div>