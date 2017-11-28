<div class="row">
	<div class="col">
		<h4 class='mb-4 text-style text-uppercase text-secondary'>
			KREDIT
		</h4>
	</div>
</div>
<div class="row align-items-center">
	<div class="col-3">
		<a href="{{ route('kredit.angsuran.index', ['kantor_aktif_id' => $kantor_aktif_id]) }}" class="card-link text-style" style="color:#fff">
			<div class="block_menu">
				<i class="d-block fa fa-credit-card fa-2x" style="padding-bottom:5px;"></i>
				BAYAR<br/>ANGSURAN
			</div>
		</a>
	</div>
	<div class="col-3">
		<a href="{{ route('kredit.penagihan.index', ['kantor_aktif_id' => $kantor_aktif_id]) }}" class="card-link text-style" style="color:#fff">
			<div class="block_menu">
				<i class="d-block fa fa-hand-paper-o fa-2x" style="padding-bottom:5px;"></i>
				PENAGIHAN<br/>ANGSURAN
			</div>
		</a>
	</div>
	<div class="col-3">
		<a href="{{ route('kredit.jaminan.index', ['kantor_aktif_id' => $kantor_aktif_id]) }}" class="card-link text-style" style="color:#fff">
			<div class="block_menu">
				<i class="d-block fa fa-arrows fa-2x" style="padding-bottom:5px;"></i>
				MUTASI<br/>JAMINAN
			</div>
		</a>
	</div>
</div>