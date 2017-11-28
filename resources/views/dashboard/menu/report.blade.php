<div class="row">
	<div class="col">
		<h4 class='mb-4 text-style text-uppercase text-secondary'>
			REPORT
		</h4>
	</div>
</div>
<div class="row align-items-center">
	<div class="col-3">
		<a href="{{ route('kredit.report.angsuran', ['kantor_aktif_id' => $kantor_aktif_id]) }}" class="card-link text-style" style="color:#fff">
			<div class="block_menu">
				<i class="d-block fa fa-line-chart fa-2x" style="padding-bottom:5px;"></i>
				PENERIMAAN<br/>ANGSURAN
			</div>
		</a>
	</div>
	<div class="col-3">
		<a href="{{ route('kredit.report.tunggakan', ['kantor_aktif_id' => $kantor_aktif_id]) }}" class="card-link text-style" style="color:#fff">
			<div class="block_menu">
				<i class="d-block fa fa-line-chart fa-2x" style="padding-bottom:5px;"></i>
				TUNGGAKAN
			</div>
		</a>
	</div>
	<div class="col-3">
		<a href="{{ route('kredit.report.penagihan', ['kantor_aktif_id' => $kantor_aktif_id]) }}" class="card-link text-style" style="color:#fff">
			<div class="block_menu">
				<i class="d-block fa fa-line-chart fa-2x" style="padding-bottom:5px;"></i>
				PENAGIHAN
			</div>
		</a>
	</div>
	<div class="col-3">
		<a href="{{ route('kredit.report.jaminan', ['kantor_aktif_id' => $kantor_aktif_id]) }}" class="card-link text-style" style="color:#fff">
			<div class="block_menu">
				<i class="d-block fa fa-line-chart fa-2x" style="padding-bottom:5px;"></i>
				PENGEMBALIAN<br/>JAMINAN
			</div>
		</a>
	</div>
</div>