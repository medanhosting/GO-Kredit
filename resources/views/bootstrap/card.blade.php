<div class="card {{ isset($class) ? $class : '' }} border-0 box-shadow">
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
			@if (!is_null($title))
				<div class="card-title">{{ $title }}</div>
			@endif
			@if (!is_null($body))
					{!! $body !!}
			@endif
			@if (!is_null($footer))
				{!! $footer !!}
			@endif
		</div>
	@endif

	{!! $slot !!}
</div>