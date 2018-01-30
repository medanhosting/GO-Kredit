@inject('carbon', 'Carbon\Carbon')

<div class="clearfix">&nbsp;</div>
<div class="clearfix">&nbsp;</div>
@component('bootstrap.card')
	@slot('title') 
		<h5 class='text-left'>
			<strong>TITIPAN MASIH DI KOLEKTOR</strong>
		</h5>
	@endslot
	@slot('body')
		<table class="table">
			<thead>
				<tr>
					<th class="text-left">KOLEKTOR</th>
					<th class="text-right">JUMLAH</th>
					<th class="text-right">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				@foreach($titipan as $k => $v)
					<tr>
						<td class="text-left">{{$v['penagihan']['karyawan']['nama']}}</td>
						<td class="text-right">{{$v['jumlah']}}</td>
						<td class="text-center">
							{!! Form::open(['url' => route('kredit.store', ['id' => $kredit_id]), 'method' => 'POST']) !!}
								@foreach(request()->all() as $k => $v)
									<input type="hidden" name="{{$k}}" value="{{$v}}">
								@endforeach
									<input type="hidden" name="nota_bayar_id" value="{{$v['id']}}">
									<input type="hidden" name="current" value="penerimaan_titipan_tagihan">
								{!! Form::bsSubmit('Terima', ['class' => 'btn btn-primary text-right']) !!}
							{!! Form::close() !!}	
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	@endslot
@endcomponent


