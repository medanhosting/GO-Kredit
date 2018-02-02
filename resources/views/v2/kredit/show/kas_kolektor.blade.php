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
				@forelse($titipan as $k => $v)
					<tr>
						<td class="text-left">{{$v['karyawan']['nama']}}</td>
						<td class="text-right">{{$v['jumlah']}}</td>
						<td class="text-center">
							{!! Form::open(['url' => route('kredit.store', ['id' => $kredit_id, 'kantor_aktif_id' => $kantor_aktif['id']]), 'method' => 'POST']) !!}
								@foreach(request()->all() as $k2 => $v2)
									<input type="hidden" name="{{$k2}}" value="{{$v2}}">
								@endforeach
									<input type="hidden" name="penagihan_id" value="{{$v['id']}}">
									<input type="hidden" name="current" value="penerimaan_titipan_tagihan">
								{!! Form::bsSubmit('Terima', ['class' => 'btn btn-primary text-right']) !!}
							{!! Form::close() !!}	
						</td>
					</tr>
				@empty
					<tr>
						<td colspan="3">Tidak ada</td>
					</tr>
				@endforelse
			</tbody>
		</table>
	@endslot
@endcomponent


