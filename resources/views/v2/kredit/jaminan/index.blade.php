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
				@slot('pre')
					<h6 class="pt-4 pl-4">MUTASI JAMINAN</h6>
				@endslot
				@slot('body')
					<form action="{{route('jaminan.index')}}" method="GET">
						<div class="row">
							<div class="col-sm-3">
								<label>Cari Nasabah</label>
							 	@foreach(request()->all() as $k => $v)
							 		@if(!in_array($k, ['q_'.$pre,'sort_'.$pre,'jaminan_'.$pre]))
								 		<input type="hidden" name="{{$k}}" value="{{$v}}">
							 		@endif
							 	@endforeach
						 		<input type="hidden" name="current" value="{{$s_pre}}">
								<input type="text" name="q_{{$s_pre}}" class="form-control w-100" placeholder="cari nama nasabah" value="{{request()->get('q_jaminan')}}">
							</div>
							<!-- CARI BERDASARKAN DOKUMEN -->
							<div class="col-sm-3">
								<label>Cari Dokumen</label>
								<input type="text" name="doc_{{$s_pre}}" class="form-control w-100" placeholder="cari dokumen" value="{{request()->get('doc_jaminan')}}">
							</div>
							<!-- FILTER BERDASARKAN JAMINAN -->
							<div class="col-sm-2">
								<label>Filter Mutasi</label>
								<select class="form-control" name="mutasi_{{$s_pre}}">
									<option value="semua">Semua Mutasi</option>
									<option value="jaminan-m" @if(str_is(request()->get('mutasi_jaminan'), 'jaminan-m')) selected @endif>Jaminan Masuk</option>
									<option value="jaminan-k" @if(str_is(request()->get('mutasi_jaminan'), 'jaminan-k')) selected @endif>Jaminan Keluar</option>
								</select>
							</div>
							<div class="col-sm-2">
								<label>Urutkan</label>
								<!-- URUTKAN BERDASARKAN NAMA/TANGGAL -->
								<select class="form-control" name="sort_{{$s_pre}}">
									<option value="nama-asc" @if(str_is(request()->get('sort_jaminan'), 'nama-asc')) selected @endif>Nama [A - Z]</option>
									<option value="nama-desc" @if(str_is(request()->get('sort_jaminan'), 'nama-desc')) selected @endif>Nama [Z - A]</option>
									<option value="pinjaman-asc" @if(str_is(request()->get('sort_jaminan'), 'pinjaman-asc')) selected @endif>Pinjaman [1 - 10]</option>
									<option value="pinjaman-desc" @if(str_is(request()->get('sort_jaminan'), 'pinjaman-desc')) selected @endif>Pinjaman [10 - 1]</option>
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
								@if($lua != $v['updated_at']->format('d/m/Y'))
									<tr>
										<td colspan="4" class="bg-light">
											{{$v['updated_at']->format('d/m/Y')}}
										</td>
									</tr>
									@php $lua = $v['updated_at']->format('d/m/Y') @endphp
								@endif
								<tr class="text-center">
									<td class="text-left">
										{{$v['kredit']['nasabah']['nama']}}<br/>
										{{$v['kredit']['nasabah']['telepon']}}
									</td>
									<td>
										@if(is_null($v['taken_at']))
											<i class="fa fa-arrow-down text-success"></i>
										@else
											<i class="fa fa-arrow-up text-danger"></i>
										@endif
									</td>
									<td>
										{{$v['description']}}
									</td>
									<td class="text-left">
										@if(str_is($v['documents']['jenis'], 'shm'))
											<h6>SHM</h6>
											Nomor Sertifikat {{$v['documents']['shm']['nomor_sertifikat']}}<br/>
											{{implode(', ', $v['documents']['shm']['alamat'])}}
										@elseif(str_is($v['documents']['jenis'], 'shgb'))
											<h6>SHGB</h6>
											Nomor Sertifikat {{$v['documents']['shgb']['nomor_sertifikat']}}<br/>
											{{implode(', ', $v['documents']['shgb']['alamat'])}}
										@else
											<h6>BPKB</h6>
											Nomor BPKB {{$v['documents']['bpkb']['nomor_bpkb']}}<br/>
											Kendaraan {{str_replace('_', ' ', $v['documents']['bpkb']['jenis'])}} - {{$v['documents']['bpkb']['merk']}} , {{$v['documents']['bpkb']['tipe']}} ({{$v['documents']['bpkb']['tahun']}})
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