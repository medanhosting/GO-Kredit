<div class="card text-left">
	<div class="card-body">
		@if ($putusan['pengajuan']['status_terakhir']['status']=='setuju')
			<p class="card-title mb-2">LEGALITAS REALISASI</p>
			<div class="progress">
				<div class="progress-bar bg-info" role="progressbar" style="width: {{$percentage}}%" aria-valuenow="{{$percentage}}" aria-valuemin="0" aria-valuemax="100">{{$percentage}}%</div>
			</div>
		@else
			<p class="card-title mb-2">LEGALITAS REALISASI</p>
			<div class="progress">
				<div class="progress-bar bg-info" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">100%</div>
			</div>
		@endif

		@if ($percentage==100 && $putusan['pengajuan']['status_terakhir']['status']=='setuju')
			<hr/>
			<p>Ceklist Sudah Lengkap</p>
			<a data-toggle="modal" data-target="#konfirmasi_putusan" data-action="{{route('putusan.update', ['id' => $putusan['pengajuan_id'], 'kantor_aktif_id' => $kantor_aktif['id'], 'status' => 'realisasi'])}}" data-content="Untuk melanjutkan ke pencairan, silahkan isi password Anda." class="modal_password btn btn-primary btn-sm btn-block text-white">Lanjutkan Pencairan</a>
		@endif
	</div>
</div>