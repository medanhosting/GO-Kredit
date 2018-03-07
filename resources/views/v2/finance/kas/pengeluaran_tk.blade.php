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
					<h5 class="py-2 pl-2 mb-0 float-left">&nbsp;&nbsp;TUTUP KAS PENGELUARAN UANG TUNAI</h5>
					<a href="{{route('kas.print', ['tipe' => 'pengeluaran_tk', 'kantor_aktif_id' => $kantor_aktif_id, 'q' => request()->get('q')])}}" target="__blank" class="text-success float-right btn btn-link">
						<i class="fa fa-file-o fa-fw"></i>&nbsp; CETAK TUTUP KAS PENGELUARAN UANG TUNAI
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
										<th class="text-left align-middle" rowspan="2">Account</th>
										<th class="text-center align-middle" rowspan="2">Pinjaman Angsuran</th>
										<th class="text-center align-middle" rowspan="2">Pinjaman Tetap</th>
										<th class="text-right align-middle" rowspan="2">Biaya Operasional</th>
										<th class="text-right align-middle" rowspan="2">Biaya Non Operasional</th>
										<th class="text-right align-middle" rowspan="2">Lain - Lain</th>
									</tr>
								</thead>
								<tbody>
									@php $lua = null @endphp
									@forelse($jurnal as $k => $v)
										
										<tr class="text-center">
											<td class="text-left">
												{{$loop->iteration}}
											</td>
											<td class="text-left">
												<a href="#">
												{{$v['nomor_faktur']}}
												</a>
											</td>
											<td class="text-right">
												
											</td>
											<td class="text-right">
												{{$idr->formatMoneyTo($v['pokok_pa'])}}
											</td>
											<td class="text-right">
												{{$idr->formatMoneyTo($v['pokok_pt'])}}
											</td>
											<td class="text-right">
												{{$idr->formatMoneyTo(0)}}
											</td>
											<td class="text-right">
												{{$idr->formatMoneyTo(0)}}
											</td>
											<td class="text-right">
												{{$idr->formatMoneyTo(0)}}
											</td>
										</tr>
									@empty
									@endforelse
									<tfoot>
										<tr>
											<td colspan="8">&nbsp;</td>
										</tr>
										<tr>
											<td class="align-middle" colspan="2" rowspan="3">Pengeluaran Setelah Tutup Kas : {{$tanggal->format('d/m/Y')}}</td>
											<td class="text-right align-middle" rowspan="3">{{$idr->formatMoneyTo($total)}}</td>
											<td class="text-right align-middle" colspan="2" rowspan="3"><i>Terbilang : {{ucwords($idr->terbilang(abs($total)))}} Rupiah</i></td>
											<th>Dibukukan</th>
											<th>Diperiksa</th>
											<th>Dibuat</th>
										</tr>
										<tr>
											<td rowspan="2">&nbsp;</td>
											<td rowspan="2">&nbsp;</td>
											<td rowspan="2">&nbsp;</td>
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