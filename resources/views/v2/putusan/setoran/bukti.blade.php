@inject('idr', 'App\Service\UI\IDRTranslater')
@inject('tanggal', 'App\Service\UI\TanggalTranslater')
@inject('carbon', 'Carbon\Carbon')

<div class="clearfix">&nbsp;</div>
<div class="row justify-content-center">
	<div class="col">
		<div class="row">
			<div class="col-7 text-left">
				<h3 class="mb-2">{{strtoupper($kantor_aktif['nama'])}}</h3>
				<p class="mb-0"><i class="fa fa-building-o fa-fw"></i>&nbsp; {{implode(' ', $kantor_aktif['alamat'])}}</p>
				<p class="mb-0"><i class="fa fa-phone fa-fw"></i>&nbsp; {{$kantor_aktif['telepon']}}</p>
			</div>
			<div class="col-5 text-right">
				<div class="row justify-content-end">
					<div class="col-3">Nomor</div>
					<div class="col-9">{{$setoran['nomor_faktur']}}</div>
				</div>
				<div class="row justify-content-end">
					<div class="col-3">Tanggal</div>
					<div class="col-9">
						{{$tanggal_s}}
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col text-center">
				<h4 class="mb-1">BUKTI SETORAN KREDIT</h4>
			</div>
		</div>
		<hr class="mt-1 mb-2" style="border-size: 2px;">
		<table>
			<tr class="align-top">
				<td colspan="3">&nbsp;</td>
				<td rowspan="5" class="w-25 pl-2 pr-2">
					<table class="table table-bordered">
						<thead class="thead-light">
							<tr>
								<th class="text-center p-2">Uraian</th>
								<th class="text-center p-2">Jumlah</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="p-1">Provisi</td>
								<td class="p-1 text-right">{{$putusan['provisi']}}</td>
							</tr>
							<tr>
								<td class="p-1">Administrasi</td>
								<td class="p-1 text-right">{{$putusan['administrasi']}}</td>
							</tr>
							<tr>
								<td class="p-1">Legal</td>
								<td class="p-1 text-right">{{$putusan['legal']}}</td>
							</tr>
							<tr>
								<td class="p-1">Biaya Notaris</td>
								<td class="p-1 text-right">{{$putusan['biaya_notaris']}}</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<th class="p-1">Total</th>
								<th class="p-1 text-right">{{ $idr->formatMoneyTo($ts) }}</th>
							</tr>
						</tfoot>
					</table>
				</td>
			</tr>
			<tr class="align-top">
				<td style="width: 12.5%">Jenis Setoran</td>
				<td style="width: 1%">:</td>
				<td class="w-25 pl-2 pr-2">
					<p class="mb-2" style="border-bottom: 1px dotted #ccc">
						Potongan Realisasi
					</p>
				</td>
			</tr>
			<tr class="align-top">
				<td style="width: 12.5%">Nomor Rekening</td>
				<td style="width: 1%">:</td>
				<td class="w-25 pl-2 pr-2">
					<p class="mb-2" style="border-bottom: 1px dotted #ccc">&nbsp;</p>
				</td>
			</tr>
			<tr class="align-top">
				<td style="width: 12.5%">Nama</td>
				<td style="width: 1%">:</td>
				<td class="w-25 pl-2 pr-2">
					<p class="mb-2" style="border-bottom: 1px dotted #ccc">
						{{ $putusan['pengajuan']['nasabah']['nama'] }}
					</p>
				</td>
			</tr>
			<tr class="align-top">
				<td style="width: 12.5%">Alamat</td>
				<td style="width: 1%">:</td>
				<td class="w-25 pl-2 pr-2 text-capitalize">
					<p class="mb-2" style="border-bottom: 1px dotted #ccc">
						{{ strtolower(implode(' ', $putusan['pengajuan']['nasabah']['alamat'])) }}
					</p>
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
				<p class="mb-0 pt-2" style="border-bottom: 1px dotted #ccc">
					<i>Terbilang : {{ ucwords($idr->terbilang($ts)) }} Rupiah</i>
				</p>
				<table class="table w-50 text-center ml-auto mr-5" style="height: 200px;">
					<tbody>
						<tr>
							<td class="border-0">{{ $kantor_aktif['alamat']['kota'] }}, {{ $tanggal_s }}</td>
						</tr>
						<tr>
							<td class="border-0">
								<p class="border border-left-0 border-right-0 border-bottom-0">
								{{ $putusan['pengajuan']['nasabah']['nama'] }}
								</p>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>