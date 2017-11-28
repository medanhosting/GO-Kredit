@inject('idr', 'App\Service\UI\IDRTranslater')

@push('main')
	<div class="container bg-white bg-shadow p-4">
		<div class="row">
			<div class="col">
				<h4 class='mb-4 text-style text-secondary'>
					<span class="text-uppercase">Laporan Penerimaan Angsuran</span> 
					<small><small>@if($angsuran->currentPage() > 1) Halaman {{$angsuran->currentPage()}} @endif</small></small>
				</h4>

				{{-- SEARCH --}}
				{!! Form::open(['method' => "GET"]) !!}
				@foreach(request()->all() as $k => $v)
			 		@if(!str_is($k, 'q'))
				 		<input type="hidden" name="{{$k}}" value="{{$v}}">
			 		@endif
			 	@endforeach
				<div class="form-row">
					<div class='col-sm-6 order-1'>{!! Form::bsText(null, 'q', null, ['placeholder' => 'search']) !!}</div>
					<!-- <div class='col-sm-1 order-2'>{!! Form::bsSelect(null, 'periode', ['daily' => 'Daily', 'monthly' => 'Monthly', 'yearly' => 'Yearly'], null) !!}</div> -->
					<div class='col-auto order-3'>{!! Form::bsSubmit('<i class="fa fa-search"></i>', ['class' => 'btn btn-primary']) !!}</div>
				</div>
				{!! Form::close() !!}

				<div class="clearfix">&nbsp;</div>
				<table class="table table-hover">
					<thead>
						<tr class="text-center">
							<th class="text-left">#</th>
							<th>Nasabah</th>
							<th>Jumlah Dibayarkan</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						@php $lua = null @endphp
						@forelse($angsuran as $k => $v)
							@php $pa = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $v['paid_at'])->format('d/m/Y') @endphp
							@if($lua != $pa)
								<tr>
									<td colspan="4" class="bg-light">
										{{$pa}}
									</td>
								</tr>
								@php $lua = $pa @endphp
							@endif
							<tr class="text-center">
								<td class="text-left">
									{{$v['nomor_kredit']}}
								</td>
								<td class="text-left">
									{{$v['kredit']['nasabah']['nama']}}
								</td>
								<td class="text-right">
									{{$idr->formatMoneyTo($v['amount'])}}
								</td>
								<td></td>
							</tr>
						@empty
							<tr>
								<td colspan="4">
									<p>Data tidak tersedia, silahkan pilih Koperasi/BPR lain</p>
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush

@push ('js')
@endpush