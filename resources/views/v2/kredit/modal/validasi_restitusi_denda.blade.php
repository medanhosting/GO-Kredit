@component('bootstrap.modal', ['id' => 'validasi-restitusi-denda', 'size' => 'modal-lg']) 
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
						{!! Form::vText(null, 'tanggal', isset($angsuran['tanggal']) ? $angsuran['tanggal'] : $tanggal_now, ['class' => 'form-control mask-date inline-edit text-info pb-0 border-input', 'placeholder' => 'dd/mm/yyyy hh:mm'], true) !!}
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
						{{$restitusi['alasan']}}
					</td>
				</tr>
				<tr>
					<td>Total</td>
					<td class="text-right">{{$idr->formatMoneyTo($stat['total_denda'])}}</td>
				</tr>

				<tr>
					<td>Kesanggupan Bayar</td>
					<td class="text-right">{{$idr->formatMoneyTo($stat['total_denda'] - $stat['total_restitusi'])}}</td>
				</tr>
			</tbody>
		</table>		
		<div class="row bg-dark">
			<div class="col text-center">
				<h4 class="mb-0 p-1 text-light">PERSETUJUAN KERINGANAN</h4>
			</div>
		</div>

		<div class="clearfix">&nbsp;</div>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th class="text-center" style="width: 15%;">Nominal Persetujuan</th>
					<th class="text-right align-middle" style="width: 30%;">
						@if($stat['total_restitusi'] > 1000000)
							{{$idr->formatMoneyto($stat['total_restitusi'])}}
						@endif
					</th>
					<th class="text-right align-middle" style="width: 30%;">
						@if($stat['total_restitusi'] <= 1000000)
							{{$idr->formatMoneyto($stat['total_restitusi'])}}
						@endif
					</th>
					<th class="text-right align-middle" style="width: 30%">
						&nbsp;
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
		<button type="submit" name="is_approved" value=0 class = "btn btn-primary">Tolak</button>  
		<button type="submit" name="is_approved" value=1 class = "btn btn-primary">Setuju</button>  
	@endslot 
@endcomponent 