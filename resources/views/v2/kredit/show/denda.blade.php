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
					<th class="text-center align-middle">Angs<br/>Ke-</th>
					<th class="text-right align-middle">Denda</th>
					<th class="text-right align-middle">Restitusi</th>
					<th class="text-center align-middle">Sudah<br/>Dibayar</th>
				</tr>
			</thead>
			<tbody>
				@foreach($denda as $k => $v)
					<tr>
						<td class="text-center">{{$v['nth']}}</td>
						<td class="text-right">{{$idr->formatMoneyTo($v['denda'])}}</td>
						<td class="text-right">{{$idr->formatMoneyTo($v['restitusi_denda'])}}</td>
						<td class="text-center">
							@if(!is_null($v['nota_bayar_id']))
								<a  href="{{route('angsuran.show', array_merge(['id' => $aktif['nomor_kredit'], 'nota_bayar_id' => $v['nota_bayar_id'], 'case' => 'denda'], request()->all()))}}">
									<i class="fa fa-check text-primary"></i>
								</a>
							@endif
						</td>
					</tr>
					@if(is_null($v['nota_bayar_id']) && !str_is($v['denda'], 'Rp 0'))
						@php $is_paid 	= false; @endphp
					@endif
				@endforeach
			</tbody>
			<tfoot>
				@if(!$is_paid && count($denda))
				<tr>
					<th class="" colspan="4">
							<a href="#" class="btn btn-block btn-primary btn-bayar-denda" data-toggle="modal" data-target="#summary-denda" 
							data-url-denda="{{ route('angsuran.denda', ['id' => $aktif['nomor_kredit']]) }}" 
							data-kantor-aktif-id="{{ $kantor_aktif_id }}"
							data-url-terbilang="{[ route('terbilang') }}">Bayar</a>
					</th>
				</tr>
				@if($stat['total_restitusi'])
				<tr>
					<th colspan="4">
						<a href="#" class="btn btn-block btn-warning btn-bayar-denda" data-toggle="modal" data-target="#validasi-restitusi-denda" 
						data-url-denda="{{ route('angsuran.denda', ['id' => $aktif['nomor_kredit']]) }}" 
						data-kantor-aktif-id="{{ $kantor_aktif_id }}"
						data-url-terbilang="{[ route('terbilang') }}">Konfirmasi Permohonan Restitusi</a>
					</th>
				</tr>
				@else
				<tr>
					<th colspan="4">
						<a href="#" class="btn btn-block btn-primary btn-bayar-denda" data-toggle="modal" data-target="#permintaan-restitusi-denda" 
						data-url-denda="{{ route('angsuran.denda', ['id' => $aktif['nomor_kredit']]) }}" 
						data-kantor-aktif-id="{{ $kantor_aktif_id }}"
						data-url-terbilang="{[ route('terbilang') }}">Restitusi Denda</a>
					</th>
				</tr>
				@endif
				@endif
			</tfoot>
		</table>

		{!! Form::open(['url' => route('kredit.update', ['id' => $aktif['id'], 'kantor_aktif_id' => $kantor_aktif_id, 'current' => 'denda']), 'method' => 'PATCH']) !!}
			@include('v2.kredit.modal.nota_denda', [
				'kredit_aktif' 	=> $aktif,
				'tanggal_now'	=> $carbon->now()->format('d/m/Y H:i'),
			])

			{!! Form::hidden('current', 'denda') !!}
			{!! Form::hidden('potongan', 'Rp 0') !!}

			@include('v2.kredit.modal.konfirmasi_denda')
		{!! Form::close() !!}

		{!! Form::open(['url' => route('kredit.update', ['id' => $aktif['id'], 'kantor_aktif_id' => $kantor_aktif_id, 'current' => 'permintaan_restitusi']), 'method' => 'PATCH']) !!}
			@include('v2.kredit.modal.permintaan_restitusi_denda', [
				'kredit_aktif' 	=> $aktif,
				'tanggal_now'	=> $carbon->now()->format('d/m/Y H:i'),
			])

			{!! Form::hidden('current', 'permintaan_restitusi') !!}

		{!! Form::close() !!}

		{!! Form::open(['url' => route('kredit.update', ['id' => $aktif['id'], 'kantor_aktif_id' => $kantor_aktif_id, 'current' => 'validasi_restitusi']), 'method' => 'PATCH']) !!}
			@include('v2.kredit.modal.validasi_restitusi_denda', [
				'kredit_aktif' 	=> $aktif,
				'tanggal_now'	=> $carbon->now()->format('d/m/Y H:i'),
			])

			{!! Form::hidden('current', 'validasi_restitusi') !!}

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

			ajxDend.defineOnSuccess(function(resp){
				parsingDataDenda(resp.data);
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