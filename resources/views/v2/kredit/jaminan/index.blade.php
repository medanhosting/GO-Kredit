@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 d-flex text-center"><i class="fa fa-credit-card-alt mr-2"></i> KREDIT</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3 sidebar-plain">
			@include('v2.kredit.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				@slot('header')
					<h5 class="py-2 pl-3 mb-0">&nbsp;&nbsp;MUTASI JAMINAN</h5>
				@endslot
				@slot('body')
					<form action="{{route('jaminan.index')}}" method="GET">
						<div class="row">
							<div class="col-sm-3">
								<label>Cari Nasabah</label>
						 		<input type="hidden" name="kantor_aktif_id" value="{{$kantor_aktif_id}}">
								<input type="text" name="q_jaminan" class="form-control w-100" placeholder="cari nama nasabah" value="{{request()->get('q_jaminan')}}">
							</div>
							<!-- CARI BERDASARKAN DOKUMEN -->
							<div class="col-sm-3">
								<label>Cari Dokumen</label>
								<input type="text" name="doc_jaminan" class="form-control w-100" placeholder="cari dokumen" value="{{request()->get('doc_jaminan')}}">
							</div>
							<!-- FILTER BERDASARKAN JAMINAN -->
							<div class="col-sm-2">
								<label>Filter Mutasi</label>
								<select class="form-control" name="mutasi_jaminan">
									<option value="">Semua Mutasi</option>
									<option value="in" @if(str_is(request()->get('mutasi_jaminan'), 'in')) selected @endif>Jaminan Masuk</option>
									<option value="out" @if(str_is(request()->get('mutasi_jaminan'), 'out')) selected @endif>Jaminan Keluar</option>
								</select>
							</div>
							<div class="col-sm-2">
								<label>Urutkan</label>
								<!-- URUTKAN BERDASARKAN NAMA/TANGGAL -->
								<select class="form-control" name="sort_jaminan">
									<option value="tanggal-asc" @if(str_is(request()->get('sort_jaminan'), 'tanggal-asc')) selected @endif>Tanggal [1 - 31]</option>
									<option value="tanggal-desc" @if(str_is(request()->get('sort_jaminan'), 'tanggal-desc')) selected @endif>Tanggal [31 - 1]</option>
								</select>
							</div>
						</div>
						<div class="clearfix">&nbsp;</div>
						<div class="row">
							<!-- CARI BERDASARKAN DOKUMEN -->
							<div class="col-sm-4">
								<label>Cari Tanggal</label>
								<div class="form-row">
									<div class='col-sm-5 order-1'>{!! Form::bsText(null, 'start', null, ['placeholder' => 'mulai', 'class' => 'mask-date form-control']) !!}</div>
									<div class="col-auto order-2"><i class="fa fa-minus pt-3"></i></div>
									<div class='col-sm-5 order-3'>{!! Form::bsText(null, 'end', null, ['placeholder' => 'sampai', 'class' => 'mask-date form-control']) !!}</div>
								</div>
							</div>
							<div class="col-sm-2 pl-1">
								<label>&nbsp;</label><br/>
								<button class="btn btn-primary" type="submit">Go!</button>
							</div>
						</div>
					</form>

					<div class="clearfix">&nbsp;</div>
					<table class="table table-bordered table-hover">
						<thead>
							<tr class="text-center">
								<th class="text-left">Nasabah</th>
								<th colspan="2">Catatan</th>
								<th>Dokumen</th>
							</tr>
						</thead>
						<tbody>
							@php $lua = null @endphp
							@forelse($jaminan as $k => $v)
								@php $tgl =  \Carbon\Carbon::createFromFormat('d/m/Y H:i', $v['tanggal'])->format('d/m/Y') @endphp
								@if($lua != $tgl)
									<tr>
										<td colspan="4" class="bg-light">
											{{$tgl}}
										</td>
									</tr>
									@php $lua = $tgl @endphp
								@endif
								<tr class="text-center">
									<td class="text-left">
										{{$v['kredit']['nasabah']['nama']}}<br/>
										{{$v['kredit']['nasabah']['telepon']}}
									</td>
									<td>
										@if(str_is($v['tag'], 'in'))
											<i class="fa fa-arrow-down text-success"></i>
										@else
											<i class="fa fa-arrow-up text-danger"></i>
										@endif
									</td>
									<td>
										{{$v['deskripsi']}}
									</td>
									<td class="text-left w-50">
										@if(str_is($v['dokumen']['jenis'], 'shm'))
											<h6>SHM - {{strtoupper(str_replace('_',' ',$v['kategori']))}}</h6>
											Nomor Sertifikat {{$v['dokumen']['shm']['nomor_sertifikat']}}<br/>
											{{implode(', ', $v['dokumen']['shm']['alamat'])}}
										@elseif(str_is($v['dokumen']['jenis'], 'shgb'))
											<h6>SHGB - {{strtoupper(str_replace('_',' ',$v['kategori']))}}</h6>
											Nomor Sertifikat {{$v['dokumen']['shgb']['nomor_sertifikat']}}<br/>
											{{implode(', ', $v['dokumen']['shgb']['alamat'])}}
										@else
											<h6>BPKB - {{strtoupper(str_replace('_',' ',$v['kategori']))}}</h6>
											Nomor BPKB {{$v['dokumen']['bpkb']['nomor_bpkb']}}<br/>
											Kendaraan {{str_replace('_', ' ', $v['dokumen']['bpkb']['jenis'])}} - {{$v['dokumen']['bpkb']['merk']}} , {{$v['dokumen']['bpkb']['tipe']}} ({{$v['dokumen']['bpkb']['tahun']}})
										@endif
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="4">
										<p>Data tidak tersedia, silahkan pilih Koperasi/BPR lain</p>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				@endslot
			@endcomponent
		</div>
	</div>
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush

@push ('js')
@endpush