<div class="form-group">
	@if ($label)
		{!! Form::label('', $label, ['class' => 'text-uppercase']) !!}
	@endif

	@if ($addon)
		<div class='input-group'>
			<div class="input-group-addon">{{ $addon }}</div>
	@endif

			{!! Form::text($name, $value, array_merge(['class' => 'form-control ' . ($errors->has($name)  && $show_error ? 'is-invalid' : '')], ($attributes ? $attributes : []))) !!}
			
	@if ($addon)
		</div>
	@endif

	
	@if ($errors->has($name) && $show_error)
		<div class="invalid-feedback">
			@foreach ($errors->get($name) as $v)
				{{ $v }}<br>
			@endforeach
		</div>
	@endif
</div>