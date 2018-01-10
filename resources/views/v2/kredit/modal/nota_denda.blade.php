@component('bootstrap.modal', ['id' => 'summary-denda', 'size' => 'modal-lg']) 
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
					<div class="col-6">{{$kantor_aktif['id']}} / {{$angsuran['nomor_kredit']}}</div>
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
				<h4 class="mb-2">BUKTI PEMBAYARAN DENDA</h4>
			</div>
		</div>
		<hr class="mt-1 mb-2" style="border-size: 2px;">

		<table>
			<tr class="align-top">
				<td style="width: 12.5%">AC / SPK</td>
				<td style="width: 1%">:</td>
				<td class="w-50 pl-2 pr-2">
					<p class="mb-2" style="border-bottom: 1px dotted #ccc">gak tau variabelnya</p>
				</td>
				<td style="width: 12.5%">Nama</td>
				<td style="width: 1%">:</td>
				<td class="w-25 pl-2 pr-2">
					<p class="mb-2" style="border-bottom: 1px dotted #ccc">
						{{ $kredit_aktif['nasabah']['nama'] }}
					</p>
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
						<th class="text-right" style="width: 20%;">Denda</th>
						<th class="text-right">Potongan</th>
						<th class="text-right">Subtotal</th>
					</tr>
				</thead>
				<tbody>
					<tr id="angsuran-row" style="display: none;">
						<td class="angs-iteration text-center"></td>
						<td class="angs-title"></td>
						<td class="angs-denda text-right"></td>
						<td class="angs-potongan text-right"></td>
						<td class="angs-subtotal text-right"></td>
					</tr>
				</tbody>
			</table>
		</div>
	@endslot 
	
	@slot ('footer')
		<a href="#" data-dismiss="modal" class="btn btn-link text-secondary">Batal</a>
		<a href="#" class="btn btn-outline-primary" data-toggle="modal" data-target="#konfirmasi-angsuran">Konfirmasi</a>
	@endslot 
@endcomponent 