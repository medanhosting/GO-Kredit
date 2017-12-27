<div class="clearfix">&nbsp;</div>
<form action="{{route('kantor.index')}}" method="GET">
	<div class="row">
		<div class="col-sm-3">
			<label>Cari Kode Kantor / Nama</label>
		 	@foreach(request()->all() as $k => $v)
		 		@if(!in_array($k, ['q','sort']))
			 		<input type="hidden" name="{{$k}}" value="{{$v}}">
		 		@endif
		 	@endforeach
			<input type="text" name="q" class="form-control w-100" placeholder="cari kode kantor / nama" value="{{request()->get('q')}}">
		</div>
		<!-- FILTER BERDASARKAN JAMINAN -->
		<div class="col-sm-2">
			<label>Jenis Kantor</label>
			<select class="form-control" name="jenis">
				<option value="semua">Semua Jenis</option>
				<option value="bpr" @if(str_is(request()->get('jenis'), 'bpr')) selected @endif>BPR</option>
				<option value="koperasi" @if(str_is(request()->get('jenis'), 'koperasi')) selected @endif>Koperasi</option>
			</select>
		</div>
		<!-- FILTER BERDASARKAN JAMINAN -->
		<div class="col-sm-3">
			<label>Tipe Kantor</label>
			<select class="form-control" name="tipe">
				<option value="semua">Semua Tipe</option>
				<option value="holding" @if(str_is(request()->get('tipe'), 'holding')) selected @endif>Holding</option>
				<option value="pusat" @if(str_is(request()->get('tipe'), 'pusat')) selected @endif>Pusat</option>
				<option value="cabang" @if(str_is(request()->get('tipe'), 'cabang')) selected @endif>Cabang</option>
			</select>
		</div>
		<div class="col-sm-2">
			<label>Urutkan</label>
			<!-- URUTKAN BERDASARKAN NAMA/TANGGAL -->
			<select class="form-control" name="sort">
				<option value="nama-asc" @if(str_is(request()->get('sort'), 'nama-asc')) selected @endif>Nama [A - Z]</option>
				<option value="nama-desc" @if(str_is(request()->get('sort'), 'nama-desc')) selected @endif>Nama [Z - A]</option>
			</select>
		</div>
		<div class="col-sm-2 pl-1">
			<label>&nbsp;</label><br/>
			<button class="btn btn-primary" type="submit">Go!</button>
		</div>
	</div>
</form>
<div class="clearfix">&nbsp;</div>
<div class="float-right">
	{{ $kantor->appends(array_merge(request()->all()))->links() }}
</div>
<table class="table table-bordered">
		<thead>
		<tr>
			<th>#</th>
			<th>Kode kantor</th>
			<th>Nama</th>
			<th>Tipe</th>
			<th class="w-50">Alamat</th>
		</tr>
		</thead>
		<tbody>
		@forelse($kantor as $k => $v)
			<tr>
				<td>{{(($kantor->currentPage() - 1) * $kantor->perPage()) + $k + 1}}</td>
				<td>{{$v['id']}}</td>
				<td>
					<span class="badge badge-primary">{{$v['jenis']}}</span> 
					{{$v['nama']}}
					<br/><i class="fa fa-phone"></i> {{$v['telepon']}}
				</td>
				<td>{{ucwords($v['tipe'])}}</td>
				<td class="w-50">
					@foreach($v['alamat'] as $k2 => $v2)
						{{ucwords($k2)}} {{ucwords($v2)}}
					@endforeach
				</td>
		    </tr>
		@empty
			<tr>
				<td colspan="5">
					Data tidak tersedia
				</td>
			</tr>
			@endforelse
	</tbody>
</table>