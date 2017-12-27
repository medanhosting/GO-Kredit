<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
	<li class="nav-item">
		<a class="nav-link active {{$is_survei_character_tab}}" data-toggle="tab" href="#character" role="tab">
			Karakter
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link {{$is_survei_condition_tab}}" data-toggle="tab" href="#condition" role="tab">
			Kondisi
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link {{$is_survei_capacity_tab}}" data-toggle="tab" href="#capacity" role="tab">
			Kapasitas
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link {{$is_survei_capital_tab}}" data-toggle="tab" href="#capital" role="tab">
			Kapital
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link {{$is_survei_collateral_tab}}" data-toggle="tab" href="#collateral" role="tab">
			Kolateral
		</a>
	</li>
</ul>

@if(count($survei))
<!-- Tab panes -->
<div class="tab-content">
	<div class="tab-pane fade show active {{$is_survei_character_tab}}" id="character" role="tabpanel">
		<div class="clearfix">&nbsp;</div>
		@foreach($survei['character']['dokumen_survei']['character'] as $k => $v )
			@if(is_array($v))
				@foreach($v as $k2 => $v2)
					<div class="row mb-1">
						<div class="col-sm-4 text-right">
							<p class="text-secondary mb-1">
								{{strtoupper(str_replace('_', ' ', $k))}} {{strtoupper(str_replace('_', ' ', $k2))}}
							</p>
						</div> 
						<div class="col-sm-8 text-left">
							<p class="mb-1">
								{{ ucfirst(strtolower(str_replace('_', ' ', $v2))) }}
							</p>
						</div> 
					</div>
				@endforeach
			@else
				<div class="row">
					<div class="col-sm-4 text-right">
						<p class="text-secondary mb-1">
							{{strtoupper(str_replace('_', ' ', $k))}}
						</p>
					</div> 
					<div class="col-sm-8 text-left">
						<p class="mb-1">
							{{ ucfirst(strtolower(str_replace('_', ' ', $v))) }}
						</p>
					</div> 
				</div>
			@endif
		@endforeach
	</div>

	<div class="tab-pane fade {{$is_survei_condition_tab}}" id="condition" role="tabpanel">
		<div class="clearfix">&nbsp;</div>
		@foreach($survei['condition']['dokumen_survei']['condition'] as $k => $v )
			@if(is_array($v))
				@foreach($v as $k2 => $v2)
					<div class="row mb-1">
						<div class="col-sm-4 text-right">
							<p class="text-secondary mb-1">
								{{strtoupper(str_replace('_', ' ', $k))}} {{strtoupper(str_replace('_', ' ', $k2))}}
							</p>
						</div> 
						<div class="col-sm-8 text-left">
							<p class="mb-1">
								{{ ucfirst(strtolower(str_replace('_', ' ', $v2))) }}
							</p>
						</div> 
					</div>
				@endforeach
			@else
				<div class="row">
					<div class="col-sm-4 text-right">
						<p class="text-secondary mb-1">
							{{strtoupper(str_replace('_', ' ', $k))}}
						</p>
					</div> 
					<div class="col-sm-8 text-left">
						<p class="mb-1">
							{{ ucfirst(strtolower(str_replace('_', ' ', $v))) }}
						</p>
					</div> 
				</div>
			@endif
		@endforeach
	</div>

	<div class="tab-pane fade {{$is_survei_capacity_tab}}" id="capacity" role="tabpanel">
		<div class="clearfix">&nbsp;</div>
		@foreach($survei['capacity']['dokumen_survei']['capacity'] as $k => $v )
			<div class="row">
				<div class="col-sm-12 text-left">
					@if(is_array($v))
						<p class="text-secondary mt-2"><strong><u>{{ strtoupper(str_replace('_', ' ', $k)) }}</u></strong></p>
						@foreach($v as $k2 => $v2)
							@if(is_array($v2))
								@foreach($v2 as $k3 => $v3)
									<div class="row">
										<div class="col-sm-4 text-right">
											<p class="text-secondary mb-1">
												{{strtoupper(str_replace('_', ' ', $k3))}}
											</p>
										</div> 
										<div class="col-sm-8 text-left">
											<p class="mb-1">
												{{ ucfirst(strtolower(str_replace('_', ' ', $v3))) }}
											</p>
										</div> 
									</div>
								@endforeach
							@else
								<div class="row">
									<div class="col-sm-4 text-right">
										<p class="text-secondary mb-1">
											{{strtoupper(str_replace('_', ' ', $k2))}}
										</p>
									</div> 
									<div class="col-sm-8 text-left">
										<p class="mb-1">
											{{ ucfirst(strtolower(str_replace('_', ' ', $v2))) }}
										</p>
									</div> 
								</div>
							@endif

							@if ($loop->last)
								<div class="clearfix">&nbsp;</div>
							@endif
						@endforeach
					@else
						<div class="row">
							<div class="col-sm-4 text-right">
								<p class="text-secondary mb-1">
									{{strtoupper(str_replace('_', ' ', $k))}}
								</p>
							</div> 
							<div class="col-sm-8 text-left">
								<p class="mb-1">
									{{ ucfirst(strtolower(str_replace('_', ' ', $v))) }}
								</p>
							</div> 
						</div>
					@endif
				</div> 
			</div>
		@endforeach
	</div>

	<div class="tab-pane fade {{$is_survei_capital_tab}}" id="capital" role="tabpanel">
		<div class="clearfix">&nbsp;</div>
		@foreach($survei['capital']['dokumen_survei']['capital'] as $k => $v )
			<div class="row">
				<div class="col-sm-12 text-left">
					<p class="text-secondary mt-2"><strong><u>{{ strtoupper(str_replace('_', ' ', $k)) }}</u></strong></p>
					@foreach($v as $k2 => $v2)
						@if(is_array($v2))
							@foreach($v2 as $k3 => $v3)
								<div class="row">
									<div class="col-sm-4 text-right">
										<p class="text-secondary mb-1">
											{{strtoupper(str_replace('_', ' ', $k3))}}
										</p>
									</div> 
									<div class="col-sm-8 text-left">
										<p class="mb-1">
											@if (in_array($k3, ['lama_menempati', 'masa_sewa', 'lama_angsuran', 'lama_usaha', 'jangka_waktu']))
												{{ ucfirst(strtolower(str_replace('_', ' ', $v3))) }} Tahun
											@else
												{{ ucfirst(strtolower(str_replace('_', ' ', $v3))) }}
											@endif
										</p>
									</div> 
								</div>
							@endforeach
						@else
							<div class="row">
								<div class="col-sm-4 text-right">
									<p class="text-secondary mb-1">
										{{strtoupper(str_replace('_', ' ', $k2))}}
									</p>
								</div> 
								<div class="col-sm-8 text-left">
									<p class="mb-1">
										@if (in_array($k2, ['lama_menempati', 'masa_sewa', 'lama_angsuran', 'lama_usaha', 'jangka_waktu']))
											{{ ucfirst(strtolower(str_replace('_', ' ', $v2))) }} Tahun
										@elseif (in_array($k2, ['luas_rumah']))
											{{ ucfirst(strtolower(str_replace('_', ' ', $v2))) }} M<sup>2</sup>
										@elseif (in_array($k2, ['panjang_rumah', 'lebar_rumah']))
											{{ ucfirst(strtolower(str_replace('_', ' ', $v2))) }} M
										@else
											{{ ucfirst(strtolower(str_replace('_', ' ', $v2))) }}
										@endif
									</p>
								</div> 
							</div>
						@endif
					@endforeach
				</div> 
			</div>
		@endforeach
	</div>

	<div class="tab-pane fade {{$is_survei_collateral_tab}}" id="collateral" role="tabpanel">
		<div class="clearfix">&nbsp;</div>
		@foreach($survei['collateral'] as $k0 => $v0 )
			<p class="text-secondary mt-2"><strong><u>{{strtoupper(str_replace('_', ' ', $v0['jenis']))}} {{($k0+1)}} </u></strong></p>
			<div class="row">
				<div class="col-3">
					<p class="text-secondary mb-0">Foto Jaminan</p>
					@foreach($v0['foto']['arsip_foto'] as $k2 => $v2)
						<img src="{{$v2['url']}}"  class="img img-fluid" alt="" style="width: 100%; height: 120px; padding-bottom: 15px;">
					@endforeach
				</div>
				<div class="col-9">
					@foreach($v0['dokumen_survei']['collateral'] as $k => $v )
						@if(!str_is($k, 'jenis'))
							<div class="row">
								<div class="col-sm-12 text-left">
									@if(is_array($v))
										@foreach($v as $k2 => $v2)
											@if(is_array($v2))
												@foreach($v2 as $k3 => $v3)
													<div class="row">
														<div class="col text-right">
															<p class="text-secondary mb-1">
																{{strtoupper(str_replace('_', ' ', $k3))}}
															</p>
														</div> 
														<div class="col-sm-7 text-left">
															<p class="mb-1">
																{{ ucfirst(strtolower(str_replace('_', ' ', $v3))) }}
															</p>
														</div> 
													</div>
												@endforeach
											@else
												<div class="row">
													<div class="col text-right">
														<p class="text-secondary mb-1">
															{{strtoupper(str_replace('_', ' ', $k2))}}
														</p>
													</div> 
													<div class="col-sm-7 text-left">
														<p class="mb-1">
															@if (in_array($k2, ['luas_tanah', 'luas_bangunan']))
																{{ ucfirst(strtolower(str_replace('_', ' ', $v2))) }} M<sup>2</sup>
															@elseif (in_array($k2, ['panjang_tanah', 'lebar_tanah', 'lebar_bangunan', 'panjang_bangunan']))
																{{ ucfirst(strtolower(str_replace('_', ' ', $v2))) }} M
															@else
																{{ ucfirst(strtolower(str_replace('_', ' ', $v2))) }}
															@endif
														</p>
													</div> 
												</div>
											@endif

											@if ($loop->iteration % 6 == 0)
												<div class="clearfix">&nbsp;</div>
											@endif
										@endforeach
									@else
										<div class="row">
											<div class="col-sm-4 text-right">
												<p class="text-secondary mb-1">
													{{strtoupper(str_replace('_', ' ', $k))}}
												</p>
											</div> 
											<div class="col-sm-8 text-left">
												<p class="mb-1">
													{{ ucfirst(strtolower(str_replace('_', ' ', $v))) }}
												</p>
											</div> 
										</div>
									@endif
								</div> 
							</div> 
						@endif
					@endforeach
				</div>
			</div>
		@endforeach
	</div>
</div>

@else
<div class="row">
	<div class="col">
		<p>Tidak ada data display</p>
	</div>
</div>
@endif
