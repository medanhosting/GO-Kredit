@inject('idr', 'App\Service\UI\IDRTranslater')

@push('main')
	<div class="container bg-white bg-shadow p-4">
		<div class="row">
			<div class="col">
				<h4 class='mb-4 text-style text-secondary'>
					<span class="text-uppercase">Daftar Tunggakan</span> 
					<small><small>@if($tunggakan->currentPage() > 1) Halaman {{$tunggakan->currentPage()}} @endif</small></small>
				</h4>
				<div class="row">
					<div class="col-12">
						<form action="{{route('kredit.penagihan.index', request()->all())}}" method="GET">
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
						<tr class="text-center">
							<th class="text-left">#</th>
							<th class="text-left">Nasabah</th>
							<th>Total Tunggakan</th>
							<th>Jatuh Tempo</th>
							<th>Total Penagihan</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						@forelse($tunggakan as $k => $v)
							<tr class="text-center">
								<td class="text-left">
									{{$v['nomor_kredit']}}
								</td>
								<td class="text-left">
									{{$v['kredit']['nasabah']['nama']}}
								</td>
								<td class="text-right">
									{{$idr->formatMoneyTo($v['tunggakan'])}}
								</td>
								<td>
									{{Carbon\Carbon::createfromformat('d/m/Y H:i', $v['tanggal'])->adddays(\Config::get('kredit.batas_pembayaran_angsuran_hari'))->format('d/m/Y H:i')}}
								</td>
								<td>
									{{$v->kredit->penagihan->count()}}
								</td>
								<td>
									<a href="{{route('kredit.penagihan.show', ['id' => $v['nomor_kredit'], 'kantor_aktif_id' => $kantor_aktif['id']])}}">Buatkan Jadwal Penagihan</a>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="6">
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