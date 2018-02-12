@inject('carbon', 'Carbon\Carbon')

{!! Form::open(['url' => route('kredit.update', ['id' => $aktif['id'], 'kantor_aktif_id' => $kantor_aktif_id, 'current' => 'validasi_restitusi']), 'method' => 'PATCH']) !!}
	<div class="row">
		<div class="col-12">
			{!! Form::bsText('Tanggal', 'tanggal', $carbon::now()->format('d/m/Y H:i'), ['class' => 'form-control mask-date inline-edit text-info py-1 border-input', 'placeholder' => 'dd/mm/yyyy hh:mm'], true) !!}
		</div>
	</div>

	<div class="row">
		<div class="col-12 pb-1">
			JENIS RESTITUSI
		</div>
		<div class="col-12 pb-3">
			{{ucwords(str_replace('_', ' ', $restitusi['jenis']))}}
		</div>
	</div>

	<div class="row">
		<div class="col-12 pb-1">
			NOMINAL RESTITUSI
		</div>
		<div class="col-12 pb-3 text-style">
			{{$restitusi['jumlah']}}
		</div>
	</div>

	<div class="row">
		<div class="col-12 pb-1">
			ALASAN
		</div>
		<div class="col-12 pb-3">
			{{$restitusi['alasan']}}
		</div>
	</div>

	{!! Form::hidden('current', 'validasi_restitusi') !!}
	<div class="clearfix">&nbsp;</div>
	<a href="#" class="btn btn-danger" data-toggle="modal" data-target="#konfirmasi_permohonan_restitusi_acc" data-parsing="0">Tolak</a> &emsp;
	<a href="#" class="btn btn-primary" data-toggle="modal" data-target="#konfirmasi_permohonan_restitusi_acc" data-parsing="1">Setuju</a>

	@include('v2.kredit.modal.konfirmasi_permohonan_restitusi_acc')
{!! Form::close() !!}	