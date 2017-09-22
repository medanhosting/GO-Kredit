@push('main')
	<div class="container bg-white bg-shadow p-4">
		<div class="row">
			<div class="col">
				<h4 class='mb-4 text-style text-uppercase text-secondary'>
					Daftar Permohonan Kredit
				</h4>
				<a href="{{ route('pengajuan.permohonan.create') }}" class="btn btn-primary text-capitalize text-style mb-2">tambah baru</a>
			  	<table class="table table-responsive table-bordered">
			  		<thead class="thead-default">
			  			<tr>
			  				<th>#</th>
			  				<th>No. Pengajuan</th>
			  				<th>Tgl Pengajuan</th>
			  				<th>Jummlah Pinjaman</th>
			  				<th>Nasabah</th>
			  				<th></th>
			  			</tr>
			  		</thead>
			  		<tbody>
			  			<tr>
			  				<td rowspan="2">1</td>
			  				<td>000000</td>
			  				<td>23/09/2017</td>
			  				<td>20.000.000</td>
			  				<td>Suhento Mommi</td>
			  				<td rowspan="2"><a href="#" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#delete">Action</a></td>
			  			</tr>
			  			<tr>
			  				<td colspan="4">
			  					<p>Jaminan :</p>
		  						<p class="text-secondary text-capitalize mb-1">kendaraan</p>
			  					<table class="table table-sm bg-white no-border">
			  						{{-- <thead>
			  							<tr>
			  								<th>#</th>
			  								<th>Jenis</th>
			  								<th>No. BPKB</th>
			  								<th>Merk</th>
			  								<th>Tipe</th>
			  								<th>Tahun</th>
			  								<th>Harga Jaminan Customer</th>
			  							</tr>
			  						</thead> --}}
			  						<tbody>
				  						<tr>
				  							<td class="text-center">1.</td>
				  							<td>Roda 4</td>
				  							<td>D-8903249</td>
				  							<td>Mitsubishi</td>
				  							<td>L300</td>
				  							<td class="text-center">2009</td>
				  							<td class="text-right">Rp. 98.000.000</td>
				  						</tr>
				  						<tr>
				  							<td class="text-center">2.</td>
				  							<td>Roda 2</td>
				  							<td>D-8903249</td>
				  							<td>Honda</td>
				  							<td>Beat Sporty</td>
				  							<td class="text-center">2009</td>
				  							<td class="text-right">Rp. 98.000.000</td>
				  						</tr>
				  					</tbody>
			  					</table>

			  					<p class="text-secondary text-capitalize mb-1">tanah &amp; bangunan</p>
			  					<table class="table table-sm bg-white no-border">
			  						{{-- <thead>
			  							<tr>
			  								<th>#</th>
			  								<th>Jenis</th>
			  								<th>No. BPKB</th>
			  								<th>Merk</th>
			  								<th>Tipe</th>
			  								<th>Tahun</th>
			  								<th>Harga Jaminan Customer</th>
			  							</tr>
			  						</thead> --}}
			  						<tbody>
				  						<tr>
				  							<td class="text-center">1.</td>
				  							<td>Tanah &amp; Bangunan</td>
				  							<td>HGB (2020)</td>
				  							<td>98804</td>
				  							<td>60 M<sup>2</sup> / 120 M<sup>2</sup></td>
				  							<td class="text-right">Rp. 98.000.000</td>
				  						</tr>
				  						<tr>
				  							<td class="text-center">2.</td>
				  							<td>Tanah &amp; Bangunan</td>
				  							<td>SHM</td>
				  							<td>98804</td>
				  							<td>36 M<sup>2</sup> / 60 M<sup>2</sup></td>
				  							<td class="text-right">Rp. 98.000.000</td>
				  						</tr>
				  						<tr>
				  							<td class="text-center">3.</td>
				  							<td>Tanah</td>
				  							<td>SHM</td>
				  							<td>98804</td>
				  							<td>60 M<sup>2</sup></td>
				  							<td class="text-right">Rp. 98.000.000</td>
				  						</tr>
				  					</tbody>
			  					</table>
		  						{{-- <ol class="pl-3">
		  							<li class="text-capitalize">(roda 4) mitsubishi colt l300 th 2010 dengan harga Rp. 100.000.000</li>
		  							<li class="text-capitalize">(roda 2) yamaha mio th 2009 dengan harga Rp. 9.000.000</li>
		  						</ol> --}}
			  				</td>
			  			</tr>
			  		</tbody>
			  	</table>
			</div>
		</div>
	</div>
	
	<!--///////////////////////
	/// MODAL 		///////
 	/////////////////////// -->

	<!-- jaminan kendaraan -->
	@component ('bootstrap.modal', ['id' => 'delete'])
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
	@endcomponent
@endpush

@push('submenu')
	@include('template.submenu.submenu')
@endpush