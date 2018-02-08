<div class="card {{ $class }} border-0 box-shadow h-100">
	@if ($image_url)
		<img class="card-img-top" src="{{ $image_url }}">
	@endif

	@if ($pre)
		{!! $pre !!}
	@endif

	@if ($header)
		<div class="card-header p-1 bg-light">
			{!! $header !!}
		</div>
	@endif

	@if ($title || $body || $footer)
		<div class="card-body">
			@if ($title)
				<div class="card-title">{{ $title }}</div>
			@endif
			@if ($body)
				<p class="card-text">
					{!! $body !!}
				</p>
			@endif
			@if ($footer)
				{!! $footer !!}
			@endif
		</div>
	@endif

	{!! $slot !!}
</div>