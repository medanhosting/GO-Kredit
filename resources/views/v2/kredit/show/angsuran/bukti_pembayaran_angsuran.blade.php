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
						&nbsp;&nbsp;DETAIL BUKTI ANGSURAN
					</h5>
					
					<a href="{{ route('angsuran.print', ['id' => $angsuran['nomor_kredit'], 'nota_bayar_id' => $angsuran['id'], 'kantor_aktif_id' => $kantor_aktif['id']]) }}" target="__blank" class="text-success float-right btn btn-link">
						<i class="fa fa-file-o fa-fw"></i>&nbsp; CETAK BUKTI ANGSURAN
					</a>
				@endslot

				<div class="card-body">
					<div class="row">
						<div class="col-12 col-sm-12 col-md-12">
							<div class="clearfix">&nbsp;</div>
							<div class="row justify-content-center">
								<div class="col">
									{{--  {!! Form::open(['url' => route('putusan.update', ['id' => $putusan['pengajuan_id'], 'kantor_aktif_id' => $kantor_aktif_id]), 'method' => 'PATCH']) !!}  --}}
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
												<h4 class="mb-2">BUKTI ANGSURAN KREDIT</h4>
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
												<td style="width: 12.5%">AO</td>
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
														{{ $angsuran['kredit']['nasabah']['nama'] }}
													</p>
												</td>
												<td style="width: 12.5%">Angsuran Ke-</td>
												<td style="width: 1%">:</td>
												<td class="w-25 pl-2 pr-2 text-capitalize">
													<p class="mb-2" style="border-bottom: 1px dotted #ccc">
														@foreach($angsuran['details'] as $k => $v)
															@if ($loop->last)
																{{ $v['nth'] }}
															@else
																{{ $v['nth'] }}, 
															@endif
														@endforeach
													</p>
												</td>
											</tr>
											<tr class="align-top">
												<td style="width: 12.5%">Alamat</td>
												<td style="width: 1%">:</td>
												<td class="w-25 pl-2 pr-2 text-capitalize">
													<p class="mb-2" style="border-bottom: 1px dotted #ccc">
														{{ strtolower(implode(' ', $angsuran['kredit']['nasabah']['alamat'])) }}
													</p>
												</td>
												<td style="width: 12.5%">Sisa Angsuran</td>
												<td style="width: 1%">:</td>
												<td class="w-25 pl-2 pr-2">
													<p class="mb-2" style="border-bottom: 1px dotted #ccc">{{ $idr->formatMoneyTo($sisa_angsuran) }}</p>
												</td>
											</tr>
											<tr class="align-top">
												<td style="width: 12.5%">Telp.</td>
												<td style="width: 1%">:</td>
												<td class="w-25 pl-2 pr-2">
													<p class="mb-2" style="border-bottom: 1px dotted #ccc">{{ $angsuran['kredit']['nasabah']['telepon'] }}</p>
												</td>
											</tr>
											<tr>
												<td colspan="6">
													<div class="clearfix">&nbsp;</div>
													<div class="clearfix">&nbsp;</div>
													<table class="table w-100 table-bordered">
														<thead>
															<tr>
																<th>#</th>
																<th>Angsuran</th>
																<th class="text-right">Pokok</th>
																<th class="text-right">Bunga</th>
																<th class="text-right">Potongan</th>
																<th class="text-right">Sub Total</th>
															</tr>
														</thead>
														<tbody>
															@foreach ($angsuran['details'] as $k => $v)
																<tr>
																	<td>{{ $loop->iteration }}</td>
																	<td>Angsuran ke- {{ $v['nth'] }}</td>
																	<td class="text-right">{{ $idr->formatMoneyTo($v['pokok']) }}</td>
																	<td class="text-right">{{ $idr->formatMoneyTo($v['bunga']) }}</td>
																	<td class="text-right">{{ $idr->formatMoneyTo($v['potongan']) }}</td>
																	<td class="text-right">{{ $idr->formatMoneyTo($v['subtotal']) }}</td>
																</tr>
															@endforeach
														</tbody>
														<tfoot>
															<tr>
																<td class="text-right" colspan="5"><h5><strong>Total</strong></h5></td>
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
									{{--  {!! Form::close() !!}  --}}
								</div>
							</div>
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
