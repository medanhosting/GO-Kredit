<table class="table table-sm table-responsive">
	<thead class="thead-default">
		<tr>
			<th>Ke</th>
			<th>Petugas</th>
			<th>Tanggal</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		@isset ($survei)
			@foreach($survei as $k => $v)
				<tr>
					<td>{{ ($k+1) }}</td>
					<td>{{ $v['surveyor']['nama'] }}</td>
					<td>{{ $v['tanggal'] }}</td>
					<td>
						<a href="#">
							<i class="fa fa-eye"></i>
						</a>
					</td>
				</tr>
			@endforeach
		@endisset

		@empty ($survei)
			<tr>
				<td class="text-center" colspan="4">Belum ada data survei</td>
			</tr>
		@endempty
	</tbody>
</table> 