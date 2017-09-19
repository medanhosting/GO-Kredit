<div class="row">
	<div class="col">
		<h4 class="pb-4">Overview</h4>
	</div>
</div>
<nav class="nav ">
	<a href="#pinjaman" class="nav-item nav-link " >Pinjaman</a>
	<a href="#jaminan" class="nav-item nav-link" >Jaminan</a>
	<a href="#nasabah" class="nav-item nav-link" >Nasabah</a>
</nav>
<div class="row">
	<div class="col">
		<div class="" >
			{{-- div pinjaman --}}
			<div class="mb-4" id="pinjaman">
				@component ('bootstrap.field_value', ['field' => 'Pokok Pinjaman', 'value' => $permohonan['pokok_pinjaman'] ? $permohonan['pokok_pinjaman'] : '', 'class_row' => 'mt-4']) @endcomponent
				@component ('bootstrap.field_value', ['field' => 'Kemampuan Angsur', 'value' => $permohonan['kemampuan_angsur'] ? $permohonan['kemampuan_angsur'] : '']) @endcomponent
			</div>
			{{-- div jaminan --}}
			<div class="" id="jaminan">
				<div class="row mt-4">
					<div class="col"><h6 class="text-capitalize"><u>Kendaraan</u></h6></div>
				</div>
				<div class="row">
					<div class="col">
						@include ('pengajuan.permohonan.jaminan_kendaraan.components.table')
					</div>
				</div>
				<div class="clearfix">&nbsp;</div>
				<div class="row">
					<div class="col"><h6 class="text-capitalize"><u>Tanah &amp; bangunan</u></h6></div>
				</div>
				<div class="row">
					<div class="col">
						@include ('pengajuan.permohonan.jaminan_tanah_bangunan.components.table')
					</div>
				</div>
			</div>
			<div class="" id="nasabah">
				<div class="row mt-4">
					<div class="col">
						@isset ($permohonan['nasabah'])
							<div class="row">
								<div class="col"><h6 class="text-capitalize"><u>pribadi</u></h6></div>
							</div>
							{{-- NIK --}}
							<div class="row">
								<div class="col-4">
									<p class="text-secondary text-uppercase">nik</p>
								</div>
								<div class="col">
									<p class="text-capitalize">
										{{ $permohonan['nasabah']['nik'] }} 
										@isset ($permohonan['nasabah']['is_ktp'])
											&nbsp;<span class="badge badge-info">E-KTP</span>
										@endisset
									</p>
								</div>
							</div>
							{{-- Nama --}}
							@component ('bootstrap.field_value', ['field' => 'nama', 'value' => $permohonan['nasabah']['nama'] ? $permohonan['nasabah']['nama'] : '']) @endcomponent
							
							{{-- tempat_lahir --}}
							@component ('bootstrap.field_value', ['field' => 'tempat lahir', 'value' => $permohonan['nasabah']['tempat_lahir'] ? $permohonan['nasabah']['tempat_lahir'] : '']) @endcomponent
							
							{{-- tanggal_lahir --}}
							@component ('bootstrap.field_value', ['field' => 'tanggal lahir', 'value' => $permohonan['nasabah']['tanggal_lahir'] ? $permohonan['nasabah']['tanggal_lahir'] : '']) @endcomponent
							
							{{-- jenis_kelamin --}}
							@component ('bootstrap.field_value', ['field' => 'jenis kelamin', 'value' => $permohonan['nasabah']['jenis_kelamin'] ? $permohonan['nasabah']['jenis_kelamin'] : '']) @endcomponent
							
							{{-- status_perkawinan --}}
							@component ('bootstrap.field_value', ['field' => 'status perkawinan', 'value' => $permohonan['nasabah']['status_perkawinan'] ? $permohonan['nasabah']['status_perkawinan'] : '']) @endcomponent
							
							{{-- alamat --}}
							<div class="row">
								<div class="col-4">
									<p class="text-secondary text-capitalize">alamat</p>
								</div>
								<div class="col">
									@isset ($permohonan['nasabah']['alamat']['alamat'])
										<p class="text-capitalize mb-1">{{ $permohonan['nasabah']['alamat']['alamat'] }}</p>
										<p class="text-capitalize mb-1">
											@isset ($permohonan['nasabah']['alamat']['rt'])
												RT {{ $permohonan['nasabah']['alamat']['rt'] }}
											@endisset

											@isset ($permohonan['nasabah']['alamat']['rw'])
												 / RW {{ $permohonan['nasabah']['alamat']['rw'] }}
											@endisset
										</p>
										<p class="text-capitalize mb-1">
											@isset ($permohonan['nasabah']['alamat']['regensi'])
												{{ $permohonan['nasabah']['alamat']['regensi'] }}	
											@endisset

											@isset ($permohonan['nasabah']['alamat']['provinsi'])
												 - {{ $permohonan['nasabah']['alamat']['provinsi'] }}
											@endisset
										</p>
									@endisset

									@empty ($permohonan['nasabah']['alamat']['alamat'])
										<p class="mb-1">Alamat belum diinputkan</p>
									@endempty
								</div>
							</div>

							<div class="clearfix">&nbsp;</div>
							<div class="row">
								<div class="col"><h6 class="text-capitalize"><u>kontak</u></h6></div>
							</div>
							{{-- telepon --}}
							@component ('bootstrap.field_value', ['field' => 'no. telepon', 'value' => $permohonan['nasabah']['telepon'] ? $permohonan['nasabah']['telepon'] : '']) @endcomponent
							
							{{-- nomor_whatsapp --}}
							@component ('bootstrap.field_value', ['field' => 'no. whatsapp', 'value' => $permohonan['nasabah']['nomor_whatsapp'] ? $permohonan['nasabah']['nomor_whatsapp'] : '']) @endcomponent
							
							{{-- email --}}
							@component ('bootstrap.field_value', ['field' => 'email', 'value' => $permohonan['nasabah']['email'] ? $permohonan['nasabah']['email'] : '']) @endcomponent

							<div class="clearfix">&nbsp;</div>
							<div class="row">
								<div class="col"><h6 class="text-capitalize"><u>pekerjaan</u></h6></div>
							</div>

							{{-- jenis pekerjaan --}}
							@component ('bootstrap.field_value', ['field' => 'pekerjaan', 'value' => $permohonan['nasabah']['pekerjaan'] ? $permohonan['nasabah']['pekerjaan'] : '']) @endcomponent

							{{-- penghasilan bersih --}}
							@component ('bootstrap.field_value', ['field' => 'penghasilan bersih', 'value' => $permohonan['nasabah']['penghasilan_bersih'] ? $permohonan['nasabah']['penghasilan_bersih'] : '']) @endcomponent
						@endisset
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@empty($permohonan)
	<div class="row">
		<div class="col">Maaf data nasabah belum diisi</div>
	</div>
@endempty