<div class="container-fluid bg-white" style="border-bottom: 2px solid #f5f5f5;">
	<div class="row">
		<div class="col pl-0 pr-0">
			<nav class="nav m-2">
				<a href="{{ route('home', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="nav-link">
				<i class="fa fa-tachometer"></i>&nbsp;&nbsp;Dashboard</a>

				@if(array_intersect($acl_menu['pengajuan.pengajuan'], $scopes->scopes))
				<a href="{{ route('pengajuan.index', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="nav-link">
				<i class="fa fa-paper-plane"></i>&nbsp;&nbsp;Pengajuan</a>
				@elseif(array_intersect($acl_menu['pengajuan.putusan'], $scopes->scopes))
				<a href="{{ route('putusan.index', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="nav-link">
				<i class="fa fa-paper-plane"></i>&nbsp;&nbsp;Pengajuan</a>
				@else
				<a href="#" class="nav-link disabled"><i class="fa fa-paper-plane"></i>&nbsp;&nbsp;Pengajuan</a>
				@endif
				
				@if(array_intersect($acl_menu['kredit.kredit'], $scopes->scopes))
				<a href="{{ route('kredit.index', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="nav-link">
				<i class="fa fa-credit-card-alt"></i>&nbsp;&nbsp;Kredit</a>
				@elseif(array_intersect($acl_menu['kredit.tunggakan'], $scopes->scopes))
				<a href="{{ route('tunggakan.index', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="nav-link">
				<i class="fa fa-credit-card-alt"></i>&nbsp;&nbsp;Kredit</a>
				@else
				<a href="#" class="nav-link disabled"><i class="fa fa-credit-card-alt"></i>&nbsp;&nbsp;Kredit</a>
				@endif

				@if(array_intersect($acl_menu['keuangan'], $scopes->scopes))
				<a href="{{ route('jurnal.index', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="nav-link">
				<i class="fa fa-line-chart"></i>&nbsp;&nbsp;Keuangan</a>
				@else
				<a href="#" class="nav-link disabled"><i class="fa fa-line-chart"></i>&nbsp;&nbsp;Keuangan</a>
				@endif

				@if(array_intersect($acl_menu['kantor'], $scopes->scopes))
				<a href="{{ route('kantor.index', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="nav-link">
				<i class="fa fa-briefcase"></i>&nbsp;&nbsp;Kantor</a>
				@else
				<a href="#" class="nav-link disabled"><i class="fa fa-briefcase"></i>&nbsp;&nbsp;Kantor</a>
				@endif

				@if(array_intersect($acl_menu['inspektor'], $scopes->scopes))
				<a href="{{ route('audit.index', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="nav-link">
				<i class="fa fa-user-secret"></i>&nbsp;&nbsp;Inspektor</a>
				@else
				<a href="#" class="nav-link disabled"><i class="fa fa-user-secret"></i>&nbsp;&nbsp;Inspektor</a>
				@endif

				@if(array_intersect($acl_menu['test'], $scopes->scopes))
				<a href="{{ route('jp.index', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="nav-link">
				<i class="fa fa-gavel"></i>&nbsp;&nbsp;Test</a>
				@else
				<a href="#" class="nav-link disabled">
				<i class="fa fa-gavel"></i>&nbsp;&nbsp;Test</a>
				@endif
			</nav>
		</div>
	</div>
</div>