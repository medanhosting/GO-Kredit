<div class="clearfix">&nbsp;</div>
<div class="row">
	<div class="col-sm-6">
		<h5 class="text-uppercase">Impor Data CSV</h5>
		<p class="text-muted">
			Impor Data CSV mudahkan Anda untuk melakukan proses input data kantor dalam jumlah banyak. Anda hanya perlu mengikuti 3 langkah mudah berikut.
		</p>
		<br/>
		<p class="p-b-md">
			<strong>1. Persiapkan Template</strong><br/><span class="text-muted">
			Tenang, kami sudah menyiapkan template yang siap Anda gunakan dengan mengunduh tautan dibawah ini.<br/> 
			</span>
			<a target="_blank" href="{{ route('download', ['filename' => 'template_kantor.csv']) }}" no-data-pjax>
				<i class="fa fa-download" aria-hidden="true"></i>
				Mulai Unduh Template
			</a>
		</p>
		<p class="p-b-md">
			<strong>2. Isikan Data</strong><br/><span class="text-muted">
			Setelah template ter-unduh, buka dokumen dan isikan data kantor sesuai dengan inputan yang telah tersedia. Setelah Anda selesai mengisikan data, simpan dokumen tersebut.
			</span>
		</p>
		<p class="p-b-md">
			<strong>3. Impor Data</strong><br/><span class="text-muted">
			Pilih dokumen yang akan Anda unggah pada section <strong>Upload File</strong>. Tekan Impor Data, dan tunggu proses hingga selesai.
			</span>
		</p>						

		<div class="clearfix">&nbsp;</div>
		{!! Form::open(['url' => route('manajemen.kantor.batch', ['kantor_aktif_id' => $kantor_aktif['id']]), 'files' => true]) !!}

			<fieldset class="form-group">
				<p class="text-sm">Upload File</p>
				{!! Form::file('kantor') !!}
				{!! Form::submit('Impor Data', ['class' => 'btn btn-primary']) !!}
			</fieldset>

		{!! Form::close() !!}
	</div>
</div>