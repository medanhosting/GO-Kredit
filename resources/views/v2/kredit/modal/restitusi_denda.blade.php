@component('bootstrap.modal', ['id' => 'retuitit-denda', 'size' => 'modal-lg']) 
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
						gak tau variable
					</p>
				</td>
			</tr>
		</table>
		<div class="clearfix">&nbsp;</div>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th class="text-center" style="width: 20%;">Tunggakan</th>
					<th class="text-right" style="width: 20%;">Jumlah</th>
					<th class="text-left" style="width: 40%;">Alasan</th>
					<th class="text-center" style="width: 20%">Tanda Tangan</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="">Pokok</td>
					<td class="text-right">Rp 10.050.000</td>
					<td class="text-left" rowspan="5">
						{!! Form::vTextarea(null, 'alasan', $restitusi['alasan'], ['class' => 'form-control inline-edit border-input text-info pb-1', 'placeholder' => 'Alasan', 'rows' => 5, 'cols' => 8, 'style' => 'resize: none;'], true) !!}
					</td>
					<td class="text-center" rowspan="5">&nbsp;</td>
				</tr>
				<tr>
					<td class="">Jasa</td>
					<td class="text-right">Rp 10.050.000</td>
				</tr>
				<tr>
					<td class="">Denda</td>
					<td class="text-right">Rp 10.050.000</td>
				</tr>
				<tr>
					<td class="">Total</td>
					<td class="text-right">Rp 10.050.000</td>
				</tr>
				<tr>
					<td class="">Kesanggupan Bayar</td>
					<td class="text-right">Rp 10.050.000</td>
				</tr>
			</tbody>
		</table>

		<div class="row bg-dark">
			<div class="col text-center">
				<h4 class="mb-0 p-1 text-light">PERSETUJUAN KERINGANAN</h4>
			</div>
		</div>

		<div class="clearfix"></div>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th class="text-center" style="width: 15%;">Nominal Persetujuan</th>
					<th class="text-right" style="width: 30%;">
						{!! Form::vText(null, 'legal', $putusan['legal'], ['class' => 'form-control inline-edit text-info mask-money border-input pb-1 w-100', 'placeholder' => 'Rp 70.000'], true) !!}
					</th>
					<th class="text-right" style="width: 30%;">
						{!! Form::vText(null, 'legal', $putusan['legal'], ['class' => 'form-control inline-edit text-info mask-money border-input pb-1 w-100', 'placeholder' => 'Rp 70.000'], true) !!}
					</th>
					<th class="text-right" style="width: 30%">
						{!! Form::vText(null, 'legal', $putusan['legal'], ['class' => 'form-control inline-edit text-info mask-money border-input pb-1 w-100', 'placeholder' => 'Rp 70.000'], true) !!}
					</th>
				</tr>
			</thead>
			<tbody>
				<tr style="height: 20px;">
					<td class="">Tanda Tangan</td>
					<td class="text-center">Komisaris</td>
					<td class="text-center">Pimpinan</td>
					<td class="text-center">Bag. Kredit</td>
				</tr>
				<tr style="height: 20px;">
					<td class="">Disposisi</td>
					<td class="text-center"></td>
					<td class="text-center"></td>
					<td class="text-center"></td>
				</tr>
			</tbody>
		</table>
	@endslot 
	
	@slot ('footer')
		<a href="#" data-dismiss="modal" class="btn btn-link text-secondary">Batal</a>
		<a href="#" class="btn btn-outline-primary" data-toggle="modal" data-target="#konfirmasi-denda">Konfirmasi</a>
	@endslot 
@endcomponent 