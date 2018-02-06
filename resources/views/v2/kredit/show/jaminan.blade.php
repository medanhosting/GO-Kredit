<div class="clearfix">&nbsp;</div>
<div class="row">
	<div class="col-sm-12">
		<table class="table table-hover table-bordered">
			<thead>
				<tr class="text-center">
					<th>Update Terakhir</th>
					<th>Stok Saat Ini</th>
					<th>Dokumen</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				@php $lua = null @endphp
				@forelse($jaminan as $k => $v)
					@php $pa = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $v['tanggal'])->format('d/m/Y') @endphp
					<tr class="text-center">
						<td>
							{{$pa}}
						</td>
						<td>
							{{str_replace('_',' ',ucwords($v['status']))}}
						</td>
						<td class="text-right w-50">
							@if(str_is($v['dokumen']['jenis'], 'shm'))
								<h6>SHM - {{strtoupper(str_replace('_',' ',$v['kategori']))}}</h6>
								Nomor Sertifikat {{$v['dokumen']['shm']['nomor_sertifikat']}}<br/>
								{{implode(', ', $v['dokumen']['shm']['alamat'])}}
							@elseif(str_is($v['dokumen']['jenis'], 'shgb'))
								<h6>SHGB - {{strtoupper(str_replace('_',' ',$v['kategori']))}}</h6>
								Nomor Sertifikat {{$v['dokumen']['shgb']['nomor_sertifikat']}}<br/>
								{{implode(', ', $v['dokumen']['shgb']['alamat'])}}
							@else
								<h6>BPKB - {{strtoupper(str_replace('_',' ',$v['kategori']))}}</h6>
								Nomor BPKB {{$v['dokumen']['bpkb']['nomor_bpkb']}}<br/>
								Kendaraan {{str_replace('_', ' ', $v['dokumen']['bpkb']['jenis'])}} - {{$v['dokumen']['bpkb']['merk']}} , {{$v['dokumen']['bpkb']['tipe']}} ({{$v['dokumen']['bpkb']['tahun']}})
							@endif
						</td>
						<td>
							<a class="update_jaminan text-success" data-toggle="modal" data-target="#update-jaminan" data-action="{{route('jaminan.update', ['id' => $v['id'], 'kantor_aktif_id' => $kantor_aktif_id])}}">Ubah Stok Jaminan</a>
						</td>
					</tr>
				@empty
					<tr>
						<td colspan="4">
							<p>Data tidak tersedia, silahkan pilih Koperasi/BPR lain</p>
						</td>
					</tr>
				@endforelse
			</tbody>
		</table>
	</div>
</div>


@component ('bootstrap.modal', ['id' => 'update-jaminan', 'form' => true, 'method' => 'patch', 'url' => '#'])
	@slot ('title')
		Update Stok Jaminan
	@endslot

	@slot ('body')
		<p>Untuk update jaminan harap lengkapi data berikut!</p>

		<div class="form-group">
			{!! Form::bsText('Tanggal', 'tanggal', null, ['placeholder' => '13/11/2017', 'class' => 'mask-date form-control w-50']) !!}

			{!! Form::label('Stok Terkini', '', ['class' => 'text-uppercase mb-1']) !!}

			{!! Form::bsSelect(null, 'stok', ['aktif' => 'Aktif', 'hapus_buku' => 'Hapus Buku', 'bermasalah' => 'Bermasalah', 'keluar_bukan_pelunasan' => 'Keluar Bukan Pelunasan'], ['placeholder' => '13/11/2017', 'class' => 'mask-date form-control']) !!}

			{!! Form::bsTextarea('Catatan', 'deskripsi', null, ['placeholder' => 'Perpanjangan STNK']) !!}
		</div>
	@endslot

	@slot ('footer')
		<a href="#" data-dismiss="modal" class="btn btn-link text-secondary">Batal</a>
		<a href="#" data-toggle="modal" data-target="#konfirmasi_ubah_jaminan" class="btn btn-primary">Simpan</a>
	@endslot
	@include('v2.kredit.modal.konfirmasi_ubah_jaminan')
@endcomponent

@push ('js')	
	<script type="text/javascript">
	//MODAL PARSE DATA ATTRIBUTE//
	$("a.update_jaminan").on("click", parsingDataAttributeJaminanKeluar);

	function parsingDataAttributeJaminanKeluar(){
		$('#update-jaminan').find('form').attr('action', $(this).attr("data-action"));
	}
	$('.modal#konfirmasi_ubah_jaminan').on('show.bs.modal', function(e){
		//$('.modal#update-jaminan').modal('hide');
	});
	</script>

@endpush
