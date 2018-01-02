@inject('idr', 'App\Service\UI\IDRTranslater')
@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 d-flex text-center"><i class="fa fa-credit-card-alt mr-2"></i> KREDIT</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3 sidebar-plain">
			@include('v2.kredit.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				@slot('pre')
					<h6 class="pt-4 pl-4">PENERIMAAN ANGSURAN</h6>
				@endslot
				@slot('body')
					{{-- SEARCH --}}
					{!! Form::open(['method' => "GET"]) !!}
						@foreach(request()->all() as $k => $v)
					 		@if(!str_is($k, 'q'))
						 		<input type="hidden" name="{{$k}}" value="{{$v}}">
					 		@endif
					 	@endforeach
						<div class="form-row">
							<div class='col-sm-2 order-1'>{!! Form::bsText(null, 'start', null, ['placeholder' => 'mulai', 'class' => 'mask-date form-control']) !!}</div>
							<div class="col-auto order-2"><i class="fa fa-minus pt-3"></i></div>
							<div class='col-sm-2 order-3'>{!! Form::bsText(null, 'end', null, ['placeholder' => 'sampai', 'class' => 'mask-date form-control']) !!}</div>
							<!-- <div class='col-sm-1 order-2'>{!! Form::bsSelect(null, 'periode', ['daily' => 'Daily', 'monthly' => 'Monthly', 'yearly' => 'Yearly'], null) !!}</div> -->
							<div class='col-auto order-3'>{!! Form::bsSubmit('<i class="fa fa-search"></i>', ['class' => 'btn btn-primary']) !!}</div>
						</div>
					{!! Form::close() !!}

					<div class="clearfix">&nbsp;</div>
					<table class="table table-bordered">
						<thead>
							<tr class="text-center">
								<th class="text-left">#</th>
								<th class="text-left">Nasabah</th>
								<th class="text-right">Pokok</th>
								<th class="text-right">Bunga</th>
								<th class="text-right">Denda</th>
								<th class="text-right">Potongan</th>
								<th class="text-right">Subtotal</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							@php $lua = null @endphp
							@forelse($angsuran as $k => $v)
								@php $pa = \Carbon\Carbon::createfromformat('d/m/Y H:i', $v['tanggal'])->format('d/m/Y') @endphp
								@if($lua != $pa)
									<tr>
										<td colspan="8" class="bg-light">
											{{$pa}}
										</td>
									</tr>
									@php $lua = $pa @endphp
								@endif
								<tr class="text-center">
									<td class="text-left">
										{{$v['kredit']['nomor_kredit']}}
									</td>
									<td class="text-left">
										{{$v['kredit']['nasabah']['nama']}}
									</td>
									<td class="text-right">
										{{$idr->formatMoneyTo($v['pokok'])}}
									</td>
									<td class="text-right">
										{{$idr->formatMoneyTo($v['bunga'])}}
									</td>
									<td class="text-right">
										{{$idr->formatMoneyTo($v['denda'])}}
									</td>
									<td class="text-right">
										{{$idr->formatMoneyTo($v['potongan'])}}
									</td>
									<td class="text-right">
										{{$idr->formatMoneyTo($v['subtotal'])}}
									</td>
									<td>
										<a href="{{route('kredit.show', ['id' => $v['kredit']['id'], 'kantor_aktif_id' => $kantor_aktif_id, 'current' => 'penagihan'])}}">Lihat Kredit</a>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="8">
										<p>Data tidak tersedia, silahkan pilih Koperasi/BPR lain</p>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				@endslot
			@endcomponent
		</div>
	</div>
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush

@push('css')
@endpush      