@component('bootstrap.modal', ['id' => 'nota_angsuran', 'size' => 'modal-lg']) 
	@slot ('title') 
		Bayar Angsuran
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
						{!! Form::vText(null, 'tanggal', request()->get('tanggal'), ['class' => 'form-control mask-date inline-edit text-info pb-0 border-input', 'placeholder' => 'dd/mm/yyyy hh:mm'], true) !!}
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
				<td style="width: 12.5%" class="text-left">AC / SPK</td>
				<td style="width: 1%">:</td>
				<td class="w-25 pl-2 pr-2 text-left">
					<p class="mb-2" style="border-bottom: 1px dotted #ccc">{{$kredit['nomor_kredit']}}</p>
				</td>
				<td style="width: 12.5%" class="text-left">AO</td>
				<td style="width: 1%">:</td>
				<td class="w-25 pl-2 pr-2 text-left">
					<p class="mb-2" style="border-bottom: 1px dotted #ccc">&nbsp;{{$kredit['ao']['nama']}}</p>
				</td>
			</tr>
			<tr class="align-top">
				<td style="width: 12.5%" class="text-left">Nama</td>
				<td style="width: 1%">:</td>
				<td class="w-25 pl-2 pr-2 text-left">
					<p class="mb-2" style="border-bottom: 1px dotted #ccc">
						{{ $kredit['nasabah']['nama'] }}
					</p>
				</td>
				<td style="width: 12.5%" class="text-left">Angsuran Ke-</td>
				<td style="width: 1%">:</td>
				<td class="w-25 pl-2 pr-2 text-capitalize text-left">
					<p class="mb-2 periode_bln" style="border-bottom: 1px dotted #ccc">{{implode(', ', $faktur['nth'])}}&nbsp;</p>
				</td>
			</tr>
			<tr class="align-top">
				<td style="width: 12.5%" class="text-left">Alamat</td>
				<td style="width: 1%">:</td>
				<td class="w-25 pl-2 pr-2 text-capitalize text-left">
					<p class="mb-2" style="border-bottom: 1px dotted #ccc">
						{!! ucfirst(strtolower(implode(', ', $kredit['nasabah']['alamat']))) !!}
					</p>
				</td>
				<td style="width: 12.5%" class="text-left">Sisa Angsuran</td>
				<td style="width: 1%">:</td>
				<td class="w-25 pl-2 pr-2 text-capitalize text-left">
					<p class="mb-2 sisa_angsuran" style="border-bottom: 1px dotted #ccc">{{$idr->formatMoneyTo($stat['sisa_hutang'] - $faktur['bayar_hutang'])}}</p>
				</td>
			</tr>
			<tr class="align-top">
				<td style="width: 12.5%" class="text-left">Telp.</td>
				<td style="width: 1%">:</td>
				<td class="w-25 pl-2 pr-2 text-left">
					<p class="mb-2" style="border-bottom: 1px dotted #ccc">{{ $kredit['nasabah']['telepon'] }}</p>
				</td>
			</tr>
		</table>
		<div class="clearfix">&nbsp;</div>
		<div id="temp-angsuran">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th class="text-center" style="width: 5%;">#</th>
						<th class="text-left">Deskripsi</th>
						<th class="text-right">Subtotal</th>
					</tr>
				</thead>
				<tbody>
					@foreach($faktur['isi'] as $k => $v)
					<tr>
						<td>{{($k+1)}}</td>
						<td class="text-left">{{$v['deskripsi']}}</td>
						<td class="text-right">{{$v['jumlah']}}</td>
					</tr>
					@endforeach
					@if($faktur['potongan_titipan'] > 0)
					<tr class="text-danger">
						<td colspan="2">Saldo Titipan</td>
						<td class="text-right">{{$idr->formatMoneyTo($faktur['potongan_titipan'])}}</td>
					</tr>
					@endif
				</tbody>
				<tfoot>
					<tr>
						<td colspan="2" class="text-right">Total</td>
						<td class="text-right">{{$idr->formatMoneyTo($faktur['total'])}}</td>
					</tr>
				</tfoot>
			</table>
		</div>
		<div class="row">
			<div class="col-8">
			</div>
			<div class="col-4">
				<p class="text-left text-uppercase mb-1">Disetor Ke</p>
				{!! Form::select('nomor_perkiraan', $akun, null, ['class' => 'form-control custom-select inline-edit border-input text-info']) !!}
			</div>
		</div>
	@endslot 
	
	@slot ('footer')
		<a href="#" data-dismiss="modal" class="btn btn-link text-secondary">Batal</a>
		<a href="#" class="btn btn-outline-primary" data-toggle="modal" data-target="#konfirmasi-angsuran">Konfirmasi</a>
	@endslot 
@endcomponent 