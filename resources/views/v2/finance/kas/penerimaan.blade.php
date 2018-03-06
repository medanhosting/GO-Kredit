@inject('idr', 'App\Service\UI\IDRTranslater')
@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 text-center"><i class="fa fa-line-chart mr-2"></i> KEUANGAN</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3 sidebar-plain">
			@include('v2.finance.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				@slot('header')
					<h5 class="py-2 pl-2 mb-0 float-left">&nbsp;&nbsp;PENERIMAAN KAS</h5>
					<a href="#" target="__blank" class="text-success float-right btn btn-link">
						<i class="fa fa-file-o fa-fw"></i>&nbsp; CETAK PENERIMAAN KAS
					</a>
				@endslot

				<div class="card-body">
					<div class="row">
						<div class="col-12 col-sm-12 col-md-12">
							{{-- SEARCH --}}
							{!! Form::open(['method' => "GET"]) !!}
								@foreach(request()->all() as $k => $v)
									@if(!str_is($k, 'q'))
										<input type="hidden" name="{{$k}}" value="{{$v}}">
									@endif
								@endforeach
								<div class="form-row align-items-end">
									<div class='col-sm-2 order-1'>
										{!! Form::bsText('Tanggal', 'q', null, ['placeholder' => 'tanggal', 'class' => 'mask-date form-control']) !!}
									</div>
									<div class='col-auto order-3'>
										<div class="form-group">
											<label>&nbsp;</label>
											{!! Form::bsSubmit('<i class="fa fa-search"></i>', ['class' => 'btn btn-primary']) !!}
										</div>
									</div>
								</div>
							{!! Form::close() !!}

							<div class="clearfix">&nbsp;</div>
							<table class="table table-bordered" style="font-size:10px;">
								<thead>
									<tr class="text-center">
										<th class="text-left align-middle" rowspan="2" style="width:2%;">No</th>
										<th class="text-left align-middle" rowspan="2" style="width:18%;">Bukti</th>

										<th class="text-center align-middle" rowspan="2" style="width:8%;">Provisi</th>
										<th class="text-center align-middle" rowspan="2" style="width:8%;">Adm</th>
										<th class="text-center align-middle" rowspan="2" style="width:8%;">Legal</th>

										<th class="text-center align-middle" colspan="2" style="width:16%;">Pokok</th>
										<th class="text-center align-middle" colspan="2" style="width:16%;">Bunga</th>
										<th class="text-center align-middle" rowspan="2" style="width:8%;">Denda</th>
										<th class="text-center align-middle" rowspan="2" style="width:8%;">Titipan</th>
										<th class="text-center align-middle" rowspan="2" style="width:8%;">Lain - Lain</th>
									</tr>
									<tr>
										<th class="text-center" style="width:8%;">PA</th>
										<th class="text-center" style="width:8%;">PT</th>
										<th class="text-center" style="width:8%;">PA</th>
										<th class="text-center" style="width:8%;">PT</th>
									</tr>
								</thead>
								<tbody>
									@php $lua = null @endphp
									@forelse($jurnal as $k => $v)
										
										<tr class="text-center">
											<td class="text-center">
												{{$loop->iteration}}
											</td>
											<td class="text-left">
												<a href="{{route('kredit.show', ['id' => $v['morph_reference_id'], 'kantor_aktif_id' => $kantor_aktif_id, 'current' => 'angsuran'])}}">
												{{$v['nomor_faktur']}}
												</a>
											</td>
											<!-- <td class="text-left">
												Angsuran Kredit Nomor {{$v['nomor_kredit']}}
											</td> -->
											<td class="text-right">
												{{$idr->formatMoneyTo($v['provisi'])}}
											</td>
											<td class="text-right">
												{{$idr->formatMoneyTo($v['administrasi'])}}
											</td>
											<td class="text-right">
												{{$idr->formatMoneyTo($v['legal'])}}
											</td>
											<td class="text-right">
												{{$idr->formatMoneyTo($v['pokok_pa'])}}
											</td>
											<td class="text-right">
												{{$idr->formatMoneyTo($v['pokok_pt'])}}
											</td>
											<td class="text-right">
												{{$idr->formatMoneyTo($v['bunga_pa'])}}
											</td>
											<td class="text-right">
												{{$idr->formatMoneyTo($v['bunga_pt'])}}
											</td>
											<td class="text-right">
												{{$idr->formatMoneyTo($v['denda'])}}
											</td>
											<td class="text-right">
												{{$idr->formatMoneyTo($v['titipan'])}}
											</td>
											<td class="text-right">
												{{$idr->formatMoneyTo($v['lain_lain'])}}
											</td>
										</tr>
									@empty
									@endforelse
									<tfoot>
										<tr>
											<td colspan="12">&nbsp;</td>
										</tr>
										<tr>
											<td colspan="2">JUMLAH PENERIMAAN HARI INI</td>
											<td class="text-right">{{$idr->formatMoneyTo($total)}}</td>
											<td colspan="3">JUMLAH ANGSURAN JATUH TEMPO</td>
											<td class="text-right">{{$idr->formatMoneyTo($total_jt)}}</td>
											<td colspan="5" rowspan="5">
												<table style="width: 100%;height: 200px">
													<thead>
														<tr class="text-center">
															<th style="width: 33%">
																Dibuat
															</th>
															<th style="width: 33%">
																Dibukukan
															</th>
															<th style="width: 33%">
																Diperiksa
															</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td>&nbsp;</td>
															<td>&nbsp;</td>
															<td>&nbsp;</td>
														</tr>
													</tbody>
												</table>
											</td>
											<!-- <th>Dibuat</th> -->
										</tr>
										<tr>
											<td colspan="2">SALDO KEMARIN TANGGAL {{$today->subdays(1)->format('d/m/Y')}}</td>
											<td class="text-right">{{$idr->formatMoneyTo($p_total)}}</td>
											<td colspan="3">JUMLAH PELUNASAN DIPERCEPAT DAN PENURUNAN POKOK</td>
											<td class="text-right">{{$idr->formatMoneyTo($total_a)}}</td>
										<tr>
											<td colspan="2">JUMLAH KONTROL</td>
											<td></td>
											<td colspan="3">TOTAL ANGSURAN</td>
											<td class="text-right">{{$idr->formatMoneyTo($total_jt + $total_a)}}</td>
										</tr>
										<tr>
											<td colspan="2" rowspan="2">{{$kantor_aktif['nama']}}<br/>
											<small>{{implode(' ', $kantor_aktif['alamat'])}}</small>
											</td>
											<td rowspan="2">SALDO KAS HARI INI<br/> {{$today->adddays(1)->format('d/m/Y')}}</td>
											<td rowspan="2" class="text-right" colspan="2"><h3>{{$idr->formatMoneyTo($total + $p_total )}}</h3></td>
											<td colspan="2" rowspan="2"><i>Terbilang : {{ucwords($idr->terbilang(abs($total + $p_total) ))}} Rupiah</i></td>
										</tr>
									</tfoot>
								</tbody>
							</table>
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

@push('css')
@endpush      