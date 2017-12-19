<!-- FILTER AREA -->
<div class="clearfix">&nbsp;</div>
<div class="clearfix">&nbsp;</div>
<form action="{{route('pengajuan.index')}}" method="GET">
	<div class="row">
		<div class="col-sm-3">
			<label>Cari Nasabah</label>
		 	@foreach(request()->all() as $k => $v)
		 		@if(!in_array($k, ['q_'.$pre,'sort_'.$pre,'jaminan_'.$pre]))
			 		<input type="hidden" name="{{$k}}" value="{{$v}}">
		 		@endif
		 	@endforeach
	 		<input type="hidden" name="current" value="{{$s_pre}}">
			<input type="text" name="q_{{$s_pre}}" class="form-control w-100" placeholder="cari nama nasabah" value="{{request()->get('q_'.$s_pre)}}">
		</div>
		<!-- FILTER BERDASARKAN JAMINAN -->
		<div class="col-sm-3">
			<label>Filter Berdasarkan Jaminan</label>
			<select class="form-control" name="jaminan_{{$s_pre}}">
				<option value="semua">Semua Jaminan</option>
				<option value="jaminan-bpkb" @if(str_is(request()->get('jaminan_'.$s_pre), 'jaminan-bpkb')) selected @endif>Jaminan BPKB</option>
				<option value="jaminan-sertifikat" @if(str_is(request()->get('jaminan_'.$s_pre), 'jaminan-sertifikat')) selected @endif>Jaminan Sertifikat</option>
			</select>
		</div>
		<div class="col-sm-2">
			<label>Urutkan</label>
			<!-- URUTKAN BERDASARKAN NAMA/TANGGAL -->
			<select class="form-control" name="sort_{{$s_pre}}">
				<option value="nama-asc" @if(str_is(request()->get('sort_'.$s_pre), 'nama-asc')) selected @endif>Nama [A - Z]</option>
				<option value="nama-desc" @if(str_is(request()->get('sort_'.$s_pre), 'nama-desc')) selected @endif>Nama [Z - A]</option>
				<option value="tanggal-asc" @if(str_is(request()->get('sort_'.$s_pre), 'tanggal-asc')) selected @endif>Tanggal [1 - 31]</option>
				<option value="tanggal-desc" @if(str_is(request()->get('sort_'.$s_pre), 'tanggal-desc')) selected @endif>Tanggal [31 - 1]</option>
			</select>
		</div>
		<div class="col-sm-2 pl-1">
			<label>&nbsp;</label><br/>
			<button class="btn btn-primary" type="submit">Go!</button>
		</div>
	</div>
</form>

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
			<th>Catatan</th>
		</tr>
	</thead>
	<tbody>
		@foreach($data as $k => $v)
			<tr href="{{route('pengajuan.show', ['id' => $v['id'], 'kantor_aktif_id' => $kantor_aktif_id])}}">
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
				<td>
					{{$v['status_terakhir']['progress']}} dikerjakan
				</td>
			</tr>
		@endforeach
	</tbody>
</table>
