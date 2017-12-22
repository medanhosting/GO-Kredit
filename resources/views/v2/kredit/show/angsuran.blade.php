@inject('idr', 'App\Service\UI\IDRTranslater')
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
					<th>Pokok</th>
					<th>Bunga</th>
					<th>Denda</th>
					<th class="text-right">Jumlah</th>
					<th class="text-center">Bayar Sekarang</th>
				</tr>
			</thead>
			<tbody>
				@foreach($angsuran as $k => $v)
				<tr @if($v['is_tunggakan']) class="text-danger" @endif>
					<td>{{$k+1}}</td>
					<td class="text-left">{{Carbon\Carbon::parse($v['tanggal_bayar'])->format('d/m/Y H:i')}}</td>
					<td class="text-right">{{$idr->formatMoneyTo($v['pokok'])}}</td>
					<td class="text-right">{{$idr->formatMoneyTo($v['bunga'])}}</td>
					<td class="text-right">{{$idr->formatMoneyTo($v['denda'])}}</td>
					<td class="text-right">{{$idr->formatMoneyTo($v['subtotal'])}}</td>
					<td class="text-center">
						@if(is_null($v['nota_bayar_id']))
							<input type="checkbox" name="nth[]" value="{{$v['nth']}}">
						@else
							<a href="{{route('kredit.angsuran.show', array_merge(['id' => $id, 'nota_bayar_id' => $v['nota_bayar_id']], request()->all()))}}"><i class="fa fa-check"></i></a>
						@endif
					</td>
				</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<th colspan="5">Total</th>
					<th class="text-right">{{$idr->formatMoneyTo($total)}}</th>
					<th class="text-right"><button type="submit" class="btn btn-primary">&emsp;Bayar&emsp;</button></th>
				</tr>
			</tfoot>
		</table>
		{!!Form::close()!!}
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
	</script>
@endpush