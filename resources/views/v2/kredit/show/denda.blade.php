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
						<td class="text-center">{{ $loop->iteration }}</td>
						<td class="text-left">Angsuran ke- {{$v['nth']}}</td>
						<td class="text-right">{{$idr->formatMoneyTo($v['denda'])}}</td>
						<td class="text-right">{{$idr->formatMoneyTo($v['potongan_denda'])}}</td>
						<td class="text-right">{{$idr->formatMoneyTo($v['subtotal'])}}</td>
						<td></td>
					</tr>
					@if(is_null($v['nota_bayar_id']) && !str_is($v['denda'], 'Rp 0'))
						@php $is_paid 	= false; @endphp
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
						@if(!$is_paid && count($denda))
							<a href="#" class="btn btn-block btn-primary btn-bayar-denda" data-toggle="modal" data-target="#summary-denda" 
							data-url-denda="{{ route('angsuran.denda', ['id' => $aktif['nomor_kredit']]) }}" 
							data-kantor-aktif-id="{{ $kantor_aktif_id }}"
							data-url-terbilang="{[ route('terbilang') }}">Bayar</a>
						@endif
					</th>
				</tr>
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
					{{--  <tr>
						<th colspan="4">Nota Bayar</th>
					</tr>  --}}
				{{--  @endif  --}}
			</tfoot>
		</table>

		{!! Form::open(['url' => route('kredit.update', ['id' => $aktif['id'], 'kantor_aktif_id' => $kantor_aktif_id]), 'method' => 'PATCH']) !!}
			@include('v2.kredit.modal.nota_denda', [
				'kredit_aktif' 	=> $aktif,
				'tanggal_now'	=> $carbon->now()->format('d/m/Y H:i'),
			])

			{!! Form::hidden('current', 'denda') !!}
			{!! Form::hidden('potongan', 'Rp 0') !!}

			@include('v2.kredit.modal.konfirmasi_denda')
		{!! Form::close() !!}
	</div>
</div>

@push('js')
	<script type="text/javascript">
		// place for variable
		var subtotal = 0;

		// place for function
		function parsingDataDenda(data, extended = false) {
			var root = $('tr#denda-row');
			var parent = root.parent();

			$.each(data, function(i, v){
				var temp = root.clone();
				temp.show()
					.removeAttr('id');

				$.each(v, function(i2, v2) {
					if (i2 == 'subtotal') subtotal = subtotal + v2;

					if ((i2 != 'nth')) {
						temp.find('td.dend-' + i2).html('Rp ' + window.numberFormat.set(v2));
					} else {
						temp.find('td.dend-title').html('Angsuran ke- ' + v2);
					}
				});

				temp.find('td.dend-iteration').html(i+1);
				parent.append(temp);
			});

			if (extended === true) {
				parent.append('<tr> \
									<td class="text-right" colspan="4"><h5>Total</h5></td> \
									<td class="text-right"><h5>Rp ' + window.numberFormat.set(subtotal)  + '</h5></td> \
							</tr>');
			} else {
				parent.append('<tr> \
						<td class="text-right text-info align-middle" colspan="4"><h5 class="mb-0"><strong>Total</strong></h5></td> \
						<td class="text-right text-info align-middle"><h5 class="mb-0"><strong>Rp ' + window.numberFormat.set(subtotal)  + '</strong></h5></td> \
				</tr>');
			}

			// terbilang
			var ajxTerbilang = window.ajax;
		
			ajxTerbilang.defineOnSuccess(function(resp){
				parent.append('<tr> \
									<td class="text-left"><strong>Terbilang</strong></td> \
									<td class="text-left text-capitalize" colspan="7"><i>	' + resp.data + '</i></td> \
								</tr>');
			});

			ajxTerbilang.defineOnError(function(resp){

			});

			ajxTerbilang.get('{{ route("terbilang")  }}', {money: subtotal})
		}

		// place for event html
		$('.btn-bayar-denda').on('click', function(e){
			e.preventDefault();

			var ajxDend = window.ajax;
			var urlLinkDenda,
				urlLinkPotongan,
				dataAjax = {},
				kantor_aktif_id = $(this).attr('data-kantor-aktif-id');
				
			urlLinkDenda = $(this).attr('data-url-denda');
			// urlLinkPotongan = $(this).attr('data-url-potongan');

			ajxDend.defineOnSuccess(function(resp){
				// check potongan
				// if (checked_all() === true) {
				// 	var ajxPot = window.ajax;
				// 	var dataAjax2 = {};
					
				// 	parsingData(resp.data, true);

				// 	ajxPot.defineOnSuccess(function(resp){
				// 		var parent2 = $('tr#angsuran-row').parent();
				// 		var potongan = resp.data.replace(/\./g, '').replace('-', '').slice(3);

				// 		parent2.append('<tr> \
				// 							<td class="text-right" colspan="5"><h5>Potongan</h5></td> \
				// 							<td class="text-right text-danger"><h5>' + resp.data  + '</h5></td> \
				// 						</tr>');

				// 		var total_angsuran = subtotal - potongan;

				// 		parent2.append('<tr> \
				// 				<td class="text-right text-info" colspan="5"><h5><strong>Total Bayar Angsuran</strong></h5></td> \
				// 				<td class="text-right text-info"><h5><strong>Rp ' + window.numberFormat.set(total_angsuran)  + '</strong></h5></td> \
				// 			</tr>');

				// 		// terbilang
				// 		var ajxTerbilang = window.ajax;

				// 		ajxTerbilang.defineOnSuccess(function(resp){
				// 			parent2.append('<tr> \
				// 								<td class="text-left"><strong>Terbilang</strong></td> \
				// 								<td class="text-left text-capitalize" colspan="7"><i>	' + resp.data + '</i></td> \
				// 							</tr>');
				// 		});
			
				// 		ajxTerbilang.defineOnError(function(resp){
			
				// 		});
			
				// 		ajxTerbilang.get('{{ route("terbilang")  }}', {money: total_angsuran});
				// 	});

				// 	ajxPot.defineOnError(function(resp){
				// 		console.log('error');
				// 	});

				// 	dataAjax2.kantor_aktif_id = kantor_aktif_id;

				// 	ajxPot.get(urlLinkPotongan, dataAjax2);
				// } else {
					parsingDataDenda(resp.data);
				// }
			});

			ajxDend.defineOnError(function(resp){
				console.log('error');
			});

			dataAjax.kantor_aktif_id = kantor_aktif_id;

			ajxDend.get(urlLinkDenda, dataAjax);
		});

		$('#summary-denda').on('hide.bs.modal', function(e) {
			var root = $(this).find('tr#denda-row');
			var parent = root.parent();

			parent.children(':not(#denda-row)').remove();
			$('.periode_bln').html('');
		});

		$('#konfirmasi-denda').on('show.bs.modal', function(e) {
			$('#summary-denda').modal('hide');
		});
	</script>
@endpush