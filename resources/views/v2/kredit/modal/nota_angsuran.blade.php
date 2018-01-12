@inject('rekening', 'Thunderlabid\Kredit\Models\Rekening')

@php 
	$rek 	= $rekening->get();
@endphp

@component('bootstrap.modal', ['id' => 'summary-angsuran', 'size' => 'modal-lg']) 
	@slot ('title') 
		Detail Angsuran
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
					<div class="col-6">#</div>
				</div>
				<div class="row justify-content-end">
					<div class="col-3">Tanggal</div>
					<div class="col-6">
						{!! Form::vText(null, 'tanggal', isset($angsuran['tanggal']) ? $angsuran['tanggal'] : $tanggal_now, ['class' => 'form-control mask-date-time inline-edit text-info pb-0 border-input', 'placeholder' => 'dd/mm/yyyy hh:mm'], true) !!}
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col text-center">
				<h4 class="mb-2">BUKTI ANGSURAN KREDIT</h4>
			</div>
		</div>
		<hr class="mt-1 mb-2" style="border-size: 2px;">

		<table>
			<tr class="align-top">
				<td style="width: 12.5%">AC / SPK</td>
				<td style="width: 1%">:</td>
				<td class="w-25 pl-2 pr-2">
					<p class="mb-2" style="border-bottom: 1px dotted #ccc">{{$kredit_aktif['nomor_kredit']}}</p>
				</td>
				<td style="width: 12.5%">AO</td>
				<td style="width: 1%">:</td>
				<td class="w-25 pl-2 pr-2">
					<p class="mb-2" style="border-bottom: 1px dotted #ccc">&nbsp;{{$kredit_aktif['ao']['nama']}}</p>
				</td>
			</tr>
			<tr class="align-top">
				<td style="width: 12.5%">Nama</td>
				<td style="width: 1%">:</td>
				<td class="w-25 pl-2 pr-2">
					<p class="mb-2" style="border-bottom: 1px dotted #ccc">
						{{ $kredit_aktif['nasabah']['nama'] }}
					</p>
				</td>
				<td style="width: 12.5%">Alamat</td>
				<td style="width: 1%">:</td>
				<td class="w-25 pl-2 pr-2 text-capitalize">
					<p class="mb-2" style="border-bottom: 1px dotted #ccc">
						{!! ucfirst(strtolower(implode(', ', $kredit_aktif['nasabah']['alamat']))) !!}
					</p>
				</td>
			</tr>
			<tr class="align-top">
				<td style="width: 12.5%">Telp.</td>
				<td style="width: 1%">:</td>
				<td class="w-25 pl-2 pr-2">
					<p class="mb-2" style="border-bottom: 1px dotted #ccc">{{ $kredit_aktif['nasabah']['telepon'] }}</p>
				</td>
				<td style="width: 12.5%">Periode Bulan</td>
				<td style="width: 1%">:</td>
				<td class="w-25 pl-2 pr-2 text-capitalize">
					<p class="mb-2 periode_bln" style="border-bottom: 1px dotted #ccc"></p>
				</td>
			</tr>
		</table>
		<div class="clearfix">&nbsp;</div>
		<div id="temp-angsuran">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th class="text-center" style="width: 5%;">#</th>
						<th class="text-left" style="width: 22%;">Deskripsi</th>
						<th class="text-right" style="width: 20%;">Pokok</th>
						<th class="text-right">Bunga</th>
						<th class="text-right">Potongan</th>
						<th class="text-right">Subtotal</th>
					</tr>
				</thead>
				<tbody>
					<tr id="angsuran-row" style="display: none;">
						<td class="angs-iteration text-center"></td>
						<td class="angs-title"></td>
						<td class="angs-pokok text-right"></td>
						<td class="angs-bunga text-right"></td>
						<td class="angs-potongan text-right"></td>
						<td class="angs-subtotal text-right"></td>
					</tr>
					<tr id="titipan-row"></tr>
					<tr id="potongan-row"></tr>
					<tr id="total-all-row"></tr>
				</tbody>
			</table>
		</div>
		<div class="clearfix"></div>
		{{--  <div class="form-group row">
			<div class="col-2">
				<label class="mb-0">Ke Rekening</label>
			</div>
			<div class="col-4">
				<select name="rekening_id" id="" class="form-control custom-select">
					@foreach($rek as $k => $v)
						<option value="{{ $v['id'] }}">{{ $v['nama'] }}</option>
					@endforeach
				</select>
			</div>
		</div>  --}}
	@endslot 
	
	@slot ('footer')
		<a href="#" data-dismiss="modal" class="btn btn-link text-secondary">Batal</a>
		<a href="#" class="btn btn-outline-primary" data-toggle="modal" data-target="#konfirmasi-angsuran">Konfirmasi</a>
	@endslot 
@endcomponent 