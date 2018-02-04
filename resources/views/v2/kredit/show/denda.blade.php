@inject('carbon', 'Carbon\Carbon')

<div class="clearfix">&nbsp;</div>
<div class="clearfix">&nbsp;</div>
@component('bootstrap.card')
	@slot('title') 
		<h5 class='text-left'>
			<strong>RIWAYAT RESTITUSI DENDA</strong>
		</h5>
	@endslot
	@slot('body')
				<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th class="text-center align-middle">NO</th>
					<th class="text-center align-middle">TANGGAL</th>
					<th class="text-center align-middle">RESTITUSI</th>
					<th class="text-center align-middle">STATUS</th>
				</tr>
			</thead>
			<tbody>
				@foreach($denda as $k => $v)
					<tr>
						<td class="text-center">{{ $loop->iteration }}</td>
						<td class="text-center">{{$carbon::parse($v['tanggal'])->format('d/m/Y')}}</td>
						<td class="text-right">
							{{$v['jumlah']}}<br/>
							<small><i>{{str_replace('_', ' ', $v['jenis'])}}</i></small>
						</td>
						<td class="text-center">
							@if(is_null($v['is_approved']))
								<span class="badge badge-warning">Menunggu<br/>Konfirmasi</span>
							@elseif($v['is_approved'])
								<span class="badge badge-success">Disetujui</span>
							@else
								<span class="badge badge-danger">Ditolak</span>
							@endif
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	@endslot
@endcomponent