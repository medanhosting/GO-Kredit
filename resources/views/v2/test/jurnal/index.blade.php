@inject('idr', 'App\Service\UI\IDRTranslater')
@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 d-flex text-center"><i class="fa fa-gavel mr-2"></i> TEST</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3 sidebar-plain">
			@include('v2.test.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				@slot('header')
					<h5 class="py-2 pl-3 mb-0 float-left">&nbsp;&nbsp;TEST JURNAL</h5>
				@endslot

				<div class="card-body">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Tanggal</th>
								<th>Bukti</th>
								<th>Keterangan</th>
								<th>Debit</th>
								<th>Kredit</th>
							</tr>
						</thead>
						<tbody>
							@foreach($jurnal as $k => $v)
							<tr>
								<td>{{$v['tanggal']}}</td>
								<td>{{$v['nomor_faktur']}}</td>
								<td>
									@if($v['debit'])
										{{$v['coa']['akun']}}
									@else
										&emsp;{{$v['coa']['akun']}}
									@endif
								</td>
								<td>
									{{$v['debit']}}
								</td>
								<td>
									{{$v['kredit']}}
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			@endcomponent
		</div>
	</div>
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush

@push('css')
@endpush