@inject('carbon', 'Carbon\Carbon')

{!! Form::open(['url' => route('kredit.update', ['id' => $aktif['id'], 'kantor_aktif_id' => $kantor_aktif_id, 'current' => 'permintaan_restitusi']), 'method' => 'PATCH']) !!}
	<div class="row">
		<div class="col-12">
			{!! Form::bsText('Tanggal', 'tanggal', $carbon::now()->format('d/m/Y H:i'), ['class' => 'form-control mask-date inline-edit text-info pb-0 border-input', 'placeholder' => 'dd/mm/yyyy hh:mm'], true) !!}
		</div>
	</div>

	<div class="row">
		<div class="col-12 pb-1">
			JENIS RESTITUSI
		</div>
		<div class="col-12">
			<div class="form-check form-check-inline">
				{!! Form::radio('jenis', 'restitusi_3_hari', true, ['class' => 'form-check-input select-jenis-restitusi ml-0', 'id' => 'resitusi_3_hari']) !!}
				<label for="resitusi_3_hari" class="form-check-label">Restitusi 3 Hari</label>
			</div>
			<div class="form-check form-check-inline">
				{!! Form::radio('jenis', 'restitusi_nominal', false, ['class' => 'form-check-input select-jenis-restitusi ml-0', 'id' => 'restitusi_nominal']) !!}
				<label for="restitusi_nominal" class="form-check-label">Restitusi Nominal</label>
			</div>
		</div>

	</div>

	<div class="row pt-3" id="text-nominal-restitusi">
		<div class="col-12">
			{!! Form::bsText('Nominal Restitusi', 'nominal', 'Rp 0', ['class' => 'form-control inline-edit text-info mask-money border-input w-100']) !!}
		</div>
	</div>
	<div class="row pt-3" id="label-nominal-restitusi">
		<div class="col-12">
			{!! Form::label(null, 'NOMINAL RESTITUSI') !!}
		</div>
		<div class="col-12">
			{!! Form::label(null, $idr->formatMoneyTo($r3d), ['id' => 'label-nominal-restitusi', 'class' => 'pb-2']) !!}
		</div>
	</div>
	<div class="row">
		<div class="col-12">
			{!! Form::bsTextarea('Alasan', 'alasan', null, ['class' => 'form-control inline-edit border-input text-info pb-1', 'placeholder' => 'Alasan', 'rows' => 3, 'style' => 'resize: none;'], true) !!}
		</div>
	</div>
	{!! Form::hidden('current', 'permintaan_restitusi') !!}
	<div class="clearfix">&nbsp;</div>
	<a href="#" data-toggle="modal" data-target="#konfirmasi_permohonan_restitusi" class="btn btn-primary text-right">Ajukan</a>
	@include('v2.kredit.modal.konfirmasi_permohonan_resitusi')
{!! Form::close() !!}	

@push ('js')
	<script type="text/javascript">
		
		$("#text-nominal-restitusi").hide();
		$("#label-nominal-restitusi").show();

		$(".select-jenis-restitusi").on("change", displayNominalRestitusi);

		///HIDE MASA BERLAKU JAMINAN TB JIKA JENIS SHM///
		function displayNominalRestitusi(){
			if($(this).val()=='restitusi_3_hari'){
				$("#text-nominal-restitusi").hide();
				$("#label-nominal-restitusi").show();
			}else{
				$("#text-nominal-restitusi").show();
				$("#label-nominal-restitusi").hide();
			}
		}
	</script>
@endpush