<div class="row">
	<div class="col">
		<h5 class="pb-4">Kerabat/Keluarga</h5>
	</div>
</div>
@isset ($permohonan['nasabah']['keluarga'])
	<table class="table table-sm">
		<thead class="thead-default">
			<tr>
				<th>#</th>
				<th>Hubungan</th>
				<th>Nama</th>
				<th>NIK</th>
				<th>Telepon</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			@foreach ($permohonan['nasabah']['keluarga'] as $k => $v)
				<tr>
					<td>{{ $k+1 }}</td>
					@foreach ($v as $k2 => $v2)
						<td class="text-capitalize">{{ $v2 }}</td>
					@endforeach
					<td>
						<a href="#" class="text-primary ml-2 mr-2"><i class="fa fa-edit"></i></a>
						<a href="#" class="text-danger ml-2 mr-2"><i class="fa fa-trash"></i></a>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
@endisset

@empty ($permohonan['nasabah']['keluarga'])
	<table class="table table-sm">
		<thead class="thead-default">
			<tr>
				<th>#</th>
				<th>Hubungan</th>
				<th>Nama</th>
				<th>NIK</th>
				<th>Telepon</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="6" class="text-center">Belum ada data keluarga disimpan</td>
			</tr>
		</tbody>
	</table>
@endempty