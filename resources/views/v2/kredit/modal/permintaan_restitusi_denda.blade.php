@component('bootstrap.modal', ['id' => 'permintaan-restitusi-denda', 'size' => 'modal-lg']) 
	@slot ('title') 
		Permohonan Keringanan Denda
	@endslot 

	@slot ('body')
		<div class="row">
			<div class="col-6 text-left">
				<h3 class="mb-2">{{strtoupper($kantor_aktif['nama'])}}</h3>
				<ul class="list-unstyled fa-ul ml-4">
					<li>
						<i class="fa fa-building-o fa-li"></i>{{implode(' ', $kantor_aktif['alamat'])}}</li>
					<li>
						<i class="fa fa-phone fa-li"></i>{{$kantor_aktif['telepon']}}</li>
				</ul>
			</div>
			<div class="col-6 text-right">
				<div class="row justify-content-end">
					<div class="col-3">Nomor</div>
					<div class="col-6">{{$angsuran['nomor_faktur']}}</div>
				</div>
				<div class="row justify-content-end">
					<div class="col-3">Tanggal</div>
					<div class="col-6">
						{!! Form::vText(null, 'tanggal', isset($angsuran['tanggal']) ? $angsuran['tanggal'] : $tanggal_now, ['class' => 'form-control mask-date-time inline-edit text-info pb-0 border-input', 'placeholder' => 'dd/mm/yyyy hh:mm'], true) !!}
					</div>
				</div>
			</div>
		</div>
		<div class="row bg-dark">
			<div class="col text-center">
				<h4 class="mb-0 p-1 text-light">PERMOHONAN KERINGANAN DENDA</h4>
			</div>
		</div>
		<div class="clearfix">&nbsp;</div>
		<table class="w-100">
			<tr class="align-top">
				<td style="width: 12.5%">Nama Debitur</td>
				<td style="width: 1.5%">:</td>
				<td class="pl-2 pr-2" style="width: 36%;">
					<p class="mb-2" style="border-bottom: 1px dotted #ccc">{{ $kredit_aktif['nasabah']['nama'] }}</p>
				</td>
				<td style="width: 12.5%">AC / SPK</td>
				<td style="width: 1.5%">:</td>
				<td class="pl-2 pr-2" style="width: 36%;">
					<p class="mb-2" style="border-bottom: 1px dotted #ccc">{{$aktif['nomor_kredit']}}</p>
				</td>
			</tr>
			<tr class="align-top">
				<td style="width: 12.5%">Alamat</td>
				<td style="width: 1.5%">:</td>
				<td class="pl-2 pr-2" style="width: 36%;">
					<p class="mb-2" style="border-bottom: 1px dotted #ccc;">
						{{implode(' ', $kredit_aktif['nasabah']['alamat'])}}
					</p>
				</td>
			</tr>
		</table>
		<div class="clearfix">&nbsp;</div>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th class="text-center" style="width: 15%;">Tunggakan</th>
					<th class="text-right" style="width: 30%;">Jumlah</th>
					<th class="text-left" style="width: 45%;">Alasan</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Denda</td>
					<td class="text-right">{{$idr->formatMoneyTo($stat['total_denda'])}}</td>
					<td class="text-left" rowspan="5">
						{!! Form::bsTextarea(null, 'alasan', null, ['class' => 'form-control inline-edit border-input text-info pb-1', 'placeholder' => 'Alasan', 'rows' => 5, 'cols' => 8, 'style' => 'resize: none;'], true) !!}
					</td>
				</tr>
				<tr>
					<td>Total</td>
					<td class="text-right">{{$idr->formatMoneyTo($stat['total_denda'])}}</td>
				</tr>

				<tr>
					<td>Jenis Restitusi</td>
					<td class="text-right">
						{!! Form::bsSelect(null, 'jenis', ['restitusi_3_hari' => 'Restitusi 3 Hari', 'restitusi_nominal' => 'Restitusi Nominal'],restitusi_3_hari, ['class' => 'form-control inline-edit text-info border-input pb-1 w-100'], true) !!}
					</td>
				</tr>
				<tr>
					<td>Nominal Restitusi</td>
					<td class="text-right">
						{!! Form::bsText(null, 'nominal', null, ['class' => 'form-control inline-edit text-info mask-money border-input pb-1 w-100', 'placeholder' => 'Rp 70.000'], true) !!}
					</td>
				</tr>
			</tbody>
		</table>
	@endslot 
	
	@slot ('footer')
		<a href="#" data-dismiss="modal" class="btn btn-link text-secondary">Batal</a>
		{!! Form::bsSubmit('Ajukan', ['class' => 'btn btn-primary']) !!}
	@endslot 
@endcomponent 