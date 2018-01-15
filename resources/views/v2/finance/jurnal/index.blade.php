@inject('idr', 'App\Service\UI\IDRTranslater')
@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 d-flex text-center"><i class="fa fa-line-chart mr-2"></i> Keuangan</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3 sidebar-plain">
			@include('v2.finance.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				@slot('body')
					<nav class="nav nav-tabs" id="myTab" role="tablist">
						@foreach($akun as $k => $v)
							<a class="nav-item nav-link @if($k==0) active show @endif" id="nav-{{$v['id']}}-tab" data-toggle="tab" href="#nav-{{$v['id']}}" role="tab" aria-controls="nav-{{$v['id']}}" aria-selected="true">{{$v['akun']}}</a>
						@endforeach
					</nav>
					<div class="tab-content" id="nav-tabContent">
						@foreach($akun as $k => $v)
						<div class="tab-pane fade @if($k==0) active show @endif" id="nav-{{$v['id']}}" role="tabpanel" aria-labelledby="nav-{{$v['id']}}-tab">
							<div class="clearfix">&nbsp;</div>
							<div class="row">
								<div class="col-6">
									<table class="table table-bordered">
										<thead>
											<tr>
												<th colspan="3" class="text-left">JURNAL {{strtoupper($v['akun'])}} MASUK</th>
											</tr>
											<tr>
												<th>Perkiraan</th>
												<th>Uraian</th>
												<th class="text-right">Jumlah</th>
											</tr>
										</thead>
										<tbody>
											@foreach($v['subakun'] as $k2 => $v2)
											<tr>
												<td>{{$v2['nomor_perkiraan']}}</td>
												<td>{{$v2['akun']}}</td>
												@php $total 	= array_sum(array_column($v2['detailsin']->toArray(), 'jumlah')) @endphp
												<td class="text-right">{{$idr->formatMoneyTo($total)}}</td>
											</tr>
											@endforeach
										</tbody>
									</table>
								</div>
								<div class="col-6">
									<table class="table table-bordered">
										<thead>
											<tr>
												<th colspan="3" class="text-left">JURNAL {{strtoupper($v['akun'])}} KELUAR</th>
											</tr>
											<tr>
												<th>Perkiraan</th>
												<th>Uraian</th>
												<th class="text-right">Jumlah</th>
											</tr>
										</thead>
										<tbody>
											@foreach($v['subakun'] as $k2 => $v2)
											<tr>
												<td>{{$v2['nomor_perkiraan']}}</td>
												<td>{{$v2['akun']}}</td>
												@php $total 	= array_sum(array_column($v2['detailsout']->toArray(), 'jumlah')) @endphp
												<td class="text-right">{{$idr->formatMoneyTo(abs($total))}}</td>
											</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>

						</div>
						@endforeach
					</div>
				@endslot
			@endcomponent
		</div>
	</div>
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush

@push('css')
@endpush