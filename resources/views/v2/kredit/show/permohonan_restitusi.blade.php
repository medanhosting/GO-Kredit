@inject('carbon', 'Carbon\Carbon')

{!! Form::open(['url' => route('kredit.update', ['id' => $aktif['id'], 'kantor_aktif_id' => $kantor_aktif_id, 'current' => 'permintaan_restitusi']), 'method' => 'PATCH']) !!}
	@component('bootstrap.card')
		@slot('title') 
			<h5 class='text-left'>
				<strong>PERMOHONAN RESTITUSI</strong>
			</h5>
		@endslot
		@slot('body')
			<div class="row">
				<div class="col-12">
					{!! Form::bsText('Tanggal', 'tanggal', $carbon::now()->format('d/m/Y H:i'), ['class' => 'form-control mask-date-time inline-edit text-info pb-0 border-input', 'placeholder' => 'dd/mm/yyyy hh:mm'], true) !!}
				</div>
			</div>

			<div class="row">
				<div class="col-12 pb-1">
					JENIS RESTITUSI
				</div>
				<div class="col-6">
					{!! Form::radio('jenis', 'restitusi_3_hari', true, ['class' => 'select-jenis-restitusi']) !!} Restitusi 3 Hari
				</div>
				<div class="col-6">
					{!! Form::radio('jenis', 'restitusi_nominal', false, ['class' => 'select-jenis-restitusi']) !!} Restitusi Nominal
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
			<a href="#" data-toggle="modal" data-target="#konfirmasi_permohonan_restitusi" class="btn btn-primary text-right">Ajukan</a>
		@endslot
		@include('v2.kredit.modal.konfirmasi_permohonan_resitusi')
	@endcomponent
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