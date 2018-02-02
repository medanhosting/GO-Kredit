@inject('carbon', 'Carbon\Carbon')

<div class="clearfix">&nbsp;</div>
<div class="clearfix">&nbsp;</div>
@component('bootstrap.card')
	@slot('title') 
		<h5 class='text-left'>
			<strong>BUKTI TRANSAKSI</strong>
		</h5>
	@endslot
	@slot('body')
		<table class="table table-hover">
			<thead>
				<tr>
					<th class="text-center">#</th>
					<th class="text-left">TANGGAL</th>
					<th class="text-left">KETERANGAN</th>
					<th class="text-right">JUMLAH</th>
					<th class="text-center">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				@foreach($notabayar as $k => $v)
					<tr>
						<td class="text-center">{{ $loop->iteration }}</td>
						<td class="text-left">{{$carbon::parse($v['tanggal_bayar'])->format('d/m/Y')}}</td>
						<td class="text-left">{{$v['deskripsi']}}</td>
						<td class="text-right">{{$v['jumlah']}}</td>
						<td class="text-center">
							@if(str_is($v['jenis'], 'angsuran'))
								<a href="{{ route('angsuran.print', ['id' => $v['morph_reference_id'], 'nota_bayar_id' => $v['id'], 'kantor_aktif_id' => $kantor_aktif['id']]) }}" target="__blank" class="text-success">
									CETAK
								</a>
							@elseif(str_is($v['jenis'], 'angsuran_sementara'))
								<a href="{{ route('angsuran.print', ['id' => $v['morph_reference_id'], 'nota_bayar_id' => $v['id'], 'kantor_aktif_id' => $kantor_aktif['id'], 'case' => 'sementara']) }}" target="__blank" class="text-success">
									CETAK
								</a>
							@else
								<a href="#"></a>
							@endif
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	@endslot
@endcomponent


