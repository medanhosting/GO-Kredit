@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 d-flex text-center"><i class="fa fa-paper-plane mr-2"></i> PENGAJUAN</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3">
			@include('v2.pengajuan.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				@slot('pre')
					<h6 class="pt-4 pl-4"><a href="{{route('pengajuan.index', ['kantor_aktif_id' => $kantor_aktif_id])}}"><i class="fa fa-angle-left"></i></a>&nbsp;&nbsp;DETAIL PENGAJUAN</h6>
				@endslot
				@slot('body')
					<div class="row">
						<div class="col-sm-2">
							<p class="pb-2 mb-0">
								<a id="permohonan-menu" class="text-success">Permohonan</a>
							</p>
							<p class="pb-2 mb-0">
								<a id="survei-menu" class="text-success">Survei</a>
							</p>
							<p class="pb-2 mb-0">
								<a id="analisa-menu" class="text-success">Analisa</a>
							</p>
							<p class="pb-2 mb-0">
								<a id="putusan-menu" class="text-success">Putusan</a>
							</p>
						</div>
						<div class="col-sm-10">
							<div id="permohonan-panel">
								@if(str_is($permohonan['status'], 'permohonan'))
									@include('v2.pengajuan.permohonan.form')
								@else
									@include('v2.pengajuan.permohonan.list')
								@endif
							</div>
							<div id="survei-panel">
								@if(str_is($permohonan['status'], 'survei'))
									@include('v2.pengajuan.survei.form')
								@else
									@include('v2.pengajuan.survei.list')
								@endif
							</div>
							<div id="analisa-panel">
								@if(str_is($permohonan['status'], 'analisa'))
									@include('v2.pengajuan.analisa.form')
								@else
									@include('v2.pengajuan.analisa.list')
								@endif
							</div>
							<div id="putusan-panel">
								@if(str_is($permohonan['status'], 'putusan') && !str_is($permohonan['progress'], 'sudah'))
									@include('v2.pengajuan.putusan.form')
								@else
									@include('v2.pengajuan.putusan.list')
								@endif
							</div>
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
		$("#permohonan-panel").show();
		$("#survei-panel").hide();
		$("#analisa-panel").hide();
		$("#putusan-panel").hide();

		$("#permohonan-menu").click(function(){
			$("#permohonan-panel").show();
			$("#survei-panel").hide();
			$("#analisa-panel").hide();
			$("#putusan-panel").hide();
		});
		$("#survei-menu").click(function(){
			$("#permohonan-panel").hide();
			$("#survei-panel").show();
			$("#analisa-panel").hide();
			$("#putusan-panel").hide();
		});
		$("#analisa-menu").click(function(){
			$("#permohonan-panel").hide();
			$("#survei-panel").hide();
			$("#analisa-panel").show();
			$("#putusan-panel").hide();
		});
		$("#putusan-menu").click(function(){
			$("#permohonan-panel").hide();
			$("#survei-panel").hide();
			$("#analisa-panel").hide();
			$("#putusan-panel").show();
		});
	</script>
@endpush