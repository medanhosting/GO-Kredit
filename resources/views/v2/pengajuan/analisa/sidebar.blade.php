<div class="card text-left">
	<div class="card-body">
		<p class="card-title mb-2">ANALISA KREDIT</p>
		<div class="progress">
			<div class="progress-bar bg-info" role="progressbar" style="width: {{$percentage}}%" aria-valuenow="{{$percentage}}" aria-valuemin="0" aria-valuemax="100">{{$percentage}}%</div>
		</div>
		<hr/>

		@if(!is_null($analisa['analis']))
			<p class="text-secondary mb-1">ANALIS</p>
			<p class="mb-0">- {{$analisa['analis']['nama']}}</p>
			<hr/>
		@endif

		<p class="text-secondary mb-1">NASABAH</p>
		@if ($permohonan)
			<ul class="fa-ul mt-1">
				<li class="mb-1"><i class="fa-li fa fa-user mt-1"></i> {{ $permohonan['nasabah']['nama'] }}</li>
				<li class="mb-1"><i class="fa-li fa fa-phone mt-1"></i> {{ $permohonan['nasabah']['telepon'] }}</li>
				<li class="mb-1"><i class="fa-li fa fa-map-marker mt-1"></i> {{ implode(', ', $permohonan['nasabah']['alamat']) }}</li>
			</ul>
		@else
			<ul class="fa-ul mt-1 ml-0">
				{{-- DIGANTI AJA NANTI TULISANNYA KETIKA SUDAH ADA VARIABLENYA --}}
				<li class="mb-1">Tidak ada data nasabah atau belum diset variablenya</li>
			</ul>
		@endif


		@if($percentage==100)
		<hr/>
		<p>Analisa Sudah Lengkap</p>
			<a data-toggle="modal" data-target="#ajukan-putusan" data-action="{{route('pengajuan.pengajuan.assign_putusan', ['id' => $permohonan['id'], 'kantor_aktif_id' => $kantor_aktif['id'], 'status' => 'analisa'])}}" class="modal_putusan btn btn-primary btn-sm btn-block">
				Ajukan Ke Komite Kredit
			</a> 
		@endif
	</div>
</div>