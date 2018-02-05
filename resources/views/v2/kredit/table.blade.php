@inject('glossary', 'App\Service\UI\Glossary')
@inject('idr', 'App\Service\UI\IDRTranslater')
@inject('tanggal', 'App\Service\UI\TanggalTranslater')

<!-- FILTER AREA -->
<div class="clearfix">&nbsp;</div>
<div class="clearfix">&nbsp;</div>
<form action="{{route('kredit.index')}}" method="GET">
	<div class="row">
		<div class="col-sm-3">
			<label>Cari Nasabah</label>
		 	@foreach(request()->all() as $k => $v)
		 		@if(!in_array($k, ['q','sort','jaminan']))
			 		<input type="hidden" name="{{$k}}" value="{{$v}}">
		 		@endif
		 	@endforeach
	 		<input type="hidden" name="current" value="{{$s_pre}}">
			<input type="text" name="q" class="form-control w-100" placeholder="cari nama nasabah" value="{{request()->get('q')}}">
		</div>
		<!-- FILTER BERDASARKAN JAMINAN -->
		<div class="col-sm-2">
			<label>Filter Jaminan</label>
			<select class="form-control custom-select" name="jaminan">
				<option value="semua">Semua Jaminan</option>
				<option value="jaminan-bpkb" @if(str_is(request()->get('jaminan'), 'jaminan-bpkb')) selected @endif>Jaminan BPKB</option>
				<option value="jaminan-sertifikat" @if(str_is(request()->get('jaminan'), 'jaminan-sertifikat')) selected @endif>Jaminan Sertifikat</option>
			</select>
		</div>
		<!-- FILTER BERDASARKAN JAMINAN -->
		<div class="col-sm-3">
			<label>Filter Jenis Pinjaman</label>
			<select class="form-control custom-select" name="pinjaman">
				<option value="semua">Semua Jenis Pinjaman</option>
				<option value="pinjaman-a" @if(str_is(request()->get('pinjaman'), 'pinjaman-a')) selected @endif>Pinjaman Angsuran</option>
				<option value="pinjaman-t" @if(str_is(request()->get('pinjaman'), 'pinjaman-t')) selected @endif>Pinjaman Musiman</option>
			</select>
		</div>
		<div class="col-sm-2">
			<label>Urutkan</label>
			<!-- URUTKAN BERDASARKAN NAMA/TANGGAL -->
			<select class="form-control custom-select" name="sort">
				<option value="nama-asc" @if(str_is(request()->get('sort'), 'nama-asc')) selected @endif>Nama [A - Z]</option>
				<option value="nama-desc" @if(str_is(request()->get('sort'), 'nama-desc')) selected @endif>Nama [Z - A]</option>
				<option value="pinjaman-asc" @if(str_is(request()->get('sort'), 'pinjaman-asc')) selected @endif>Pinjaman [1 - 10]</option>
				<option value="pinjaman-desc" @if(str_is(request()->get('sort'), 'pinjaman-desc')) selected @endif>Pinjaman [10 - 1]</option>
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
<div class="clearfix">&nbsp;</div>
<table class="table table-bordered table-hover">
	<thead>
		<tr>
			<th rowspan="2" class="align-middle text-center">#</th>
			<th rowspan="2" class="align-middle text-center">Nama</th>
			<th rowspan="2" class="align-middle text-center">Jaminan</th>
			<th rowspan="2" class="align-middle text-center">Jenis Pinjaman</th>
			<th rowspan="2" class="align-middle text-right">Pokok Pinjaman</th>
			<th colspan="2" class="text-center">Pembayaran Berikut</th>
		</tr>
		<tr>
			<th class="text-center">Tanggal</th>
			<th class="text-right">Jumlah</th>
		</tr>
	</thead>
	<tbody>
		@foreach($data as $k => $v)
			<tr href="{{route('kredit.show', ['id' => $v['id'], 'kantor_aktif_id' => $kantor_aktif_id])}}">
				<td>{{ $data->firstItem() + $k }} </td>
				<td>{{$v['nasabah']['nama']}}</td>
				<td>
					@foreach($v['jaminan'] as $k)
						<p class="mb-1">
							{{strtoupper($k['dokumen']['jenis'])}} Nomor : {{strtoupper($k['dokumen'][$k['dokumen']['jenis']]['nomor_bpkb'])}}
							{{strtoupper($k['dokumen'][$k['dokumen']['jenis']]['nomor_sertifikat'])}}
						</p>
					@endforeach
				</td>
				<td class="text-center">
					{{$glossary::pinjaman($v['jenis_pinjaman'])}}
				</td>
				<td class="text-right">{{$v['plafon_pinjaman']}}</td>
				<td class="text-center">
					{{$tanggal->formatDatetimeTo($v['tanggal_pembayaran_berikut'])}}
				</td>
				<td class="text-right">
					{{$idr->formatMoneyTo($v['jumlah_pembayaran_berikut'])}}
				</td>
			</tr>
		@endforeach
	</tbody>
</table>
