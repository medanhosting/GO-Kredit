@inject('idr', 'App\Service\UI\IDRTranslater')
@inject('tanggal', 'App\Service\UI\TanggalTranslater')
@inject('carbon', 'Carbon\Carbon')

@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 text-center"><i class="fa fa-credit-card-alt mr-2"></i> KREDIT</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3 sidebar-plain">
			@include('v2.kredit.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				@slot('header')
					<h5 class="py-2 pl-2 mb-0 float-left">
						<a href="{{route('kredit.show', ['id' => $angsuran['kredit']['id'], 'kantor_aktif_id' => $kantor_aktif_id, 'current' => 'angsuran'])}}">
							<i class="fa fa-chevron-left"></i> 
						</a>
						&nbsp;&nbsp;DETAIL BUKTI PEMBAYARAN DENDA
					</h5>
					
					<a href="{{ route('angsuran.print', ['id' => $angsuran['nomor_kredit'], 'nota_bayar_id' => $angsuran['id'], 'kantor_aktif_id' => $kantor_aktif['id'], 'case' => 'denda']) }}" target="__blank" class="text-success float-right btn btn-link">
						<i class="fa fa-file-o fa-fw"></i>&nbsp; CETAK BUKTI PEMBAYARAN DENDA
					</a>
				@endslot

				<div class="card-body">
					<div class="clearfix">&nbsp;</div>
					<div class="row">
						<div class="col-6 text-left">
							<h3 class="mb-2">{{strtoupper($kantor_aktif['nama'])}}</h3>
							<p class="mb-0"><i class="fa fa-building-o fa-fw"></i>&nbsp; {{implode(' ', $kantor_aktif['alamat'])}}</p>
							<p class="mb-0"><i class="fa fa-phone fa-fw"></i>&nbsp; {{$kantor_aktif['telepon']}}</p>
						</div>
						<div class="col-6 text-right">
							<div class="row justify-content-end">
								<div class="col-2">Nomor</div>
								<div class="col-6">{{$angsuran['nomor_faktur']}}</div>
							</div>
							<div class="row justify-content-end">
								<div class="col-2">Tanggal</div>
								<div class="col-6">
									{{ $tanggal_bayar->format('d/m/Y') }}
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
					<div class="clearfix">&nbsp;</div>
					<table class="w-100">
						<tr class="align-top">
							<td style="width: 12.5%">AC / SPK</td>
							<td style="width: 1%">:</td>
							<td class="w-25 pl-2 pr-2">
								<p class="mb-2" style="border-bottom: 1px dotted #ccc">{{$angsuran['nomor_kredit']}}</p>
							</td>
							<td style="width: 12.5%">Nama</td>
							<td style="width: 1%">:</td>
							<td class="w-25 pl-2 pr-2">
								<p class="mb-2" style="border-bottom: 1px dotted #ccc">
									{{ $angsuran['kredit']['nasabah']['nama'] }}
								</p>
							</td>
						</tr>
						<tr>
							<td colspan="6">
								<div class="clearfix">&nbsp;</div>
								<div class="clearfix">&nbsp;</div>
								<table class="table table-bordered">
									<thead>
										<tr>
											<th class="text-center" style="width: 5%;">#</th>
											<th class="text-left" style="width: 22%;">Deskripsi</th>
											<th class="text-right">Subtotal</th>
										</tr>
									</thead>
									<tbody>
										@foreach ($angsuran['details'] as $k => $v)
											<tr>
												<td>{{ $loop->iteration }}</td>
												<td>{{ $v['deskripsi'] }}</td>
												<td class="text-right">{{ $v['jumlah'] }}</td>
											</tr>
										@endforeach
									</tbody>
									<tfoot>
										<tr>
											<td class="text-right" colspan="2"><h5><strong>Total</strong></h5></td>
											<td class="text-right"><h5><strong>{{ $angsuran['jumlah'] }}</strong></h5></td>
										</tr>
									</tfoot>
								</table>
							</td>
						</tr>
					</table>
					<div class="row">
						<div class="col-6">
							<table class="table table-bordered w-100 mt-4">
								<thead class="thead-light">
									<tr>
										<th class="text-center p-2 w-25">Dibuat</th>
										<th class="text-center p-2 w-25">Diperiksa</th>
										<th class="text-center p-2 w-25">Disetujui</th>
										<th class="text-center p-2 w-25">Dibayar</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="padding: 35px;">&nbsp;</td>
										<td style="padding: 35px;">&nbsp;</td>
										<td style="padding: 35px;">&nbsp;</td>
										<td style="padding: 35px;">&nbsp;</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="col-6">
							<table class="table w-50 text-center ml-auto mr-5 mt-2" style="height: 220px;">
								<tbody>
									<tr>
										<td class="border-0">{{ $kantor_aktif['alamat']['kota'] }}, {{ $tanggal_bayar->format('d/m/Y') }}</td>
									</tr>
									<tr>
										<td class="border-0">
											<p class="border border-left-0 border-right-0 border-bottom-0">{{$angsuran['kredit']['nasabah']['nama']}}</p>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="row">
						<div class="col-6">
						</div>
						<div class="col-6">
						</div>
					</div>
				</div>
			@endcomponent
		</div>
	</div>
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush
