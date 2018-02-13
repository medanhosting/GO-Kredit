@inject('idr', 'App\Service\UI\IDRTranslater')
@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h1 class='h5 mx-5 px-5 d-flex text-center'>	
				<h1 class="text-center m-0 p-0">Welcome</h1>
				<h4 class="text-center mt-0 pt-0">GOKREDIT - ITERASI KREDIT [BETA-2.0.0]</h4>
			</h1>
			<hr>
		</div>
	</div>
	<div class="clearfix">&nbsp;</div>
	<div class="row">
		<div class="col-3">
			<a href="#" style="text-decoration: none !important; color:inherit">
			@component('bootstrap.card')
				@slot('title') <h4 class='text-center'>{{ number_format($data['total_unit']) }}</h4><hr> @endslot
				@slot('body') <p class='text-center'>TOTAL UNIT TERGABUNG</p> @endslot
			@endcomponent
			</a>
		</div>
		<div class="col-3">
			<a href="#" style="text-decoration: none !important; color:inherit">
			@component('bootstrap.card')
				@slot('title') <h4 class='text-center'>{{ number_format($data['total_karyawan']) }}</h4><hr> @endslot
				@slot('body') <p class='text-center'>TOTAL KARYAWAN UNIT INI</p> @endslot
			@endcomponent
			</a>
		</div>
		<div class="col-3">
			<a href="#" style="text-decoration: none !important; color:inherit">
			@component('bootstrap.card')
				@slot('title') <h4 class='text-center'>{{ number_format($data['total_pengajuan']) }}</h4><hr> @endslot
				@slot('body') <p class='text-center'>TOTAL PENGAJUAN UNIT INI</p> @endslot
			@endcomponent
			</a>
		</div>
		<div class="col-3">
			<a href="#" style="text-decoration: none !important; color:inherit">
			@component('bootstrap.card')
				@slot('title') <h4 class='text-center p-1'>{{ number_format($data['total_kredit']) }}</h4><hr> @endslot
				@slot('body') <p class='text-center'>TOTAL KREDIT UNIT INI</p> @endslot
			@endcomponent
			</a>
		</div>
	</div>
	<div class="clearfix">&nbsp;</div>
	<div class="row">
		<div class="col-4">
			@component('bootstrap.card')
				@slot('title') 
					<h4 class='text-center'>LIST PENGAJUAN UNTUK SURVEI<br><small>[HARI INI]</small></h4> 
				@endslot
				@slot('body') 
					<table class="w-100 p-0 mt-4 table table-hover">
						<tbody>
							@forelse($data['list_survei'] as $k => $v)
								<tr class="row m-0" href='{{ route('pengajuan.show', ['id' => $v['id'], 'kantor_aktif_id' => request()->get('kantor_aktif_id') ] ) }}'>
									<td class="col-auto">
										<h6 class="mb-1"><strong>{{($v['nasabah']['nama'])}}</strong><br/>
										<i class="fa fa-phone"></i>&nbsp;{{ $v['nasabah']['telepon']}}
									</td>
									<td class="text-right col">
										<p class="p-0 mt-1 m-0">{!!implode(' ', $v['nasabah']['alamat'])!!}</p>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="2">-</td>
								</tr>
							@endforelse
						</tbody>
						<tfoot>
							<tr>
								<td colspan="2" class="text-right"><a href="{{route('pengajuan.index', ['current' => 'survei', 'kantor_aktif_id' => request()->get('kantor_aktif_id')])}}"><i class="fa fa-arrow-circle-o-right"></i> more </a></td>
							</tr>
						</tfoot>
					</table>
				@endslot
			@endcomponent
		</div>
		<div class="col-4">
			@component('bootstrap.card')
				@slot('title') 
					<h4 class='text-center'>KREDIT MENUNGGU REALISASI<br><small>[HARI INI]</small></h4> 
				@endslot
				@slot('body') 
					<table class="w-100 p-0 mt-4 table table-hover">
						<tbody>
							@forelse($data['list_realisasi'] as $k => $v)
								<tr class="row m-0" href='{{ route('putusan.show', ['id' => $v['id'], 'kantor_aktif_id' => request()->get('kantor_aktif_id') ] ) }}'>
									<td class="col">
										<h6 class="mb-0"><strong>{{($v['nasabah']['nama'])}}</strong><br/>
										<i class="fa fa-phone"></i>&nbsp;{{ $v['nasabah']['telepon']}}
									</td>
									<td class="text-right col-auto">
										<span class="text-info">{{$v['putusan']['plafon_pinjaman']}}</span>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="2">-</td>
								</tr>
							@endforelse
						</tbody>
						<tfoot>
							<tr>
								<td colspan="2" class="text-right"><a href="{{route('putusan.index', ['current' => 'setuju', 'kantor_aktif_id' => request()->get('kantor_aktif_id')])}}"><i class="fa fa-arrow-circle-o-right"></i> more </a></td>
							</tr>
						</tfoot>
					</table>
				@endslot
			@endcomponent
		</div>
		<div class="col-4">
			@component('bootstrap.card')
				@slot('title') 
					<h4 class='text-center'>ANGSURAN JATUH TEMPO<br><small>[HARI INI]</small></h4> 
				@endslot
				@slot('body') 
					<table class="w-100 p-0 mt-4 table table-hover">
						<tbody>
							@forelse($data['list_angsuran'] as $k => $v)
								<tr class="row m-0" href='{{ route('kredit.show', ['id' => $v['kredit']['id'], 'kantor_aktif_id' => request()->get('kantor_aktif_id'), 'current' => 'angsuran' ] ) }}'>
									<td class="col">
										<h6 class="mb-0"><strong>{{($v['kredit']['nasabah']['nama'])}}</strong><br/>
										<i class="fa fa-phone"></i>&nbsp;{{ $v['kredit']['nasabah']['telepon']}}
									</td>
									<td class="text-right col-auto">
										<span class="text-info">{{$v['jumlah']}}</span>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="2">-</td>
								</tr>
							@endforelse
						</tbody>
						<tfoot>
							<tr>
								<td colspan="2" class="text-right"><a href="{{route('kredit.index', ['kantor_aktif_id' => request()->get('kantor_aktif_id')])}}"><i class="fa fa-arrow-circle-o-right"></i> more </a></td>
							</tr>
						</tfoot>
					</table>
				@endslot
			@endcomponent
		</div>
	</div>
	<div class="clearfix">&nbsp;</div>
	<div class="row">
		<div class="col-4">
			@component('bootstrap.card')
				@slot('title') 
					<h4 class='text-center'>JAMINAN KELUAR BUKAN PELUNASAN<br><small>[HARI INI]</small></h4> 
				@endslot
				@slot('body') 
					<table class="w-100 p-0 mt-4 table table-hover">
						<tbody>
							@forelse($data['list_jaminan_keluar'] as $k => $v)
								<tr class="row m-0" href='{{ route('kredit.show', ['id' => $v['kredit']['id'], 'kantor_aktif_id' => request()->get('kantor_aktif_id'), 'current' => 'jaminan' ] ) }}'>
									<td class="col">
										@if(str_is($v['documents']['jenis'], 'shm'))
											<h6 class="mb-0"><strong>SHM</strong></h6>
											<i>{{$v['documents']['shm']['nomor_sertifikat']}}</i><br/>
											{{implode(', ', $v['documents']['shm']['alamat'])}}
										@elseif(str_is($v['documents']['jenis'], 'shgb'))
											<h6 class="mb-0"><strong>SHGB</strong></h6>
											<i>{{$v['documents']['shgb']['nomor_sertifikat']}}</i><br/>
											{{implode(', ', $v['documents']['shgb']['alamat'])}}
										@else
											<h6 class="mb-0"><strong>BPKB</strong></h6>
											<i>{{$v['documents']['bpkb']['nomor_bpkb']}}</i><br/>
											Kendaraan {{str_replace('_', ' ', $v['documents']['bpkb']['jenis'])}} - {{$v['documents']['bpkb']['merk']}} , {{$v['documents']['bpkb']['tipe']}} ({{$v['documents']['bpkb']['tahun']}})
										@endif
									</td>
									<td class="text-right col-auto">
										{{ $v['description']}}
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="2">-</td>
								</tr>
							@endforelse
						</tbody>
						<tfoot>
							<tr>
								<td colspan="2" class="text-right"><a href="{{route('jaminan.index', ['kantor_aktif_id' => request()->get('kantor_aktif_id')])}}"><i class="fa fa-arrow-circle-o-right"></i> more </a></td>
							</tr>
						</tfoot>
					</table>
				@endslot
			@endcomponent
		</div>
		<div class="col-8">
			@component('bootstrap.card')
				@slot('title') 
					<h4 class='text-center'>TUNGGAKAN KREDIT<br><small>[HARI INI]</small></h4> 
				@endslot
				@slot('body') 
					<table class="table table-bordered">
						<thead>
							<tr class="text-center">
								<th>Nasabah</th>
								<th>Sejak</th>
								<th class="text-right">Total Tunggakan</th>
								<th class="text-right">Sisa Hutang</th>
								<th>Surat Peringatan</th>
							</tr>
						</thead>
						<tbody>
							@forelse($data['list_tunggakan'] as $k => $v)
								<tr class="text-center" href="{{route('kredit.show', ['id' => $v['kredit']['id'], 'kantor_aktif_id' => $kantor_aktif_id, 'current' => 'penagihan'])}}">
									<td class="text-left">
										<p class="mb-0">{{ $v['kredit']['nasabah']['nama'] }}</p>
										<p class="mb-0">{{ $v['kredit']['nasabah']['telepon'] }}</p>
									</td>
									<td>
										{{Carbon\Carbon::createfromformat('d/m/Y H:i', $v['tanggal'])->diffForHumans()}}
									</td>
									<td class="text-right">
										{{ $idr->formatMoneyTo($v['tunggakan']) }}
									</td>
									<td class="text-right">
										{{ $idr->formatMoneyTo($v['sisa_hutang']) }}
									</td>
									<td class="text-left">
										@foreach($v['kredit']['suratperingatan'] as $v0)
											<span class="text-danger">{{ucwords(str_replace('_', ' ', $v0['tag']))}}</span><br>
											<small>{{Carbon\Carbon::createfromformat('d/m/Y H:i', $v0['tanggal'])->diffForHumans()}}</small><br/>
										@endforeach
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="5" class="text-left">
										-
									</td>
								</tr>
							@endforelse
						</tbody>
						<tfoot>
							<tr>
								<td colspan="5" class="text-right">
									<a href="{{route('tunggakan.index', ['kantor_aktif_id' => request()->get('kantor_aktif_id')])}}"><i class="fa fa-arrow-circle-o-right"></i> more </a>
								</td>
							</tr>
						</tfoot>
					</table>
				@endslot
			@endcomponent
		</div>
	</div>
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush