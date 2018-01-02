@inject('idr', 'App\Service\UI\IDRTranslater')
<div class="clearfix">&nbsp;</div>

<h6 class="text-secondary">PENAGIHAN</h6>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Tanggal</th>
			<th>Surat Peringatan</th>
			<th>Penerima</th>
			<th class="text-right">Pelunasan</th>
			<th class="text-right">Titipan</th>
		</tr>
	</thead>
	<tbody>
		@foreach($penagihan as $k => $v)
		<tr>
			<td>{{$v['tanggal']}}</td>
			<td class="text-left">
				{{ucwords(str_replace('_',' ',$v['suratperingatan']['tag']))}}
			</td>
			<td>{{$v['penerima']['nama']}}</td>
			<td class="text-right">{{$idr->formatMoneyTo($v['pelunasan'])}}</td>
			<td class="text-right">{{$idr->formatMoneyTo($v['titipan'])}}</td>
		</tr>
		@endforeach
	</tbody>
</table>
