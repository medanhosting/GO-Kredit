<div class="card text-left">
	<div class="card-body">
		@if($setoran)
			<p class="text-secondary mb-1">DISETOR KE</p>
			<p class="mb-0">{{$akun[$setoran['nomor_perkiraan']]}}</p>
			<hr/>

			<p>Bukti Setoran Realisasi</p>
			<a href="{{route('putusan.print', ['id' => $putusan['pengajuan_id'], 'kantor_aktif_id' => $kantor_aktif['id'], 'mode' => 'setoran_realisasi'])}}" target="__blank" class="btn btn-primary btn-sm btn-block">
				Print
			</a>
		@else
		{!! Form::open(['url' => route('putusan.update', ['id' => $putusan['pengajuan_id'], 'kantor_aktif_id' => $kantor_aktif_id, 'setoran' => 'true']), 'method' => 'PATCH']) !!}
			<div class="row">
				<div class="col">
					{!! Form::bsSelect('Disetor Ke', 'nomor_perkiraan', $akun, '', ['class' => 'form-control text-info inline-edit'], true) !!}
				</div>
			</div>
			<div class="row">
				<div class="col">
					{!! Form::bsSubmit('Tandai Setoran Awal', ['class' => ' btn btn-primary btn-sm btn-block text-white']) !!}
				</div>
			</div>
		{!! Form::close() !!}
		@endif
	</div>
</div>