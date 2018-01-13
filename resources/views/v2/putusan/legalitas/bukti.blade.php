@inject('idr', 'App\Service\UI\IDRTranslater')
@inject('tanggal', 'App\Service\UI\TanggalTranslater')
@inject('carbon', 'Carbon\Carbon')

<div class="clearfix">&nbsp;</div>
<div class="row justify-content-center">
	<div class="col">
		{!! Form::open(['url' => route('putusan.update', ['id' => $putusan['pengajuan_id'], 'kantor_aktif_id' => $kantor_aktif_id]), 'method' => 'PATCH']) !!}
			<div class="row">
				<div class="col-6 text-left">
					<h3 class="mb-2">{{strtoupper($kantor_aktif['nama'])}}</h3>
					<p class="mb-0"><i class="fa fa-building-o fa-fw"></i>&nbsp; {{implode(' ', $kantor_aktif['alamat'])}}</p>
					<p class="mb-0"><i class="fa fa-phone fa-fw"></i>&nbsp; {{$kantor_aktif['telepon']}}</p>
				</div>
				<div class="col-6 text-right">
					<div class="row justify-content-end">
						<div class="col-2">Nomor</div>
						<div class="col-6">{{$kantor_aktif['id']}} / {{$putusan['pengajuan_id']}}</div>
					</div>
					<div class="row justify-content-end">
						<div class="col-2">Tanggal</div>
						<div class="col-6">
							@if(str_is($putusan['pengajuan']['status_terakhir']['progress'], 'sudah') &&  str_is($putusan['pengajuan']['status_terakhir']['status'], 'setuju'))
								{!! Form::vText(null, 'tanggal_pencairan', $carbon::now()->format('d/m/Y H:i'), ['class' => 'form-control inline-edit border-input text-info  mask-date-time w-50 ml-auto', 'placeholder' => 'dd/mm/yyyy hh:mm'], true) !!}
							@else
								{{$putusan['pengajuan']['status_terakhir']['tanggal']}}
							@endif
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col text-center">
					<h4 class="mb-1">BUKTI REALISASI KREDIT</h4>
				</div>
			</div>
			<hr class="mt-1 mb-2" style="border-size: 2px;">
			<p class="mb-3">Sudah terima dari {{ strtoupper($kantor_aktif['nama']) }} sejumlah uang dibawah ini untuk direalisasi kredit:</p>
			<table>
				<tr class="align-top">
					<td style="width: 12.5%">Nomor Rekening</td>
					<td style="width: 1%">:</td>
					<td class="w-25 pl-2 pr-2">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">&nbsp;</p>
					</td>
					<td style="width: 12.5%">No. SPK</td>
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
					<td style="width: 12.5%">Usaha</td>
					<td style="width: 1%">:</td>
					<td class="w-25 pl-2 pr-2">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">&nbsp;</p>
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
					<td style="width: 12.5%">Jenis Pinjaman</td>
					<td style="width: 1%">:</td>
					<td class="w-25 pl-2 pr-2">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">{{strtoupper($putusan['pengajuan']['analisa']['jenis_pinjaman'])}}</p>
					</td>
				</tr>
				<tr class="align-top">
					<td style="width: 12.5%">Jaminan</td>
					<td style="width: 1%">:</td>
					<td class="w-25 pl-2 pr-2">
					@foreach($survei['jaminan_kendaraan'] as $k => $v)
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">
							BPKB
							{{strtoupper($v['dokumen_survei']['collateral']['bpkb']['tipe'])}}
							{{strtoupper($v['dokumen_survei']['collateral']['bpkb']['merk'])}}
							NOMOR 
							{{strtoupper($v['dokumen_survei']['collateral']['bpkb']['nomor_bpkb'])}}
						</p>
					@endforeach
					@foreach($survei['jaminan_tanah_bangunan'] as $k => $v)
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">
							{{strtoupper($v['dokumen_survei']['collateral']['jenis'])}}
							{{strtoupper($v['dokumen_survei']['collateral'][$v['dokumen_survei']['collateral']['jenis']]['tipe'])}}
							NOMOR 
							{{strtoupper($v['dokumen_survei']['collateral'][$v['dokumen_survei']['collateral']['jenis']]['nomor_sertifikat'])}}
						</p>
					@endforeach
					</td>
					<td style="width: 12.5%">Nama AO</td>
					<td style="width: 1%">:</td>
					<td class="w-25 pl-2 pr-2 text-capitalize">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">
							{{ $putusan['pengajuan']['ao']['nama'] }}
						</p>
					</td>
				</tr>
				<tr class="align-top">
					<td style="width: 12.5%">Nominal</td>
					<td style="width: 1%">:</td>
					<td class="pl-2 pr-2 w-75" colspan="4">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">
							{{ ($putusan['plafon_pinjaman']) }}
						</p>
					</td>
				</tr>
				<tr class="align-top">
					<td style="width: 12.5%">Terbilang</td>
					<td style="width: 1%">:</td>
					<td class="pl-2 pr-2 text-capitalize" colspan="4">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">
							{{ $idr->terbilang($idr->formatMoneyFrom($putusan['plafon_pinjaman'])) }}
						</p>
					</td>
				</tr>
				<tr class="align-top">
					<td style="width: 12.5%">Jangka Waktu</td>
					<td style="width: 1%">:</td>
					<td class="w-25 pl-2 pr-2">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">
							{{ $putusan['jangka_waktu'] }} Bulan
						</p>
					</td>
					<td style="width: 12.5%">Suku Bunga</td>
					<td style="width: 1%">:</td>
					<td class="w-25 pl-2 pr-2">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">
							{{ $putusan['suku_bunga'] }} %
						</p>
					</td>
				</tr>
				<tr class="align-top">
					<td style="width: 12.5%">Angsuran per Bulan</td>
					<td style="width: 1%">:</td>
					<td class="w-25 pl-2 pr-2">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">
							{{ $putusan['pengajuan']['kemampuan_angsur'] }}
						</p>
					</td>
					<td style="width: 12.5%">Tgl Jatuh Tempo per Bulan</td>
					<td style="width: 1%">:</td>
					<td class="w-25 pl-2 pr-2">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">
							{{ $carbon::createFromFormat('d/m/Y H:i', $putusan['pengajuan']['status_terakhir']['tanggal'])->format('d') }}
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
					<table class="table w-50 text-center ml-auto mr-5" style="height: 220px;">
						<tbody>
							<tr>
								<td class="border-0">{{ $kantor_aktif['alamat']['kota'] }}, {{ $carbon->now()->format('d/m/Y') }}</td>
							</tr>
							<tr>
								<td class="border-0">
									<p class="border border-left-0 border-right-0 border-bottom-0">Nama terang dan tanda tangan</p>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			
			@if(str_is($putusan['pengajuan']['status_terakhir']['progress'], 'sudah') &&  str_is($putusan['pengajuan']['status_terakhir']['status'], 'setuju'))
				<hr>
				<div class="row">
					<div class="col-6">
					</div>
					<div class="col-6">
						{!! Form::vSelect('Diambil Dari', 'kode_akun', $akun, '', ['class' => 'form-control text-info inline-edit text-right'], true) !!}
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						{!! Form::bsSubmit('Realisasikan', ['class' => 'btn btn-primary float-right']) !!}
					</div>
				</div>
			@endif
		{!! Form::close() !!}
	</div>
</div>