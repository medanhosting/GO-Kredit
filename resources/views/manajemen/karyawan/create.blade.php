@push('main')
	<div class="container bg-white bg-shadow p-4">
		<div class="row">
			<div class="col">
				<h4 class='mb-4 text-style text-secondary'>
					<span class="text-uppercase">{{$title}}</span> 
					@if(is_null($karyawan['id']))
						<small> / <small><a href="{{route('manajemen.karyawan.upload', ['kantor_aktif_id' => $kantor_aktif['id']])}}" class="">Upload Data Karyawan</a></small></small>
					@endif
				</h4>
			</div>
		</div>
		
		<div class="clearfix">&nbsp;</div>

		@if(is_null($karyawan['id']))
			{!! Form::open(['url' => route('manajemen.karyawan.store', ['kantor_aktif_id' => $kantor_aktif['id']])]) !!}
		@else
			{!! Form::open(['url' => route('manajemen.karyawan.update', ['id' => $karyawan['id'], 'kantor_aktif_id' => $kantor_aktif['id']]), 'method' => 'PATCH']) !!}
		@endif

			<div class="row p-b-sm">
				<div class="col-sm-6">
					<fieldset class="form-group">
						<label class="text-sm">
							<strong>
								DATA PRIBADI
							</strong>
						</label>
					</fieldset>

					<fieldset class="form-group">
						<label class="text-sm">Nama</label>
						<div class="row">
							<div class="col-xs-12 col-sm-12 col-md-10">
								{!! Form::text('nama', $karyawan['nama'], ['class' => 'form-control required', 'placeholder' => 'Masukkan nama karyawan']) !!}			
							</div>
						</div>
					</fieldset>

					<fieldset class="form-group">
						<label class="text-sm">Email</label>
						<div class="row">
							<div class="col-xs-12 col-sm-12 col-md-10">
								{!! Form::email('email', $karyawan['email'], ['class' => 'form-control required', 'placeholder' => 'Masukkan email karyawan']) !!}			
							</div>
						</div>
					</fieldset>

					<fieldset class="form-group">
						<label class="text-sm">Telepon</label>
						<div class="row">
							<div class="col-xs-12 col-sm-12 col-md-10">
								{!! Form::text('telepon', $karyawan['telepon'], ['class' => 'form-control required', 'placeholder' => 'Masukkan nomor telepon']) !!}			
							</div>
						</div>
					</fieldset>	

					<fieldset class="form-group">
						<label class="text-sm">Alamat Lengkap</label>
						<div class="row">
							<div class="col-xs-12 col-sm-12 col-md-10">
								<div class="input-group">
									{!! Form::textarea('alamat', $karyawan['alamat']['alamat'], [
										'class' => 'form-control required', 
										'placeholder' => 'Masukkan alamat',
									]) !!}
								</div>							
							</div>
						</div>
					</fieldset>

					@if(!is_null($karyawan['id']))
						<fieldset class="form-group">
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-10">
									<!-- @if(!is_null($karyawan['id']))
									<a class="btn btn-default" href="{{ URL::previous() }}" no-data-pjax>Batal</a>
									@endif -->
									<button type="submit" class="btn btn-primary">{{ is_null($karyawan['id']) ? 'Tambahkan' : 'Simpan' }}</button>
								</div>
							</div>
						</fieldset>
					@endif
				</div>
				<div class="col-sm-6">
					@if(is_null($karyawan['id']))
					<fieldset class="form-group">
						<label class="text-sm">
							<strong>
								DATA PENEMPATAN
							</strong>
						</label>
					</fieldset>

					<fieldset class="form-group">
						<label class="text-sm">Password</label>
						<div class="row">
							<div class="col-xs-12 col-sm-12 col-md-10">
								{!! Form::password('password', ['class' => 'form-control required', 'placeholder' => 'Password Karyawan']) !!}
							</div>
						</div>
					</fieldset>	

					<fieldset class="form-group">
						<label class="text-sm">Konfirmasi Password</label>
						<div class="row">
							<div class="col-xs-12 col-sm-12 col-md-10">
								{!! Form::password('confirm_password', ['class' => 'form-control required', 'placeholder' => 'Konfirmasi Password Karyawan']) !!}
							</div>
						</div>
					</fieldset>	

					<fieldset class="form-group">
						<label class="text-sm">Kode Kantor</label>
						<div class="row">
							<div class="col-xs-12 col-sm-12 col-md-10">
								{!! Form::text('kantor[kantor_id]', null, ['class' => 'form-control required', 'placeholder' => 'Masukkan kode kantor']) !!}			
							</div>
						</div>
					</fieldset>

					<fieldset class="form-group">
						<label class="text-sm">Jabatan</label>
						<div class="row">
							<div class="col-xs-12 col-sm-12 col-md-10">
								{!! Form::text('kantor[role]', null, ['class' => 'form-control required', 'placeholder' => 'Masukkan jabatan']) !!}			
							</div>
						</div>
					</fieldset>

					<fieldset class="form-group">
						<label class="text-sm">Tanggal Masuk Kerja</label>
						<div class="row">
							<div class="col-xs-12 col-sm-12 col-md-10">
								{!! Form::text('kantor[tanggal_masuk]', null, ['class' => 'form-control required', 'placeholder' => 'Masukkan tanggal masuk']) !!}			
							</div>
						</div>
					</fieldset>

					<fieldset class="form-group">
						<label class="text-sm">Scopes</label>
						<div class="row">
							<div class="col-xs-12 col-sm-12 col-md-10">
								<select class="scopesselect form-control" name="kantor[scopes][]" multiple="multiple">
									@foreach($scopes as $k => $v)
										<option value="{{$v}}">Manage {{ucwords($v)}}</option>
									@endforeach
								</select>	
							</div>
						</div>
					</fieldset>
					<fieldset class="form-group">
						<div class="row">
							<div class="col-xs-12 col-sm-12 col-md-10">
								<!-- @if(!is_null($karyawan['id']))
								<a class="btn btn-default" href="{{ URL::previous() }}" no-data-pjax>Batal</a>
								@endif -->
								<button type="submit" class="btn btn-primary">{{ is_null($karyawan['id']) ? 'Tambahkan' : 'Simpan' }}</button>
							</div>
						</div>
					</fieldset>
					@endif
				</div>
			</div>
		{!! Form::close() !!}
	</div>
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush

@push('js')
	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

	<script type="text/javascript">
	   	$(document).ready(function() {
		    $('.scopesselect').select2();
		});
	</script>
@endpush