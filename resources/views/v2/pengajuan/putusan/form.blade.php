<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" data-toggle="tab" href="#form-putusan" role="tab">
			Putusan Komite Kredit 
		</a>
	</li>
	<!-- <li class="nav-item">
		<a class="nav-link disabled" data-toggle="tab" href="#legal-realisasi" role="tab">
			Legalitas Realisasi 
		</a>
	</li> -->
</ul>

<!-- Tab panes -->
<div class="tab-content">
	<div class="tab-pane fade show active" id="form-putusan" role="tabpanel">
		<div class="clearfix">&nbsp;</div>
		{!! Form::open(['url' => route('pengajuan.update', ['id' => $permohonan['id'], 'kantor_aktif_id' => $kantor_aktif_id]), 'method' => 'PATCH']) !!}
			<div class="row">
				<div class="col">
					{!! Form::vText('Tanggal Putusan', 'tanggal', $putusan['tanggal'], ['class' => 'form-control mask-date-time inline-edit text-info pb-1 border-input w-25', 'placeholder' => 'dd/mm/yyyy hh:mm'], true) !!}
				</div>
			</div>
			<div class="row">
				<div class="col">
					{!! Form::vText('Plafon Pinjaman', 'plafon_pinjaman', $putusan['plafon_pinjaman'], ['class' => 'form-control inline-edit text-info mask-money border-input pb-1 w-50', 'placeholder' => 'Rp 6.000.000'], true) !!}
				</div>
			</div>
			<div class="row">
				<div class="col">
					{!! Form::vText('Suku Bunga', 'suku_bunga', $putusan['suku_bunga'], ['class' => 'form-control inline-edit border-input text-info pb-1', 'placeholder' => '0.4'], true, null, null, '%',  ['class_input_group' => 'w-25', 'class_input_group_append' => 'border-0 bg-white']) !!}
				</div>
			</div>
			<div class="row">
				<div class="col">
					{!! Form::vText('Jangka Waktu', 'jangka_waktu', $putusan['jangka_waktu'], ['class' => 'form-control inline-edit border-input text-info pb-1', 'placeholder' => '12'], true, null, null, 'Bulan',  ['class_input_group' => 'w-25', 'class_input_group_append' => 'border-0 bg-white']) !!}
				</div>
			</div>
			<div class="row">
				<div class="col">
					{!! Form::vText('Provisi', 'perc_provisi', $putusan['perc_provisi'], ['class' => 'form-control inline-edit border-input text-info pb-1', 'placeholder' => '0.4'], true, null, null, '%',  ['class_input_group' => 'w-25', 'class_input_group_append' => 'border-0 bg-white']) !!}
				</div>
			</div>
			<div class="row">
				<div class="col">
					{!! Form::vText('Administrasi', 'administrasi', $putusan['administrasi'], ['class' => 'form-control inline-edit text-info mask-money border-input pb-1 w-50', 'placeholder' => 'Rp 60.000'], true) !!}
				</div>
			</div>
			<div class="row">
				<div class="col">
					{!! Form::vText('Legal', 'legal', $putusan['legal'], ['class' => 'form-control inline-edit text-info mask-money border-input pb-1 w-50', 'placeholder' => 'Rp 70.000'], true) !!}
				</div>
			</div>
			<div class="row">
				<div class="col">
					{!! Form::vText('Biaya Notaris', 'biaya_notaris', $putusan['biaya_notaris'], ['class' => 'form-control inline-edit text-info mask-money border-input pb-1 w-50', 'placeholder' => 'Rp 70.000'], true) !!}
				</div>
			</div>
			<div class="row">
				<div class="col">
					{!! Form::vSelect('Putusan', 'putusan', ['setuju' => 'Setuju', 'tolak' => 'Tolak'], $putusan['putusan'], ['class' => 'form-control custom-select text-info border-input inline-edit pb-1 w-25'], true) !!}
				</div>
			</div>
			<div class="row">
				<div class="col">
					{!! Form::vTextarea('Catatan', 'catatan', $putusan['catatan'], ['class' => 'form-control inline-edit border-input text-info pb-1', 'placeholder' => 'Ganti Jaminan', 'rows' => 5, 'cols' => 8, 'style' => 'resize: none;'], true) !!}
				</div>
			</div>


		{!! Form::bsSubmit('Simpan', ['class' => 'btn btn-primary float-right mr-3']) !!}
		{!! Form::close() !!}
	</div>
	<!-- <div class="tab-pane" id="legal-realisasi" role="tabpanel">
		<div class="clearfix">&nbsp;</div>
		
	</div> -->
</div>

@include('v2.pengajuan.modal.assign_komite_putusan')

@push ('js')
	<script type="text/javascript">
		$("a.modal_putusan").on("click", gantiCaption);

		function gantiCaption(){
			$('.modal-title').text($(this).attr("data-title"));
			$('.modal-body').find('p:first').text($(this).attr("data-desc"));
		}
	</script>
@endpush
