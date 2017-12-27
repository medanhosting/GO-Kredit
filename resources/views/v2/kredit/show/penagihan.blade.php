@inject('idr', 'App\Service\UI\IDRTranslater')
<div class="clearfix">&nbsp;</div>

<h6 class="text-secondary">PENAGIHAN</h6>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Tanggal</th>
			<th>Surat Peringatan</th>
			<th>Penerima</th>
			<th>Pelunasan</th>
			<th>Titipan</th>
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
			<td>{{$idr->formatMoneyTo($v['pelunasan'])}}</td>
			<td>{{$idr->formatMoneyTo($v['titipan'])}}</td>
		</tr>
		@endforeach
	</tbody>
</table>
