<div class="card text-left">
	<div class="card-body">
		@if($notabayar)
			<p class="text-secondary mb-1">DIAMBIL DARI</p>
			<p class="mb-0">{{$akun[$notabayar['nomor_perkiraan']]}}</p>
			<hr/>

			<p>Bukti Realisasi</p>
			<a href="{{route('putusan.print', ['id' => $putusan['pengajuan_id'], 'kantor_aktif_id' => $kantor_aktif['id'], 'mode' => 'bukti_realisasi'])}}" target="__blank" class="btn btn-primary btn-sm btn-block">
				Print
			</a>
		@else
		{!! Form::open(['url' => route('putusan.update', ['id' => $putusan['pengajuan_id'], 'kantor_aktif_id' => $kantor_aktif_id, 'lokasi_id' => $lokasi['id']]), 'method' => 'PATCH']) !!}
			<div class="row">
				<div class="col">
					{!! Form::bsSelect('Diambil Dari', 'nomor_perkiraan', $akun, '', ['class' => 'form-control text-info inline-edit'], true) !!}
				</div>
			</div>
			<div class="row">
				<div class="col">
					{!! Form::bsSubmit('Tandai Pencairan', ['class' => ' btn btn-primary btn-sm btn-block text-white']) !!}
				</div>
			</div>
		{!! Form::close() !!}
		@endif
	</div>
</div>