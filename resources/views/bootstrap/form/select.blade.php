<div class="form-group">
	@if ($label)
		{!! Form::label('', $label, ['class' => 'text-uppercase mb-1']) !!}
	@endif
	{!! Form::select($name, $options, $value, array_merge(['class' => 'custom-select ' . ($errors->has($name)  && $show_error ? 'is-invalid' : '')], ($attributes ? $attributes : []))) !!}
	@if ($errors->has($name) && $show_error)
		<div class="invalid-feedback">
			@foreach ($errors->get($name) as $v)
				{{ $v }}<br>
			@endforeach
		</div>
	@endif
</div>