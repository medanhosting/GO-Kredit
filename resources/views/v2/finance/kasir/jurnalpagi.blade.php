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
										<th class="text-center align-middle" rowspan="2">NO</th>
										<th class="text-center align-middle" rowspan="2">NAMA</th>
										<th class="text-center align-middle" rowspan="2">NO.AC</th>
										<th class="text-center align-middle" rowspan="2">SPK</th>
										<th class="text-center align-middle" rowspan="2">TANGGAL<br/>REALISASI</th>
										<th class="text-center align-middle" rowspan="2">JATUH<br/>TEMPO</th>
										<th class="text-center align-middle" rowspan="2">BBN<br/>KE</th>
										<th class="text-center align-middle" colspan="2">BEBAN POKOK</th>
										<th class="text-center align-middle" colspan="2">BEBAN BUNGA</th>
										<th class="text-center align-middle" rowspan="2">BEBAN<br/>DENDA</th>
										<th class="text-center align-middle" rowspan="2">TGK</th>
										<th class="text-center align-middle" colspan="2">TUNGGAKAN POKOK</th>
										<th class="text-center align-middle" colspan="2">TUNGGAKAN BUNGA</th>
										<th class="text-center align-middle" rowspan="2">T</th>
										<th class="text-center align-middle" rowspan="2">P.TITIPAN POKOK<br/>PA</th>
										<th class="text-center align-middle" colspan="2">P.TITIPAN BUNGA</th>
									</tr>
									<tr>
										<th class="text-center">PA</th>
										<th class="text-center">PT</th>
										<th class="text-center">PA</th>
										<th class="text-center">PT</th>
										<th class="text-center">PA</th>
										<th class="text-center">PT</th>
										<th class="text-center">PA</th>
										<th class="text-center">PT</th>
										<th class="text-center">PA</th>
										<th class="text-center">PT</th>
									</tr>
								</thead>
								<tbody>
									@php 
										$total_pa_pokok 	= 0;
										$total_pa_bunga 	= 0;
										$total_pt_pokok 	= 0;
										$total_pt_bunga 	= 0;
										$total_denda 		= 0;
										$total_pa_t_pokok 	= 0;
										$total_pa_t_bunga 	= 0;
										$total_pt_t_pokok 	= 0;
										$total_pt_titip_bunga 	= 0;
										$total_pa_titip_pokok 	= 0;
										$total_pa_titip_bunga 	= 0;
									@endphp 
									@forelse($kredit as $k => $v)
										<tr class="text-center">
											<td class="text-left">
												{{$loop->iteration}}
											</td>
											<td class="text-left">
												{{$v['nasabah']['nama']}}
											</td>
											<td>
											</td>
											<td class="text-left">
												{{$v['nomor_kredit']}}
											</td>
											<td class="text-left">
												{{$carbon::createfromformat('d/m/Y H:i', $v['tanggal'])->format('d/m/Y')}}
											</td>
											<td class="text-left">
												{{$carbon::createfromformat('d/m/Y H:i', $v['angsuran_terakhir']['tanggal'])->format('d/m/Y')}}
											</td>
											<td class="text-center">
												{{$v['angsuran'][0]['nth']}}
											</td>

											@if(str_is($v['jenis_pinjaman'], 'pa'))
											<td class="text-right">
												@php $total_pa_pokok = $total_pa_pokok + $v['angsuran'][0]['pokok'] @endphp
												{{$idr->formatMoneyTo($v['angsuran'][0]['pokok'])}}
											</td>
											<td class="text-right">
											</td>
											<td class="text-right">
												@php $total_pa_bunga = $total_pa_bunga + $v['angsuran'][0]['bunga'] @endphp
												{{$idr->formatMoneyTo($v['angsuran'][0]['bunga'])}}
											</td>
											<td class="text-right">
											</td>
											<td class="text-right">
												@if($v['denda'][0]['denda'])
												@php $total_denda = $total_denda + $v['denda'][0]['denda'] @endphp
												{{$idr->formatMoneyTo($v['denda'][0]['denda'])}}
												@endif
											</td>
											@if($v['tunggakan'][0]['tgk'])
											<td class="text-center">
												{{$v['tunggakan'][0]['tgk']/2}}
											</td>
											<td class="text-right">
												@php $total_pa_t_pokok = $total_pa_t_pokok + $v['tunggakan'][0]['pokok'] @endphp
												{{$idr->formatMoneyTo($v['tunggakan'][0]['pokok'])}}
											</td>
											<td class="text-right">
											</td>
											<td class="text-right">
												@php $total_pa_t_bunga = $total_pa_t_bunga + $v['tunggakan'][0]['bunga'] @endphp
												{{$idr->formatMoneyTo($v['tunggakan'][0]['bunga'])}}
											</td>
											<td class="text-right">
											</td>
											@else
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
											@endif
											@php 	
												$total 	= $v['angsuran'][0]['pokok'] + $v['angsuran'][0]['bunga'];
												$titip 	= floor($v['titipan'][0]['titipan']/$total);
											@endphp

											@if($titip > 0)
											<td class="text-center">
												T
											</td>
											<td class="text-right">
												@php $total_pa_titip_pokok = $total_pa_titip_pokok + $v['angsuran'][0]['pokok'] @endphp
												{{$idr->formatMoneyTo($v['angsuran'][0]['pokok'])}}
											</td>
											<td class="text-right">
												@php $total_pa_titip_bunga = $total_pa_titip_bunga + $v['angsuran'][0]['bunga'] @endphp
												{{$idr->formatMoneyTo($v['angsuran'][0]['bunga'])}}
											</td>
											<td class="text-right">
											</td>
											@else
												<td></td>
												<td></td>
												<td></td>
												<td></td>
											@endif
										@else
											<td class="text-right">
											</td>
											<td class="text-right">
												@php $total_pt_pokok = $total_pt_pokok + $v['angsuran'][0]['pokok'] @endphp
												{{$idr->formatMoneyTo($v['angsuran'][0]['pokok'])}}
											</td>
											<td class="text-right">
											</td>
											<td class="text-right">
												@php $total_pt_bunga = $total_pt_bunga + $v['angsuran'][0]['bunga'] @endphp
												{{$idr->formatMoneyTo($v['angsuran'][0]['bunga'])}}
											</td>
											<td class="text-right">
												@if($v['denda'][0]['denda'])
												@php $total_denda = $total_denda + $v['denda'][0]['denda'] @endphp
												{{$idr->formatMoneyTo($v['denda'][0]['denda'])}}
												@endif
											</td>
											@if($v['tunggakan'][0]['tgk'])
											<td class="text-center">
												{{$v['tunggakan'][0]['tgk']}}
											</td>
											<td class="text-right">
											</td>
											<td class="text-right">
												@php $total_pt_t_pokok = $total_pt_t_pokok + $v['tunggakan'][0]['pokok'] @endphp
												{{$idr->formatMoneyTo($v['tunggakan'][0]['pokok'])}}
											</td>
											<td class="text-right">
											</td>
											<td class="text-right">
												@php $total_pt_t_bunga = $total_pt_t_bunga + $v['tunggakan'][0]['bunga'] @endphp
												{{$idr->formatMoneyTo($v['tunggakan'][0]['bunga'])}}
											</td>
											@else
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
											@endif
											@php 	
												$total 	= $v['angsuran'][0]['pokok'] + $v['angsuran'][0]['bunga'];
												$titip 	= floor($v['titipan'][0]['titipan']/$total);
											@endphp

											@if($titip > 0)
											<td class="text-center">
												T
											</td>
											<td class="text-right">
											</td>
											<td class="text-right">
											</td>
											<td class="text-right">
												@php $total_pt_titip_bunga = $total_pt_titip_bunga + $v['angsuran'][0]['bunga'] @endphp
												{{$idr->formatMoneyTo($v['angsuran'][0]['bunga'])}}
											</td>
											@else
												<td></td>
												<td></td>
												<td></td>
												<td></td>
											@endif
										@endif
										</tr>
									@empty
									@endforelse
								</tbody>
								<tfoot>
									<tr>
										<th class="text-left" colspan="7">Jumlah Hari Ini</th>
										<th class="text-right">{{$idr->formatMoneyTo($total_pa_pokok)}}</th>
										<th class="text-right">{{$idr->formatMoneyTo($total_pt_pokok)}}</th>
										<th class="text-right">{{$idr->formatMoneyTo($total_pa_bunga)}}</th>
										<th class="text-right">{{$idr->formatMoneyTo($total_pt_bunga)}}</th>
										<th class="text-right">{{$idr->formatMoneyTo($total_denda)}}</th>
										<th class="text-right"></th>
										<th class="text-right">{{$idr->formatMoneyTo($total_pa_t_pokok)}}</th>
										<th class="text-right">{{$idr->formatMoneyTo($total_pt_t_pokok)}}</th>
										<th class="text-right">{{$idr->formatMoneyTo($total_pa_t_bunga)}}</th>
										<th class="text-right">{{$idr->formatMoneyTo($total_pt_t_bunga)}}</th>
										<th class="text-right"></th>
										<th class="text-right">{{$idr->formatMoneyTo($total_pa_titip_pokok)}}</th>
										<th class="text-right">{{$idr->formatMoneyTo($total_pa_titip_bunga)}}</th>
										<th class="text-right">{{$idr->formatMoneyTo($total_pt_titip_bunga)}}</th>
									</tr>
								</tfoot>
							</table>

							<div class="row">
								<div class="col-4">
									<table class="table table-bordered" style="font-size:8px;">
										<tbody>
											<tr>
												<td>
													120.300
												</td>
												<td>
													Piut Pokok PA JT
												</td>
												<td>
													{{$idr->formatMoneyTo($total_pa_pokok)}}
												</td>
											</tr>
											<tr>
												<td>
													&emsp;120.100
												</td>
												<td>
													&emsp;PA
												</td>
												<td>
													{{$idr->formatMoneyTo($total_pa_pokok)}}
												</td>
											</tr>

											<tr>
												<td>
													140.100
												</td>
												<td>
													Piut Bunga PA
												</td>
												<td>
													{{$idr->formatMoneyTo($total_pa_bunga)}}
												</td>
											</tr>
											<tr>
												<td>
													&emsp;260.110
												</td>
												<td>
													&emsp;PYD BUNGA
												</td>
												<td>
													{{$idr->formatMoneyTo($total_pa_bunga)}}
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<div class="col-4">
									<table class="table table-bordered" style="font-size:8px;">
										<tbody>
											<tr>
												<td>
													120.400
												</td>
												<td>
													Piut Pokok PT JT
												</td>
												<td>
													{{$idr->formatMoneyTo($total_pt_pokok)}}
												</td>
											</tr>
											<tr>
												<td>
													&emsp;120.200
												</td>
												<td>
													&emsp;PT
												</td>
												<td>
													{{$idr->formatMoneyTo($total_pt_pokok)}}
												</td>
											</tr>

											<tr>
												<td>
													140.200
												</td>
												<td>
													Piut Bunga PT
												</td>
												<td>
													{{$idr->formatMoneyTo($total_pt_bunga)}}
												</td>
											</tr>
											<tr>
												<td>
													&emsp;260.110
												</td>
												<td>
													&emsp;PYD BUNGA
												</td>
												<td>
													{{$idr->formatMoneyTo($total_pt_bunga)}}
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>

							<div class="row">
								<div class="col-4">
									<table class="table table-bordered" style="font-size:8px;">
										<tbody>
											<tr>
												<td>
													140.600
												</td>
												<td>
													Piutang Denda
												</td>
												<td>
													{{$idr->formatMoneyTo($total_denda)}}
												</td>
											</tr>
											<tr>
												<td>
													&emsp;260.120
												</td>
												<td>
													&emsp;PYD DENDA
												</td>
												<td>
													{{$idr->formatMoneyTo($total_denda)}}
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<div class="col-4">
									<table class="table table-bordered" style="font-size:8px;">
										<tbody>
											<tr>
												<td>
													200.210
												</td>
												<td>
													Titipan Angsuran
												</td>
												<td>
													{{$idr->formatMoneyTo($total_pa_titip_pokok + $total_pa_titip_bunga + $total_pt_titip_bunga)}}
												</td>
											</tr>
											<tr>
												<td>
													&emsp;120.300
												</td>
												<td>
													&emsp;Piut Pokok PA JT
												</td>
												<td>
													{{$idr->formatMoneyTo($total_pa_titip_pokok)}}
												</td>
											</tr>
											<tr>
												<td>
													&emsp;140.100
												</td>
												<td>
													&emsp;Piut Bunga PA
												</td>
												<td>
													{{$idr->formatMoneyTo($total_pa_titip_bunga)}}
												</td>
											</tr>
											<tr>
												<td>
													&emsp;140.200
												</td>
												<td>
													&emsp;Piut Bunga PT
												</td>
												<td>
													{{$idr->formatMoneyTo($total_pt_titip_bunga)}}
												</td>
											</tr>
										</tbody>
									</table>
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

@push('css')
	<style type="text/css">
		.table th, .table td {
			padding: 5px;
		}
	</style>
@endpush