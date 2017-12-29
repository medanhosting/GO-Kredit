<div class="card text-left">
	<div class="card-body">
		@if ($permohonan['status_terakhir']['status']=='permohonan')
			<p class="card-title mb-2">PERMOHONAN KREDIT</p>
			<div class="progress">
				<div class="progress-bar bg-info" role="progressbar" style="width: {{$percentage}}%" aria-valuenow="{{$percentage}}" aria-valuemin="0" aria-valuemax="100">{{$percentage}}%</div>
			</div>
		@else
			<p class="card-title mb-2">PERMOHONAN KREDIT</p>
			<div class="progress">
				<div class="progress-bar bg-info" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">100%</div>
			</div>
		@endif
		<hr/>

		@if(!is_null($permohonan['ao']))
			<p class="text-secondary mb-1">AO</p>
			<p>- {{$permohonan['ao']['nama']}}</p>
			<hr/>
		@endif

		
		@if(!is_null($permohonan['dokumen_pelengkap']['tanda_tangan']))
			<p class="text-secondary mb-1">SPESIMEN TTD</p>
			<img src="{{$permohonan['dokumen_pelengkap']['tanda_tangan']}}" class="img-fluid" alt="Foto KTP">
			<hr/>
		@endif

		<p class="mb-2">Form Pengajuan</p>
		<a href="{{route('pengajuan.pengajuan.print', ['id' => $permohonan['id'], 'mode' => 'permohonan_kredit', 'kantor_aktif_id' => $kantor_aktif['id']])}}" target="__blank" class="btn btn-primary btn-sm btn-block">
			Print
		</a>

		@if ($percentage==100 && $permohonan['status_terakhir']['status']=='permohonan')
			<hr/>
			<p>Data Sudah Lengkap</p>
			<a data-toggle="modal" data-target="#assign-survei" data-action="{{route('pengajuan.assign', ['id' => $permohonan['id'], 'kantor_aktif_id' => $kantor_aktif['id'], 'status' => 'permohonan'])}}" class="modal_assign btn btn-primary btn-sm btn-block text-white">Assign Untuk Survei</a>
		@endif

		@if ($percentage==100 && $v['nasabah']['is_lama'] && $flag_jam && $permohonan['status_terakhir']['status']=='permohonan')
			<hr/>
			<p>Nasabah & Jaminan Lama</p>
			<a data-toggle="modal" data-target="#lanjut-analisa" data-action="{{route('pengajuan.assign', ['id' => $survei['pengajuan_id'], 'kantor_aktif_id' => $kantor_aktif['id'], 'status' => 'permohonan'])}}" class="modal_analisa btn btn-primary btn-sm btn-block">Lanjutkan Analisa</a>
		@endif
	</div>
</div>