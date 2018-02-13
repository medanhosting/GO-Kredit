@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 d-flex text-center"><i class="fa fa-briefcase mr-2"></i> KANTOR</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3 sidebar-plain">
			@include('v2.kantor.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				@slot('header')
					<h5 class="py-2 pl-2 mb-0">
						<a href="{{route('karyawan.index', ['kantor_aktif_id' => $kantor_aktif_id])}}">
							<i class="fa fa-chevron-left"></i> 
						</a>
						&nbsp;&nbsp;EDIT KARYAWAN
					</h5>
				@endslot

				<div class="card-body">
					<div class="row">
						<div class="col-12 col-sm-12 col-md-12">
							<nav class="nav nav-tabs underline" id="myTab" role="tablist">
								<a class="nav-item nav-link {{$is_create_tab}}" id="nav-create-tab" data-toggle="tab" href="#nav-create" role="tab" aria-controls="nav-create" aria-selected="true">Data Karyawan</a>
								<a class="nav-item nav-link {{$is_penempatan_tab}}" id="nav-penempatan-tab" data-toggle="tab" href="#nav-penempatan" role="tab" aria-controls="nav-penempatan" aria-selected="true">Penempatan Karyawan</a>
							</nav>
							<div class="tab-content" id="nav-tabContent">
								<div class="tab-pane fade {{$is_create_tab}}" id="nav-create" role="tabpanel" aria-labelledby="nav-create-tab">
									@include('v2.kantor.karyawan.form')
								</div>
								<div class="tab-pane fade {{$is_penempatan_tab}}" id="nav-penempatan" role="tabpanel" aria-labelledby="nav-penempatan-tab">
									<div class="clearfix">&nbsp;</div>
									<table class="table table-bordered">
										<thead>
											<tr>
												<th class="text-center">#</th>
												<th class="text-center">Kantor</th>
												<th class="text-center">Jabatan</th>
												<th class="text-center" style="width: 30%">Scopes</th>
												<th class="text-center">Masa Kerja</th>
												<th class="text-center">&nbsp;</th>
											</tr>
										</thead>
										<tbody>
											@forelse($karyawan['penempatan'] as $k => $v)
											<tr>
												<td class="text-center">{{ ($k + 1) }}</td>
												<td class="text-left">{{ ucwords(str_replace('_', ' ', $v['kantor']['nama'])) }}</td>
												<td class="text-left">{{ ucwords(str_replace('_', ' ', $v['role'])) }}</td>
												<td class="text-left" style="width: 30%">
													@foreach($v['scopes'] as $k3 => $v3)
														<span class="badge badge-secondary"> manage {{$v3}} </span>
													@endforeach
												</td>
												<td class="text-left" style="width: 25%">{{ $v['tanggal_masuk'] }} - 
													@if(is_null($v['tanggal_keluar']))
														<i>sekarang</i>
													@else
														{{ $v['tanggal_keluar'] }}
													@endif
												</td>
												<td class="text-center">
													@if(is_null($v['tanggal_keluar']))
													<a href="#" data-toggle="modal" class="btn btn-success btn-sm mutasi_karyawan" data-target="#mutasi" data-action="{{route('karyawan.update', ['orang_id' => $karyawan['id'],'penempatan_id' => $v['id'], 'kantor_aktif_id' => $kantor_aktif['id'], 'mode' => 'mutasi'])}}">
														MUTASI
													</a> 
													<br/><br/>
													<a href="#" data-toggle="modal" class="btn btn-success btn-sm resign_karyawan" data-target="#resign" data-action="{{route('karyawan.update', ['orang_id' => $karyawan['id'],'penempatan_id' => $v['id'], 'kantor_aktif_id' => $kantor_aktif['id'], 'mode' => 'resign'])}}">
														RESIGN
													</a>
													<br/><br/>
													<a href="#" data-toggle="modal" class="btn btn-success btn-sm edit_kewenangan" data-target="#edit" data-action="{{route('karyawan.update', ['orang_id' => $karyawan['id'],'penempatan_id' => $v['id'], 'kantor_aktif_id' => $kantor_aktif['id'], 'mode' => 'edit_kewenangan'])}}" data-jabatan="{{$v['role']}}" data-scopes="{{$v['scopes']}}">
														EDIT KEWENANGAN
													</a>
													@endif
												</td>
											</tr>
											@empty
												<tr>
													<td colspan="6" class="text-center"><i class="text-secondary">tidak ada data</i></td>
												</tr>
											@endforelse
										</tbody>
										<tfoot>
											<tr>
												<td colspan="6" class="text-right">
													<a href="#" class="btn btn-success btn-sm assign_karyawan" data-toggle="modal" data-target="#assign" data-action="{{route('karyawan.update', ['orang_id' => $karyawan['id'], 'kantor_aktif_id' => $kantor_aktif['id'], 'mode' => 'assign'])}}">
														PENEMPATAN BARU
													</a>
												</td>
											</tr>
										</tfoot>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			@endcomponent
		</div>
	</div>

	@component ('bootstrap.modal', ['id' => 'resign', 'form' => true, 'method' => 'patch', 'url' => '#'])
		@slot ('title')
			Resign dari jabatan ini
		@endslot

		@slot ('body')
			<p>Untuk resign jabatan ini, harap mengisi tanggal resign</p>
			<fieldset class="form-group">
				<label class="text-sm">Tanggal Keluar</label>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-10">
						{!! Form::text('tanggal_keluar', Carbon\Carbon::now()->format('d/m/Y H:i'), ['class' => 'form-control required', 'placeholder' => 'Masukkan tanggal keluar']) !!}			
					</div>
				</div>
			</fieldset>
		@endslot

		@slot ('footer')
			<a href="#" data-dismiss="modal" class="btn btn-link text-secondary">Batal</a>
			{!! Form::bsSubmit('Simpan', ['class' => 'btn btn-primary']) !!}
		@endslot
	@endcomponent

	@component ('bootstrap.modal', ['id' => 'mutasi', 'form' => true, 'method' => 'patch', 'url' => '#'])
		@slot ('title')
			Pindah dari jabatan ini
		@endslot

		@slot ('body')
			<p>Untuk pindah jabatan ini, harap mengisi kode kantor yang baru dan tanggal pindah</p>

			<fieldset class="form-group">
				<label class="text-sm">KODE KANTOR</label>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12">
						@include('v2.kantor.ajax-kode-pusat', ['kantor' => ['pusat' => $kantor_aktif]])
					</div>
				</div>
			</fieldset>

			<fieldset class="form-group">
				<label class="text-sm">Tanggal pindah</label>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-10">
						{!! Form::text('tanggal_pindah', Carbon\Carbon::now()->format('d/m/Y H:i'), ['class' => 'form-control required', 'placeholder' => 'Masukkan tanggal pindah']) !!}			
					</div>
				</div>
			</fieldset>
		@endslot

		@slot ('footer')
			<a href="#" data-dismiss="modal" class="btn btn-link text-secondary">Batal</a>
			{!! Form::bsSubmit('Simpan', ['class' => 'btn btn-primary']) !!}
		@endslot
	@endcomponent

	@component ('bootstrap.modal', ['id' => 'assign', 'form' => true, 'method' => 'patch', 'url' => '#'])
		@slot ('title')
			Penempatan Baru
		@endslot

		@slot ('body')
			<p>Untuk penempatan baru, harap mengisi form berikut</p>
			<fieldset class="form-group">
				<label class="text-sm">KODE KANTOR</label>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12">
						@include('v2.kantor.ajax-kode-pusat', ['kantor' => ['pusat' => $kantor_aktif]])
					</div>
				</div>
			</fieldset>

			<fieldset class="form-group">
				<label class="text-sm">Tanggal Masuk Kerja</label>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12">
						{!! Form::text('kantor[tanggal_masuk]', null, ['class' => 'form-control required', 'placeholder' => 'Masukkan tanggal masuk']) !!}			
					</div>
				</div>
			</fieldset>

			<fieldset class="form-group">
				<label class="text-sm">JABATAN</label>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12">
						<select class="jabatan-select form-control" name="kantor[role]" style="width:100%"></select>
					</div>
				</div>
			</fieldset>

			<fieldset class="form-group">
				<label class="text-sm">WEWENANG</label>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12">
						<select class="scopes-select form-control" name="kantor[scopes][]" multiple="multiple" style="width:100%"></select>	
					</div>
				</div>
			</fieldset>
		@endslot

		@slot ('footer')
			<a href="#" data-dismiss="modal" class="btn btn-link text-secondary">Batal</a>
			{!! Form::bsSubmit('Simpan', ['class' => 'btn btn-primary']) !!}
		@endslot
	@endcomponent


	@component ('bootstrap.modal', ['id' => 'edit', 'form' => true, 'method' => 'patch', 'url' => '#'])
		@slot ('title')
			Edit Kewenangan
		@endslot

		@slot ('body')
			<p>Untuk edit kewenangan, harap mengisi form berikut</p>
			<fieldset class="form-group">
				<label class="text-sm">JABATAN</label>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12">
						<select class="jabatan-select form-control" name="kantor[role]" style="width:100%"></select>
					</div>
				</div>
			</fieldset>
			<fieldset class="form-group">
				<label class="text-sm">WEWENANG</label>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12">
						<select class="scopes-select form-control" name="kantor[scopes][]" multiple="multiple" style="width:100%"></select>	
					</div>
				</div>
			</fieldset>
		@endslot

		@slot ('footer')
			<a href="#" data-dismiss="modal" class="btn btn-link text-secondary">Batal</a>
			{!! Form::bsSubmit('Simpan', ['class' => 'btn btn-primary']) !!}
		@endslot
	@endcomponent
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush

@push('css')
@endpush

@push('js')
	<script type="text/javascript">
		//MODAL PARSE DATA ATTRIBUTE//
		$("a.assign_karyawan").on("click", parsingDataAttributeAssign);

		function parsingDataAttributeAssign(){
			$('#assign').find('form').attr('action', $(this).attr("data-action"));
		}

		$("a.resign_karyawan").on("click", parsingDataAttributeResign);

		function parsingDataAttributeResign(){
			$('#resign').find('form').attr('action', $(this).attr("data-action"));
		}

		$("a.mutasi_karyawan").on("click", parsingDataAttributeMutasi);

		function parsingDataAttributeMutasi(){
			$('#mutasi').find('form').attr('action', $(this).attr("data-action"));
		}

		$("a.edit_kewenangan").on("click", parsingDataAttributeEditWewenang);

		function parsingDataAttributeEditWewenang(){
			$('#edit').find('form').attr('action', $(this).attr("data-action"));
		}
	</script>
@endpush