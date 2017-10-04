	@if ($label)
	<div class="row form-group">
		<div class="col-sm-4 text-right">
		{!! Form::label('', $label, ['class' => 'text-uppercase mb-1']) !!}
		</div>
		<div class="col text-left">
	@else
	<div class="form-group">
	@endif
	@if ($append || $prepend)
		<div class='input-group'>
	@endif

	@if ($prepend)
		<div class="input-group-addon">{!! $prepend !!}</div>
	@endif

	@if($errors->has($name)  && $show_error && isset($attributes['class']))
		@php
		$attributes['class'] 	= $attributes['class'].' is-invalid';
		@endphp
	@endif

	{!! Form::text($name, $value, array_merge(['class' => 'form-control ' . (($errors->has($name)  && $show_error) ? 'is-invalid' : 'is-invalid')], ($attributes ? $attributes : []))) !!}

	@if ($append)
		<div class="input-group-addon">{!! $append !!}</div>
	@endif
			
	@if ($append || $prepend)
		</div>
	@endif

	@if ($helper_text)
		<small class="form-text text-muted">{!! $helper_text !!}</small>
	@endif

	@if ($errors->has($name) && $show_error)
		<div class="invalid-feedback">
			@foreach ($errors->get($name) as $v)
				{{ $v }}<br>
			@endforeach
		</div>
	@endif
	@if ($label)
	</div>
	@endif
</div>