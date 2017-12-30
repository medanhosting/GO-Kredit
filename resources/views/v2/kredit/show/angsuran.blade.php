@inject('idr', 'App\Service\UI\IDRTranslater')

<div class="clearfix">&nbsp;</div>
<div class="clearfix">&nbsp;</div>
<div class="row">
	<div class="col-sm-12">
		{!! Form::open(['url' => route('kredit.store', ['id' => $id]), 'method' => 'POST']) !!}

			@foreach(request()->all() as $k => $v)
				<input type="hidden" name="{{$k}}" value="{{$v}}">
			@endforeach

			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th width="5">No</th>
						<th>Jatuh Tempo</th>
						<th class="text-right">Pokok</th>
						<th class="text-right">Bunga</th>
						<th class="text-right">Denda</th>
						<th class="text-right">Potongan</th>
						<th class="text-right">Jumlah</th>
						<th class="text-center">
							<input type="checkbox" class="check-all">
						</th>
						<th class="text-center">Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach($angsuran as $k => $v)
					<tr @if($v['is_tunggakan']) class="text-danger" @endif>
						<td class="text-center">{{ $loop->iteration }}</td>
						<td class="text-left">{{Carbon\Carbon::parse($v['tanggal_bayar'])->format('d/m/Y H:i')}}</td>
						<td class="text-right">{{$idr->formatMoneyTo($v['pokok'])}}</td>
						<td class="text-right">{{$idr->formatMoneyTo($v['bunga'])}}</td>
						<td class="text-right">{{$idr->formatMoneyTo($v['denda'])}}</td>
						<td class="text-right">{{$idr->formatMoneyTo($v['potongan'])}}</td>
						<td class="text-right">{{$idr->formatMoneyTo($v['subtotal'] - $v['potongan'])}}</td>
						<td class="text-center">
							@if (is_null($v['nota_bayar_id']))
								<input type="checkbox" name="nth[]" value="{{$v['nth']}}">
							@else
								<i class="fa fa-check text-primary"></i>
								
							@endif
						</td>
						<td class="text-center">
							@if (is_null($v['nota_bayar_id']))
								<a href="#" class="text-primary btn-bayar" data-value="{{ $v['nth'] }}">Bayar</a>
							@else
								<a href="{{route('kredit.angsuran.show', array_merge(['id' => $id, 'nota_bayar_id' => $v['nota_bayar_id']], request()->all()))}}">
									Daftar Angsuran	
								</a>
							@endif
						</td>
					</tr>
					@endforeach
				</tbody>
				<tfoot>
					<tr>
						<th class="text-right align-middle" colspan="6">
							<h5 class="mb-0"><strong>Total</strong></h5>
						</th>
						<th class="text-right align-middle">
							<h5 class="mb-0"><strong>{{ $idr->formatMoneyTo($total) }}</strong></h5>
						</th>
						<th class="text-center align-middle" colspan="2">
							<button type="submit" class="btn btn-primary btn-bayar-semua invisible">&emsp;Bayar yang tercentang&emsp;</button>
						</th>
					</tr>
				</tfoot>
			</table>
		{!!Form::close()!!}

		{!! Form::open(['url' => route('kredit.store', ['id' => $id]), 'method' => 'POST', 'class' => 'kredit-single']) !!}
			@foreach(request()->all() as $k => $v)
				<input type="hidden" name="{{$k}}" value="{{$v}}">
			@endforeach
			{!! Form::hidden('nth[]', null, ['class' => 'kredit-single-checkbox']) !!}
		{!! Form::close() !!}
	</div>
</div>

@component ('bootstrap.modal', ['id' => 'bayar-angsuran', 'form' => true, 'method' => 'patch', 'url' => route('kredit.update', ['id' => $id, 'kantor_aktif_id' => $kantor_aktif['id'], 'nth' => request()->get('nth')]) ])
		@slot ('title')
			Tandai Angsuran Lunas
		@endslot

		@slot ('body')
			<p>Tanggal pelunasan akan terhitung tepat ketika Anda mengisi password berikut</p>

			{!! Form::bsPassword('password', 'password', ['placeholder' => 'Password']) !!}
		@endslot

		@slot ('footer')
			<a href="#" data-dismiss="modal" class="btn btn-link text-secondary">Batal</a>
			{!! Form::bsSubmit('Simpan', ['class' => 'btn btn-primary']) !!}
		@endslot
@endcomponent

@push('js')
	<script type="text/javascript">
		//MODAL PARSE DATA ATTRIBUTE//
		$("a.modal_angsuran").on("click", parsingAttributeModalAngsuran);

		function parsingAttributeModalAngsuran(){
			$('#bayar-angsuran').find('form').attr('action', $(this).attr("data-action"));
		}
		
		var flag = 0;
		$('input.check-all').on('change', function(){
			if ($(this).is(':checked')) {
				$('input[name="nth[]"]').prop('checked', true);
			} else {
				$('input[name="nth[]"]').prop('checked', false);
			}

			checked_on_checked();
		});

		$('input[name="nth[]"]').on('change', function(){
			checked_on_checked();
		});

		function checked_on_checked(){
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

		$('.btn-bayar').on('click', function(e) {
			e.preventDefault();

			var value = $(this).attr('data-value');

			$('.kredit-single-checkbox').val(value);
			$('form.kredit-single').submit();
		});
	</script>
@endpush