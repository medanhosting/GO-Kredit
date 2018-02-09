@inject('carbon', 'Carbon\Carbon')

{!! Form::open(['url' => route('kredit.update', ['id' => $aktif['id'], 'kantor_aktif_id' => $kantor_aktif_id, 'current' => 'validasi_restitusi']), 'method' => 'PATCH']) !!}
	<div class="row">
		<div class="col-12">
			{!! Form::bsText('Tanggal', 'tanggal', $carbon::now()->format('d/m/Y H:i'), ['class' => 'form-control mask-date-time inline-edit text-info pb-0 border-input', 'placeholder' => 'dd/mm/yyyy hh:mm'], true) !!}
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
		<div class="col-12 pb-3">
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

	<button type="submit" name="is_approved" value=0 class = "btn btn-primary">Tolak</button>  &emsp; 
	<button type="submit" name="is_approved" value=1 class = "btn btn-primary">Setuju</button>  
{!! Form::close() !!}	
