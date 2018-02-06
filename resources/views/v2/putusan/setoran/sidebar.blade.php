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
			<div class="row">
				<div class="col">
					{!! Form::bsSelect('Disetor Ke', 'nomor_perkiraan', array_merge([null => 'Pilih'], $akun), '', ['class' => 'form-control text-info inline-edit'], true) !!}
				</div>
			</div>
			<div class="row">
				<div class="col">
					<a data-toggle="modal" data-target="#konfirmasi_putusan" data-action="{{ route('putusan.update', ['id' => $putusan['pengajuan_id'], 'kantor_aktif_id' => $kantor_aktif_id, 'setoran' => 'true']) }}" data-content="Untuk menandai setoran awal, silahkan isi password Anda untuk melanjutkan." data-parse-nomor-perkiraan="" class="modal_password btn btn-primary btn-sm btn-block text-white" id="tandai_setoran_awal">Tandai Setoran Awal</a>
				</div>
			</div>
		@endif
	</div>
</div>

@push('js')
	<script type="text/javascript">
		$('select[name="nomor_perkiraan"]').on('change', function(e) {
			var actionLink = $(document.getElementById('tandai_setoran_awal')).attr('data-action');
			$(document.getElementById('tandai_setoran_awal')).attr('data-action', actionLink + '&nomor_perkiraan=' + $(this).val());
		});
	</script>
@endpush