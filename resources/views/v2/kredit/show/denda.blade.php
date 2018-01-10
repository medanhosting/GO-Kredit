@inject('idr', 'App\Service\UI\IDRTranslater')
@inject('carbon', 'Carbon\Carbon')

@php
	$is_paid 	= true;
@endphp

<div class="clearfix">&nbsp;</div>
<div class="clearfix">&nbsp;</div>
<div class="row">
	<div class="col-sm-12">
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th class="text-center" style="width: 5%">No</th>
					<th class="text-left" style="width: 25%">Deskprisi</th>
					<th class="text-right" style="width: 15%">Denda</th>
					<th class="text-right" style="width: 15%">Potongan</th>
					<th class="text-right" style="width: 25%">Subtotal</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				@foreach($denda as $k => $v)
					<tr>
						<td class="text-center">{{$loop->iteration}}</td>
						<td class="text-left">Angsuran ke- {{$v['nth']}}</td>
						<td class="text-right">{{$idr->formatMoneyTo($v['denda'])}}</td>
						<td class="text-right">{{$idr->formatMoneyTo($v['potongan_denda'])}}</td>
						<td class="text-right">{{$idr->formatMoneyTo($v['subtotal'])}}</td>
						<td></td>
					</tr>
					@if(is_null($v['nota_bayar_id']) && !str_is($v['denda'], 'rp 0'))
						@php $is_paid 	= false @endphp
					@endif
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<th class="text-right align-middle" colspan="4">
						<h5 class="mb-0"><strong>Total</strong></h5>
					</th>
					<th class="text-right align-middle">
						<h5 class="mb-0">
							<strong>
								{{ $idr->formatMoneyTo($t_denda) }}
							</strong>
						</h5>
					</th>
					<th class="">
						<a href="#" class="btn btn-block btn-primary" data-toggle="modal" data-target="#summary-denda">Bayar</a>
					</th>
				</tr>
				@if(!$is_paid && count($denda))
					{{--  <tr>
						{!! Form::open(['url' => route('kredit.update', ['id' => $aktif['id'], 'kantor_aktif_id' => $kantor_aktif_id]), 'method' => 'PATCH']) !!}
						<th class="text-right align-middle" colspan="3">
							{{Form::hidden('current', 'denda')}}
							{!! Form::vText('Tanggal', 'tanggal', '11/11/2017 00:00', ['class' => 'form-control mask-datetime border-input text-info pb-1', 'placeholder' => 'potongan'], true) !!}
						</th>
						<th class="text-right align-middle">
							{!! Form::vText('Potongan', 'potongan', 'Rp 0', ['class' => 'form-control mask-money border-input text-info pb-1', 'placeholder' => 'potongan'], true) !!}
						</th>
						<th class="text-right align-middle">
							<button type="submit" class="btn btn-success">Bayar</button>
						</th>
						{!! Form::close() !!}
					</tr>  --}}
				@else
					<tr>
						<th colspan="4">Nota Bayar</th>
					</tr>
				@endif
			</tfoot>
		</table>

		@include('v2.kredit.modal.nota_denda', [
			'kredit_aktif' 	=> $aktif,
			'tanggal_now'	=> $carbon->now()->format('d/m/Y H:i'),
		])
	</div>
</div>
