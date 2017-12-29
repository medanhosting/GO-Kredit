@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 d-flex text-center"><i class="fa fa-paper-plane mr-2"></i> PENGAJUAN</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3 sidebar-plain">
			@include('v2.pengajuan.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				@slot('pre')
					<h5 class="pt-4 pl-4 mb-0">
						<a href="{{route('pengajuan.index', ['kantor_aktif_id' => $kantor_aktif_id])}}">
							<i class="fa fa-chevron-left"></i> 
						</a>
						&nbsp;&nbsp;PENGAJUAN BARU
					</h5>
				@endslot

				@slot('body')
					<div class="row">
						<div class="col">
							@include('v2.pengajuan.permohonan.form')
						</div>
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

@push('js')
	<script type="text/javascript">
		$("#permohonan-menu").click(function(){
			$("#permohonan-panel").fadeIn('fast');
			$("#survei-panel").fadeOut('fast');
			$("#analisa-panel").fadeOut('fast');
			$("#putusan-panel").fadeOut('fast');
		});
	</script>
@endpush