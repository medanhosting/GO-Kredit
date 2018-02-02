@inject('idr', 'App\Service\UI\IDRTranslater')
@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 text-center"><i class="fa fa-credit-card-alt mr-2"></i> KREDIT</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3 sidebar-plain">
			@include('v2.kredit.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				@slot('header')
					<h5 class="py-2 pl-2 mb-0">
						<a href="{{route('kredit.index', ['kantor_aktif_id' => $kantor_aktif_id])}}">
							<i class="fa fa-chevron-left"></i> 
						</a>
						&nbsp;&nbsp;DETAIL KREDIT
					</h5>
				@endslot

				<div class="card-body">
					<div class="row">
						<div class="col-12 col-sm-12 col-md-12">
							<!-- Nav tabs -->
							<ul class="nav nav-tabs underline" role="tablist">
								<li class="nav-item">
									<a class="nav-link {{$is_kredit_tab}}" data-toggle="tab" href="#kredit" role="tab">
										Kredit
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link {{$is_angsuran_tab}}" data-toggle="tab" href="#angsuran" role="tab">
										Angsuran 
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link {{$is_penagihan_tab}}" data-toggle="tab" href="#penagihan" role="tab">
										Penagihan 
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link {{$is_jaminan_tab}}" data-toggle="tab" href="#jaminan" role="tab">
										Stok Jaminan 
									</a>
								</li>
							</ul>

							<!-- Tab panes -->
							<div class="tab-content">
								<!-- tab kredit -->
								<div class="tab-pane {{$is_kredit_tab}}" id="kredit" role="tabpanel">
									@include('v2.kredit.show.kredit')
								</div>
								<!-- tab angsuran -->
								<div class="tab-pane {{$is_angsuran_tab}}" id="angsuran" role="tabpanel">
									<div class="clearfix">&nbsp;</div>
									<div class="clearfix">&nbsp;</div>
									<div class="row">

										<div class="col-4">
											@component('bootstrap.card')
												@slot('title') 
													<h4 class='text-center'>
														{{$idr->formatMoneyTo($stat['total_tunggakan'])}}
													</h4><hr> 
												@endslot
												@slot('body') <p class='text-center'>TOTAL {{$stat['jumlah_tunggakan']}} ANGSURAN JATUH TEMPO</p> @endslot
											@endcomponent
										</div>
										<div class="col-4">
											@component('bootstrap.card')
												@slot('title') 
													<h4 class='text-center'>
														{{$idr->formatMoneyTo($stat['total_titipan'])}}
													</h4><hr> 
												@endslot
												@slot('body') <p class='text-center'>TOTAL TITIPAN</p> @endslot
											@endcomponent
										</div>
										<div class="col-4">
											@component('bootstrap.card')
												@slot('title') 
													<h4 class='text-center'>
														{{$idr->formatMoneyTo($stat['total_tunggakan'] - $stat['total_titipan'])}}
													</h4><hr> 
												@endslot
												@slot('body') <p class='text-center'>TOTAL ANGSURAN YANG HARUS DIBAYAR</p> @endslot
											@endcomponent
										</div>
									</div>

									<div class="clearfix">&nbsp;</div>
									<div class="clearfix">&nbsp;</div>
									<div class="row">
										<div class="col-8">
											@include('v2.kredit.show.bayar_angsuran')
										</div>
										<div class="col-4">
											@include('v2.kredit.show.titipan_angsuran')
										</div>
										<!-- <div class="col-4">
											@component('bootstrap.card')
												@slot('title') 
													<h4 class='text-center'>
														{{$idr->formatMoneyTo($stat['total_pelunasan'])}}
													</h4><hr> 
												@endslot
												@slot('body') <p class='text-center'>SIMULASI PELUNASAN ANGSURAN</p> @endslot
											@endcomponent
										</div> -->
									</div>

									<div class="row">
										<div class="col-8">
											@include('v2.kredit.show.angsuran')
										</div>
										<div class="col-4">
											@include('v2.kredit.show.kas_kolektor')
											<!-- @include('v2.kredit.show.denda') -->
										</div>
									</div>
								</div>
								
								<!-- tab jaminan -->
								<div class="tab-pane {{$is_jaminan_tab}}" id="jaminan" role="tabpanel">
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
