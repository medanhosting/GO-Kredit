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
			<div class="row">
				<div class="col">
					{!! Form::bsSelect('Diambil Dari', 'nomor_perkiraan', array_merge([null => 'Pilih'], $akun), '', ['class' => 'form-control text-info inline-edit', 'id' => 'nomor_perkiraan_pencairan'], true) !!}
				</div>
			</div>
			<div class="row">
				<div class="col">
					<a data-toggle="modal" data-target="#konfirmasi_putusan" data-action="{{ route('putusan.update', ['id' => $putusan['pengajuan_id'], 'kantor_aktif_id' => $kantor_aktif_id, 'lokasi_id' => $lokasi['id']]) }}" data-content="Untuk menandai pencairan, silahkan isi password Anda untuk melanjutkan." data-parse-nomor-perkiraan="" class="modal_password btn btn-primary btn-sm btn-block text-white" id="tandai_pencairan">Tandai Pencairan</a>
				</div>
			</div>
		@endif
	</div>
</div>

@push('js')
	<script type="text/javascript">
		$('select[name="nomor_perkiraan"]').on('change', function(e) {
			var actionLink = $(document.getElementById('tandai_pencairan')).attr('data-action');
			$(document.getElementById('tandai_pencairan')).attr('data-action', actionLink + '&nomor_perkiraan=' + $(this).val());
		});
	</script>
@endpush