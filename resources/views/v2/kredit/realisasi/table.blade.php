@inject('glossary', 'App\Service\UI\Glossary')

<form action="{{route('realisasi.index')}}" method="GET">
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
		<div class="col-sm-2">
			<label>Filter Jaminan</label>
			<select class="form-control custom-select" name="jaminan_{{$s_pre}}">
				<option value="semua">Semua Jaminan</option>
				<option value="jaminan-bpkb" @if(str_is(request()->get('jaminan_'.$s_pre), 'jaminan-bpkb')) selected @endif>Jaminan BPKB</option>
				<option value="jaminan-sertifikat" @if(str_is(request()->get('jaminan_'.$s_pre), 'jaminan-sertifikat')) selected @endif>Jaminan Sertifikat</option>
			</select>
		</div>
		<!-- FILTER BERDASARKAN JAMINAN -->
		<div class="col-sm-3">
			<label>Filter Jenis Pinjaman</label>
			<select class="form-control custom-select" name="pinjaman_{{$s_pre}}">
				<option value="semua">Semua Jenis Pinjaman</option>
				<option value="pinjaman-a" @if(str_is(request()->get('pinjaman_'.$s_pre), 'pinjaman-a')) selected @endif>Pinjaman Angsuran</option>
				<option value="pinjaman-t" @if(str_is(request()->get('pinjaman_'.$s_pre), 'pinjaman-t')) selected @endif>Pinjaman Musiman</option>
			</select>
		</div>
		<div class="col-sm-2">
			<label>Urutkan</label>
			<!-- URUTKAN BERDASARKAN NAMA/TANGGAL -->
			<select class="form-control custom-select" name="sort_{{$s_pre}}">
				<option value="nama-asc" @if(str_is(request()->get('sort_'.$s_pre), 'nama-asc')) selected @endif>Nama [A - Z]</option>
				<option value="nama-desc" @if(str_is(request()->get('sort_'.$s_pre), 'nama-desc')) selected @endif>Nama [Z - A]</option>
				<option value="pinjaman-asc" @if(str_is(request()->get('sort_'.$s_pre), 'pinjaman-asc')) selected @endif>Pinjaman [1 - 10]</option>
				<option value="pinjaman-desc" @if(str_is(request()->get('sort_'.$s_pre), 'pinjaman-desc')) selected @endif>Pinjaman [10 - 1]</option>
			</select>
		</div>
		<div class="col-sm-2 pl-1">
			<label>&nbsp;</label><br/>
			<button class="btn btn-primary" type="submit">Go!</button>
		</div>
	</div>
</form>
<div class="clearfix">&nbsp;</div>
<!-- TABLE AREA -->
<div class="float-right">
	{{ $data->appends(array_merge(request()->all(), ['current' => $s_pre]))->links() }}
</div>
<div class="clearfix">&nbsp;</div>
<table class="table table-bordered table-hover">
	<thead>
		<tr>
			<th>#</th>
			<th>Nama</th>
			<th class="text-center">Jenis Pinjaman</th>
			<th class="text-right">Pokok Pinjaman</th>
			<th>Jaminan</th>
			<th>Tanggal Persetujuan</th>
		</tr>
	</thead>
	<tbody>
		@forelse($data as $k => $v)
			<tr href="{{route('realisasi.show', ['id' => $v['id'], 'kantor_aktif_id' => $kantor_aktif_id])}}">
				<td>{{ $data->firstItem() + $k }} </td>
				<td>{{$v['nasabah']['nama']}}<br/>{{$v['nasabah']['telepon']}}</td>
				<td class="text-center">
					{{$glossary::pinjaman($v['jenis_pinjaman'])}}
				</td>
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
		@empty
			<tr>
				<td colspan="6" class="text-center">tidak ada data</td>
			</tr>
		@endforelse
	</tbody>
</table>
