<h6 class="text-secondary ml-3"><strong><u>Tanah &amp; Bangunan</u></strong></h6>
<div class="col-auto">
	<table class="table table-sm table-responsive">
		<thead class="thead-default">
			<tr>
				<th>#</th>
				<th>Jenis</th>
				<th>Tipe Sertifikat</th>
				<th>No. Sertifikat</th>
				<th>Luas Bangunan/ Luas Tanah </th>
				<th>Atas Nama</th>
				<th>Harga Jaminan</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="7" class="text-center">Belum ada jaminan tanah &amp; bangunan</td>
			</tr>
			<tr id="clone-tanah-bangunan" style="display: none;">
				<td class="nomor"></td>
				<td class="jenis text-capitalize"></td>
				<td class="tipe text-capitalize"></td>
				<td class="nomor_sertifikat text-capitalize"></td>
				<td class="luas_tanah text-capitalize"></td>
				<td class="atas_nama text-capitalize"></td>
				<td class="harga_jaminan text-capitalize"></td>
				<td class="action"></td>

				{!! Form::hidden('jaminan[dokumen_jaminan][jenis]', null) !!}
				{!! Form::hidden('jaminan[dokumen_jaminan][tipe]', null) !!}
				{!! Form::hidden('jaminan[dokumen_jaminan][nomor_sertifikat]', null) !!}
				{!! Form::hidden('jaminan[dokumen_jaminan][luas_tanah]', null) !!}
				{!! Form::hidden('jaminan[dokumen_jaminan][luas_bangunan]', null) !!}
				{!! Form::hidden('jaminan[dokumen_jaminan][atas_nama]', null) !!}
				{!! Form::hidden('jaminan[dokumen_jaminan][nilai_jaminan]', null) !!}
			</tr>
		</tbody>
	</table>
	<div class="clearfix">&nbsp;</div>
	<a href="#" class="btn btn-primary btn-sm btn-link" data-toggle="modal" data-target="#jaminan-tanah-bangunan">Tambah Jaminan Tanah &amp; Bangunan</a>
</div>
<div class="clearfix">&nbsp;</div>
<div class="clearfix">&nbsp;</div>