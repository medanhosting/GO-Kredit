<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" data-toggle="tab" href="#form-putusan" role="tab">
			Putusan Komite Kredit 
		</a>
	</li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
	<div class="tab-pane fade show active" id="form-putusan" role="tabpanel">
		<div class="clearfix">&nbsp;</div>
		<div class="row text-justify">
			<div class="col-sm-12 text-left">
				<p>Disetujui untuk diberikan fasilitas pinjaman @if($putusan['is_baru']) baru @else perpanjangan @endif :</p>
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				<p class="text-secondary mb-1">
					Plafon Pinjaman
				</p>
			</div>
			<div class="col-sm-8 text-left">
				<p class="mb-1">
					{{$putusan['plafon_pinjaman']}}
				</p>
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				<p class="text-secondary mb-1">
					Suku Bunga
				</p>
			</div>
			<div class="col-sm-8 text-left">
				<p class="mb-1">
					{{ $putusan['suku_bunga']}} %
				</p>
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				<p class="text-secondary mb-1">
					Jangka Waktu
				</p>
			</div>
			<div class="col-sm-8 text-left">
				<p class="mb-1">
					{{$putusan['jangka_waktu']}} Bulan
				</p>
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				<p class="text-secondary mb-1">
					Biaya Tambahan
				</p>
			</div>
			<div class="col-sm-8 text-left">
				<p class="mb-1">
					{{ $putusan['provisi']}} Provisi, 
					{{$putusan['administrasi']}} Administrasi,
					{{$putusan['legal']}} Legal
				</p>
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-12 text-left">
				<p class="text-secondary mb-1"><strong><u>JAMINAN :</u></strong></p>
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-12">
				@foreach($survei['collateral'] as $k => $v)
					<p class="mt-2 mb-1">{{ $loop->iteration }}. &nbsp;{{ strtoupper($v['dokumen_survei']['collateral']['jenis']) }}</p>
					@foreach ($v['dokumen_survei']['collateral'][$v['dokumen_survei']['collateral']['jenis']] as $k2 => $v2)
						@if (in_array($k2, ['nomor_sertifikat', 'atas_nama_sertifikat', 'nilai_tanah', 'nilai_bangunan', 'harga_taksasi', 'nomor_bpkb', 'merk', 'tipe', 'atas', 'atas_nama', 'tahun', 'harga_bank']))
							<div class="row">
								<div class="col-4 text-right">
									<p class="text-secondary mb-1">
										{{ ucfirst(strtolower(str_replace('_',' ',$k2))) }}
									</p>
								</div>
								<div class="col-8 text-left">
									<p class="mb-1">
										{{ ucfirst(strtolower(str_replace('_',' ',$v2))) }}
									</p>
								</div>
							</div>
						@endif
					@endforeach
				@endforeach
			</div>
		</div>
		<div class="clearfix">&nbsp;</div>
		<div class="clearfix">&nbsp;</div>
		<div class="row text-justify">
			<div class="col-sm-12 text-left">
				<div class="alert alert-info">
					<p class="mb-1">
						<strong>{{ strtoupper($putusan['putusan']) }}</strong> dengan catatan "<strong>{{strtoupper($putusan['catatan']) }}</strong>"
					</p>
				</div>
			</div>
		</div>
	</div>
</div>