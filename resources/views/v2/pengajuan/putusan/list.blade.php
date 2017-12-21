<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" href="#form-putusan" role="tab">
			Putusan Komite Kredit 
		</a>
	</li>
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
</div>