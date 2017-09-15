@push('main')
	<div class="container bg-white bg-shadow p-4">
		<div class="row">
			<div class="col">
				<h4 class='mb-4 text-style text-secondary'>
					<span class="text-uppercase">Daftar {{($status)}} Kredit</span> 
					<small><small>@if($pengajuan->currentPage() > 1) Halaman {{$pengajuan->currentPage()}} @endif</small></small>
				</h4>
				<div class="row">
					<div class="col-5">
						<a href="{{ route('pengajuan.permohonan.create') }}" class="btn btn-primary text-capitalize text-style mb-2">pengajuan baru</a>
					</div>
					<div class="col-4">
						<form action="{{route('pengajuan.pengajuan.index', array_merge(request()->all(), ['status' => $status]))}}" method="GET">
							 <div class="input-group">
							 	@foreach(request()->all() as $k => $v)
							 		@if(!str_is($k, 'q'))
								 		<input type="hidden" name="{{$k}}" value="{{$v}}">
							 		@endif
							 	@endforeach
								<input type="text" name="q" class="form-control" placeholder="cari nama nasabah atau nomor pengajuan" value="{{request()->get('q')}}">
								<span class="input-group-btn">
									<button class="btn btn-secondary" type="submit" style="background-color:#fff;color:#aaa;border-color:#ccc">Go!</button>
								</span>
							</div>
						</form>
					</div>
					<div class="col-3 text-right">
						<div class="input-group">
							<label style="border:0px;padding:7px;">Urut Berdasarkan</label>
							<div class="dropdown">
								<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color:#fff;color:#aaa;border-color:#ccc">
									{{$order}}
								</button>
								<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
									<a class="dropdown-item" href="{{route('pengajuan.pengajuan.index', array_merge(request()->all(), ['status' => $status, 'order' => 'date-asc']))}}">Tanggal terbaru &nbsp;&nbsp;&nbsp;&nbsp;</a>
									<!-- <a class="dropdown-item" href="{{route('pengajuan.pengajuan.index', array_merge(request()->all(), ['status' => $status, 'order' => 'date-desc']))}}">Tanggal Z - A</a> -->
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="clearfix">&nbsp;</div>

				<div id="accordion" role="tablist" aria-multiselectable="true">
  					<div class="card" style="background-color:#eee;border:none;border-radius:0">
						<div class="card-header" role="tab" id="headingOne">
							<div class="row text-left">
								<div class="col-1"><strong>#</strong></div>
								<div class="col-3"><strong>No. Pengajuan</strong></div>
								<div class="col-2"><strong>Tgl Pengajuan</strong></div>
								<div class="col-2"><strong>Jumlah Pinjaman</strong></div>
								<div class="col-2"><strong>Nasabah</strong></div>
								<div class="col-2"></div>
							</div>
						</div>
	 				</div>
					@forelse($pengajuan as $k => $v)
	  					<div class="card" style="background-color:#fff;border:none;border-radius:0">
	    					<div class="card-header" role="tab" id="heading{{$k}}" style="background-color:#fff;border-bottom:1px solid #eee">
	    						<div class="row text-left">
									<div class="col-1">{{(($pengajuan->currentPage() - 1) * $pengajuan->perPage()) + $k + 1}}</div>
									<div class="col-3">
										{{$v['id']}} 
										@if($v['is_mobile']) 
											<span class="badge badge-primary"><small>Mobile</small></span> 
										@endif
									</div>
									<div class="col-2">{{$v['status_permohonan']['tanggal']}}</div>
									<div class="col-2">{{$v['pokok_pinjaman']}}</div>
									<div class="col-2">
										{{$v['nasabah']['nama']}}
									</div>
									<div class="col-2">
			    						<div class="row text-center">
											<div class="col-4">
												<a href=""><i class="fa fa-eye"></i></a>
											</div>
											<div class="col-4">
												<a href="#" data-toggle="modal" data-target="#delete"><i class="fa fa-trash"></i></a>
											</div>
											<div class="col-4">
												<a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$k}}" aria-expanded="false" aria-controls="collapse{{$k}}"><i class="fa fa-arrow-down"></i></a>
											</div>
										</div>
									</div>
								</div>
						    </div>
							<div id="collapse{{$k}}" class="collapse" role="tabpanel" aria-labelledby="heading{{$k}}">
								<div class="card-block" style="background-color:#ccc;border-bottom:1px solid #bbb;padding-bottom:20px;">
									<div class="row">
										<div class="col-1">
										</div>
										<div class="col-10">
											<div class="row" style="padding-top:20px;">
												<div class="col-12 text-right">
													<p>
														<i>
															@if(str_is($v['status_terakhir']['progress'], 'perlu'))
																menunggu {{$v['status_terakhir']['status']}}
															@elseif(str_is($v['status_terakhir']['progress'], 'sedang'))
																dalam proses {{$v['status_terakhir']['status']}}
															@elseif(str_is($v['status_terakhir']['progress'], 'sudah'))
																selesai proses {{$v['status_terakhir']['status']}}
															@endif
														</i>
													</p>
												</div>
												<div class="col-6 text-left">
													<p>Jaminan :</p>
												</div>
												<div class="col-6 text-right">
													<p><i class="fa fa-mobile"></i> {{$v['nasabah']['telepon']}}</p>
												</div>
											</div>

											<p class="text-secondary text-capitalize mb-1">kendaraan</p>
											<table class="table table-sm table-bordered" style="background-color:#ccc;">
												<thead>
													<tr>
														<th>#</th>
														<th>Jenis</th>
														<th>No. BPKB</th>
														<th>Merk</th>
														<th>Tipe [Tahun]</th>
														<th>Tahun Perolehan</th>
														<th>Harga Jaminan (*)</th>
													</tr>
												</thead> 
												<tbody>
													@forelse($v['jaminan_kendaraan'] as $kj => $vj)
													<tr>
														<td class="text-center">{{($kj + 1)}}</td>
														<td>{{ucwords(str_replace('_', ' ', $vj['dokumen_jaminan']['bpkb']['tipe']))}}</td>
														<td>{{$vj['dokumen_jaminan']['bpkb']['nomor_bpkb']}}</td>
														<td>{{ucwords($vj['dokumen_jaminan']['bpkb']['merk'])}}</td>
														<td>{{$vj['dokumen_jaminan']['bpkb']['jenis']}} [{{$vj['dokumen_jaminan']['bpkb']['tahun']}}]</td>
														<td class="text-right">{{$vj['tahun_perolehan']}}</td>
														<td class="text-right">{{$vj['nilai_jaminan']}}</td>
													</tr>
													@empty
														<tr>
															<td colspan="7" class="text-center"><i class="text-secondary">tidak ada data</i></td>
														</tr>
													@endforelse
													<tr>
														<td colspan="7" class="text-right" style="border:0">
															<small>
																<i class="text-secondary">* menurut nasabah</i>
															</small>
														</td>
													</tr>
												</tbody>
											</table>

											<p class="text-secondary text-capitalize mb-1">tanah &amp; bangunan</p>
											<table class="table table-sm table-bordered" style="background-color:#ccc;">
												<thead>
													<tr>
														<th rowspan="2" style="vertical-align:middle;">#</th>
														<th colspan="4">Sertifikat</th>
														<th rowspan="2" style="vertical-align:middle;">Tahun Perolehan</th>
														<th rowspan="2" style="vertical-align:middle;">Harga Jaminan (*)</th>
													</tr>
													<tr>
														<th>Jenis [Masa Berlaku]</th>
														<th>Nomor</th>
														<th>Tipe</th>
														<th>Luas</th>
													</tr>
												</thead>
												<tbody>
													@forelse($v['jaminan_tanah_bangunan'] as $kj => $vj)
													<tr>
														<td class="text-center">{{$kj+1}}</td>
														<td>{{strtoupper($vj['jenis'])}} </td>
														<td>
															{{$vj['dokumen_jaminan'][$vj['jenis']]['nomor_sertifikat']}}
															@if(isset($vj['dokumen_jaminan'][$vj['jenis']]['masa_berlaku_sertifikat']))
																[{{$vj['dokumen_jaminan'][$vj['jenis']]['masa_berlaku_sertifikat']}}]
															@endif
														</td>
														<td>{{str_replace('_', ' ', $vj['dokumen_jaminan'][$vj['jenis']]['tipe'])}}</td>
														<td>
															Luas Tanah : {{$vj['dokumen_jaminan'][$vj['jenis']]['luas_tanah']}}M<sup>2</sup>
															<br/>
															@if(isset($vj['dokumen_jaminan'][$vj['jenis']]['luas_bangunan']))
																Luas Bangunan : {{$vj['dokumen_jaminan'][$vj['jenis']]['luas_bangunan']}}M<sup>2</sup>
															@endif
														</td>
														<td class="text-right">{{$vj['tahun_perolehan']}}</td>
														<td class="text-right">{{$vj['nilai_jaminan']}}</td>
													</tr>
													@empty
														<tr>
															<td colspan="7" class="text-center"><i class="text-secondary">tidak ada data</i></td>
														</tr>
													@endforelse
													<tr>
														<td colspan="7" class="text-right" style="border:0">
															<small>
																<i class="text-secondary">* menurut nasabah</i>
															</small>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					@empty
	  					<div class="card" style="background-color:#fff;border:none;border-radius:0">
	    					<div class="card-header" role="tab" id="heading{{$k}}" style="background-color:#fff;border-bottom:1px solid #eee">
	    						<div class="row text-center">
									<div class="col-12"><p>Data tidak tersedia, silahkan pilih Koperasi/BPR lain</p></div>
								</div>
						    </div>
						</div>
  					@endforelse
				</div>

				<div class="clearfix">&nbsp;</div>
				<div class="row">
					<div class="col">
						{{$pengajuan->appends(request()->all())}}
					</div>
				</div>

				<!-- <table class="table table-responsive table-bordered">
					<thead class="thead-default">
						<tr>
							<th>#</th>
							<th>No. Pengajuan</th>
							<th>Tgl Pengajuan</th>
							<th>Jumlah Pinjaman</th>
							<th>Nasabah</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						@foreach($pengajuan as $k => $v)
							<tr>
								<td rowspan="2">{{(($pengajuan->currentPage() - 1) * $pengajuan->perPage()) + $k + 1}}</td>
								<td>{{$v['id']}}</td>
								<td>{{$v['status_permohonan']['tanggal']}}</td>
								<td>{{$v['pokok_pinjaman']}}</td>
								<td>{{$v['nasabah']['nama']}}</td>
								<td rowspan="2"><a href="#" class="btn btn-sm btn-primary">Action</a></td>
							</tr>
							<tr>
								<td colspan="4">
									<p>Jaminan :</p>
									<p class="text-secondary text-capitalize mb-1">kendaraan</p>
									<table class="table table-sm bg-white no-border">
										<thead>
											<tr>
												<th>#</th>
												<th>Jenis</th>
												<th>No. BPKB</th>
												<th>Merk</th>
												<th>Tipe [Tahun]</th>
												<th>Tahun Perolehan</th>
												<th>Harga Jaminan (*)</th>
											</tr>
										</thead> 
										<tbody>
											@foreach($v['jaminan_kendaraan'] as $kj => $vj)
											<tr>
												<td class="text-center">{{($kj + 1)}}</td>
												<td>{{ucwords(str_replace('_', ' ', $vj['dokumen_jaminan']['bpkb']['tipe']))}}</td>
												<td>{{$vj['dokumen_jaminan']['bpkb']['nomor_bpkb']}}</td>
												<td>{{ucwords($vj['dokumen_jaminan']['bpkb']['merk'])}}</td>
												<td>{{$vj['dokumen_jaminan']['bpkb']['jenis']}} [{{$vj['dokumen_jaminan']['bpkb']['tahun']}}]</td>
												<td class="text-right">{{$vj['tahun_perolehan']}}</td>
												<td class="text-right">{{$vj['nilai_jaminan']}}</td>
											</tr>
											@endforeach
											<tr>
												<td colspan="7" class="text-right" style="border:0">
													<small>
														<i class="text-secondary">* menurut nasabah</i>
													</small>
												</td>
											</tr>
										</tbody>
									</table>

									<p class="text-secondary text-capitalize mb-1">tanah &amp; bangunan</p>
									<table class="table table-sm bg-white no-border">
										<thead>
											<tr>
												<th rowspan="2" style="vertical-align:middle;">#</th>
												<th colspan="4">Sertifikat</th>
												<th rowspan="2" style="vertical-align:middle;">Tahun Perolehan</th>
												<th rowspan="2" style="vertical-align:middle;">Harga Jaminan (*)</th>
											</tr>
											<tr>
												<th>Jenis [Masa Berlaku]</th>
												<th>Nomor</th>
												<th>Tipe</th>
												<th>Luas</th>
											</tr>
										</thead>
										<tbody>
											@foreach($v['jaminan_tanah_bangunan'] as $kj => $vj)
											<tr>
												<td class="text-center">{{$kj+1}}</td>
												<td>{{strtoupper($vj['jenis'])}} </td>
												<td>
													{{$vj['dokumen_jaminan'][$vj['jenis']]['nomor_sertifikat']}}
													@if(isset($vj['dokumen_jaminan'][$vj['jenis']]['masa_berlaku_sertifikat']))
														[{{$vj['dokumen_jaminan'][$vj['jenis']]['masa_berlaku_sertifikat']}}]
													@endif
												</td>
												<td>{{str_replace('_', ' ', $vj['dokumen_jaminan'][$vj['jenis']]['tipe'])}}</td>
												<td>
													Luas Tanah : {{$vj['dokumen_jaminan'][$vj['jenis']]['luas_tanah']}}M<sup>2</sup>
													<br/>
													@if(isset($vj['dokumen_jaminan'][$vj['jenis']]['luas_bangunan']))
														Luas Bangunan : {{$vj['dokumen_jaminan'][$vj['jenis']]['luas_bangunan']}}M<sup>2</sup>
													@endif
												</td>
												<td class="text-right">{{$vj['tahun_perolehan']}}</td>
												<td class="text-right">{{$vj['nilai_jaminan']}}</td>
											</tr>
											@endforeach
											<tr>
												<td colspan="7" class="text-right" style="border:0">
													<small>
														<i class="text-secondary">* menurut nasabah</i>
													</small>
												</td>
											</tr>
										</tbody>
									</table>
									{{-- <ol class="pl-3">
										<li class="text-capitalize">(roda 4) mitsubishi colt l300 th 2010 dengan harga Rp. 100.000.000</li>
										<li class="text-capitalize">(roda 2) yamaha mio th 2009 dengan harga Rp. 9.000.000</li>
									</ol> --}}
								</td>
							</tr>
						@endforeach
					</tbody>
				</table> -->
			</div>
		</div>
	</div>

	@component ('bootstrap.modal', ['id' => 'delete'])
		{!! Form::open() !!}
		@slot ('title')
			Hapus Data
		@endslot

		@slot ('body')
			<p>Untuk menghapus data ini, silahkan masukkan password dibawah!</p>
			{!! Form::bsPassword(null, 'password', ['placeholder' => 'Password']) !!}
		@endslot

		@slot ('footer')
			<a href="#" data-dismiss="modal" class="btn btn-link text-secondary">Batal</a>
			<a href="#" class="btn btn-danger btn-outline">Tambahkan</a>
		@endslot
		{!! Form::close() !!}
	@endcomponent
@endpush

@push('submenu')
	<div class="container-fluid bg-light" style="background-color: #eee !important;">
		<div class="row">
			<div class="col">
				<nav class="nav">
					<a href="#" class="nav-link text-secondary">Simulasi Kredit</a>
				</nav>
			</div>
		</div>
	</div>
@endpush