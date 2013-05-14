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
{{ Asset::queue('pages', 'platform/pages::js/pages.js', 'jquery') }}

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
<script>
jQuery(document).ready(function($) {

	$('#name').keyup(function() {
		$('#slug').val($(this).slugify());
	});

});
</script>
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
						<div class="input-prepend">
							<span class="add-on">
								{{ str_finish(URL::to('/'), '/') }}
							</span>
							<input type="text" name="slug" id="slug" value="{{ Input::old('slug', $page->slug) }}" placeholder="@lang('platform/pages::form.slug_help')" required>
						</div>
						{{ $errors->first('slug', '<span class="help-inline">:message</span>') }}
					</div>
				</div>

				{{-- Enabled --}}
				<div class="control-group{{ $errors->first('enabled', ' error') }}" required>
					<label class="control-label" for="enabled">@lang('platform/pages::form.enabled')</label>
					<div class="controls">
						<select name="enabled" id="enabled" required>
							<option value="1">{{ Lang::get('general.yes') }}</option>
							<option value="0">{{ Lang::get('general.no') }}</option>
						</select>
						{{ $errors->first('enabled', '<span class="help-inline">:message</span>') }}
					</div>
				</div>

				{{-- Type --}}
				<div class="control-group{{ $errors->first('type', ' error') }}" required>
					<label class="control-label" for="type">@lang('platform/pages::form.type')</label>
					<div class="controls">
						<select name="type" id="type">
							@foreach ($types as $value => $name)
								<option value="{{ $value }}"{{ $page->type == $value ? ' selected="selected"' : ''}}>{{ $name }}</option>
							@endforeach
						</select>
						{{ $errors->first('type', '<span class="help-inline">:message</span>') }}
					</div>
				</div>

				{{-- Visibility --}}
				<div class="control-group{{ $errors->first('visibility', ' error') }}" required>
					<label for="visibility" class="control-label">@lang('platform/pages::form.visibility')</label>
					<div class="controls">
						<select name="visibility" id="visibility">
							@foreach ($visibilities as $value => $name)
								<option value="{{ $value }}">{{ $name }}</option>
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
							@foreach ($groups as $group)
								<option value="{{ $group->id }}"{{ ($page->groups->find($group->getKey())) ? ' selected="selected"' : '' }}>{{ $group->name }}</option>
							@endforeach
						</select>
						{{ $errors->first('groups', '<span class="help-inline">:message</span>') }}
					</div>
				</div>

				<div class="type-database{{ ($page->type == 'filesystem') ? ' hide' : '' }}">

					{{-- Templates --}}
					<div class="control-group{{ $errors->first('template', ' error') }}" required>
						<label class="control-label" for="template">@lang('platform/pages::form.template')</label>
						<div class="controls">
							<select name="template" id="template" required>
								@foreach ($templates as $value => $name)
									<option value="{{ $value }}"{{ $page->template == $value ? ' selected="selected"' : ''}}>{{ $name }}</option>
								@endforeach
							</select>
							{{ $errors->first('template', '<span class="help-inline">:message</span>') }}
						</div>
					</div>

					{{-- Section --}}
					<div class="control-group{{ $errors->first('section', ' error') }}" required>
						<label class="control-label" for="section">@lang('platform/pages::form.section')</label>
						<div class="controls">
							<div class="input-prepend">
								<i class="add-on">@</i>
								<input type="text" name="section" value="{{ Input::old('section', $page->section) }}" placeholder="@lang('platform/pages::form.section_help')">
							</div>
							{{ $errors->first('section', '<span class="help-inline">:message</span>') }}
						</div>
					</div>

					{{-- Value --}}
					<div class="control-group{{ $errors->first('value', ' error') }}" required>
						<label class="control-label" for="value">@lang('platform/pages::form.value')</label>
						<div class="controls">
							<textarea rows="10" name="value" id="value" required>{{ ($page->type == 'database') ? Input::old('value', $page->value) : '' }}</textarea>
							{{ $errors->first('value', '<span class="help-inline">:message</span>') }}
						</div>
					</div>

				</div>

				<div class="type-filesystem{{ ($page->type == 'database') ? ' hide' : '' }}">

					{{-- File --}}
					<div class="control-group{{ $errors->first('file', ' error') }}" required>
						<label class="control-label" for="file">@lang('platform/pages::form.file')</label>
						<div class="controls">
							<select name="file" id="file" required>
								@foreach ($files as $value => $name)
									<option value="{{ $value }}"{{ $page->file == $value ? ' selected="selected"' : ''}}>{{ $name }}</option>
								@endforeach
							</select>
							{{ $errors->first('file', '<span class="help-inline">:message</span>') }}
						</div>
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
