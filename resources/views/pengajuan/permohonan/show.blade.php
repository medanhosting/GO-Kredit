@push('main')
	<div class="container bg-white bg-shadow p-4">
		<div class="row">
			<div class="col">
				<h4 class='mb-2 text-style text-secondary'>
					<span class="text-uppercase">{{$title}}</span> 
				</h4>
			</div>
		</div>
		<div class="row ml-0 mr-0">
			<div class="col p-0">
				<ol class="breadcrumb" style="border-radius:0;">
					@foreach($breadcrumb as $k => $v)
						@if ($loop->count - 1 == $k)
							<li class="breadcrumb-item active">{{ ucwords($v['title']) }}</li>
						@else
							<li class="breadcrumb-item"><a href="{{ $v['route'] }}">{{ ucwords($v['title']) }}</a></li>
						@endif
					@endforeach
				</ol>
			</div>
		</div>
		<div class="row">
			<div class="col-3">
				@stack('menu_sidebar')
				<div class="card text-left">
					<div class="card-body">
						<h6 class="card-title">PERMOHONAN KREDIT</h6>

						<br/>
						<div class="progress">
							<div class="progress-bar" role="progressbar" style="width: {{$percentage}}%" aria-valuenow="{{$percentage}}" aria-valuemin="0" aria-valuemax="100">{{$percentage}}%</div>
						</div>
						<hr/>
						@if(!is_null($permohonan['ao']))
						<h7 class="text-secondary">AO</h7>
						<br/>
						<h7>{{$permohonan['ao']['nama']}}</h7>
						<br/>
						@endif
						<hr/>
						
						@if(!is_null($permohonan['dokumen_pelengkap']['tanda_tangan']))
						<h7 class="text-secondary">SPESIMEN TTD</h7>
						<br/>
						<img src="{{$permohonan['dokumen_pelengkap']['tanda_tangan']}}" class="img-fluid" alt="Foto KTP">
						<hr/>
						@endif
						<p>Form Pengajuan</p>
						<a href="{{route('pengajuan.pengajuan.print', ['id' => $permohonan['id'], 'mode' => 'permohonan_kredit', 'kantor_aktif_id' => $kantor_aktif['id']])}}" target="__blank" style="width:100%" class="btn btn-primary btn-sm">
							Print
						</a>
						@if($percentage==100)
						<hr/>
						<p>Data Sudah Lengkap</p>
						<a data-toggle="modal" data-target="#assign-survei" data-action="{{route('pengajuan.permohonan.assign_survei', ['id' => $permohonan['id'], 'kantor_aktif_id' => $kantor_aktif['id'], 'status' => 'permohonan'])}}" class="modal_assign btn btn-primary btn-sm text-white" style="width:100%">Assign Untuk Survei</a>
						@endif

						@if($percentage==100 && $v['nasabah']['is_lama'] && $flag_jam)
						<hr/>
						<p>Nasabah & Jaminan Lama</p>
						<a data-toggle="modal" data-target="#lanjut-analisa" data-action="{{route('pengajuan.pengajuan.assign_analisa', ['id' => $survei['pengajuan_id'], 'kantor_aktif_id' => $kantor_aktif['id'], 'status' => 'permohonan'])}}" class="modal_analisa btn btn-primary btn-sm text-white" style="width:100%">Lanjutkan Analisa</a>
						@endif
					</div>
				</div>
			</div>
			<div class="col">
				<!-- Nav tabs -->
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" data-toggle="tab" href="#kredit" role="tab">
							Kredit @if(!$checker['kredit']) <span class="text-danger">&nbsp;<i class="fa fa-exclamation"></i></span> @endif
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#nasabah" role="tab">
							Nasabah @if(!$checker['nasabah']) <span class="text-danger">&nbsp;<i class="fa fa-exclamation"></i></span> @endif
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#keluarga" role="tab">
							Keluarga @if(!$checker['keluarga']) <span class="text-danger">&nbsp;<i class="fa fa-exclamation"></i></span> @endif
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#jaminan" role="tab">
							Jaminan @if(!$checker['jaminan']) <span class="text-danger">&nbsp;<i class="fa fa-exclamation"></i></span> @endif
						</a>
					</li>
				</ul>

				<!-- Tab panes -->
				<div class="tab-content">
					<div class="tab-pane active" id="kredit" role="tabpanel">
						<div class="clearfix">&nbsp;</div>
						<p class="text-right text-secondary"><i>*klik untuk mengubah data</i></p>
						@if(is_null($permohonan['id']))
						{!! Form::open(['url' => route('pengajuan.permohonan.store', ['id' => $permohonan['id'], 'kantor_aktif_id' => $kantor_aktif_id])]) !!}
						@else
						{!! Form::open(['url' => route('pengajuan.permohonan.update', ['id' => $permohonan['id'], 'kantor_aktif_id' => $kantor_aktif_id]), 'method' => 'PATCH']) !!}
						@endif
						<div class="row">
							<div class="col">
								{!! Form::vText('Tanggal Pengajuan', 'tanggal_pengajuan', $permohonan['status_terakhir']['tanggal'], ['class' => 'form-control mask-date-time inline-edit text-info', 'placeholder' => 'dd/mm/yyyy hh:mm'], true) !!}
							</div>
						</div>
						<div class="row">
							<div class="col">
								{!! Form::vText('Pokok Jaminan', 'pokok_pinjaman', $permohonan['pokok_pinjaman'], ['class' => 'form-control mask-money inline-edit text-info', 'placeholder' => 'pokok pinjaman'], true, 'Min. Rp. 2.500.000') !!}
							</div>
						</div>
						<div class="row">
							<div class="col">
								{!! Form::vText('Kemampuan Angsur', 'kemampuan_angsur', $permohonan['kemampuan_angsur'], ['class' => 'form-control mask-money inline-edit text-info', 'placeholder' => 'kemampuan angsur'], true) !!}
							</div>
						</div>
						{!! Form::bsSubmit('Simpan', ['class' => 'btn btn-primary float-right mr-3']) !!}
						{!! Form::close() !!}
					</div>

					<div class="tab-pane" id="nasabah" role="tabpanel">
						<div class="clearfix">&nbsp;</div>
						<p class="text-right text-secondary"><i>*klik untuk mengubah data</i></p>
						@if(is_null($permohonan['id']))
						{!! Form::open(['url' => route('pengajuan.permohonan.store', ['id' => $permohonan['id'], 'kantor_aktif_id' => $kantor_aktif_id, 'status' => 'permohonan']), 'files' => true]) !!}
						@else
						{!! Form::open(['url' => route('pengajuan.permohonan.update', ['id' => $permohonan['id'], 'kantor_aktif_id' => $kantor_aktif_id, 'status' => 'permohonan']), 'files' => true, 'method' => 'PATCH']) !!}
						@endif
							<div class="row">
								<div class="col-sm-12">
									<h6 class="text-secondary"><strong><u>Profil</u></strong></h6>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label class="mb-1">NIK</label>
										<div class="input-group">
											{!! Form::text('nasabah[nik]', $permohonan['nasabah']['nik'], ['class' => 'nnik form-control inline-edit', 'placeholder' => '35-73-03-148014-0001']) !!}
											<div class="input-group-addon bg-white border-0 invisible">
												<i class="fa fa-circle-o-notch fa-spin fa-fw"></i>
												<span class="hidden-xs">Memeriksa NIK</span>
											</div>
										</div>

										@if ($errors->has($name) && $show_error)
											<div class="invalid-feedback">
												@foreach ($errors->get($name) as $v)
													{{ $v }}<br>
												@endforeach
											</div>
										@endif
									</div>
									{!! Form::bsCheckbox('Nasabah menggunakan e-KTP', 'nasabah[is_ektp]', $permohonan['nasabah']['is_ektp'], ['class' => 'nis_ektp form-check-input inline-edit'], true) !!}
									{!! Form::bsText('Nama', 'nasabah[nama]', $permohonan['nasabah']['nama'], ['class' => 'nnama form-control inline-edit', 'placeholder' => 'Tukimin']) !!}
									{!! Form::bsText('Tempat lahir', 'nasabah[tempat_lahir]', $permohonan['nasabah']['tempat_lahir'], ['class' => 'ntempat_lahir form-control inline-edit', 'placeholder' => 'Malang']) !!}
									{!! Form::bsText('Tanggal lahir', 'nasabah[tanggal_lahir]', $permohonan['nasabah']['tanggal_lahir'], ['class' => 'ntanggal_lahir form-control inline-edit mask-date', 'placeholder' => 'dd/mm/yyyy']) !!}
									{!! Form::bsSelect('Jenis Kelamin', 'nasabah[jenis_kelamin]', ['' => 'pilih', 'laki-laki' => 'Laki-Laki', 'perempuan' => 'perempuan'], $permohonan['nasabah']['jenis_kelamin'], ['class' => 'custom-select njenis_kelamin form-control inline-edit']) !!}
									{!! Form::bsSelect('Status pernikahan', 'nasabah[status_perkawinan]', array_merge(['' => 'pilih'], $status_perkawinan), $permohonan['nasabah']['status_perkawinan'], ['class' => 'custom-select nstatus_perkawinan form-control inline-edit']) !!}
									{!! Form::bsSelect('Pekerjaan', 'nasabah[pekerjaan]', array_merge(['' => 'pilih'], $jenis_pekerjaan), $permohonan['nasabah']['pekerjaan'], ['class' => 'custom-select npekerjaan form-control inline-edit']) !!}
									{!! Form::bsText('Penghasilan Bersih', 'nasabah[penghasilan_bersih]', $permohonan['nasabah']['penghasilan_bersih'], ['class' => 'npenghasilan_bersih form-control inline-edit mask-money', 'placeholder' => 'masukkan penghasilan bersih']) !!}
									<div class="clearfix">&nbsp;</div>

									<h6 class="text-secondary"><strong><u>Kontak</u></strong></h6>
									{!! Form::bsText('No. Telp', 'nasabah[telepon]', $permohonan['nasabah'][telepon], ['class' => 'ntelepon form-control inline-edit mask-no-telepon', 'placeholder' => '0888 3738 4401']) !!}
									{!! Form::bsText('No. Whatsapp', 'nasabah[nomor_whatsapp]', $permohonan['nasabah']['nomor_whatsapp'], ['class' => 'nnomor_whatsapp form-control inline-edit mask-no-handphone', 'placeholder' => '0888 3738 4401']) !!}
									{!! Form::bsText('Email', 'nasabah[email]', $permohonan['nasabah'][email], ['class' => 'nemail form-control inline-edit', 'placeholder' => 'tukimin@gmail.com']) !!}
									<div class="clearfix">&nbsp;</div>

									<h6 class="text-secondary"><strong><u>Alamat</u></strong></h6>
									<div class="row form-group">
									@if(count($permohonan['nasabah']['alamat']))
										@include('templates.alamat.ajax-alamat', ['kecamatan' => $permohonan['nasabah']['alamat']['kecamatan'], 'kota' => $permohonan['nasabah']['alamat']['kota'], 'prefix' => 'nasabah[alamat]', 'inline' => 'inline-edit', 'alamat' => $permohonan['nasabah']['alamat']])
									@else
										@include('templates.alamat.ajax-alamat', ['kecamatan' => $kantor_aktif['alamat']['kecamatan'], 'prefix' => 'nasabah[alamat]', 'inline' => 'inline-edit'])
									@endif
									</div>
								</div>
								<div class="col-sm-6">
									@if(!is_null($permohonan['dokumen_pelengkap']['ktp']))
									<img src="{{$permohonan['dokumen_pelengkap']['ktp']}}" class="img-fluid" alt="Responsive image">
									<br/>
									<label class="mb-1">GANTI FOTO KTP</label><br/>
									<label class="custom-file">
										{!! Form::file('dokumen_pelengkap[ktp]', ['class' => 'custom-file-input']) !!}
										<span class="custom-file-control"></span>
									</label>
									@else
									<img src="https://upload.wikimedia.org/wikipedia/commons/a/ac/No_image_available.svg" class="img-fluid" alt="Responsive image">
									<br/>
									<label class="mb-1">FOTO KTP</label><br/>
									<label class="custom-file">
										{!! Form::file('dokumen_pelengkap[ktp]', ['class' => 'custom-file-input']) !!}
										<span class="custom-file-control"></span>
									</label>
									@endif
								</div>
							</div>
						{!! Form::bsSubmit('Simpan', ['class' => 'btn btn-primary float-right mr-3']) !!}
						{!! Form::close() !!}
					</div>
					<div class="tab-pane" id="keluarga" role="tabpanel">
						<div class="clearfix">&nbsp;</div>
						<p class="text-right text-secondary"><i>*klik untuk mengubah data</i></p>

						@if(!is_null($permohonan['nasabah']['is_lama']) && $permohonan['nasabah']['is_lama'] == true && !count($permohonan['nasabah']['keluarga']))
							<a class="btn btn-primary text-right mb-3 text-white" id="bantuIsiKeluarga">Bantuan Pengisian</a>
						@endif
						
						@if(is_null($permohonan['id']))
						{!! Form::open(['url' => route('pengajuan.permohonan.store', ['id' => $permohonan['id'], 'kantor_aktif_id' => $kantor_aktif_id, 'status' => 'permohonan']), 'files' => true]) !!}
						@else
						{!! Form::open(['url' => route('pengajuan.permohonan.update', ['id' => $permohonan['id'], 'kantor_aktif_id' => $kantor_aktif_id, 'status' => 'permohonan']), 'files' => true, 'method' => 'PATCH']) !!}
						@endif
						<table class="table table-sm table-bordered">
							<thead class="thead-default">
								<tr>
									<th style="border:1px #aaa solid">Hubungan</th>
									<th style="border:1px #aaa solid">NIK</th>
									<th style="border:1px #aaa solid">Nama</th>
									<th style="border:1px #aaa solid">Telepon</th>
									<th style="border:1px #aaa solid" colspan="2">&nbsp;</th>
								</tr>
							</thead>
							<tbody id="formKeluarga">
								@forelse($permohonan['nasabah']['keluarga'] as $k => $v)
									<tr id="clonedKeluarga{{$k+1}}" class="clonedKeluarga">
										<td style="border:1px #aaa solid">
											{!! Form::vSelect(null, 'keluarga['.($k+1).'][hubungan]', ['anak' => 'Anak', 'orang_tua' => 'Orang Tua', 'suami' => 'Suami', 'istri' => 'Istri', 'saudara' => 'Saudara'], $v['hubungan'], ['class' => 'khubungan form-control text-info inline-edit'], true) !!}
										</td>
										<td style="border:1px #aaa solid">
											{!! Form::vText(null, 'keluarga['.($k+1).'][nik]', $v['nik'], ['class' => 'knik form-control text-info inline-edit', 'placeholder' => '35-73-03-148014-0001', 'style' => 'padding:7px;'], true) !!}
										</td>
										<td style="border:1px #aaa solid">
											{!! Form::vText(null, 'keluarga['.($k+1).'][nama]', $v['nama'], ['class' => 'knama form-control text-info inline-edit', 'placeholder' => 'Sukinem', 'style' => 'padding:7px;'], true) !!}
										</td>
										<td style="border:1px #aaa solid">
											{!! Form::vText(null, 'keluarga['.($k+1).'][telepon]', $v['telepon'], ['class' => 'ktelepon form-control text-info inline-edit', 'placeholder' => '0818 3319 4748', 'style' => 'padding:7px;'], true) !!}
										</td>
										<td style="padding-top:12px;border:1px #aaa solid">
											<a class="cloneKeluarga text-info" style="font-size:16px;padding:5px;"><i class="fa fa-copy"></i></a> 
										</td>
										<td style="padding-top:12px;border:1px #aaa solid">
											<a class="removeKeluarga text-danger" style="font-size:16px;padding:5px;"><i class="fa fa-trash"></i></a>
										</td>
									</tr>
								@empty
									<tr id="clonedKeluarga1" class="clonedKeluarga">
										<td style="border:1px #aaa solid">
											{!! Form::vSelect(null, 'keluarga[1][hubungan]', ['anak' => 'Anak', 'orang_tua' => 'Orang Tua', 'suami' => 'Suami', 'istri' => 'Istri', 'saudara' => 'Saudara'], null, ['class' => 'khubungan form-control text-info inline-edit'], true) !!}
										</td>
										<td style="border:1px #aaa solid">
											{!! Form::vText(null, 'keluarga[1][nik]', null, ['class' => 'knik form-control text-info inline-edit', 'placeholder' => '35-73-03-148014-0001', 'style' => 'padding:7px;'], true) !!}
										</td>
										<td style="border:1px #aaa solid">
											{!! Form::vText(null, 'keluarga[1][nama]', null, ['class' => 'knama form-control text-info inline-edit', 'placeholder' => 'Sukinem', 'style' => 'padding:7px;'], true) !!}
										</td>
										<td style="border:1px #aaa solid">
											{!! Form::vText(null, 'keluarga[1][telepon]', null, ['class' => 'ktelepon form-control text-info inline-edit', 'placeholder' => '0818 3319 4748', 'style' => 'padding:7px;'], true) !!}
										</td>
										<td style="padding-top:12px;border:1px #aaa solid">
											<a class="cloneKeluarga text-info" style="font-size:16px;padding:5px;"><i class="fa fa-copy"></i></a> 
										</td>
										<td style="padding-top:12px;border:1px #aaa solid">
											<a class="removeKeluarga text-danger" style="font-size:16px;padding:5px;"><i class="fa fa-trash"></i></a>
										</td>
									</tr>
								@endforelse
							</tbody>
						</table>
						{!! Form::bsSubmit('Simpan', ['class' => 'btn btn-primary float-right mr-3']) !!}
						{!! Form::close() !!}
					</div>
					<div class="tab-pane" id="jaminan" role="tabpanel">
						<div class="clearfix">&nbsp;</div>
						<p class="text-right text-secondary"><i>*klik untuk mengubah data</i></p>
						@if(is_null($permohonan['id']))
						{!! Form::open(['url' => route('pengajuan.permohonan.store', ['id' => $permohonan['id'], 'kantor_aktif_id' => $kantor_aktif_id])]) !!}
						@else
						{!! Form::open(['url' => route('pengajuan.permohonan.update', ['id' => $permohonan['id'], 'kantor_aktif_id' => $kantor_aktif_id]), 'method' => 'PATCH']) !!}
						@endif
						<p class="text-left text-secondary">JAMINAN KENDARAAN</p>
						<table class="table table-sm table-bordered">
							<thead class="thead-default">
								<tr>
									<th style="border:1px #aaa solid; width:80px;" class="text-center">Jenis</th>
									<th style="border:1px #aaa solid;" class="text-center">No. BPKB</th>
									<th style="border:1px #aaa solid;" class="text-center">Detail</th>
									<th style="border:1px #aaa solid;" class="text-center">Tahun Perolehan</th>
									<th style="border:1px #aaa solid;" class="text-center">Harga Jaminan (*)</th>
									<th style="border:1px #aaa solid;" class="text-center">&nbsp;</th>
									<th style="border:1px #aaa solid;" class="text-center">&nbsp;</th>
								</tr>
							</thead> 
							<tbody id="formJaminanKendaraan">
								@forelse ($permohonan['jaminan_kendaraan'] as $kj => $vj)
								<tr id="clonedJaminanKendaraan{{$kj+1}}" class="clonedJaminanKendaraan">
									<td style="border:1px #aaa solid" class="text-center">
										{!! Form::vSelect(null, 'jaminan_kendaraan['.($kj+1).'][jenis]', $jenis_kendaraan, $vj['dokumen_jaminan']['bpkb']['jenis'], ['class' => 'jkjenis form-control text-info inline-edit'], true) !!}
									</td>
									<td style="border:1px #aaa solid" class="text-center tdnomorbpkb">
										{!! Form::vText(null, 'jaminan_kendaraan['.($kj+1).'][nomor_bpkb]', $vj['dokumen_jaminan']['bpkb']['nomor_bpkb'], ['class' => 'jknomorbpkb form-control text-info inline-edit', 'placeholder' => 'F 12345678', 'style' => 'padding:7px;'], true) !!}
									</td>
									<td style="border:1px #aaa solid" class="text-center">
										{!! Form::vText('Merk', 'jaminan_kendaraan['.($kj+1).'][merk]', $vj['dokumen_jaminan']['bpkb']['merk'], ['class' => 'jkmerk form-control text-info inline-edit', 'placeholder' => 'Honda'], true) !!}
										{!! Form::vText('Tipe', 'jaminan_kendaraan['.($kj+1).'][tipe]', $vj['dokumen_jaminan']['bpkb']['tipe'], ['class' => 'jktipe form-control text-info inline-edit', 'placeholder' => 'Sedan'], true) !!}
										{!! Form::vText('Tahun', 'jaminan_kendaraan['.($kj+1).'][tahun]', $vj['dokumen_jaminan']['bpkb']['tahun'], ['class' => 'jktahun form-control text-info inline-edit', 'placeholder' => '2000'], true) !!}
									</td>
									<td style="border:1px #aaa solid" class="text-center">
										{!! Form::vText(null, 'jaminan_kendaraan['.($kj+1).'][tahun_perolehan]', $vj['tahun_perolehan'], ['class' => 'jktahunoleh form-control text-info inline-edit', 'placeholder' => '2000', 'style' => 'padding:7px;'], true) !!}
									</td>
									<td style="border:1px #aaa solid" class="text-center">
										{!! Form::vText(null, 'jaminan_kendaraan['.($kj+1).'][nilai_jaminan]', $vj['nilai_jaminan'], ['class' => 'jknilai form-control text-info inline-edit mask-money', 'placeholder' => 'harga jaminan', 'style' => 'padding:7px;'], true) !!}
									</td>
									<td style="border:1px #aaa solid" class="text-center">
										<a class="cloneJaminanKendaraan text-info" style="font-size:18px;padding:3px;"><i class="fa fa-copy"></i></a> 
									</td>
									<td style="border:1px #aaa solid" class="text-center">
										<a class="removeJaminanKendaraan text-danger" style="font-size:18px;padding:3px;"><i class="fa fa-trash"></i></a>
									</td>
								</tr>
								@empty
									<tr id="clonedJaminanKendaraan1" class="clonedJaminanKendaraan">
										<td style="border:1px #aaa solid" class="text-center">
											{!! Form::vSelect(null, 'jaminan_kendaraan[1][jenis]', $jenis_kendaraan, null, ['class' => 'jkjenis form-control text-info inline-edit'], true) !!}
										</td>
										<td style="border:1px #aaa solid" class="text-center tdnomorbpkb">
											{!! Form::vText(null, 'jaminan_kendaraan[1][nomor_bpkb]', null, ['class' => 'jknomorbpkb form-control text-info inline-edit', 'placeholder' => 'F 12345678', 'style' => 'padding:7px;'], true) !!}
										</td>
										<td style="border:1px #aaa solid" class="text-center">
											{!! Form::vText('Merk', 'jaminan_kendaraan[1][merk]', null, ['class' => 'jkmerk form-control text-info inline-edit', 'placeholder' => 'Honda'], true) !!}
											{!! Form::vText('Tipe', 'jaminan_kendaraan[1][tipe]', null, ['class' => 'jktipe form-control text-info inline-edit', 'placeholder' => 'Sedan'], true) !!}
											{!! Form::vText('Tahun', 'jaminan_kendaraan[1][tahun]', null, ['class' => 'jktahun form-control text-info inline-edit', 'placeholder' => '2000'], true) !!}
										</td>
										<td style="border:1px #aaa solid" class="text-center">
											{!! Form::vText(null, 'jaminan_kendaraan[1][tahun_perolehan]', null, ['class' => 'jktahunoleh form-control text-info inline-edit', 'placeholder' => '2000', 'style' => 'padding:7px;'], true) !!}
										</td>
										<td style="border:1px #aaa solid" class="text-center">
											{!! Form::vText(null, 'jaminan_kendaraan[1][nilai_jaminan]', null, ['class' => 'jknilai form-control text-info inline-edit mask-money', 'placeholder' => 'harga jaminan', 'style' => 'padding:7px;'], true) !!}
										</td>
										<td style="border:1px #aaa solid" class="text-center">
											<a class="cloneJaminanKendaraan text-info" style="font-size:18px;padding:3px;"><i class="fa fa-copy"></i></a> 
										</td>
										<td style="border:1px #aaa solid" class="text-center">
											<a class="removeJaminanKendaraan text-danger" style="font-size:18px;padding:3px;"><i class="fa fa-trash"></i></a>
										</td>
									</tr>
								@endforelse
							</tbody>
							<tfoot>
								<tr>
									<td style="border:1px #aaa solid" colspan="8" class="text-right">
										<small>
											<i class="text-secondary">* menurut nasabah</i>
										</small>
									</td>
								</tr>
							</tfoot>		
						</table>
						<div class="clearfix">&nbsp;</div>
						<p class="text-left text-secondary">JAMINAN TANAH BANGUNAN</p>
						<table class="table table-sm table-bordered">
							<thead class="thead-default">
								<tr>
									<th style="border:1px #aaa solid; width:80px;" class="text-center">Jenis</th>
									<th style="border:1px #aaa solid; width:360px;" class="text-center">Detail</th>
									<th style="border:1px #aaa solid;" class="text-center">Tahun Perolehan</th>
									<th style="border:1px #aaa solid;" class="text-center">Harga Jaminan (*)</th>
									<th style="border:1px #aaa solid;" class="text-center">&nbsp;</th>
									<th style="border:1px #aaa solid;" class="text-center">&nbsp;</th>
								</tr>
							</thead> 
							<tbody id="formJaminanTB">
								@forelse ($permohonan['jaminan_tanah_bangunan'] as $ktb => $vtb)
								<tr id="clonedJaminanTB{{$ktb+1}}" class="clonedJaminanTB">
									<td style="border:1px #aaa solid;padding:15px 25px;" class="text-center">
										{!! Form::vSelect(null, 'jaminan_tanah_bangunan['.($ktb+1).'][jenis]', $jenis_sertifikat, $vtb['jenis'], ['class' => 'jtbjenis form-control text-info inline-edit', 'style' => 'width:65px;'], true) !!}
									</td>
									<td style="border:1px #aaa solid" class="text-center">
										{!! Form::vText('No. Sertifikat', 'jaminan_tanah_bangunan['.($ktb+1).'][nomor_sertifikat]', $vtb['dokumen_jaminan'][$vtb['jenis']]['nomor_sertifikat'], ['class' => 'jtbnomorsertifikat form-control text-info inline-edit', 'placeholder' => '12-27-98-36-3-54502'], true) !!}
										
										{!! Form::vSelect('Tipe', 'jaminan_tanah_bangunan['.($ktb+1).'][tipe]', $tipe_sertifikat, $vtb['dokumen_jaminan'][$vtb['jenis']]['tipe'], ['class' => 'jtbtipe form-control text-info inline-edit', 'style' => 'padding:0px;'], true) !!}

										<div class="maber" style="@if($vtb['jenis']=='shm') display: none; @endif">
										{!! Form::vText('Berlaku Hingga', 'jaminan_tanah_bangunan['.($ktb+1).'][masa_berlaku_sertifikat]', $vtb['dokumen_jaminan'][$vtb['jenis']]['masa_berlaku_sertifikat'], ['class' => 'jtbmaber mask-year form-control text-info inline-edit', 'placeholder' => '2000'], true) !!}
										</div>
										
										{!! Form::vText('L. Tanah', 'jaminan_tanah_bangunan['.($ktb+1).'][luas_tanah]', $vtb['dokumen_jaminan'][$vtb['jenis']]['luas_tanah'], ['class' => 'jtbltanah form-control text-info inline-edit', 'placeholder' => '36'], true) !!}
									
										<div class="laban" style="@if($vtb['dokumen_jaminan'][$vtb['jenis']]['tipe']=='tanah') display: none; @endif">
										{!! Form::vText('L. Bangunan', 'jaminan_tanah_bangunan['.($ktb+1).'][luas_bangunan]', $vtb['dokumen_jaminan'][$vtb['jenis']]['luas_bangunan'], ['class' => 'jtblbangunan form-control text-info inline-edit', 'placeholder' => '24'], true) !!}
										</div>

										@include('templates.alamat.v-ajax-alamat', ['kecamatan' => $vtb['dokumen_jaminan'][$vtb['jenis']]['alamat']['kecamatan'], 'kota' => $vtb['dokumen_jaminan'][$vtb['jenis']]['alamat']['kota'], 'prefix' => 'jaminan_tanah_bangunan['.($ktb+1).'][alamat]', 'alamat' => $vtb['dokumen_jaminan'][$vtb['jenis']]['alamat'], 'class' => 'jtbalamat'])
									</td>
									<td style="border:1px #aaa solid;padding:15px;" class="text-center">
										{!! Form::vText(null, 'jaminan_tanah_bangunan['.($ktb+1).'][tahun_perolehan]', $vtb['tahun_perolehan'], ['class' => 'jtbtahunoleh form-control text-info inline-edit', 'placeholder' => '2000', 'style' => 'padding:7px;'], true) !!}
									</td>
									<td style="border:1px #aaa solid;padding:15px;" class="text-center">
										{!! Form::vText(null, 'jaminan_tanah_bangunan['.($ktb+1).'][nilai_jaminan]', $vtb['nilai_jaminan'], ['class' => 'jtbnilai form-control text-info inline-edit mask-money', 'placeholder' => 'harga jaminan', 'style' => 'padding:7px;'], true) !!}
									</td>
									<td style="border:1px #aaa solid" class="text-center">
										<a class="cloneJaminanTB text-info" style="font-size:18px;padding:3px;"><i class="fa fa-copy"></i></a> 
									</td>
									<td style="border:1px #aaa solid" class="text-center">
										<a class="removeJaminanTB text-danger" style="font-size:18px;padding:3px;"><i class="fa fa-trash"></i></a>
									</td>
								</tr>
								@empty
								<tr id="clonedJaminanTB1" class="clonedJaminanTB">
									<td style="border:1px #aaa solid;padding:15px 25px;" class="text-center">
										{!! Form::vSelect(null, 'jaminan_tanah_bangunan['.($ktb+1).'][jenis]', $jenis_sertifikat, null, ['class' => 'jtbjenis form-control text-info inline-edit', 'style' => 'width:65px;'], true) !!}
									</td>
									<td style="border:1px #aaa solid" class="text-center">
										{!! Form::vText('No. Sertifikat', 'jaminan_tanah_bangunan['.($ktb+1).'][nomor_sertifikat]', null, ['class' => 'jtbnomorsertifikat form-control text-info inline-edit', 'placeholder' => '12-27-98-36-3-54502'], true) !!}
										
										{!! Form::vSelect('Tipe', 'jaminan_tanah_bangunan['.($ktb+1).'][tipe]', $tipe_sertifikat, null, ['class' => 'jtbtipe form-control text-info inline-edit', 'style' => 'padding:0px;'], true) !!}

										<div class="maber" style="@if($vtb['jenis']=='shm') display: none; @endif">
										{!! Form::vText('Berlaku Hingga', 'jaminan_tanah_bangunan['.($ktb+1).'][masa_berlaku_sertifikat]', null, ['class' => 'jtbmaber mask-year form-control text-info inline-edit', 'placeholder' => '2000'], true) !!}
										</div>
										
										{!! Form::vText('L. Tanah', 'jaminan_tanah_bangunan['.($ktb+1).'][luas_tanah]', null, ['class' => 'jtbltanah form-control text-info inline-edit', 'placeholder' => '36'], true) !!}
									
										<div class="laban" style="display: none;">
										{!! Form::vText('L. Bangunan', 'jaminan_tanah_bangunan['.($ktb+1).'][luas_bangunan]', null, ['class' => 'jtblbangunan form-control text-info inline-edit', 'placeholder' => '24'], true) !!}
										</div>

										@include('templates.alamat.v-ajax-alamat', ['kecamatan' => $kantor_aktif['alamat']['kecamatan'], 'kota' => $kantor_aktif['alamat']['kota'], 'prefix' => 'jaminan_tanah_bangunan['.($ktb+1).'][alamat]', 'alamat' => $kantor_aktif['alamat'], 'class' => 'jtbalamat'])
									</td>
									<td style="border:1px #aaa solid;padding:15px;" class="text-center">
										{!! Form::vText(null, 'jaminan_tanah_bangunan['.($ktb+1).'][tahun_perolehan]', null, ['class' => 'jtbtahunoleh form-control text-info inline-edit', 'placeholder' => '2000', 'style' => 'padding:7px;'], true) !!}
									</td>
									<td style="border:1px #aaa solid;padding:15px;" class="text-center">
										{!! Form::vText(null, 'jaminan_tanah_bangunan['.($ktb+1).'][nilai_jaminan]', null, ['class' => 'jtbnilai form-control text-info inline-edit mask-money', 'placeholder' => 'harga jaminan', 'style' => 'padding:7px;'], true) !!}
									</td>
									<td style="border:1px #aaa solid" class="text-center">
										<a class="cloneJaminanTB text-info" style="font-size:18px;padding:3px;"><i class="fa fa-copy"></i></a> 
									</td>
									<td style="border:1px #aaa solid" class="text-center">
										<a class="removeJaminanTB text-danger" style="font-size:18px;padding:3px;"><i class="fa fa-trash"></i></a>
									</td>
								</tr>
								@endforelse
							</tbody>
							<tfoot>
								<tr>
									<td style="border:1px #aaa solid" colspan="8" class="text-right">
										<small>
											<i class="text-secondary">* menurut nasabah</i>
										</small>
									</td>
								</tr>
							</tfoot>		
						</table>
						{!! Form::bsSubmit('Simpan', ['class' => 'btn btn-primary float-right mr-3']) !!}
						{!! Form::close() !!}
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix">&nbsp;</div>
	</div>

	@component ('bootstrap.modal', ['id' => 'assign-survei', 'form' => true, 'method' => 'post', 'url' => route('pengajuan.permohonan.assign_survei', ['kantor_aktif_id' => $kantor_aktif['id'], 'status' => 'permohonan'])])
		@slot ('title')
			Assign Survei
		@endslot

		@slot ('body')
			<p>Untuk assign survei, harap melengkapi data berikut!</p>

			<div class="form-group">
				{!! Form::label('', 'SURVEYOR', ['class' => 'text-uppercase mb-1']) !!}
				<select class="ajax-karyawan custom-select form-control required" name="surveyor[nip][]" multiple="multiple" style="width:100%">
					<option value="">Pilih</option>
				</select>
			</div>
			<!-- {!! Form::bsText('Tanggal', 'tanggal', null, ['class' => 'mask-date form-control', 'placeholder' => 'dd/mm/yyyy']) !!} -->
			<!-- {!! Form::bsTextarea('catatan', 'catatan', null, ['class' => 'form-control', 'placeholder' => 'catatan', 'style' => 'resize:none;', 'rows' => 5]) !!} -->
			{!! Form::bsPassword('password', 'password', ['placeholder' => 'Password']) !!}
		@endslot

		@slot ('footer')
			<a href="#" data-dismiss="modal" class="btn btn-link text-secondary">Batal</a>
			{!! Form::bsSubmit('Simpan', ['class' => 'btn btn-primary']) !!}
		@endslot
	@endcomponent

	@include('pengajuan.ajax.modal_analisa')
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush

@push('css')
	<style type="text/css">
	
		.clonedKeluarga { padding: 10px; border-radius: 5px; margin-bottom: 10px; }

		.clonedKeluarga div { margin: 5px; }

		.clonedJaminanKendaraan { padding: 10px; border-radius: 5px; margin-bottom: 10px; }

		.clonedJaminanKendaraan div { margin: 5px; }
	</style>
@endpush
@push ('js')
		<script>

		///CLONE FORM KELUARGA///
		var regex = /^(.+?)(\d+)$/i;
		var cloneIndexKeluarga = $(".clonedKeluarga").length;

		function cloneKeluarga(){
			cloneIndexKeluarga++;

			$(this).parents(".clonedKeluarga").clone()
				.appendTo("#formKeluarga")
				.attr("id", "clonedKeluarga" +  cloneIndexKeluarga)
				.find("*")
				.each(function() {
					var id = this.id || "";
					var match = id.match(regex) || [];
					if (match.length == 3) {
						this.id = match[1] + (cloneIndexKeluarga);
					}
				})
				.on('click', 'a.cloneKeluarga', cloneKeluarga)
				.on('click', 'a.removeKeluarga', removeKeluarga);

			$("#clonedKeluarga"+cloneIndexKeluarga).find('.khubungan').attr('name', 'keluarga['+cloneIndexKeluarga+'][hubungan]');
			$("#clonedKeluarga"+cloneIndexKeluarga).find('.knik').attr('name', 'keluarga['+cloneIndexKeluarga+'][nik]');
			$("#clonedKeluarga"+cloneIndexKeluarga).find('.knama').attr('name', 'keluarga['+cloneIndexKeluarga+'][nama]');
			$("#clonedKeluarga"+cloneIndexKeluarga).find('.ktelepon').attr('name', 'keluarga['+cloneIndexKeluarga+'][telepon]');
		}

		function removeKeluarga(){
			$(this).parents(".clonedKeluarga").remove();
		}
		$("a.cloneKeluarga").on("click", cloneKeluarga);

		$("a.removeKeluarga").on("click", removeKeluarga);


		///CLONE FORM JAMINAN KENDARAAN///
		var cloneIndexJaminanKendaraan = $(".clonedJaminanKendaraan").length;

		function cloneJaminanKendaraan(){
			cloneIndexJaminanKendaraan++;

			$(this).parents(".clonedJaminanKendaraan").clone()
				.appendTo("#formJaminanKendaraan")
				.attr("id", "clonedJaminanKendaraan" +  cloneIndexJaminanKendaraan)
				.find("*")
				.each(function() {
					var id = this.id || "";
					var match = id.match(regex) || [];
					if (match.length == 3) {
						this.id = match[1] + (cloneIndexJaminanKendaraan);
					}
				})
				.on('click', 'a.cloneJaminanKendaraan', cloneJaminanKendaraan)
				.on('click', 'a.removeJaminanKendaraan', removeJaminanKendaraan)
				.on('change', 'input.jknomorbpkb', autoFillJaminanKendaraan);

				$("#clonedJaminanKendaraan"+cloneIndexJaminanKendaraan).find('.jknomorbpkb').attr('name', 'jaminan_kendaraan['+cloneIndexJaminanKendaraan+'][nomor_bpkb]');
				$("#clonedJaminanKendaraan"+cloneIndexJaminanKendaraan).find('.jkjenis').attr('name', 'jaminan_kendaraan['+cloneIndexJaminanKendaraan+'][jenis]');
				$("#clonedJaminanKendaraan"+cloneIndexJaminanKendaraan).find('.jkmerk').attr('name', 'jaminan_kendaraan['+cloneIndexJaminanKendaraan+'][merk]');
				$("#clonedJaminanKendaraan"+cloneIndexJaminanKendaraan).find('.jktipe').attr('name', 'jaminan_kendaraan['+cloneIndexJaminanKendaraan+'][tipe]');
				$("#clonedJaminanKendaraan"+cloneIndexJaminanKendaraan).find('.jktahun').attr('name', 'jaminan_kendaraan['+cloneIndexJaminanKendaraan+'][tahun]');
				$("#clonedJaminanKendaraan"+cloneIndexJaminanKendaraan).find('.jktahunoleh').attr('name', 'jaminan_kendaraan['+cloneIndexJaminanKendaraan+'][tahun_perolehan]');
				$("#clonedJaminanKendaraan"+cloneIndexJaminanKendaraan).find('.jknilai').attr('name', 'jaminan_kendaraan['+cloneIndexJaminanKendaraan+'][nilai_jaminan]');
		}

		function removeJaminanKendaraan(){
			$(this).parents(".clonedJaminanKendaraan").remove();
		}
		$("a.cloneJaminanKendaraan").on("click", cloneJaminanKendaraan);

		$("a.removeJaminanKendaraan").on("click", removeJaminanKendaraan);

		$("input.jknomorbpkb").on("change", autoFillJaminanKendaraan);
	
		///AUTO FILL JAMINAN KENDARAAN BERDASARKAN NOMOR BPKB///
		function autoFillJaminanKendaraan(){
			var tr = $(this).closest('tr');
			$.ajax({
				url: "{{route('log.bpkb')}}",
			   	data: {
					q: $(this).val()
				},
			   	success: function(data) {
			   		if(Object.keys(data).length > 1) {
						tr.find('.jkjenis').val(data.jenis);
						tr.find('.jkmerk').val(data.merk);
						tr.find('.jktipe').val(data.tipe);
						tr.find('.jktahun').val(data.tahun);
						tr.find('.jktahunoleh').val(data.tahun_perolehan);
						tr.find('.jknilai').val("Rp 0");
						// tr.find('.jknilai').val(data.nilai);
			   		}
			   },
			   type: 'GET'
			});
		}

		///CLONE FORM JAMINAN TANAH BANGUNAN///
		var cloneIndexJaminanTB = $(".clonedJaminanTB").length;

		function cloneJaminanTB(){
			cloneIndexJaminanTB++;
			$(this).parents(".clonedJaminanTB").clone()
				.appendTo("#formJaminanTB")
				.attr("id", "clonedJaminanTB" +  cloneIndexJaminanTB)
				.find("*")
				.each(function() {
					var id = this.id || "";
					var match = id.match(regex) || [];
					if (match.length == 3) {
						this.id = match[1] + (cloneIndexJaminanTB);
					}
				})
				.on('click', 'a.cloneJaminanTB', cloneJaminanTB)
				.on('click', 'a.removeJaminanTB', removeJaminanTB)
				.on('change', 'input.jtbnomorsertifikat', autoFillJaminanTB)
				.on('change', 'select.jtbjenis', checkJenisSTF)
				.on('change', 'select.jtbtipe', checkTipeSTF);


				$("#clonedJaminanTB"+cloneIndexJaminanTB).find('.jtbnomorsertifikat').attr('name', 'jaminan_tanah_bangunan['+cloneIndexJaminanTB+'][nomor_sertifikat]');
				$("#clonedJaminanTB"+cloneIndexJaminanTB).find('.jtbjenis').attr('name', 'jaminan_tanah_bangunan['+cloneIndexJaminanTB+'][jenis]');
				$("#clonedJaminanTB"+cloneIndexJaminanTB).find('.jtbtipe').attr('name', 'jaminan_tanah_bangunan['+cloneIndexJaminanTB+'][tipe]');
				$("#clonedJaminanTB"+cloneIndexJaminanTB).find('.jtbmaber').attr('name', 'jaminan_tanah_bangunan['+cloneIndexJaminanTB+'][masa_berlaku_sertifikat]');
				$("#clonedJaminanTB"+cloneIndexJaminanTB).find('.jtbltanah').attr('name', 'jaminan_tanah_bangunan['+cloneIndexJaminanTB+'][luas_tanah]');
				$("#clonedJaminanTB"+cloneIndexJaminanTB).find('.jtblbangunan').attr('name', 'jaminan_tanah_bangunan['+cloneIndexJaminanTB+'][luas_bangunan]');

				$("#clonedJaminanTB"+cloneIndexJaminanTB).find('.jtbalamat.alamat').attr('name', 'jaminan_tanah_bangunan['+cloneIndexJaminanTB+'][alamat][alamat]');
				$("#clonedJaminanTB"+cloneIndexJaminanTB).find('.jtbalamat.rt').attr('name', 'jaminan_tanah_bangunan['+cloneIndexJaminanTB+'][alamat][rt]');
				$("#clonedJaminanTB"+cloneIndexJaminanTB).find('.jtbalamat.rw').attr('name', 'jaminan_tanah_bangunan['+cloneIndexJaminanTB+'][alamat][rw]');
				$("#clonedJaminanTB"+cloneIndexJaminanTB).find('.jtbalamat.kelurahan').attr('name', 'jaminan_tanah_bangunan['+cloneIndexJaminanTB+'][alamat][kelurahan]');
				$("#clonedJaminanTB"+cloneIndexJaminanTB).find('.jtbalamat.kecamatan').attr('name', 'jaminan_tanah_bangunan['+cloneIndexJaminanTB+'][alamat][kecamatan]');
				$("#clonedJaminanTB"+cloneIndexJaminanTB).find('.jtbalamat.kota').attr('name', 'jaminan_tanah_bangunan['+cloneIndexJaminanTB+'][alamat][kota]');

				$("#clonedJaminanTB"+cloneIndexJaminanTB).find('.jktahunoleh').attr('name', 'jaminan_tanah_bangunan['+cloneIndexJaminanTB+'][tahun_perolehan]');
				$("#clonedJaminanTB"+cloneIndexJaminanTB).find('.jknilai').attr('name', 'jaminan_tanah_bangunan['+cloneIndexJaminanTB+'][nilai_jaminan]');
		}

		function removeJaminanTB(){
			$(this).parents(".clonedJaminanTB").remove();
		}
		$("a.cloneJaminanTB").on("click", cloneJaminanTB);

		$("a.removeJaminanTB").on("click", removeJaminanTB);

		$("input.jtbnomorsertifikat").on("change", autoFillJaminanTB);

		$("select.jtbjenis").on("change", checkJenisSTF);

		$("select.jtbtipe").on("change", checkTipeSTF);
	
		///AUTO FILL JAMINAN TB BERDASARKAN NOMOR SERTIFIKAT///
		function autoFillJaminanTB(){
			var tr = $(this).closest('tr');
			$.ajax({
				url: "{{route('log.sertifikat')}}",
			   	data: {
					q: $(this).val()
				},
			   	success: function(data) {
			   		if(Object.keys(data).length > 1) {
						tr.find('.jtbjenis').val(data.jenis);
						tr.find('.jtbtipe').val(data.tipe);
						tr.find('.jtbmaber').val(data.masa_berlaku_sertifikat);
						tr.find('.jtbltanah').val(data.luas_tanah);
						tr.find('.jtblbangunan').val(data.luas_bangunan);
						tr.find('.jtbalamat.alamat').val(data.alamat.alamat);
						tr.find('.jtbalamat.rt').val(data.alamat.rt);
						tr.find('.jtbalamat.rw').val(data.alamat.rw);
						tr.find('.jtbalamat.kelurahan').val(data.alamat.kelurahan);
						tr.find('.jtbalamat.kecamatan').val(data.alamat.kecamatan);
						tr.find('.jtbalamat.kota').val(data.alamat.kota);
						tr.find('.jtbtahunoleh').val(data.tahun_perolehan);
						tr.find('.jtbnilai').val("Rp 0");
						// tr.find('.jknilai').val(data.nilai);
					}
			   },
			   type: 'GET'
			});
		}

		///HIDE MASA BERLAKU JAMINAN TB JIKA JENIS SHM///
		function checkJenisSTF(){
			var tr 	= $(this).closest('tr');
			if($(this).val()=='shm'){
				tr.find('.maber').hide();
			}else{
				tr.find('.maber').show();
			}
		}

		///HIDE LUAS BANGUNAN JAMINAN TB JIKA TIPE TANAH///
		function checkTipeSTF(){
			var tr 	= $(this).closest('tr');
			if($(this).val()=='tanah'){
				tr.find('.laban').hide();
			}else{
				tr.find('.laban').show();
			}
		}

		///AUTO FILL NASABAH///
		$("input.nnik").on("change", autoFillNasabah);

		function autoFillNasabah(){
			var form = $(this).closest('form');
			$.ajax({
				url: "{{route('log.nasabah')}}",
			   	data: {
					q: $(this).val()
				},
			   	success: function(data) {
			   		if(Object.keys(data).length > 1) {
						form.find('.nis_ektp').val(data.is_ektp);
						form.find('.nnama').val(data.nama);
						form.find('.ntempat_lahir').val(data.tempat_lahir);
						form.find('.ntanggal_lahir').val(data.tanggal_lahir);
						form.find('.njenis_kelamin').val(data.jenis_kelamin);
						form.find('.nstatus_perkawinan').val(data.status_perkawinan);
						form.find('.npekerjaan').val(data.pekerjaan);
						form.find('.npenghasilan_bersih').val(data.penghasilan_bersih);
						form.find('.ntelepon').val(data.telepon);
						form.find('.nnomor_whatsapp').val(data.nomor_whatsapp);
						form.find('.nemail').val(data.email);
						form.find('.alamat').val(data.alamat.alamat);
						form.find('.rt').val(data.alamat.rt);
						form.find('.rw').val(data.alamat.rw);
						form.find('.kelurahan').val(data.alamat.kelurahan);
						form.find('.kecamatan').val(data.alamat.kecamatan);
						form.find('.kota').val(data.alamat.kota);
					}
			   },
			   type: 'GET'
			});
		}

		///AUTO FILL KELUARGA///
		$("#bantuIsiKeluarga").on("click", autoFillKeluarga);

		function autoFillKeluarga(){
			var form = $('#clonedKeluarga1');
			$.ajax({
				url: "{{route('log.nasabah')}}",
			   	data: {
					q: "{{$permohonan['nasabah']['nik']}}"
				},
			   	error: function(data) {
					alert('Maaf, tidak ada data keluarga tersimpan');
			   },
			   	success: function(data) {
			   		var count = Object.keys(data.keluarga).length;
			   		var isi = 0;
			   		$.each(data.keluarga, function (key, keluarga) {
			   			isi++;
   						$("#clonedKeluarga"+cloneIndexKeluarga).find('.khubungan').val(keluarga.hubungan);
						$("#clonedKeluarga"+cloneIndexKeluarga).find('.knik').val(keluarga.nik);
						$("#clonedKeluarga"+cloneIndexKeluarga).find('.knama').val(keluarga.nama);
						$("#clonedKeluarga"+cloneIndexKeluarga).find('.ktelepon').val(keluarga.telepon);

						if(isi<count) {
							$("#clonedKeluarga"+cloneIndexKeluarga).find('.cloneKeluarga').trigger('click');
						}
					});
			   },
			   type: 'GET'
			});

			$(this).hide();
		}

		//ASSIGN SURVEYOR
		$(".ajax-karyawan").select2({
			ajax: {
				url: "{{route('manajemen.karyawan.ajax')}}",
				data: function (params) {
						return {
							q: params.term, // search term
							kantor_aktif_id: "{{$kantor_aktif['id']}}", // search term
							scope: 'survei'
						};
					},
				processResults: function (data, params) {
					return {
						results:  $.map(data, function (karyawan) {
							return {
								text: karyawan.orang.nama,
								id: karyawan.orang.nip
							}
						})
					};
				},
			}
		});
		
		//MODAL PARSE DATA ATTRIBUTE//
		$("a.modal_assign").on("click", parsingDataAttributeModalAssign);

		function parsingDataAttributeModalAssign(){
			$('#assign-survei').find('form').attr('action', $(this).attr("data-action"));
		}
	</script>
@endpush