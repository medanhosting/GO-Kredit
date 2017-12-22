@inject('idr', 'App\Service\UI\IDRTranslater')

@push('main')
	<div class="container bg-white bg-shadow p-4">
		<div class="row">
			<div class="col">
				<h4 class='mb-4 text-style text-secondary'>
					<span class="text-uppercase">Daftar Angsuran</span> 
					<small><small>@if($angsuran->currentPage() > 1) Halaman {{$angsuran->currentPage()}} @endif</small></small>
				</h4>
				<div class="row">
					<div class="col-12">
						<form action="{{route('kredit.angsuran.index', request()->all())}}" method="GET">
							 <div class="input-group">
							 	@foreach(request()->all() as $k => $v)
							 		@if(!str_is($k, 'q'))
								 		<input type="hidden" name="{{$k}}" value="{{$v}}">
							 		@endif
							 	@endforeach
								<input type="text" name="q" class="form-control" placeholder="cari nama nasabah atau nomor kredit" value="{{request()->get('q')}}">
								<span class="input-group-btn">
									<button class="btn btn-secondary" type="submit" style="background-color:#fff;color:#aaa;border-color:#ccc;border-radius: 0px">Go!</button>
								</span>
							</div>
						</form>
					</div>
				</div>
				<div class="clearfix">&nbsp;</div>
				<table class="table table-hover">
					<thead>
						<tr>
							<th>#</th>
							<th>Nasabah</th>
							<th>Total Hutang</th>
							<th>Sisa Angsuran</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						@forelse($angsuran as $k => $v)
							<tr @if($v['is_tunggakan']) class="bg-danger" @endif>
								<td>
									{{$v['nomor_kredit']}}
								</td>
								<td>
									{{$v['kredit']['nasabah']['nama']}}
								</td>
								<td>
									{{$idr->formatMoneyTo($v['total_hutang'])}}
								</td>
								<td>
									{{$idr->formatMoneyTo($v['sisa_angsuran'])}}
								</td>
								<td>
									<a href="{{route('kredit.angsuran.show', ['id' => $v['nomor_kredit'], 'kantor_aktif_id' => $v['kredit']['kode_kantor']])}}">
										Lihat
									</a>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="5">
									<p>Data tidak tersedia, silahkan pilih Koperasi/BPR lain</p>
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush

@push ('js')
@endpush