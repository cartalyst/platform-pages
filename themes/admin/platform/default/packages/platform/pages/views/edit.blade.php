@extends('templates/default')

@section('title')
{{ Lang::get('platform/pages::general.title') }}
@stop

@section('assets')

@stop

@section('scripts')

@stop

@section('content')
<div class="page-header">
	<h3>
		{{ Lang::get('platform/pages::form.edit.legend') }}

		<small>{{ Lang::get('platform/pages::form.edit.summary') }}</small>

		<div class="pull-right">
			<a href="{{ URL::to(ADMIN_URI . '/pages') }}" class="btn btn-inverse btn-small">{{ Lang::get('button.back') }}</a>
		</div>
	</h3>
</div>

<form class="form-horizontal" action="{{ Request::fullUrl() }}" method="POST" accept-char="UTF-8" autocomplete="off">
	<!-- CSRF Token -->
	<input type="hidden" name="csrf_token" value="{{ csrf_token() }}">

	<!-- Name -->
	<div class="control-group">
		<label class="control-label" for="name">{{ Lang::get('platform/pages::form.name') }}:</label>
		<div class="controls">
			<input type="text" name="name" id="name" value="{{ Input::old('name', $page->name) }}" placeholder="{{ Lang::get('platform/pages::form.name') }}" required>
			<span class="help-block">{{ Lang::get('platform/pages::form.name_help') }}</span>
		</div>
	</div>

	<!-- Slug -->
	<div class="control-group">
		<label class="control-label" for="slug">{{ Lang::get('platform/pages::form.slug') }}:</label>
		<div class="controls">
			<input type="text" name="slug" id="slug" value="{{ Input::old('slug', $page->slug) }}" placeholder="{{ Lang::get('platform/pages::form.slug') }}" required>
			<span class="help-block">{{ Lang::get('platform/pages::form.slug_help') }}</span>
		</div>
	</div>

	<!-- Status -->
	<div class="control-group">
		<label class="control-label" for="status">{{ Lang::get('platform/pages::form.status') }}:</label>
		<div class="controls">
			<select name="status" id="status" required>
				<option value="1">{{ Lang::get('general.enabled') }}</option>
				<option value="0">{{ Lang::get('general.disabled') }}</option>
			</select>
			<span class="help-block">{{ Lang::get('platform/pages::form.status_help') }}</span>
		</div>
	</div>

	<!-- Storage Type -->
	<div class="control-group">
		<label class="control-label" for="type">{{ Lang::get('platform/pages::form.type') }}:</label>
		<div class="controls">
			<select name="type" id="type" required>
			@foreach ($storageTypes as $typeId => $typeName)
				<option value="{{ $typeId }}">{{ $typeName }}</option>
			@endforeach
			</select>
			<span class="help-block">{{ Lang::get('platform/pages::form.status_help') }}</span>
		</div>
	</div>

	<!-- Templates -->
	<div class="control-group">
		<label class="control-label" for="template">{{ Lang::get('platform/pages::form.template') }}:</label>
		<div class="controls">
			<select name="template" id="template" required>
			@foreach ($templates as $templateName => $layouts)
				<optgroup label="{{ $templateName }}">
					@foreach ($layouts as $layout)
					<option value="{{ $layout }}">{{ $layout }}</option>
					@endforeach
				</optgroup>
			@endforeach
			</select>
			<span class="help-block">{{ Lang::get('platform/pages::form.status_help') }}</span>
		</div>
	</div>

	<!-- Visibility -->
	<div class="control-group">
		<label for="visibility" class="control-label">{{ Lang::get('platform/pages::form.visibility') }}:</label>
		<div class="controls">
			<select name="visibility" id="visibility">
				@foreach ($visibility as $visibilityId => $visibilityName)
				<option value="{{ $visibilityId }}">{{ $visibilityName }}</option>
				@endforeach
			</select>
			<span class="help-block">{{ Lang::get('platform/pages::form.visibility_help') }}</span>
		</div>
	</div>

	<!-- Groups -->
	<div class="control-group">
		<label for="groups" class="control-label">{{ Lang::get('platform/pages::form.groups') }}:</label>
		<div class="controls">
			<select name="groups[]" id="groups[]" multiple="multiple">
				@foreach ($groups as $groupId => $groupName)
				<option value="{{ $groupId }}"{{ (array_key_exists($groupId, $pageGroups) ? ' selected="selected"' : '') }}>{{ $groupName }}</option>
				@endforeach
			</select>
			<span class="help-block">{{ Lang::get('platform/pages::form.groups_help') }}</span>
		</div>
	</div>

	<!-- Content -->
	<div class="control-group">
		<label class="control-label" for="value">{{ Lang::get('platform/pages::form.value') }}:</label>
		<div class="controls">
			<textarea rows="10" name="value" id="value" required>{{ Input::old('value', $page->value) }}</textarea>
			<span class="help-block">{{ Lang::get('platform/pages::form.value_help') }}</span>
		</div>
	</div>

	<!-- Form Actions -->
	<div class="form-actions">
		<a class="btn btn-small" href="{{ URL::to(ADMIN_URI . '/pages') }}">{{ Lang::get('button.cancel') }}</a>
		<button class="btn btn-small btn-primary" type="submit">{{ Lang::get('button.update') }}</button>
	</div>
</form>
@stop
