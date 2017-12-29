<div class="clearfix">&nbsp;</div>
<div class="row text-justify">
<div class="col-4">
	<div class="row text-justify">
		<div class="col-6 text-left">
			Jumlah Pinjaman
		</div> 
		<div class="col-6 text-right">
			{{$rincian['pokok_pinjaman']}}
		</div> 
	</div>

	<div class="row text-justify">
		<div class="col-6 text-left">
			Biaya Provisi
		</div> 
		<div class="col-6 text-right">
			{{$rincian['provisi']}}
		</div> 
	</div>
	<div class="row text-justify">
		<div class="col-6 text-left">
			Biaya Administrasi
		</div> 
		<div class="col-6 text-right">
			{{$rincian['administrasi']}}
		</div> 
	</div>
	<div class="row text-justify">
		<div class="col-6 text-left">
			Biaya Legal
		</div> 
		<div class="col-6 text-right">
			{{$rincian['legal']}}
		</div> 
	</div>
	<div class="row text-justify">
		<div class="col-6 text-left">
			Pinjaman Bersih
		</div> 
		<div class="col-6 text-right">
			{{$rincian['pinjaman_bersih']}}
		</div> 
	</div>
</div>
<div class="col-4">
	<div class="row text-justify">
		<div class="col-6 text-left">
			Kemampuan Angsur
		</div> 
		<div class="col-6 text-right">
			{{$rincian['kemampuan_angsur']}}
		</div> 
	</div>
	<div class="row text-justify">
		<div class="col-6 text-left">
			Lama Angsuran
		</div> 
		<div class="col-6 text-right">
			{{$rincian['lama_angsuran']}} Bulan
		</div> 
	</div>
	<div class="row text-justify">
		<div class="col-6 text-left">
			Bunga Per Tahun
		</div> 
		<div class="col-6 text-right">
			{{$rincian['bunga_per_tahun']}} % 
		</div> 
	</div>
	<div class="row text-justify">
		<div class="col-6 text-left">
			Perhitungan Bunga
		</div> 
		<div class="col-6 text-right">
			{{strtoupper($mode)}}
		</div> 
	</div>
</div>
</div>
<div class="clearfix">&nbsp;</div>
<div class="row text-justify">
<div class="col-12">
	<table class="table f8f9fa table-bordered">
  		<thead class="thead-default">
  			<tr>
  				<th>#</th>
  				<th>Bulan</th>
  				<th>Angsuran Bunga</th>
  				<th>Angsuran Pokok</th>
  				<th>Total Angsuran</th>
  				<th>Sisa Pinjaman</th>
  			</tr>
  		</thead>
  		<tbody>
			@foreach($rincian['angsuran'] as $k => $v)
	  			<tr>
		  			<td>{{($k)}}</td>
		  			<td>{{$v['bulan']}}</td>
		  			<td>{{$v['angsuran_bunga']}}</td>
		  			<td>{{$v['angsuran_pokok']}}</td>
		  			<td>{{$v['total_angsuran']}}</td>
		  			<td>{{$v['sisa_pinjaman']}}</td>
		  		</tr>
  			@endforeach
  		</tbody>
		</table>
	</div>
</div>