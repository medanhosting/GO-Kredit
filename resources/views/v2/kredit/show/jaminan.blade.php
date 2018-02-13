@component('bootstrap.card')
	<div class="card-header bg-light p-1">
		<h5 class='text-left mb-0 p-2'>
			<strong>STOK JAMINAN</strong>
		</h5>
	</div>
	<div class="card-body p-0">
		<table class="table table-hover table-bordered mb-0">
			<thead>
				<tr class="text-center">
					<th class="text-secondary">Update Terakhir</th>
					<th class="text-secondary text-left">Stok Saat Ini</th>
					<th class="text-secondary text-left">Dokumen</th>
					<th class="text-secondary">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				@forelse($aktif['jaminan'] as $k => $v)
					@php $pa = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $v['status_terakhir']['tanggal'])->format('d/m/Y') @endphp
					<tr>
						<td class="text-center">
							{{$pa}}
						</td>
						<td class="text-left">
							{{str_replace('_',' ',ucwords($v['status_terakhir']['status']))}}
							<br/>
							<small>
								@if(str_is($v['status_terakhir']['tag'], 'in'))
									[Brankas] {{$v['status_terakhir']['deskripsi']}}
								@else
									[Keluar] {{$v['status_terakhir']['deskripsi']}}
								@endif
							</small>
						</td>
						<td class="text-left w-50">
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
							@if(str_is($v['status_terakhir']['progress'], 'menunggu_validasi'))
								<a class="update_jaminan text-success" data-toggle="modal" data-target="#validasi-jaminan" data-action="{{route('jaminan.validasi', ['id' => $v['id'], 'kantor_aktif_id' => $kantor_aktif_id])}}">Validasi</a>
							@else
								@forelse($v['status_terakhir']['possible_action'] as $k2 => $v2)
								<a class="update_jaminan text-success" data-toggle="modal" data-target="#update-jaminan" data-action="{{route('jaminan.update', ['id' => $v['id'], 'kantor_aktif_id' => $kantor_aktif_id, 'stok' => $v2])}}" data-title="{{$k2}}">{{str_replace('_', ' ', $k2)}}</a>
								<br/>
								@empty
									&emsp;
									&emsp;
									&emsp;
								@endforelse
							@endif
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
@endcomponent('bootstrap.card')

@component ('bootstrap.modal', ['id' => 'validasi-jaminan', 'form' => true, 'method' => 'patch', 'url' => '#'])
	@slot ('title')
		Validasi Stok Jaminan
	@endslot

	@slot ('body')
		<p>Untuk validasi jaminan harap lengkapi data berikut!</p>

		{!! Form::bsText('Tanggal', 'tanggal', $today->format('d/m/Y'), ['placeholder' => '13/11/2017', 'class' => 'mask-date form-control w-50']) !!}

		{!! Form::bsPassword('password', 'password', ['placeholder' => 'Password']) !!}

	@endslot

	@slot ('footer')
		<a href="#" data-dismiss="modal" class="btn btn-link text-secondary">Batal</a>
		{!! Form::bsSubmit('Konfirmasi', ['class' => 'btn btn-primary']) !!}
	@endslot
@endcomponent

@component ('bootstrap.modal', ['id' => 'update-jaminan', 'form' => true, 'method' => 'patch', 'url' => '#'])
	@slot ('title')
		Update Stok Jaminan
	@endslot

	@slot ('body')
		<p>Untuk update jaminan harap lengkapi data berikut!</p>

		<div class="form-group">
			{!! Form::bsText('Tanggal', 'tanggal', $today->format('d/m/Y'), ['placeholder' => '13/11/2017', 'class' => 'mask-date form-control w-50']) !!}

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
		$('#update-jaminan').find('.modal-title').html($(this).attr("data-title"));
	}
	$('.modal#konfirmasi_ubah_jaminan').on('show.bs.modal', function(e){
		$('.modal#update-jaminan').modal('hide');
	});

	$("a.validasi_jaminan").on("click", parsingDataAttributeValidasiJaminan);

	function parsingDataAttributeValidasiJaminan(){
		$('#validasi-jaminan').find('form').attr('action', $(this).attr("data-action"));
	}
	</script>

@endpush
