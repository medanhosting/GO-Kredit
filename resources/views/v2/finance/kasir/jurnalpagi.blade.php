@inject('carbon', 'Carbon\Carbon')
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
					<h5 class="py-2 pl-2 mb-0 float-left">&nbsp;&nbsp;JURNAL PAGI</h5>
					<a href="{{ route('kas.print', ['type' => 'penerimaan', 'kantor_aktif_id' => $kantor_aktif['id']]) }}" target="__blank" class="text-success float-right btn btn-link">
						<i class="fa fa-file-o fa-fw"></i>&nbsp; CETAK JURNAL PAGI
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
										<th class="text-left align-middle" rowspan="2">NO</th>
										<th class="text-left align-middle" rowspan="2">NAMA</th>
										<th class="text-left align-middle" rowspan="2">NO.AC</th>
										<th class="text-left align-middle" rowspan="2">SPK</th>
										<th class="text-left align-middle" rowspan="2">TANGGAL<br/>REALISASI</th>
										<th class="text-left align-middle" rowspan="2">JATUH<br/>TEMPO</th>
										<th class="text-left align-middle" rowspan="2">BBN<br/>KE</th>
										<!-- <th class="text-left align-middle" rowspan="2">Uraian</th> -->
										<th class="text-center align-middle" colspan="2">BEBAN POKOK</th>
										<th class="text-center align-middle" colspan="2">BEBAN BUNGA</th>
										<th class="text-right align-middle" rowspan="2">BEBAN<br/>DENDA</th>
										<th class="text-right align-middle" rowspan="2">TGK</th>
										<th class="text-right align-middle" colspan="2">TUNGGAKAN POKOK</th>
										<th class="text-right align-middle" colspan="2">TUNGGAKAN BUNGA</th>
										<th class="text-right align-middle" rowspan="2">T</th>
										<th class="text-right align-middle" rowspan="2">P.TITIPAN POKOK<br/>PA</th>
										<th class="text-right align-middle" colspan="2">P.TITIPAN BUNGA</th>
									</tr>
									<tr>
										<th class="text-right">PA</th>
										<th class="text-right">PT</th>
										<th class="text-right">PA</th>
										<th class="text-right">PT</th>
										<th class="text-right">PA</th>
										<th class="text-right">PT</th>
										<th class="text-right">PA</th>
										<th class="text-right">PT</th>
										<th class="text-right">PA</th>
										<th class="text-right">PT</th>
									</tr>
								</thead>
								<tbody>
									@forelse($kredit as $k => $v)
										<tr class="text-center">
											<td class="text-left">
												{{$loop->iteration}}
											</td>
											<td>
												{{$v['nasabah']['nama']}}
											</td>
											<td>
												
											</td>
											<td>
												{{$v['nomor_kredit']}}
											</td>
											<td>
												{{$carbon::createfromformat('d/m/Y H:i', $v['tanggal'])->format('d/m/Y')}}
											</td>
											<td>
												{{$carbon::createfromformat('d/m/Y H:i', $v['angsuran_terakhir']['tanggal'])->format('d/m/Y')}}
											</td>
											<td>
												{{$v['angsuran'][0]['nth']}}
											</td>
											<td>
												@if(str_is($v['jenis_pinjaman'], 'pa'))
													{{$idr->formatMoneyTo($v['angsuran'][0]['pokok'])}}
												@endif
											</td>
											<td>
												@if(str_is($v['jenis_pinjaman'], 'pt'))
													{{$idr->formatMoneyTo($v['angsuran'][0]['pokok'])}}
												@endif
											</td>
											<td>
												@if(str_is($v['jenis_pinjaman'], 'pa'))
													{{$idr->formatMoneyTo($v['angsuran'][0]['bunga'])}}
												@endif
											</td>
											<td>
												@if(str_is($v['jenis_pinjaman'], 'pt'))
													{{$idr->formatMoneyTo($v['angsuran'][0]['bunga'])}}
												@endif
											</td>
											<td>
												{{$idr->formatMoneyTo($v['denda'][0]['denda'])}}
											</td>
											<td>
												@if(str_is($v['jenis_pinjaman'], 'pa'))
												{{$v['tunggakan'][0]['tgk']/2}}
												@else
												{{$v['tunggakan'][0]['tgk']}}
												@endif
											</td>
											<td>
												@if(str_is($v['jenis_pinjaman'], 'pa'))
													{{$idr->formatMoneyTo($v['tunggakan'][0]['pokok'])}}
												@endif
											</td>
											<td>
												@if(str_is($v['jenis_pinjaman'], 'pt'))
													{{$idr->formatMoneyTo($v['tunggakan'][0]['pokok'])}}
												@endif
											</td>
											<td>
												@if(str_is($v['jenis_pinjaman'], 'pa'))
													{{$idr->formatMoneyTo($v['tunggakan'][0]['bunga'])}}
												@endif
											</td>
											<td>
												@if(str_is($v['jenis_pinjaman'], 'pt'))
													{{$idr->formatMoneyTo($v['tunggakan'][0]['bunga'])}}
												@endif
											</td>
											<td>
												@if($v['titipan'][0]['titipan'] * 1)
													T
												@endif
											</td>
											<td>
												@php 	
													$total 	= $v['angsuran'][0]['pokok'] + $v['angsuran'][0]['bunga'];
													$titip 	= floor($v['titipan'][0]['titipan']/$total);
												@endphp

												@if(str_is($v['jenis_pinjaman'], 'pa') && $titip > 0)
													{{$idr->formatMoneyTo($v['angsuran'][0]['pokok'])}}
												@endif
											</td>
											<td>
												@if(str_is($v['jenis_pinjaman'], 'pa') && $titip > 0)
													{{$idr->formatMoneyTo($v['angsuran'][0]['bunga'])}}
												@endif
											</td>
											<td>
												@if(str_is($v['jenis_pinjaman'], 'pt') && $titip > 0)
													{{$idr->formatMoneyTo($v['angsuran'][0]['bunga'])}}
												@endif
											</td>
										</tr>
									@empty
									@endforelse
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