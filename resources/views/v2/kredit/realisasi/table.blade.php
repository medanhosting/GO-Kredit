<!-- TABLE AREA -->
<div class="clearfix">&nbsp;</div>
<div class="float-right">
	{{ $data->appends(array_merge(request()->all(), ['current' => $s_pre]))->links() }}
</div>
<table class="table table-bordered table-hover">
	<thead>
		<tr>
			<th>#</th>
			<th>Nama</th>
			<th class="text-right">Pokok Pinjaman</th>
			<th>Jaminan</th>
			<th>Tanggal</th>
		</tr>
	</thead>
	<tbody>
		@foreach($data as $k => $v)
			<tr href="{{route('kredit.show', ['id' => $v['id'], 'kantor_aktif_id' => $kantor_aktif_id])}}">
				<td>{{ $data->firstItem() + $k }} </td>
				<td>{{$v['nasabah']['nama']}}</td>
				<td class="text-right">{{$v['pokok_pinjaman']}}</td>
				<td>
					<p>
					@php $flag_j = true @endphp
					@foreach($v['jaminan_kendaraan'] as $jk)
						{{strtoupper($jk['jenis'])}} Nomor : {{strtoupper($jk['dokumen_jaminan'][$jk['jenis']]['nomor_bpkb'])}}<br/>
						@if($jk['dokumen_jaminan'][$jk['jenis']]['is_lama']==false)
							@php $flag_j 	= false @endphp
						@endif
					@endforeach
					@foreach($v['jaminan_tanah_bangunan'] as $jtk)
						{{strtoupper($jtk['jenis'])}} Nomor : {{strtoupper($jtk['dokumen_jaminan'][$jtk['jenis']]['nomor_sertifikat'])}}<br/>
						@if($jk['dokumen_jaminan'][$jk['jenis']]['is_lama']==false)
							@php $flag_j 	= false @endphp
						@endif
					@endforeach
					</p>
				</td>
				<td>
					{{$v['status_terakhir']['tanggal']}}
				</td>
			</tr>
		@endforeach
	</tbody>
</table>
