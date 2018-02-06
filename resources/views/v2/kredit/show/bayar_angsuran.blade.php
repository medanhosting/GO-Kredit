@inject('carbon', 'Carbon\Carbon')
@inject('idr', 'App\Service\UI\IDRTranslater')

{!! Form::open(['url' => route('kredit.store', ['id' => $kredit_id]), 'method' => 'POST']) !!}
	@foreach(request()->all() as $k => $v)
		<input type="hidden" name="{{$k}}" value="{{$v}}">
	@endforeach
	@component('bootstrap.card')
		@slot('title') 
			<h5 class='text-left'>
				<strong>BAYAR ANGSURAN PENUH</strong>
			</h5>
		@endslot
		@slot('body')
			<div class="row">
				<div class="col-12 mb-2">
					<div class="form-check px-3">
						<input type="checkbox" class="check-all form-check-input" id="check-all"> 
						<label for="check-all" class="form-check-label pl-1">
							Bayar Semua
						</label>
					</div>
				</div>
			</div>
			<div class="row">
				@foreach($angsuran as $k => $v)
					@if (is_null($v['nomor_faktur']))
						@php
							{{--  dd($v['jumlah']);  --}}
						@endphp
						<div class="col-3 mb-1">
							<div class="form-check px-3">
								<input type="checkbox" name="nth[]" value="{{$v['nth']}}" class="form-check-input check-bayar-angsuran" id="check-{{ $v['nth'] }}" data-value="{{ $idr->formatMoneyFrom($v['jumlah']) }}" data-id="{{ $v['nth'] }}">
								<label class="form-check-label pl-1" for="check-{{ $v['nth'] }}">
									Angsuran Ke - {{ $v['nth'] }}
								</label>
							</div>
						</div>
					@endif
				@endforeach
			</div>
			<div class="row mt-3">
				<div class="col-6">
					<h6 class="text-uppercase mb-1">TOTAL</h6>
					{!! Form::Label(null, 'Rp 0', ['id' => 'totalAngsuran']) !!}
				</div>
				<div class="col-6">
					<a href="#" class="btn btn-primary btn-bayar-semua invisible" 
						data-toggle="modal" 
						data-target="#summary-angsuran" 
						data-url-angsuran="{{ route('angsuran.tagihan', ['id' => $aktif['nomor_kredit']]) }}"
						data-url-potongan="{{ route('angsuran.potongan', ['id' => $aktif['nomor_kredit']]) }}" 
						data-kantor-aktif-id="{{ $kantor_aktif_id }}"
						data-url-terbilang="{{ route('terbilang') }}"
						data-url-titipan="{{ route('angsuran.titipan', ['id' => $aktif['nomor_kredit']]) }}">Bayar</a>
				</div>
			</div>
		@endslot
	@endcomponent

	@include('v2.kredit.modal.nota_angsuran', [
				'kredit_aktif' 	=> $aktif,
				'kantor_aktif'	=> $kantor_aktif,
				'angsuran'		=> $angsuran,
				'tanggal_now'	=> $carbon->now()->format('d/m/Y H:i'),
			])
			
	@include('v2.kredit.modal.konfirmasi_angsuran')

{!! Form::close() !!}	


@push('js')
	<script type="text/javascript">
		// place for variable globalr
		var flag = 0,
			subtotal = 0,
			parent,
			parent2,
			denda = 0,
			potongan = 0,
			titipan = 0,
			total_angsuran = 0,
			urlLinkAngsuran,
			urlLinkPotongan,
			urlLinkTitipan,
			kantorAktifId;

		// place for function
		function parsingAttributeModalAngsuran()
		{
			$('#bayar-angsuran').find('form').attr('action', $(this).attr("data-action"));
		}

		function checked_on_checked()
		{
			if ($('input[name="nth[]"]:checked').length >= 1) {
				flag = 1;
			} else {
				flag = 0;
			}

			if (flag == 1) {
				$('.btn-bayar-semua').removeClass('invisible');
			} else {
				$('.btn-bayar-semua').addClass('invisible');
			}
		}

		function checked_all() 
		{
			if ($('input[name="nth[]"]:checked').length == $('input[name="nth[]"]').length) {
				return true;
			} else {
				return false;
			}
		}

		function parsingAngsuran(data, extended = false) 
		{
			var root = $('tr#angsuran-row');
			var parent = $('tr#titipan-row');

			$.each(data, function(i, v){
				var temp = root.clone();
				temp.show()
					.removeAttr('id');

				$.each(v, function(i2, v2) {
					if (i2 == 'subtotal') subtotal = subtotal + v2;

					if ((i2 != 'nth')) {
						temp.find('td.angs-' + i2).html('Rp ' + window.numberFormat.set(v2));
					} else {
						temp.find('td.angs-title').html('Angsuran ke- ' + v2);

						if ((data.length - 1) == i) {
							$('.periode_bln').append(v2);
						} else {
							$('.periode_bln').append(v2 + ', ');
						}
					}
				});

				temp.find('td.angs-iteration').html(i+1);
				parent.before(temp);
			});

			if (extended === true) {
				parent.before('	<tr> \
									<td class="text-right" colspan="2"><h5>Total</h5></td> \
									<td class="text-right"><h5>Rp ' + window.numberFormat.set(subtotal)  + '</h5></td> \
								</tr>');
			} else {
				parent.before('	<tr> \
									<td class="text-right text-info" colspan="2"><h5><strong>Total</strong></h5></td> \
									<td class="text-right text-info"><h5><strong>Rp ' + window.numberFormat.set(subtotal)  + '</strong></h5></td> \
								</tr>');
			}
		}

		function parsingTitipan(url, data) 
		{
			var ajxTitipan = window.ajax;

			ajxTitipan.defineOnSuccess(function(resp){
				titipan = resp.data;
				$('#titipan-row').before('	<tr> \
				<td class="text-right" colspan="2"><h5>Titipan</h5></td> \
				<td class="text-right text-danger"><h5>Rp -' + window.numberFormat.set(titipan)  + '</h5></td> \
				</tr>');
			});
			
			ajxTitipan.defineOnError(function(resp){
				console.log('error - titipan');
			});
			
			ajxTitipan.get(url, data);
		}

		function parsingPotongan(url, data)
		{
			var ajxPot = window.ajax;
			var dataAjax2 = {};

			ajxPot.defineOnSuccess(function(resp){
				$('#potongan-row').html('<td class="text-right" colspan="2"><h5>Potongan</h5></td> \
										<td class="text-right text-danger"><h5>' + resp.data  + '</h5></td>');
				
				potongan = resp.data.replace(/\./g, '').replace('-', '').slice(3);
			});

			ajxPot.defineOnError(function(resp){
				console.log('error - potongan');
			});

			ajxPot.get(url, data);
		}

		function parsingTerbilang(url, data)
		{
			// terbilang
			var ajxTerbilang = window.ajax;

			ajxTerbilang.defineOnSuccess(function(resp){
				var tmpParent = $('tr#total-all-row');
				tmpParent.after('<tr> \
				<td class="text-left"><strong>Terbilang</strong></td> \
				<td class="text-left text-capitalize" colspan="7"><i>	' + resp.data + '</i></td> \
				</tr>');
			});

			ajxTerbilang.defineOnError(function(resp){
				console.log('error terbilang');
			});

			ajxTerbilang.get('{{ route("terbilang")  }}', data);
		}

		function totalInChecked()
		{

		}
		


		// place for event
		//MODAL PARSE DATA ATTRIBUTE//
		$("a.modal_angsuran").on("click", parsingAttributeModalAngsuran);
		
		$('input.check-all').on('change', function(){
			if ($(this).is(':checked')) {
				$('input[name="nth[]"]').prop('checked', true);
			} else {
				$('input[name="nth[]"]').prop('checked', false);
			}

			checked_on_checked();
		});

		$('input[name="nth[]"]').on('change', function(){
			var id = parseInt($(this).attr('data-id'));
			var total = 0;
			
			$('input[name="nth[]"]').each(function(index, value) {
				var dataID = parseInt($(value).attr('data-id'))
				if ($(value).attr('data-id') <= id) {
					$(value).prop('checked', true);
					total = total + parseInt($(value).attr('data-value'));
					console.log(total);
				} else {
					$(value).prop('checked', false);
				}
			});
			$('#totalAngsuran').html('Rp ' + window.numberFormat.set(total));
			checked_on_checked();
		});

		$('.btn-bayar-semua').on('click', function(e){
			e.preventDefault();

			var ajxBuy = window.ajax;
			var	dataNth = [],
				dataAjax = {};


			kantorAktifId = $(this).attr('data-kantor-aktif-id');
				
			urlLinkAngsuran = $(this).attr('data-url-angsuran');
			urlLinkPotongan = $(this).attr('data-url-potongan');
			urlLinkTitipan = $(this).attr('data-url-titipan');
	
			ajxBuy.defineOnSuccess(function(resp){
				// parsingTitipan(urlLinkTitipan, {kantor_aktif_id: kantorAktifId});
				
				// // check potongan
				// if (checked_all() === true) {
				// 	parsingAngsuran(resp.data, true);
				// 	parsingPotongan(urlLinkPotongan, {kantor_aktif_id: kantorAktifId});
				// } else {
				// 	parsingAngsuran(resp.data);
				// }

				// setTimeout(function(){
				// 	total_angsuran = subtotal - potongan - titipan;
	
				// 	$('#total-all-row').html('<td class="text-right text-info" colspan="2"><h5><strong>Total Bayar Angsuran</strong></h5></td> \
				// 						<td class="text-right text-info"><h5><strong>Rp ' + window.numberFormat.set(total_angsuran)  + '</strong></h5></td>');
										
				// 	parsingTerbilang(resp.data, { money: total_angsuran});
				// }, 1500);
			});
	
			ajxBuy.defineOnError(function(resp){
				console.log('error-angsuran');
			});
	
			$('input[name="nth[]"]').each(function(){
				if ($(this).is(':checked')) {
					dataNth.push($(this).val());
				}
			});
			// dataAjax.nth = dataNth;
			dataAjax.kantor_aktif_id = kantorAktifId;
			console.log(JSON.stringify(dataAjax));
			ajxBuy.get(urlLinkAngsuran, dataAjax);
		});

		$('#summary-angsuran').on('hide.bs.modal', function(e) {
			var root = $(this).find('tr#angsuran-row');
			var parent = root.parent();

			subtotal = 0;
			potongan = 0;

			parent.children().not('#angsuran-row').not('#titipan-row').not('#potongan-row').not('#total-all-row').remove();
			$('#titipan-row').html('');
			$('#potongan-row').html('');
			$('#total-all-row').html('');
			$('.periode_bln').html('');
		});

		$('#konfirmasi-angsuran').on('show.bs.modal', function(e) {
			$('#summary-angsuran').modal('hide');
		});
	</script>
@endpush