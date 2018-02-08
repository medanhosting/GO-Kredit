@inject('carbon', 'Carbon\Carbon')
@inject('idr', 'App\Service\UI\IDRTranslater')

{!! Form::open(['url' => route('kredit.store', ['id' => $kredit_id]), 'method' => 'POST']) !!}
	@foreach(request()->all() as $k => $v)
		<input type="hidden" name="{{$k}}" value="{{$v}}">
	@endforeach
	@component('bootstrap.card')
		@slot('title') 
			<h5 class='text-left'>
				<strong>BAYAR ANGSURAN</strong>
			</h5>
		@endslot
		@slot('body')
			<div class="row">
				<div class="col-12 mb-2">
					<div class="form-check form-check-inline">
						<input class="form-check-input ml-0" type="radio" value="all" name="inlineRadioOptions" id="all" checked>
						<label class="form-check-label pl-4" for="all">Bayar Penuh</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input ml-0" type="radio" value="part" name="inlineRadioOptions" id="part">
						<label class="form-check-label pl-4" for="part">Bayar Sebagian</label>
					</div>
					<div class="clearfix">&nbsp;</div>
					<div class="row">
						<div class="col">
								{!! Form::bsText('Tanggal', 'tanggal', $carbon::now()->format('d/m/Y H:i'), ['class' => 'form-control mask-date-time inline-edit text-info pb-0 border-input', 'placeholder' => 'dd/mm/yyyy hh:mm'], true) !!}
						</div>
					</div>
					<div id="all-tab">
						<div class="row">
							<div class="col-12">
								<p class="mb-1">BAYAR ANGSURAN</p>
								@php
									{{--  dd($angsuran);  --}}
								@endphp
								<select name="temp_nth" id="select-nth" class="form-control custom-select text-info text-left inline-edit border-input pl-2">
									<option value="">Pilih</option>
									@foreach ($angsuran as $k => $v)
										<option value="{{ $v['nth'] }}" data-value="{{ $idr->formatMoneyFrom($v['jumlah']) }}">{{ $v['nth'] }} Angsur</option>
									@endforeach
								</select>
								<input type="hidden" name="nth[]" id="input-nth">
							</div>
						</div>
					</div>
					<div id="part-tab" style="display: none;">
						<div class="row">
							<div class="col-12">
								{!! Form::bsText('Nominal', 'nominal', null, ['class' => 'form-control mask-money inline-edit text-info pb-0 border-input', 'placeholder' => 'Rp 330.000', 'id' => 'input-nominal'], true) !!}
								{!! Form::hidden('current', 'bayar_sebagian') !!}
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix">&nbsp;</div>
			<div class="row">
				<div class="col-5">
					<p class="mb-2">TOTAL</p>
				</div>
				<div class="col-7 text-right">
					<h5>{!! Form::Label(null, 'Rp 0', ['id' => 'totalAngsuran']) !!}</h5>
				</div>
			</div>
			<div class="clearfix">&nbsp;</div>
			<div class="row">
				<div class="col-12">
					<a href="#" class="btn btn-primary" data-toggle="modal" data-target="#konfirmasi-angsuran">Bayar</a>
					{{--  <a href="#" class="btn btn-primary btn-block">Bayar</a>  --}}
					{{--  <a href="#" class="btn btn-primary btn-bayar-semua invisible" 
						data-toggle="modal" 
						data-target="#summary-angsuran" 
						data-url-angsuran="{{ route('angsuran.tagihan', ['id' => $aktif['nomor_kredit']]) }}"
						data-url-potongan="{{ route('angsuran.potongan', ['id' => $aktif['nomor_kredit']]) }}" 
						data-kantor-aktif-id="{{ $kantor_aktif_id }}"
						data-url-terbilang="{{ route('terbilang') }}"
						data-url-titipan="{{ route('angsuran.titipan', ['id' => $aktif['nomor_kredit']]) }}">Bayar</a>  --}}
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
		
		var panel = {
			ALL: 'all',
			PART: 'part',
			TOTAL: 'totalAngsuran'
		};
		var element = {
			SELECT_NTH: 'select-nth',
			INPUT_NTH: 'input-nth',
			INPUT_NOMINAL: 'input-nominal'
		};

		// place for function
		function parsingAttributeModalAngsuran()
		{
			$('#bayar-angsuran').find('form').attr('action', $(this).attr("data-action"));
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

		function resetPanelAll()
		{
			$('#' + element.SELECT_NTH).val(null);
			$('#' + element.INPUT_NTH).val(null);
			$('#' + panel.TOTAL).html('Rp 0');
		}

		function resetPanelPart()
		{
			$('#' + element.INPUT_NOMINAL).val(null);
			$('#' + panel.TOTAL).html('Rp 0');
		}

		// place for event
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

		$('input[name="inlineRadioOptions"]').on('change', function(e) {
			var value = $(this).val();

			if (value == panel.ALL) {
				$('#' + panel.PART + '-tab').hide();
				$('#' + panel.ALL + '-tab').fadeIn();
				resetPanelPart();
			} else {
				$('#' + panel.ALL + '-tab').hide();
				$('#' + panel.PART + '-tab').fadeIn();		
				resetPanelAll();
			}
		});
		$('#' + element.SELECT_NTH).on('change', function(e) {
			var total = 0;
			var selected = parseInt($(this).val());
			var tempNth = {};

			$(this).find('option').each(function(i, v){
				if (parseInt($(v).val()) <= selected) {
					total = total + parseInt($(v).attr('data-value'));
					tempNth[i-1] = i;
				}
			});
			$('#' + element.INPUT_NTH).val(JSON.stringify(tempNth));
			$('#' + panel.TOTAL).html('Rp ' + window.numberFormat.set(total));
		});

		$('#' + element.INPUT_NOMINAL).on('change', function(e) {
			var value = $(this).val();

			$('#' + panel.TOTAL).html('Rp ' + window.numberFormat.set(value.replace(/\./g, '').slice(3)));
		});
	</script>
@endpush