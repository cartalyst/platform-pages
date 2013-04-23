@extends('templates/default')

{{-- Page title --}}
@section('title')
@lang('platform/pages::general.update.title') ::
@parent
@stop

{{-- Queue Assets --}}
{{ Asset::queue('redactor', 'platform/content::css/redactor.css', 'style') }}
{{ Asset::queue('redactor', 'platform/content::js/redactor.min.js', 'jquery') }}
{{ Asset::queue('redactor-plugins', 'platform/content::js/redactor-plugins.js', 'redactor') }}
{{ Asset::queue('editor', 'platform/content::js/editor.js', 'media-chooser') }}

{{-- Partial Assets --}}
@section('assets')
@parent
@stop

{{-- Inline Styles --}}
@section('styles')
@parent
@stop

{{-- Inline Scripts --}}
@section('scripts')
@parent
@stop

{{-- Page content --}}
@section('content')
<section id="page-create">

	<header class="clearfix">
		<h1><a class="icon-reply" href="{{ URL::toAdmin('pages') }}"></a> @lang('platform/pages::general.update.title')</h1>
	</header>

	<hr>

	<section class="content">
		<form class="form-horizontal" action="{{ Request::fullUrl() }}" method="POST" accept-char="UTF-8" autocomplete="off">
			{{-- CSRF Token --}}
			<input type="hidden" name="csrf_token" value="{{ csrf_token() }}">

			<fieldset>
				<legend>@lang('platform/pages::form.update.legend')</legend>

				{{-- Name --}}
				<div class="control-group{{ $errors->first('name', ' error') }}" required>
					<label class="control-label" for="name">@lang('platform/pages::form.name')</label>
					<div class="controls">
						<input type="text" name="name" id="name" value="{{ Input::old('name', $page->name) }}" placeholder="@lang('platform/pages::form.name_help')" required>
						{{ $errors->first('name', '<span class="help-inline">:message</span>') }}
					</div>
				</div>

				{{-- Slug --}}
				<div class="control-group{{ $errors->first('slug', ' error') }}" required>
					<label class="control-label" for="slug">@lang('platform/pages::form.slug')</label>
					<div class="controls">
						<input type="text" name="slug" id="slug" value="{{ Input::old('slug', $page->slug) }}" placeholder="@lang('platform/pages::form.slug_help')" required>
						{{ $errors->first('slug', '<span class="help-inline">:message</span>') }}
					</div>
				</div>

				{{-- Status --}}
				<div class="control-group{{ $errors->first('status', ' error') }}" required>
					<label class="control-label" for="status">@lang('platform/pages::form.status')</label>
					<div class="controls">
						<select name="status" id="status" required>
							<option value="1">@lang('general.enabled')</option>
							<option value="0">@lang('general.disabled')</option>
						</select>
						{{ $errors->first('status', '<span class="help-inline">:message</span>') }}
					</div>
				</div>

				{{-- Storage Type --}}
				<div class="control-group{{ $errors->first('type', ' error') }}" required>
					<label class="control-label" for="type">@lang('platform/pages::form.type')</label>
					<div class="controls">
						<select name="type" id="type" required>
						@foreach ($storageTypes as $typeId => $typeName)
							<option value="{{ $typeId }}">{{ $typeName }}</option>
						@endforeach
						</select>
						{{ $errors->first('type', '<span class="help-inline">:message</span>') }}
					</div>
				</div>

				{{-- Templates --}}
				<div class="control-group{{ $errors->first('template', ' error') }}" required>
					<label class="control-label" for="template">@lang('platform/pages::form.template')</label>
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
						{{ $errors->first('template', '<span class="help-inline">:message</span>') }}
					</div>
				</div>

				{{-- Visibility --}}
				<div class="control-group{{ $errors->first('visibility', ' error') }}" required>
					<label for="visibility" class="control-label">@lang('platform/pages::form.visibility')</label>
					<div class="controls">
						<select name="visibility" id="visibility">
							@foreach ($visibility as $visibilityId => $visibilityName)
							<option value="{{ $visibilityId }}">{{ $visibilityName }}</option>
							@endforeach
						</select>
						{{ $errors->first('visibility', '<span class="help-inline">:message</span>') }}
					</div>
				</div>

				{{-- Groups --}}
				<div class="control-group{{ $errors->first('groups', ' error') }}" required>
					<label for="groups" class="control-label">@lang('platform/pages::form.groups')</label>
					<div class="controls">
						<select name="groups[]" id="groups[]" multiple="multiple">
							@foreach ($groups as $groupId => $groupName)
							<option value="{{ $groupId }}"{{ (array_key_exists($groupId, $pageGroups) ? ' selected="selected"' : '') }}>{{ $groupName }}</option>
							@endforeach
						</select>
						{{ $errors->first('groups', '<span class="help-inline">:message</span>') }}
					</div>
				</div>

				{{-- Content --}}
				<div class="control-group{{ $errors->first('value', ' error') }}" required>
					<label class="control-label" for="value">@lang('platform/pages::form.value')</label>
					<div class="controls">
						<textarea rows="10" name="value" id="value" required>{{ Input::old('value', $page->value) }}</textarea>
						{{ $errors->first('value', '<span class="help-inline">:message</span>') }}
					</div>
				</div>

				{{-- Form Actions --}}
				<div class="form-actions">
					<a class="btn btn-small" href="{{ URL::toAdmin('pages') }}">@lang('button.cancel')</a>

					<button class="btn btn-small btn-primary" type="submit">@lang('button.update')</button>
				</div>
			</fieldset>
		</form>

	</section>

</section>
@stop
