@push('css')
	<style>
		.nav-tabs .nav-link.active {
			border-radius: 0 !important;
			border-top: 0 !important;
			border-left: 0 !important;
			border-right: 0 !important;
			border-bottom: 1px solid #eee;
		}
		.nav-tabs .nav-link:hover, .nav-tabs .nav-link:hover .active{
			border: 0 !important;
		}
	</style>
@endpush

@push('main')
	<div class="container bg-white bg-shadow">

		<div class="row">
			<div class="col p-5">
				<h4 class='mb-4 text-style text-uppercase text-secondary'>
					Permohonan Kredit Baru
				</h4>
				<nav class="nav nav-tabs mb-5 border" role="tablist" style="background-color: #fafafa;">
					<a href="#kredit" class="nav-item nav-link active w-25 text-primary rounded-0" data-toggle="tab" role="tab" aria-controls="kredit" aria-expanded="true"><h6 class="mb-0">1 &nbsp;kredit</h6></a>
					<a href="#nasabah" class="nav-item nav-link  w-25" data-toggle="tab" role="tab" aria-controls="nasabah" aria-expanded="true"><h6 class="mb-0">2 &nbsp;nasabah</h6></a>
					<a href="#jaminan" class="nav-item nav-link  w-25" data-toggle="tab" role="tab" aria-controls="jaminan" aria-expanded="true"><h6 class="mb-0">3 &nbsp;jaminan</h6></a>
				</nav>

				{!! Form::open(['url' => route('pengajuan.permohonan.store'), 'files' => true, 'thunder-validation-submitvalidation' => true, 'class' => 'thunder-validation-form']) !!}
					<div class="tab-content">
						<!-- data kredit -->
						<div class="tab-pane fade show active" id="kredit" role="tabpanel">
							<h5 class="text-gray mb-4 pl-3">Data Kredit</h5>
							@include ('pengajuan.permohonan.kredit.form')
							<div class="clearfix">&nbsp;</div>
							<a href="#nasabah" class="btn btn-primary float-right mr-3" data-toggle="tab" role="tab" aria-controls="nasabah" aria-expanded="true">Selanjutnya</a>
						</div>

						<!-- data nasabah -->
						<div class="tab-pane fade" id="nasabah" role="tabpanel">
							<h5 class="text-gray mb-4 pl-3">Data Nasabah &amp; Keluarga</h5>
							@include ('pengajuan.permohonan.nasabah.form')

							<div class="clearfix">&nbsp;</div>
							<a href="#kredit" class="btn btn-primary btn-outline float-left ml-3" data-toggle="tab" role="tab" aria-controls="kredit" aria-expanded="true">Sebelumnya</a>
							<a href="#jaminan" class="btn btn-primary float-right mr-3" data-toggle="tab" role="tab" aria-controls="jaminan" aria-expanded="true">Selanjutnya</a>
						</div>	

						<div class="tab-pane fade" id="jaminan" role="tabpanel">
							<h5 class="text-gray mb-4 pl-3">Data Jaminan</h5>
							<!-- table jaminan kendaraan -->
							@include ('pengajuan.permohonan.jaminan_kendaraan.components.table')

							<!-- table jaminan tanah bangunan -->
							@include ('pengajuan.permohonan.jaminan_tanah_bangunan.components.table')
							
							<div class="clearfix">&nbsp;</div>
							<a href="#nasabah" class="btn btn-primary float-left ml-4" data-toggle="tab" role="tab" aria-controls="nasabah" aria-expanded="true">Sebelumnya</a>
							{!! Form::bsSubmit('Ajukan Permohonan', ['class' => 'btn btn-primary float-right mr-3']) !!}
						</div>
					</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
	
<!--///////////////////////
	/// MODAL 		///////
 	/////////////////////// -->
 	<!-- jaminan kendaraan -->
	@component ('bootstrap.modal', ['id' => 'jaminan-kendaraan'])
		@slot ('title')
			Jaminan Kendaraan
		@endslot

		@slot ('body')
			@include ('pengajuan.permohonan.jaminan_kendaraan.components.form')	
		@endslot

		@slot ('footer')
			<a href="#" data-dismiss="modal" class="btn btn-default">Batal</a>
			<a href="#" class="btn btn-primary add" data-modal="add" data-dismiss="modal" data-id="jaminan-kendaraan" data-input="['jenis', 'merk', 'tipe', tahun', 'nomor_bpkb', nilai_jaminan', 'tahun_perolehan']" data-target="#">Tambahkan</a>
		@endslot
	@endcomponent

	<!-- jaminan tanah & bangunan -->
	@component ('bootstrap.modal', ['id' => 'jaminan-tanah-bangunan'])
		@slot ('title')
			Jaminan Tanah &amp; Bangunan
		@endslot

		@slot ('body')
			@include ('pengajuan.permohonan.jaminan_tanah_bangunan.components.form')	
		@endslot

		@slot ('footer')
			<a href="#" data-dismiss="modal" class="btn btn-default">Batal</a>
			<a href="#" class="btn btn-primary add" data-modal="add">Tambahkan</a>
		@endslot
	@endcomponent

@endpush

@push('submenu')
	<div class="container-fluid bg-light" style="background-color: #eee !important;">
		<div class="row">
			<div class="col">
				<nav class="nav">
					<a href="#" class="nav-link text-secondary">Simulasi Kredit</a>
				</nav>
			</div>
		</div>
	</div>
@endpush

@push ('js')
	<script>
		inputKendaraan = ['jenis', 'merk', 'tipe', 'tahun', 'nomor_bpkb', 'nilai_jaminan', 'tahun_perolehan']; 
		elementID = $(document.getElementById('clone-kendaraan'));
		
		function templateClone() {
		}

		function getData () {

		}

		$('.add').on('click', function (e) {
			e.preventDefault();
			dataInput = $(this).attr('data-input');
			// dataInput = JSON.parse('"' + dataInput + '"');
			dataId = $(this).attr('data-id');

			for (x=0; x<inputKendaraan.length; x++) {
				elClone = elementID.clone();
				dataForm = $(document.getElementById(dataId)).find('[name="' + inputKendaraan[x] + '"]').val();

				elClone.find('.' + inputKendaraan[x]).val(dataForm);
				elClone.find('name["' + inputKendaraan[x] + '"]').val(dataForm);

				// console.log($(document.getElementById(dataId)).find('[name="' + inputKendaraan[x] +'"]'));
				
				
			}
			// $.map(dataInput, function(k, v) {
				// $(document.getElementById(dataInput)).find('input[name="'  '"]')
			// });
		});

		$('.modal').on('hide.bs.modal', function(e) {
			el = $(this).find('input');
			console.log({ elemnent: e, find: el});
		});
	</script>
@endpush