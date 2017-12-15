@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 d-flex text-center"><i class="fa fa-paper-plane mr-2"></i> PENGAJUAN</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3">
			@include('v2.pengajuan.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				@slot('body')
					<nav class="nav nav-tabs" id="myTab" role="tablist">
						<a class="nav-item nav-link active" id="nav-dalam-permohonan-tab" data-toggle="tab" href="#nav-dalam-permohonan" role="tab" aria-controls="nav-dalam-permohonan" aria-selected="true">Permohonan <span class="badge badge-success">{{$permohonan->total()}}</span></a>
						<a class="nav-item nav-link" id="nav-survei-tab" data-toggle="tab" href="#nav-survei" role="tab" aria-controls="nav-survei" aria-selected="false">Survei <span class="badge badge-success">{{$survei->total()}}</a>
						<a class="nav-item nav-link" id="nav-analisa-tab" data-toggle="tab" href="#nav-analisa" role="tab" aria-controls="nav-analisa" aria-selected="false">Analisa <span class="badge badge-success">{{$analisa->total()}}</a>
						<a class="nav-item nav-link" id="nav-putusan-tab" data-toggle="tab" href="#nav-putusan" role="tab" aria-controls="nav-putusan" aria-selected="false">Putusan <span class="badge badge-success">{{$putusan->total()}}</a>
					</nav>
					<div class="tab-content" id="nav-tabContent">
						<div class="tab-pane fade show active" id="nav-dalam-permohonan" role="tabpanel" aria-labelledby="nav-dalam-permohonan-tab">
							<div class="clearfix">&nbsp;</div>
							{{ $permohonan->appends(request()->all())->links() }}
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>#</th>
										<th>Nasabah</th>
										<th>Pokok Pinjaman</th>
										<th>Jaminan</th>
										<th>Tanggal</th>
										<th>Catatan</th>
									</tr>
								</thead>
								<tbody>
									@foreach($permohonan as $k => $v)
										<tr>
											<td colspan="6" class="bg-light">{{$v['id']}}</td>
										</tr>
										<tr>
											<td>{{ $permohonan->firstItem() + $k }}</td>
											<td>{{$v['nasabah']['nama']}}</td>
											<td class="text-right">{{$v['pokok_pinjaman']}}</td>
											<td>
												@php $flag_j = true @endphp
												@foreach($v['jaminan_kendaraan'] as $jk)
													<p style="margin:5px;">{{strtoupper($jk['jenis'])}} Nomor : {{strtoupper($jk['dokumen_jaminan'][$jk['jenis']]['nomor_bpkb'])}}</p>
													@if($jk['dokumen_jaminan'][$jk['jenis']]['is_lama']==false)
														@php $flag_j 	= false @endphp
													@endif
												@endforeach
												@foreach($v['jaminan_tanah_bangunan'] as $jtk)
													<p style="margin:5px;">{{strtoupper($jtk['jenis'])}} Nomor : {{strtoupper($jtk['dokumen_jaminan'][$jtk['jenis']]['nomor_sertifikat'])}}</p>
													@if($jk['dokumen_jaminan'][$jk['jenis']]['is_lama']==false)
														@php $flag_j 	= false @endphp
													@endif
												@endforeach
											</td>
											<td>
												{{$v['status_terakhir']['tanggal']}}
											</td>
											<td>
												@if(!$v['is_complete'])
													Data Belum Lengkap. 
													<a href="{{route('pengajuan.permohonan.show', ['id' => $v['id'], 'kantor_aktif_id' => $kantor_aktif['id']])}}"><i>Lengkapi Sekarang</i></a>
												@elseif($v['nasabah']['is_lama'] && $flag_j)
													Nasabah & Jaminan Lama. 
													<a data-toggle="modal" data-target="#lanjut-analisa" data-action="{{route('pengajuan.pengajuan.assign_analisa', ['id' => $v['id'], 'kantor_aktif_id' => $kantor_aktif['id'], 'status' => 'permohonan'])}}" class="modal_analisa  text-success"><i>Lanjutkan Analisa</i></a>
												@else
													Data Sudah Lengkap. 
													<a class="modal_assign text-success" data-toggle="modal" data-target="#assign-survei" data-action="{{route('pengajuan.permohonan.assign_survei', ['id' => $v['id'], 'kantor_aktif_id' => $kantor_aktif['id'], 'status' => 'permohonan'])}}"><i>Assign Untuk Survei</i></a>
												@endif
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
						<div class="tab-pane fade" id="nav-survei" role="tabpanel" aria-labelledby="nav-survei-tab">
							<div class="clearfix">&nbsp;</div>
							{{ $survei->appends(request()->all())->links() }}
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>#</th>
										<th>Nasabah</th>
										<th>Pokok Pinjaman</th>
										<th>Jaminan</th>
										<th>Tanggal</th>
										<th>Catatan</th>
									</tr>
								</thead>
								<tbody>
									@foreach($survei as $k => $v)
										<tr>
											<td colspan="6" class="bg-light">{{$v['id']}}</td>
										</tr>
										<tr>
											<td>{{ $survei->firstItem() + $k }}</td>
											<td>{{$v['nasabah']['nama']}}</td>
											<td class="text-right">{{$v['pokok_pinjaman']}}</td>
											<td>
												@php $flag_j = true @endphp
												@foreach($v['jaminan_kendaraan'] as $jk)
													<p style="margin:5px;">{{strtoupper($jk['jenis'])}} Nomor : {{strtoupper($jk['dokumen_jaminan'][$jk['jenis']]['nomor_bpkb'])}}</p>
													@if($jk['dokumen_jaminan'][$jk['jenis']]['is_lama']==false)
														@php $flag_j 	= false @endphp
													@endif
												@endforeach
												@foreach($v['jaminan_tanah_bangunan'] as $jtk)
													<p style="margin:5px;">{{strtoupper($jtk['jenis'])}} Nomor : {{strtoupper($jtk['dokumen_jaminan'][$jtk['jenis']]['nomor_sertifikat'])}}</p>
													@if($jk['dokumen_jaminan'][$jk['jenis']]['is_lama']==false)
														@php $flag_j 	= false @endphp
													@endif
												@endforeach
											</td>
											<td>
												{{$v['status_terakhir']['tanggal']}}
											</td>
											<td>
												@if(!$v['is_complete'])
													
												@endif
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
						<div class="tab-pane fade" id="nav-analisa" role="tabpanel" aria-labelledby="nav-analisa-tab">...</div>
						<div class="tab-pane fade" id="nav-putusan" role="tabpanel" aria-labelledby="nav-putusan-tab">...</div>
					</div>
				@endslot
			@endcomponent
		</div>
	</div>
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush

@push('css')
@endpush