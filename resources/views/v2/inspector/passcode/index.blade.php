@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 d-flex text-center"><i class="fa fa-user-secret mr-2"></i> INSPECTOR</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3 sidebar-plain">
			@include('v2.inspector.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				@slot('header')
					<h5 class="py-2 pl-2 mb-0">&nbsp;&nbsp;PASSCODE</h5>
				@endslot
				<div class="card-body">
					<div class="row">
						<div class="col-12 col-sm-12 col-md-12 text-right">
							<a href="#" data-toggle="modal" data-target="#passcode_baru" data-action="{{route('passcode.store', ['kantor_aktif_id' => $kantor_aktif['id']])}}" class="modal_passcode btn btn-outline-primary text-capitalize text-style mb-2" class="btn btn-success">+</a>
						</div>
					</div>
					<div class="clearfix">&nbsp;</div>
					<div class="row">
						<div class="col-12 col-sm-12 col-md-12">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>#</th>
										<th>Kredit</th>
										<th>Passcode</th>
										<th>Expired</th>
									</tr>
								</thead>
								<tbody>
									@foreach($passcode as $k => $v)
									<tr>
										<td>{{ (($passcode->currentPage() - 1) * $passcode->perPage()) + $loop->iteration }}</td>
										<td>
											{{$v['survei']['pengajuan']['id']}} <br/>
											{{$v['survei']['pengajuan']['nasabah']['nama']}}
										</td>
										<td>{{$v['passcode']}}</td>
										<td>{{$v['expired_at']}}</td>
									</tr>
									@endforeach
								</tbody>
							</table>

							<div class="clearfix">&nbsp;</div>
							<div class="row">
								<div class="col">
									{{$passcode->appends(request()->all())}}
								</div>
							</div>
						</div>
					</div>
				</div>
			@endcomponent
		</div>
	</div>
	@include('v2.inspector.passcode.modal')
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush

@push('css')
@endpush