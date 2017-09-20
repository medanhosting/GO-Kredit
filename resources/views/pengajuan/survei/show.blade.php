@isset ($title)
	<div class="row">
		<div class="col">
			<h5 class="pb-4">Survei</h5>
		</div>
	</div>
@endif

{{-- nav survei --}}
<nav class="nav nav-tabs">
	<a href="#karakter-panel" class="nav-item nav-link active" role="tab" data-toggle="tab" aria-expanded="true">Karakter</a>
	<a href="#kondisi-panel" class="nav-item nav-link" role="tab" data-toggle="tab">Kondisi</a>
	<a href="#kapasitas-panel" class="nav-item nav-link" role="tab" data-toggle="tab">Kapasitas</a>
	<div class="dropdown">
		<a href="#kapital-panel" class="nav-item nav-link dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Kapital</a>
		<div class="dropdown-menu">
			<a href="#rumah-kapital-panel" class="dropdown-item" role="tab" data-toggle="tab">Rumah</a>
			<a href="#kendaraan-kapital-panel" class="dropdown-item" role="tab" data-toggle="tab">Kendaraan</a>
			<a href="#usaha-kapital-panel" class="dropdown-item" role="tab" data-toggle="tab">Usaha</a>
		</div>
	</div>
	<a href="#kolateral-panel" class="nav-item nav-link" role="tab" data-toggle="tab">Kolateral</a>
</nav>

<div class="row">
	<div class="col">
		<div class="tab-content">
			{{-- karakter --}}
			<div class="tab-pane fade show active" id="karakter-panel" role="tabpanel">
				@include ('pengajuan.survei.karakter.components.overview')
			</div>

			{{-- kondisi --}}
			<div class="tab-pane fade" id="kondisi-panel" role="tabpanel">
				@include ('pengajuan.survei.kondisi.components.overview')
			</div>

			{{-- kapasitas --}}
			<div class="tab-pane fade" id="kapasitas-panel" role="tabpanel">
				@include ('pengajuan.survei.kapasitas.components.overview')
			</div>

			{{-- kapital - rumah --}}
			<div class="tab-pane fade" id="rumah-kapital-panel" role="tabpanel">
				@include ('pengajuan.survei.kapital.rumah.components.overview')
			</div>

			{{-- kapital - kendaraan --}}
			<div class="tab-pane fade" id="kendaraan-kapital-panel" role="tabpanel">
				@include ('pengajuan.survei.kapital.kendaraan.components.overview')
			</div>			

			{{-- kapital, usaha --}}
			<div class="tab-pane fade" id="usaha-kapital-panel" role="tabpanel">
				@include ('pengajuan.survei.kapital.usaha.components.overview')
			</div>

			{{-- kolateral --}}
			<div class="tab-pane fade" id="kolateral-panel" role="tabpanel">
				kolateral
			</div>
		</div>
	</div>
</div>