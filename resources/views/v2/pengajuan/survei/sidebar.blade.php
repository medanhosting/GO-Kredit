<div class="card text-left">
	<div class="card-body">
		<p class="card-title mb-2">SURVEI KREDIT</p>
		<div class="progress">
			<div class="progress-bar bg-info" role="progressbar" style="width: {{$percentage}}%" aria-valuenow="{{$percentage}}" aria-valuemin="0" aria-valuemax="100">{{$percentage}}%</div>
		</div>
		<hr/>

		@if (!is_null($survei['surveyor']))
			<p class="text-secondary mb-1">SURVEYOR</p>
			@foreach($survei['surveyor'] as $k => $v)
				<p class="mb-0">- {{$v['nama']}}</p>
			@endforeach
			<hr/>
		@endif

		{!! Form::open(['url' => route('pengajuan.survei.update', ['id' => $survei['id'], 'kantor_aktif_id' => $kantor_aktif_id, 'lokasi_id' => $lokasi['id']]), 'method' => 'PATCH']) !!}
			<div class="row">
				<div class="col">
					{!! Form::bsText('Tanggal Survei', 'tanggal_survei', $survei['tanggal'], ['class' => 'form-control mask-date-time inline-edit text-info', 'placeholder' => 'dd/mm/yyyy hh:mm'], true) !!}
				</div>
			</div>
			<div class="row">
				<div class="col">
					{!! Form::bsSubmit('Ubah Tanggal', ['class' => 'btn btn-primary btn-sm btn-block']) !!}
				</div>
			</div>
		{!! Form::close() !!}
		<hr/>

		<p class="text-secondary mb-1">NASABAH</p>
		<ul class="fa-ul mt-1">
			<li class="mb-1"><i class="fa-li fa fa-user mt-1"></i> {{ $permohonan['nasabah']['nama'] }}</li>
			<li class="mb-1"><i class="fa-li fa fa-phone mt-1"></i> {{ $permohonan['nasabah']['telepon'] }}</li>
			<li class="mb-1"><i class="fa-li fa fa-map-marker mt-1"></i> {{ implode(' ', $permohonan['nasabah']['alamat']) }}</li>
		</ul>

		<hr/>
		<p>Form Survei</p>
		<a href="{{route('pengajuan.pengajuan.print', ['id' => $survei['pengajuan_id'], 'mode' => 'survei_report', 'kantor_aktif_id' => $kantor_aktif['id']])}}" target="__blank" class="btn btn-primary btn-sm btn-block">
			Print
		</a>
		@if ($percentage==100)
			<hr/>
			<p>Survei Sudah Lengkap</p>
			<a data-toggle="modal" data-target="#lanjut-analisa" data-action="{{route('pengajuan.pengajuan.assign_analisa', ['id' => $survei['pengajuan_id'], 'kantor_aktif_id' => $kantor_aktif['id'], 'status' => 'permohonan'])}}" class="modal_analisa btn btn-primary btn-sm btn-block text-white">Lanjutkan Analisa</a>
		@endif
	</div>
</div>