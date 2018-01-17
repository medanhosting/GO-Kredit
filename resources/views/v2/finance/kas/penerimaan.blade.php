@inject('idr', 'App\Service\UI\IDRTranslater')
@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 text-center"><i class="fa fa-credit-card-alt mr-2"></i> KREDIT</h5>
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
					<a href="{{ route('kas.print', ['type' => 'penerimaan', 'kantor_aktif_id' => $kantor_aktif['id']]) }}" target="__blank" class="text-success float-right btn btn-link">
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
										<th class="text-left align-middle" rowspan="2">No</th>
										<th class="text-left align-middle" rowspan="2">Bukti</th>
										<!-- <th class="text-left align-middle" rowspan="2">Uraian</th> -->
										<th class="text-center align-middle" colspan="2">Pokok</th>
										<th class="text-center align-middle" colspan="2">Bunga</th>
										<th class="text-right align-middle" rowspan="2">Denda</th>
										<th class="text-right align-middle" rowspan="2">Titipan</th>
										<th class="text-right align-middle" rowspan="2">Lain - Lain</th>
									</tr>
									<tr>
										<th class="text-right">PA</th>
										<th class="text-right">PT</th>
										<th class="text-right">PA</th>
										<th class="text-right">PT</th>
									</tr>
								</thead>
								<tbody>
									@php $lua = null @endphp
									@forelse($angsuran as $k => $v)
										
										<tr class="text-center">
											<td class="text-left">
												{{$loop->iteration}}
											</td>
											<td class="text-left">
												<a href="{{route('kredit.show', ['id' => $v['kredit']['id'], 'kantor_aktif_id' => $kantor_aktif_id, 'current' => 'angsuran'])}}">
												{{$v['nomor_faktur']}}
												</a>
											</td>
											<!-- <td class="text-left">
												Angsuran Kredit Nomor {{$v['nomor_kredit']}}
											</td> -->
											<td class="text-right">
												@if(str_is($v['kredit']['jenis_pinjaman'], 'pa'))
												{{$idr->formatMoneyTo($v['pokok'])}}
												@else
												{{$idr->formatMoneyTo(0)}}
												@endif
											</td>
											<td class="text-right">
												@if(str_is($v['kredit']['jenis_pinjaman'], 'pt'))
												{{$idr->formatMoneyTo($v['pokok'])}}
												@else
												{{$idr->formatMoneyTo(0)}}
												@endif
											</td>
											<td class="text-right">
												@if(str_is($v['kredit']['jenis_pinjaman'], 'pa'))
												{{$idr->formatMoneyTo($v['bunga'])}}
												@else
												{{$idr->formatMoneyTo(0)}}
												@endif
											</td>
											<td class="text-right">
												@if(str_is($v['kredit']['jenis_pinjaman'], 'pt'))
												{{$idr->formatMoneyTo($v['bunga'])}}
												@else
												{{$idr->formatMoneyTo(0)}}
												@endif
											</td>
											<td class="text-right">
												{{$idr->formatMoneyTo($v['denda'])}}
											</td>
											<td class="text-right">
												{{$idr->formatMoneyTo($v['titipan'])}}
											</td>
											<td class="text-right">
												{{$idr->formatMoneyTo(0)}}
												<!-- {{$idr->formatMoneyTo($v['potongan'] + $v['potongan_denda'])}} -->
											</td>
										</tr>
									@empty
									@endforelse
									<tfoot>
										<tr>
											<td colspan="9">&nbsp;</td>
										</tr>
										<tr>
											<td colspan="2">JUMLAH PENERIMAAN HARI INI</td>
											<td class="text-right">{{$idr->formatMoneyTo(array_sum(array_column($angsuran->toArray(), 'subtotal')))}}</td>
											<td colspan="2">JUMLAH ANGSURAN JATUH TEMPO</td>
											<td class="text-right">{{$idr->formatMoneyTo($total_jatuh_tempo)}}</td>
											<th>Dibukukan</th>
											<th>Diperiksa</th>
											<th>Dibuat</th>
										</tr>
										<tr>
											<td colspan="2">SALDO KEMARIN TANGGAL {{$today->subdays(1)->format('d/m/Y')}}</td>
											<td class="text-right">{{$idr->formatMoneyTo($total_money_yesterday)}}</td>
											<td colspan="2">JUMLAH PELUNASAN DIPERCEPAT DAN PENURUNAN POKOK</td>
											<td class="text-right">{{$idr->formatMoneyTo($total_pelunasan)}}</td>
											<td rowspan="4">&nbsp;</td>
											<td rowspan="4">&nbsp;</td>
											<td rowspan="4">&nbsp;</td>
										</tr>
										<tr>
											<td colspan="2">JUMLAH KONTROL</td>
											<td></td>
											<td colspan="2">TOTAL ANGSURAN</td>
											<td class="text-right">{{$idr->formatMoneyTo($total_angsuran)}}</td>
										</tr>
										<tr>
											<td colspan="2" rowspan="2">{{$kantor_aktif['nama']}}<br/>
											<small>{{implode(' ', $kantor_aktif['alamat'])}}</small>
											</td>
											<td rowspan="2">SALDO KAS HARI INI<br/> {{$today->adddays(1)->format('d/m/Y')}}</td>
											<td rowspan="2" class="text-right"><h3>{{$idr->formatMoneyTo($total_money + $total_money_yesterday )}}</h3></td>
											<td colspan="2" rowspan="2"><i>Terbilang : {{ucwords($idr->terbilang(abs($total_money + $total_money_yesterday) ))}} Rupiah</i></td>
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