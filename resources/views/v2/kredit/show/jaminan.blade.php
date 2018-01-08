<div class="clearfix">&nbsp;</div>
<div class="row">
	<div class="col-sm-12">
		<table class="table table-hover table-bordered">
			<thead>
				<tr class="text-center">
					<th colspan="2">Catatan</th>
					<th>Dokumen</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				@php $lua = null @endphp
				@forelse($jaminan as $k => $v)
					@php $pa = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $v['tanggal'])->format('d/m/Y') @endphp
					@if($lua != $pa)
						<tr>
							<td colspan="4" class="bg-light">
								{{$pa}}
							</td>
						</tr>
						@php $lua = $pa @endphp
					@endif
					<tr class="text-center">
						<td>
							@if(str_is($v['tag'], 'in'))
								<i class="fa fa-arrow-down text-success"></i>
							@else
								<i class="fa fa-arrow-up text-danger"></i>
							@endif
						</td>
						<td>
							{{$v['description']}}
						</td>
						<td class="text-right">
							@if(str_is($v['documents']['jenis'], 'shm'))
								<h6>SHM</h6>
								Nomor Sertifikat {{$v['documents']['shm']['nomor_sertifikat']}}<br/>
								{{implode(', ', $v['documents']['shm']['alamat'])}}
							@elseif(str_is($v['documents']['jenis'], 'shgb'))
								<h6>SHGB</h6>
								Nomor Sertifikat {{$v['documents']['shgb']['nomor_sertifikat']}}<br/>
								{{implode(', ', $v['documents']['shgb']['alamat'])}}
							@else
								<h6>BPKB</h6>
								Nomor BPKB {{$v['documents']['bpkb']['nomor_bpkb']}}<br/>
								Kendaraan {{str_replace('_', ' ', $v['documents']['bpkb']['jenis'])}} - {{$v['documents']['bpkb']['merk']}} , {{$v['documents']['bpkb']['tipe']}} ({{$v['documents']['bpkb']['tahun']}})
							@endif
						</td>
						<td>
							@if(str_is($v['possible_action'], 'ajukan_jaminan_keluar'))
								<a class="jaminan_keluar text-success" data-toggle="modal" data-target="#ajukan-jaminan-keluar" data-action="{{route('jaminan.update', ['id' => $v['id'], 'kantor_aktif_id' => $kantor_aktif_id])}}">Ajukan Jaminan Keluar</a>
							@elseif(str_is($v['possible_action'], 'otorisasi_jaminan_masuk'))
								<a class="otorisasi_mutasi text-success" data-toggle="modal" data-target="#otorisasi-mutasi-jaminan" data-action="{{route('jaminan.update', ['id' => $v['id'], 'kantor_aktif_id' => $kantor_aktif_id])}}">Otorisasi Jaminan Masuk</a>
							@elseif(str_is($v['possible_action'], 'otorisasi_jaminan_keluar'))
								<a class="otorisasi_mutasi text-success" data-toggle="modal" data-target="#otorisasi-mutasi-jaminan" data-action="{{route('jaminan.update', ['id' => $v['id'], 'kantor_aktif_id' => $kantor_aktif_id])}}">Otorisasi Jaminan Keluar</a>
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
</div>


@component ('bootstrap.modal', ['id' => 'ajukan-jaminan-keluar', 'form' => true, 'method' => 'patch', 'url' => '#'])
	@slot ('title')
		Ajukan Jaminan Keluar
	@endslot

	@slot ('body')
		<p>Untuk pengajuan jaminan keluar harap lengkapi data berikut!</p>

		<div class="form-group">
			{!! Form::bsText('Tanggal Keluar', 'out', null, ['placeholder' => '13/11/2017', 'class' => 'mask-date form-control']) !!}

			{!! Form::bsText('Perkiraan Tanggal Kembali', 'in', null, ['placeholder' => '13/11/2017', 'class' => 'mask-date form-control']) !!}

			{!! Form::bsTextarea('Alasan Keluar', 'description', null, ['placeholder' => 'Perpanjangan STNK']) !!}
		</div>
	@endslot

	@slot ('footer')
		<a href="#" data-dismiss="modal" class="btn btn-link text-secondary">Batal</a>
		{!! Form::bsSubmit('Simpan', ['class' => 'btn btn-primary']) !!}
	@endslot
@endcomponent

@component ('bootstrap.modal', ['id' => 'otorisasi-mutasi-jaminan', 'form' => true, 'method' => 'patch', 'url' => '#'])
	@slot ('title')
		Otorisasi jaminan masuk / keluar
	@endslot

	@slot ('body')
		<p>Untuk otorisasi jaminan, harap mengisi password Anda!</p>

		{!! Form::bsPassword('password', 'password', ['placeholder' => 'Password']) !!}
	@endslot

	@slot ('footer')
		<a href="#" data-dismiss="modal" class="btn btn-link text-secondary">Batal</a>
		{!! Form::bsSubmit('Simpan', ['class' => 'btn btn-primary']) !!}
	@endslot
@endcomponent

@push ('js')	
	<script type="text/javascript">
	//MODAL PARSE DATA ATTRIBUTE//
	$("a.jaminan_keluar").on("click", parsingDataAttributeJaminanKeluar);

	function parsingDataAttributeJaminanKeluar(){
		$('#ajukan-jaminan-keluar').find('form').attr('action', $(this).attr("data-action"));
	}

	//MODAL PARSE DATA ATTRIBUTE//
	$("a.otorisasi_mutasi").on("click", parsingDataAttributeOtorisasiMutasi);

	function parsingDataAttributeOtorisasiMutasi(){
		$('#otorisasi-mutasi-jaminan').find('form').attr('action', $(this).attr("data-action"));
	}
	</script>

@endpush
