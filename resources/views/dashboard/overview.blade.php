@push('main')
	{{-- @if (isset($scopes))
		@foreach ($scopes as $key => $value)
			@if ($value['scope'] == 'pengajuan')
				<div class="container bg-white p-4 bg-menu">
					<div class="row">
						<div class="col">
							<h4 class="mb-4 text-style text-uppercase">Pengajuan Kredit</h4>
						</div>
					</div>
					<div class="row">
						<div class="col">&nbsp;</div>
							@foreach ($value['features'] as $key2 => $value2)
								<div class="col">
									<div class="card text-center border-0">
										<div class="card-body p-2">
											<a href="#" class="card-link text-style text-capitalize">
												<i class="d-block fa fa-gear fa-2x pb-2"></i>
												{{ $value2['scope'] }}
											</a>
										</div>
									</div>
								</div>
									@if (!empty($value2['features']))
										<div class="col">
											@foreach ($value2['features'] as $key3 => $value3)
												<div class="row">
													<div class="col">
														<div class="card text-center border-0">
															<div class="card-body p-2">
																<a href="#" class="card-link text-style text-capitalize">
																	<i class="d-block fa fa-gear fa-2x pb-2"></i>
																	{{ $value3['scope'] }}
																</a>
															</div>
														</div>
														@if (!empty($value3['features']))
															</div>

															<div class="row">
																@foreach ($value3['features'] as $key4 => $value4)
																	<div class="col">
																		<div class="card text-center border-0">
																			<div class="card-body p-2">
																				<a href="#" class="card-link text-style text-capitalize">
																					<i class="d-block fa fa-gear fa-2x pb-2"></i>
																					{{ $value4['scope'] }}
																				</a>
																			</div>
																		</div>
																	</div>
																@endforeach
															</div>
														@endif
													</div>
												</div>
											@endforeach
										</div>
									@endif
								
							@endforeach
						<div class="col">&nbsp;</div>
					</div>
				@endif
			</div>
		@endforeach
	@endif --}}
	<div class="container bg-white p-4 bg-shadow">
		<div class="row">
			<div class="col">
				<h4 class='mb-4 text-style text-uppercase text-secondary'>
					Pengajuan Kredit
				</h4>
			</div>
		</div>
		<div class="row align-items-center">
			<div class="col">
				<div class="card text-center border-0" style="height: 100px;">
					<div class="card-body p-2">
						<a href="{{ route('pengajuan.pengajuan.index', ['status' => 'permohonan', 'kantor_aktif_id' => $kantor_aktif_id]) }}" class="card-link text-style">
							<i class="d-block fa fa-gear fa-2x"></i>
							Permohonan
						</a>
					</div>
				</div>
			</div>
			<div class="arrow"><i class="fa fa-arrow-right fa-2x"></i></div>
			<div class="col">
				<div class="card text-center border-0" style="height: 100px;">
					<div class="card-body p-2">
						<a href="{{ route('pengajuan.pengajuan.index', ['status' => 'survei', 'kantor_aktif_id' => $kantor_aktif_id]) }}" class="card-link text-style">
							<i class="d-block fa fa-home fa-2x"></i>
							Survei
						</a>
					</div>
				</div>
			</div>
			<div class="arrow"><i class="fa fa-arrow-right fa-2x"></i></div>
			<div class="col">
				<div class="card text-center border-0" style="height: 100px;">
					<div class="card-body p-2">
						<a href="{{ route('pengajuan.pengajuan.index', ['status' => 'analisa', 'kantor_aktif_id' => $kantor_aktif_id]) }}" class="card-link text-style">
							<i class="d-block fa fa-home fa-2x"></i>
							Analisa
						</a>
					</div>
				</div>
			</div>
			<div class="arrow"><i class="fa fa-arrow-right fa-2x"></i></div>
			<div class="col">
				<div class="card text-center border-0" style="height: 100px;">
					<div class="card-body p-2">
						<a href="{{ route('pengajuan.pengajuan.index', ['status' => 'analisa', 'kantor_aktif_id' => $kantor_aktif_id]) }}" class="card-link text-style">
							<i class="d-block fa fa-home fa-2x"></i>
							Keputusan
						</a>
					</div>
				</div>
			</div>
			<div class="col">
				<div class="row">
					<div class="arrow text-gray-dark"><i class="fa fa-arrow-right fa-2x"></i></div>
					<div class="col-12">
						<div class="card text-center border-0" style="height: 100px;">
							<div class="card-body p-2">
								<a href="{{ route('pengajuan.pengajuan.index', ['status' => 'setuju', 'kantor_aktif_id' => $kantor_aktif_id]) }}" class="card-link text-style">
									<i class="d-block fa fa-home fa-2x"></i>
									Setujui
								</a>
							</div>
						</div>
					</div>
					<div class="arrow"><i class="fa fa-arrow-right fa-2x"></i></div>
					<div class="col-12">
						<div class="card text-center border-0" style="height: 100px;">
							<div class="card-body p-2">
								<a href="{{ route('pengajuan.pengajuan.index', ['status' => 'tolak', 'kantor_aktif_id' => $kantor_aktif_id]) }}" class="card-link text-style">
									<i class="d-block fa fa-home fa-2x"></i>
									Tolak
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col">
				<div class="row align-items-start">
					<div class="arrow"><i class="fa fa-arrow-right fa-2x"></i></div>
					<div class="col-12">
						<div class="card text-center border-0" style="height: 100px;">
							<div class="card-body p-2">
								<a href="{{ route('pengajuan.pengajuan.index', ['status' => 'realisasi', 'kantor_aktif_id' => $kantor_aktif_id]) }}" class="card-link text-style">
									<i class="d-block fa fa-home fa-2x"></i>
									Realisasi
								</a>
							</div>
						</div>
					</div>
					<div class="arrow"><i class="fa fa-arrow-right fa-2x"></i></div>
					<div class="col-12">
						<div class="card text-center border-0" style="height: 100px;">
							<div class="card-body p-2">
								<a href="{{ route('pengajuan.pengajuan.index', ['status' => 'expired', 'kantor_aktif_id' => $kantor_aktif_id]) }}" class="card-link text-style">
									<i class="d-block fa fa-home fa-2x"></i></h4>
									Expired
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endpush