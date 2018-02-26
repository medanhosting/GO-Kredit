@inject('idr', 'App\Service\UI\IDRTranslater')

@component('bootstrap.card')
	@slot('header') 
		<h5 class='text-left p-2 mb-0'>
			<strong>SIMULASI BAYAR ANGSURAN</strong>
		</h5>
	@endslot
	@slot('body')

	{!! Form::open(['url' => route('kredit.show', ['id' => $kredit_id]), 'method' => 'GET']) !!}
		@foreach(request()->all() as $k => $v)
			<input type="hidden" name="{{$k}}" value="{{$v}}">
		@endforeach
		<div class="row">
			<div class="col-12 mb-2">
				<div class="form-check form-check-inline">
					<input class="form-check-input ml-0" type="radio" value="all" name="current_pay" id="all" @if(!request()->has('current_pay') || request()->get('current_pay')=='all') checked @endif>
					<label class="form-check-label" for="all">Bayar Penuh</label>
				</div>
				<div class="form-check form-check-inline">
					<input class="form-check-input ml-0" type="radio" value="part" name="current_pay" id="part" @if(request()->get('current_pay')=='part') checked @endif>
					<label class="form-check-label" for="part">Bayar Sebagian</label>
				</div>
				<div class="clearfix">&nbsp;</div>
				<div class="row">
					<div class="col">
						{!! Form::bsText('Tanggal', 'tanggal', $today->format('d/m/Y H:i'), ['class' => 'form-control mask-date inline-edit text-info py-1 border-input', 'placeholder' => 'dd/mm/yyyy hh:mm'], true) !!}
					</div>
				</div>
				<div id="all-tab">
					<div class="row">
						<div class="col-12">
							<div class="form-group">
								<label>BAYAR ANGSURAN</label>
								<select name="jumlah_angsuran" id="select-nth" class="form-control custom-select text-info text-left inline-edit border-input pl-2">
									<option value="">Pilih</option>
									@foreach ($sisa_angsuran as $k => $v)
										<option value="{{ $k+1 }}" @if($k+1 == request()->get('jumlah_angsuran')) selected @endif>{{ $k+1 }} Angsuran</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
				</div>

				<div id="part-tab" style="display: none;">
					<div class="row">
						<div class="col-12">
							{!! Form::bsText('Nominal', 'nominal', null, ['class' => 'form-control mask-money inline-edit text-info pb-0 border-input', 'placeholder' => 'Rp 330.000', 'id' => 'input-nominal'], true) !!}
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix">&nbsp;</div>
		@if(!is_null($bayar))
			@php $total = 0 @endphp
			@foreach($bayar as $k2 => $v2)
				<div class="row">
					<div class="col-6">
						<p class="mb-2 @if($v2['amount'] < 0) text-danger @endif text-sm">{{str_replace('_', ' ', strtoupper($v2['tag']))}}</p>
					</div>
					<div class="col-6 text-right">
						<h5 class="text-style @if($v2['amount'] < 0) text-danger @endif">{!! Form::Label(null, $idr->formatMoneyto($v2['jumlah'])) !!}</h5>
					</div>
				</div>
				@php $total = $total + $v2['jumlah'] @endphp
			@endforeach

			<div class="row">
				<div class="col-6">
					<p class="mb-2 text-sm"><strong>TOTAL</strong></p>
				</div>
				<div class="col-6 text-right">
					<h5 class="text-style"><strong>{!! Form::Label(null, $idr->formatMoneyto($total)) !!}</strong></h5>
				</div>
			</div>
			<div class="clearfix">&nbsp;</div>
			<div class="row">
				<div class="col-6">
					<input type="hidden" name="bayar" value=true>
					<input type="hidden" name="current" value="angsuran">
					{!! Form::bsSubmit('Simulasi', ['class' => 'btn btn-primary']) !!}
				</div>
			{!! Form::close() !!}	
	
				<div class="col-6 text-right">
					{!! Form::open(['url' => route('kredit.update', ['id' => $kredit_id]), 'method' => 'PATCH']) !!}
					@foreach(request()->all() as $k => $v)
						<input type="hidden" name="{{$k}}" value="{{$v}}">
					@endforeach
					<input type="hidden" name="jumlah" value="{{$idr->formatMoneyto($faktur['total'])}}">
					<a data-toggle="modal" data-target="#nota_angsuran" data-action="{{route('kredit.update', ['id' => $kredit['id'], 'kantor_aktif_id' => $kantor_aktif['id'], 'current' => 'angsuran'])}}" class="modal_nota_angsuran btn btn-primary text-white">Bayar</a>
					
						@include('v2.kredit.modal.nota_angsuran', ['bayar' => $bayar, 'kredit' => $aktif])
						@include('v2.kredit.modal.konfirmasi_angsuran', ['bayar' => $bayar, 'kredit' => $aktif])
					
					{!! Form::close() !!}	
				</div>
			</div>
		@else
			<div class="clearfix">&nbsp;</div>
			<div class="row">
				<div class="col-12">
					<input type="hidden" name="bayar" value=true>
					<input type="hidden" name="current" value="angsuran">
					{!! Form::bsSubmit('Simulasi', ['class' => 'btn btn-primary']) !!}
				</div>
			</div>
			{!! Form::close() !!}	
		@endif
	@endslot
@endcomponent

@push('js')
	<script type="text/javascript">

		var panel = {
			ALL: 'all',
			PART: 'part',
			TOTAL: 'totalAngsuran'
		};

		setPanel($('input[name="current_pay"]').val(), panel, $('input[name="current_pay"]'));

		$('input[name="current_pay"]').on('change', function(element) {
			setPanel($(this).val(), panel, element);
		});

		function setPanel(value, panel, element){
			if (value == panel.ALL) {
				$('#' + panel.PART + '-tab').hide();
				$('#' + panel.ALL + '-tab').fadeIn();
				resetPanelPart(element);
			} else {
				$('#' + panel.ALL + '-tab').hide();
				$('#' + panel.PART + '-tab').fadeIn();		
				resetPanelAll(element);
			}
		}

		function resetPanelAll(element){
			$('#' + element.SELECT_NTH).val(null);
			$('#' + element.INPUT_NTH).val(null);
			$('#' + panel.TOTAL).html('Rp 0');
		}

		function resetPanelPart(element){
			$('#' + element.INPUT_NOMINAL).val(null);
			$('#' + panel.TOTAL).html('Rp 0');
		}
		$('button[type="submit"]').on('click', function(e){
			e.stopPropagation();
		});
	</script>
@endpush