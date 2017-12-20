<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" href="#form-putusan" role="tab">
			Putusan Komite Kredit 
		</a>
	</li>
	@if(is_legal_allowed)
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" href="#legalitas-realisasi" role="tab">
			Legalitas Realisasi
		</a>
	</li>
	@endif
</ul>

<!-- Tab panes -->
<div class="tab-content">
	<div class="tab-pane" id="form-putusan" role="tabpanel">
		<div class="clearfix">&nbsp;</div>
		<div class="row text-justify">
			<div class="col-sm-12 text-left">
				<p>Disetujui untuk diberikan fasilitas pinjaman @if($putusan['is_baru']) baru @else perpanjangan @endif :</p>
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				<p>Plafon Pinjaman</p>
			</div>
			<div class="col-sm-8 text-left">
				<p>{{$putusan['plafon_pinjaman']}}</p>
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				<p>Suku Bunga</p>
			</div>
			<div class="col-sm-8 text-left">
				<p>{{$putusan['suku_bunga']}} %</p>
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				<p>Jangka Waktu</p>
			</div>
			<div class="col-sm-8 text-left">
				<p>{{$putusan['jangka_waktu']}}</p>
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				<p>Biaya Tambahan</p>
			</div>
			<div class="col-sm-8 text-left">
				<p>{{$putusan['provisi']}} provisi, {{$putusan['administrasi']}} administrasi,
				{{$putusan['legal']}} legal</p>
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				Jaminan
			</div>
			<div class="col-sm-8 text-left">
				<br/>
				<ol class="row">
					@foreach($survei['collateral'] as $k => $v)
						<li class="col-sm-12" style="padding:15px;">
							{{strtoupper($v['dokumen_survei']['collateral']['jenis'])}}<br/>
							@foreach($v['dokumen_survei']['collateral'][$v['dokumen_survei']['collateral']['jenis']] as $k2 => $v2)
								@if(in_array($k2, ['nomor_sertifikat', 'atas_nama_sertifikat', 'nilai_tanah', 'nilai_bangunan', 'harga_taksasi', 'nomor_bpkb', 'merk', 'tipe', 'atas', 'atas_nama', 'tahun', 'harga_bank']))
									{{str_replace('_',' ',$k2)}} {{str_replace('_',' ',$v2)}} <br/>
								@endif
							@endforeach
						</li>
					@endforeach
				</ol>
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-12 text-left">
				{{strtoupper($putusan['putusan'])}} dengan catatan {{strtoupper($putusan['catatan'])}}
			</div>
		</div>
	</div>
	@if(is_legal_allowed)
		<div class="tab-pane" id="legalitas-realisasi" role="tabpanel">
			<div class="clearfix">&nbsp;</div>
			<div class="row">
				<div class="col">
					{!! Form::open(['url' => route('pengajuan.putusan.update', ['id' => $putusan['pengajuan_id'], 'kantor_aktif_id' => $kantor_aktif_id]), 'method' => 'PATCH']) !!}
					<div class="row">
						<div class="col">
							{!! Form::vText('Tanggal Realisasi', 'tanggal_realisasi', $realisasi['tanggal'], ['class' => 'form-control inline-edit mask-date-time', 'placeholder' => 'dd/mm/yyyy hh:mm'], true) !!}
						</div>
					</div>

					<div class="row">
						<div class="col">
							{!! Form::vSelect('Fotokopi KTP Pemohon', 'checklists[objek][fotokopi_ktp_pemohon]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['objek']['fotokopi_ktp_pemohon'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>

					<div class="row">
						<div class="col">
							{!! Form::vSelect('Fotokopi KTP Keluarga', 'checklists[objek][fotokopi_ktp_keluarga]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['objek']['fotokopi_ktp_keluarga'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>

					<div class="row">
						<div class="col">
							{!! Form::vSelect('Fotokopi KK', 'checklists[objek][fotokopi_kk]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['objek']['fotokopi_kk'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>

					<div class="row">
						<div class="col">
							{!! Form::vSelect('Fotokopi Akta Nikah/Cerai/Pisah Harta', 'checklists[objek][fotokopi_akta_nikah_cerai_pisah_harta]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['objek']['fotokopi_akta_nikah_cerai_pisah_harta'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>

					<div class="row">
						<div class="col">
							{!! Form::vSelect('Fotokopi NPWP/SIUP', 'checklists[objek][fotokopi_npwp_siup]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['objek']['fotokopi_npwp_siup'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>

					<div class="row">
						<div class="col">
							{!! Form::vSelect('BPKB Asli & Fotocopy', 'checklists[objek][bpkb_asli_dan_fotokopi]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['objek']['bpkb_asli_dan_fotokopi'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>

					<div class="row">
						<div class="col">
							{!! Form::vSelect('Fotokopi Faktur & STNK', 'checklists[objek][fotokopi_faktur_dan_stnk]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['objek']['fotokopi_faktur_dan_stnk'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>

					<div class="row">
						<div class="col">
							{!! Form::vSelect('Kwitansi Jual/Beli Kosongan', 'checklists[objek][kwitansi_jual_beli_kosongan]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['objek']['kwitansi_jual_beli_kosongan'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>

					<div class="row">
						<div class="col">
							{!! Form::vSelect('Kwitansi KTP Sesuai BPKB', 'checklists[objek][kwitansi_ktp_sesuai_bpkb]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['objek']['kwitansi_ktp_sesuai_bpkb'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>
					<div class="row">
						<div class="col">
							{!! Form::vSelect('Asuransi Kendaraan', 'checklists[objek][asuransi_kendaraan]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['objek']['asuransi_kendaraan'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>
					<div class="row">
						<div class="col">
							{!! Form::vSelect('Sertifikat Asli & Fotokopi', 'checklists[objek][sertifikat_asli_dan_fotokopi]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['objek']['sertifikat_asli_dan_fotokopi'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>
					<div class="row">
						<div class="col">
							{!! Form::vSelect('AJB', 'checklists[objek][ajb]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['objek']['ajb'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>
					<div class="row">
						<div class="col">
							{!! Form::vSelect('IMB', 'checklists[objek][imb]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['objek']['imb'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>
					<div class="row">
						<div class="col">
							{!! Form::vSelect('PBB Terakhir', 'checklists[objek][pbb_terakhir]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['objek']['pbb_terakhir'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>
					<div class="row">
						<div class="col">
							{!! Form::vSelect('Check Fisik', 'checklists[objek][check_fisik]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['objek']['check_fisik'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>
					<div class="row">
						<div class="col">
							{!! Form::vSelect('Foto Jaminan', 'checklists[objek][foto_jaminan]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['objek']['foto_jaminan'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>

					<div class="row">
						<div class="col">
							{!! Form::vSelect('Permohonan Kredit', 'checklists[pengikat][permohonan_kredit]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['pengikat']['permohonan_kredit'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>

					<div class="row">
						<div class="col">
							{!! Form::vSelect('Survei Report', 'checklists[pengikat][survei_report]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['pengikat']['survei_report'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>

					<div class="row">
						<div class="col">
							{!! Form::vSelect('Persetujuan Komite', 'checklists[pengikat][persetujuan_komite]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['pengikat']['persetujuan_komite'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>

					<div class="row">
						<div class="col">
							{!! Form::vSelect('Perjanjian Kredit', 'checklists[pengikat][perjanjian_kredit]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['pengikat']['perjanjian_kredit'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>

					<div class="row">
						<div class="col">
							{!! Form::vSelect('Pengakuan Hutang', 'checklists[pengikat][pengakuan_hutang]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['pengikat']['pengakuan_hutang'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>
					<div class="row">
						<div class="col">
							{!! Form::vSelect('Pernyataan Analis', 'checklists[pengikat][pernyataan_analis]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['pengikat']['pernyataan_analis'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>
					<div class="row">
						<div class="col">
							{!! Form::vSelect('Penggantian Jaminan', 'checklists[pengikat][penggantian_jaminan]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['pengikat']['penggantian_jaminan'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>
					<div class="row">
						<div class="col">
							{!! Form::vSelect('SKMHT/APHT', 'checklists[pengikat][skmht_apht]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['pengikat']['skmht_apht'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>
					<div class="row">
						<div class="col">
							{!! Form::vSelect('FEO', 'checklists[pengikat][feo]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['pengikat']['feo'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>
					<div class="row">
						<div class="col">
							{!! Form::vSelect('Surat Persetujuan Keluarga', 'checklists[pengikat][surat_persetujuan_keluarga]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['pengikat']['surat_persetujuan_keluarga'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>
					<div class="row">
						<div class="col">
							{!! Form::vSelect('Surat Persetujuan Pemasangan Plang', 'checklists[pengikat][surat_persetujuan_plang]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['pengikat']['surat_persetujuan_plang'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>
					<div class="row">
						<div class="col">
							{!! Form::vSelect('Surat Pernyataan Belum Balik Nama', 'checklists[pengikat][pernyataan_belum_balik_nama]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['pengikat']['pernyataan_belum_balik_nama'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>
					<div class="row">
						<div class="col">
							{!! Form::vSelect('Surat Kuasa Pembebanan FEO', 'checklists[pengikat][kuasa_pembebanan_feo]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['pengikat']['kuasa_pembebanan_feo'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>
					<div class="row">
						<div class="col">
							{!! Form::vSelect('Surat Kuasa Menjual & Menarik Jaminan', 'checklists[pengikat][kuasa_menjual_dan_menarik_jaminan]', ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada', 'cadangkan' => 'Tidak Perlu'], $putusan['checklists']['pengikat']['kuasa_menjual_dan_menarik_jaminan'], ['class' => 'form-control text-info inline-edit'], true) !!}
						</div>
					</div>
				{!! Form::bsSubmit('Simpan', ['class' => 'btn btn-primary float-right mr-3']) !!}
				{!! Form::close() !!}

				</div>
			</div>
		</div>
	@endif
</div>