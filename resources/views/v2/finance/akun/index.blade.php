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
				@slot('header')
					<h5 class="py-2 pl-3 mb-0">&nbsp;&nbsp;AKUN</h5>
				@endslot
				@slot('body')
					<table class="table table-bordered">
						<thead>
							<tr>
								<th class="text-left">Aktiva</th>
								<th class="text-right">Pasiva</th>
							</tr>
						</thead>
						<tbody>
							@foreach(range(0, $counter-1) as $k)
							<tr>
								<td class="text-left">{{$aktiva[$k]['akun']}}</td>
								<td class="text-right">{{$pasiva[$k]['akun']}}</td>
							</tr>
							@endforeach
						</tbody>
						<tfoot>
							<tr>
								<td class="text-left"><a  class="nav-link toggle_akun" data-toggle="modal" data-target="#akun" href="#" data-action="{{route('akun.store', ['kantor_aktif_id' => $kantor_aktif['id'], 'is_pasiva' => false])}}">Tambah Akun Aktiva</a></td>
								<td class="text-right"><a  class="nav-link toggle_akun" data-toggle="modal" data-target="#akun" href="#" data-action="{{route('akun.store', ['kantor_aktif_id' => $kantor_aktif['id'], 'is_pasiva' => true])}}">Tambah Akun Pasiva</a></td>
							</tr>
						</tfoot>
					</table>
				@endslot
			@endcomponent
		</div>
	</div>
	

	@component ('bootstrap.modal', ['id' => 'akun', 'form' => true, 'method' => 'post', 'url' => route('akun.store', ['kantor_aktif_id' => $kantor_aktif['id']]) ])
		@slot ('title')
			Tambahkan Akun
		@endslot

		@slot ('body')
			<p>Akun akan terdaftar untuk kebutuhan jurnal</p>

			{!! Form::bsText('Kode Akun', 'kode_akun', null, ['class' => 'form-control', 'placeholder' => 'BANK.BCA.091237']) !!}
			{!! Form::bsText('Nama Akun', 'akun', null, ['class' => 'form-control', 'placeholder' => 'Bank BCA 091237 Malang']) !!}
			{!! Form::hidden('kantor_aktif_id', $kantor_aktif['id']) !!}
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

@push('js')
	<script type="text/javascript">
		//MODAL PARSE DATA ATTRIBUTE//
		$("a.toggle_akun").on("click", parsingAttributeModalAkun);

		function parsingAttributeModalAkun(){
			$('#akun').find('form').attr('action', $(this).attr("data-action"));
		}
	</script>
@endpush