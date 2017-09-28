<div class="container-fluid bg-light" style="background-color: #eee !important;">
	<div class="row">
		<div class="col">
			<nav class="nav">
				<a href="{{ route('home', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="nav-link text-secondary">Menu Utama</a>
				<a href="{{route('simulasi', ['mode' => 'pa', 'kantor_aktif_id' => $kantor_aktif['id']])}}" class="nav-link text-secondary">Simulasi Kredit PA</a>
				<a href="{{route('simulasi', ['mode' => 'pt', 'kantor_aktif_id' => $kantor_aktif['id']])}}" class="nav-link text-secondary">Simulasi Kredit PT</a>
				<a href="{{route('pengajuan.permohonan.create', ['status' => 'permohonan', 'kantor_aktif_id' => $kantor_aktif['id']])}}" class="nav-link text-secondary">Permohonan Kredit Baru</a>
			</nav>
		</div>
	</div>
</div>