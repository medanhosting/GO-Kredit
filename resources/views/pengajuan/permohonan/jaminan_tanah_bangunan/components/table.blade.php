<h6 class="text-secondary ml-3"><strong><u>Tanah &amp; Bangunan</u></strong></h6>
<div class="col-auto">
	<table class="table table-sm table-responsive">
		<thead class="thead-default">
			<tr>
				<th>#</th>
				<th>Tipe</th>
				<th>Jenis</th>
				<th class="text-center">No. Sertifikat</th>
				<th>Model</th>
				<th class="text-center">Luas Bangunan/ Luas Tanah </th>
				<th>Atas Nama</th>
				<th class="text-center">Nilai Jaminan</th>
				<th class="text-center">Tahun Perolehan</th>
				<th></th>
			</tr>
		</thead>
		<tbody id="content-tanah-bangunan">
			<tr id="content-tanah-bangunan-default">
				<td colspan="10" class="text-center">Belum ada jaminan tanah &amp; bangunan</td>
			</tr>
			<tr id="clone-tanah-bangunan" style="display: none;">
				<td class="nomor"></td>
				<td class="tipe text-capitalize"></td>
				<td class="jenis_sertifikat text-capitalize"></td>
				<td class="nomor_sertifikat text-capitalize text-center"></td>
				<td class="model text-capitalize"></td>
				<td class="luas_tanah text-capitalize text-center"></td>
				<td class="atas_nama text-capitalize"></td>
				<td class="nilai_jaminan text-capitalize text-right"></td>
				<td class="tahun_perolehan text-center"></td>
				<td class="action"></td>

				{!! Form::hidden('jenis', null, ['disabled' => true]) !!}
				{!! Form::hidden('tipe', null, ['disabled' => true]) !!}
				{!! Form::hidden('nomor_sertifikat', null, ['disabled' => true]) !!}
				{!! Form::hidden('luas_tanah', null, ['disabled' => true]) !!}
				{!! Form::hidden('luas_bangunan', null, ['disabled' => true]) !!}
				{!! Form::hidden('atas_nama', null, ['disabled' => true]) !!}
				{!! Form::hidden('nilai_jaminan', null, ['disabled' => true]) !!}
				{!! Form::hidden('tahun_perolehan', null, ['disabled' => true]) !!}
				{!! Form::hidden('alamat[alamat]', null, ['disabled' => true]) !!}
				{!! Form::hidden('alamat[rw]', null, ['disabled' => true]) !!}
				{!! Form::hidden('alamat[rt]', null, ['disabled' => true]) !!}
				{!! Form::hidden('alamat[kota]', null, ['disabled' => true]) !!}
				{!! Form::hidden('alamat[kecamatan]', null, ['disabled' => true]) !!}
				{!! Form::hidden('alamat[kelurahan]', null, ['disabled' => true]) !!}
				{!! Form::hidden('tahun_perolehan', null, ['disabled' => true]) !!}
			</tr>
		</tbody>
	</table>
	<div class="clearfix">&nbsp;</div>
	<a href="#" class="btn btn-primary btn-sm btn-link" data-toggle="modal" data-target="#jaminan-tanah-bangunan">Tambah Jaminan Tanah &amp; Bangunan</a>
</div>
<div class="clearfix">&nbsp;</div>
<div class="clearfix">&nbsp;</div>