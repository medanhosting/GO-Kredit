@inject('idr', 'App\Service\UI\IDRTranslater')
@push('main')
	<div class="clearfix">&nbsp;</div>	
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 text-center"><i class="fa fa-credit-card-alt mr-2"></i> KREDIT</h5>
			<hr>
		</div>
	</div>
	<div class="clearfix">&nbsp;</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3 sidebar-plain">
			@include('v2.kredit.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				<div class="card-header bg-success border-light text-light">
					<h5 class="py-2 pl-2 mb-0">
						<a href="{{route('kredit.index', ['kantor_aktif_id' => $kantor_aktif_id])}}">
							<i class="fa fa-chevron-left text-light"></i> 
						</a>
						&nbsp;&nbsp;DETAIL KREDIT
					</h5>
				</div>

				<div class="card-body p-0">
					<div class="row p-4 bg-success text-white">
						<div class="col-6">
							<p class="text-gray-light mb-0">Nomor Kredit ( Jenis Kredit )</p>
							<h4>{{ $aktif['nomor_kredit'] }} ( {{ strtoupper($aktif['jenis_pinjaman']) }} )</h4>
							<p class="text-gray-light mb-0">Pinjaman ( Bunga )</p>
							<h4>{{ strtoupper($aktif['plafon_pinjaman']) }} ( {{ strtoupper($aktif['suku_bunga']) }} % )</h4>
							<p class="text-gray-light mb-0">Tanggal</p>							
							<h5>{{ strtoupper($aktif['tanggal']) }}</h5>
						</div>
						<div class="col-6">
							<p class="text-gray-light mb-0">Nama ( Jenis Kelamin )</p>							
							<h4>{{ $aktif['nasabah']['nama'] }} ( {{ $aktif['nasabah']['jenis_kelamin'] }} )</h4>
							<p class="text-gray-light mb-0">Telepon</p>
							<h4>{{ $aktif['nasabah']['telepon'] }}</h4>
							<p class="mb-0 text-gray-light">Alamat</p>
							<h5>{!! ucfirst(strtolower(implode(', ', $aktif['nasabah']['alamat']))) !!}</h5>
						</div>
					</div>
					<div class="row">
						<div class="col-12 col-sm-12 col-md-12">
							<!-- Nav tabs -->
							<ul class="nav nav-tabs underline" role="tablist">
								<li class="nav-item">
									<a class="nav-link px-4 {{$is_angsuran_tab}}" data-toggle="tab" href="#angsuran" role="tab">
										Angsuran 
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link px-4 {{ $is_denda_tab }}" href="#denda" role="tab" data-toggle="tab">
										Denda
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link px-4 {{$is_penagihan_tab}}" data-toggle="tab" href="#penagihan" role="tab">
										Penagihan 
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link px-4 {{$is_jaminan_tab}}" data-toggle="tab" href="#jaminan" role="tab">
										Stok Jaminan 
									</a>
								</li>
							</ul>

							<!-- Tab panes -->
							<div class="tab-content">
								<!-- tab angsuran -->
								<div class="tab-pane p-4 {{$is_angsuran_tab}}" id="angsuran" role="tabpanel">
									@include('v2.kredit.show.angsuran')
								</div>
								<!-- tab denda -->
								<div class="tab-pane p-4 {{$is_denda_tab}}" id="denda" role="tabpanel">
									@include('v2.kredit.show.denda')
								</div>
								<!-- tab penagihan -->
								<div class="tab-pane p-4 {{$is_penagihan_tab}}" id="penagihan" role="tabpanel">
									@include('v2.kredit.show.penagihan')
								</div>
								<!-- tab jaminan -->
								<div class="tab-pane p-4 {{$is_jaminan_tab}}" id="jaminan" role="tabpanel">
									@include('v2.kredit.show.jaminan')
								</div>
							</div>
						</div>
					</div>
				</div>
			@endcomponent
		</div>
	</div>
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush

@push('js')
	<script type="text/javascript">
		window.formInputMask.init();
	</script>
@endpush
