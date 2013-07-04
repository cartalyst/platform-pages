@extends('templates/default')

{{-- Page title --}}
@section('title', trans("platform/pages::general.{$pageSegment}.title", array('page' => ! empty($page) ? $page->name : null)))

{{-- Queue Assets --}}
{{ Asset::queue('redactor', 'styles/css/vendor/imperavi/redactor.css', 'style') }}
{{ Asset::queue('redactor', 'js/vendor/imperavi/redactor.js', 'jquery') }}
{{ Asset::queue('slugify', 'js/vendor/platform/slugify.js', 'jquery') }}
{{ Asset::queue('validate', 'js/vendor/platform/validate.js', 'jquery') }}
{{ Asset::queue('pages', 'platform/pages::js/pages.js', 'jquery') }}
{{ Asset::queue('redactor-editor', 'platform/pages::js/editor.js', 'redactor') }}

{{-- Inline Styles --}}
@section('styles')
@parent
@stop

{{-- Inline Scripts --}}
@section('scripts')
@parent
<script>
	H5F.setup(document.getElementById('page-form'));
</script>
@stop

{{-- Page content --}}
@section('content')
<form id="page-form" class="form-horizontal" action="{{ Request::fullUrl() }}" method="POST" accept-char="UTF-8" autocomplete="off">

	{{-- CSRF Token --}}
	<input type="hidden" name="_token" value="{{ csrf_token() }}">

	<header class="page__header">

		<div class="page__actions">
			<h1>
				<a class="icon-reply" href="{{ URL::toAdmin('pages') }}"></a> {{ trans("platform/pages::general.{$pageSegment}.title", array('page' => ! empty($page) ? $page->name : null)) }}
			</h1>
		</div>

	</header>

	<section class="page__content">

		<fieldset>
			<legend>{{ trans("platform/pages::form.{$pageSegment}.legend") }}</legend>

			{{-- Name --}}
			<div class="control-group{{ $errors->first('name', ' error') }}" required>
				<label class="control-label" for="name">{{ trans('platform/pages::form.name') }}</label>
				<div class="controls">
					<input type="text" name="name" id="name" value="{{{ Input::old('name', ! empty($page) ? $page->name : null) }}}" placeholder="{{ trans('platform/pages::form.name_help') }}" required>
					{{ $errors->first('name', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			{{-- Slug --}}
			<div class="control-group{{ $errors->first('slug', ' error') }}" required>
				<label class="control-label" for="slug">{{ trans('platform/pages::form.slug') }}</label>
				<div class="controls">
					<input type="text" name="slug" id="slug" value="{{{ Input::old('slug', ! empty($page) ? $page->slug : null) }}}" placeholder="{{ trans('platform/pages::form.slug_help') }}" required>
					{{ $errors->first('slug', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			{{-- URI --}}
			<div class="control-group{{ $errors->first('uri', ' error') }}" required>
				<label class="control-label" for="uri">{{ trans('platform/pages::form.uri') }}</label>
				<div class="controls">
					<div class="input-prepend">
						<span class="add-on">
							{{ str_finish(URL::to('/'), '/') }}
						</span>
						<input type="text" name="uri" id="uri" value="{{{ Input::old('uri', ! empty($page) ? $page->uri : null) }}}" placeholder="{{ trans('platform/pages::form.uri_help') }}" required>
					</div>
					{{ $errors->first('uri', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			{{-- Enabled --}}
			<div class="control-group{{ $errors->first('enabled', ' error') }}" required>
				<label class="control-label" for="enabled">{{ trans('platform/pages::form.status') }}</label>
				<div class="controls">
					<select name="enabled" id="enabled" required>
						<option value="1"{{ Input::old('enabled', ! empty($page) ? (int) $page->enabled : 1) === 1 ? ' selected="selected"' : null }}>{{ trans('general.enabled') }}</option>
						<option value="0"{{ Input::old('enabled', ! empty($page) ? (int) $page->enabled : 1) === 0 ? ' selected="selected"' : null }}>{{ trans('general.disabled') }}</option>
					</select>
					{{ $errors->first('enabled', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			{{-- Type --}}
			<div class="control-group{{ $errors->first('type', ' error') }}" required>
				<label class="control-label" for="type">{{ trans('platform/pages::form.type') }}</label>
				<div class="controls">
					<select name="type" id="type">
						<option value="database"{{ Input::old('type', ! empty($page) ? $page->type : 'database') == 'database' ? ' selected="selected"' : null }}>{{ trans('platform/content::form.database') }}</option>
						<option value="filesystem"{{ Input::old('type', ! empty($page) ? $page->type : 'database') == 'filesystem' ? ' selected="selected"' : null }}>{{ trans('platform/content::form.filesystem') }}</option>
					</select>
					{{ $errors->first('type', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			{{-- Visibility --}}
			<div class="control-group{{ $errors->first('visibility', ' error') }}" required>
				<label for="visibility" class="control-label">{{ trans('platform/pages::form.visibility.legend') }}</label>
				<div class="controls">
					<select name="visibility" id="visibility">
						<option value="always"{{ Input::old('visibility', ! empty($page) ? $page->visibility : 'always') == 'always' ? ' selected="selected"' : null }}>{{ trans('platform/pages::form.visibility.always') }}</option>
						<option value="logged_in"{{ Input::old('visibility', ! empty($page) ? $page->visibility : 'always') == 'logged_in' ? ' selected="selected"' : null }}>{{ trans('platform/pages::form.visibility.logged_in') }}</option>
						<option value="admin"{{ Input::old('visibility', ! empty($page) ? $page->visibility : 'always') == 'admin' ? ' selected="selected"' : null }}>{{ trans('platform/pages::form.visibility.admin') }}</option>
					</select>
					{{ $errors->first('visibility', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			{{-- Groups --}}
			<div class="control-group{{ $errors->first('groups', ' error') }}" required>
				<label for="groups" class="control-label">{{ trans('platform/pages::form.groups') }}</label>
				<div class="controls">
					<select name="groups[]" id="groups[]" multiple="multiple">
					@foreach ($groups as $group)
						<option value="{{ $group->id }}"{{ array_key_exists($group->id, $pageGroups) ? ' selected="selected"' : null }}>{{ $group->name }}</option>
					@endforeach
					</select>
					{{ $errors->first('groups', '<span class="help-inline">:message</span>') }}
				</div>
			</div>


			<div class="type-database{{ Input::old('type', ! empty($page) ? $page->type : 'database') == 'filesystem' ? ' hide' : null }}">

				{{-- Templates --}}
				<div class="control-group{{ $errors->first('template', ' error') }}" required>
					<label class="control-label" for="template">{{ trans('platform/pages::form.template') }}</label>
					<div class="controls">
						<select name="template" id="template" required>
						@foreach ($templates as $value => $name)
							<option value="{{ $value }}"{{ Input::old('template', ! empty($page) ? $page->template : null) == $value ? ' selected="selected"' : null}}>{{ $name }}</option>
						@endforeach
						</select>
						{{ $errors->first('template', '<span class="help-inline">:message</span>') }}
					</div>
				</div>

				{{-- Section --}}
				<div class="control-group{{ $errors->first('section', ' error') }}" required>
					<label class="control-label" for="section">{{ trans('platform/pages::form.section') }}</label>
					<div class="controls">
						<div class="input-prepend">
							<i class="add-on">@</i>
							<input type="text" name="section" value="{{{ Input::old('section', ! empty($page) ? $page->section : null) }}}" placeholder="{{ trans('platform/pages::form.section_help') }}">
						</div>
						{{ $errors->first('section', '<span class="help-inline">:message</span>') }}
					</div>
				</div>

				{{-- Value --}}
				<div class="control-group{{ $errors->first('value', ' error') }}" required>
					<label class="control-label" for="value">{{ trans('platform/pages::form.value') }}</label>
					<div class="controls">
						<textarea rows="10" name="value" id="value"{{{ Input::old('value', ! empty($page) ? $page->type : null) == 'database' ? ' required' : null }}}>{{ Input::old('value', ! empty($page) ? $page->value : null) }}</textarea>
						{{ $errors->first('value', '<span class="help-inline">:message</span>') }}
					</div>
				</div>

			</div>

			<div class="type-filesystem{{ Input::old('type', ! empty($page) ? $page->type : 'database') == 'database' ? ' hide' : null }}">

				{{-- File --}}
				<div class="control-group{{ $errors->first('file', ' error') }}" required>
					<label class="control-label" for="file">{{ trans('platform/pages::form.file') }}</label>
					<div class="controls">
						<select name="file" id="file" required>
						@foreach ($files as $value => $name)
							<option value="{{ $value }}"{{ Input::old('file', ! empty($page) ? $page->file : null) == $value ? ' selected="selected"' : null}}>{{ $name }}</option>
						@endforeach
						</select>
						{{ $errors->first('file', '<span class="help-inline">:message</span>') }}
					</div>
				</div>

			</div>

		</fieldset>

	</section>

	<footer class="page__footer">

		<nav class="actions actions--right">
			<ul class="navigation navigation--inline-circle">
				@if( ! empty($page) and $pageSegment != 'copy')
				<li>
					<a class="danger tip" data-placement="top" data-toggle="modal" data-target="#platform-modal-confirm" href="{{ URL::toAdmin("pages/delete/{$page->slug}") }}" title="{{ trans('button.delete') }}"><i class="icon-trash"></i></a>
				</li>
				<li>
					<a class="tip" data-placement="top" href="{{ URL::toAdmin("pages/copy/{$page->slug}") }}" title="{{ trans('button.copy') }}"><i class="icon-copy"></i></a>
				</li>
				@endif
				<li>
					<button class="tip" data-placement="top" title="{{ trans('button.save') }}" type="submit"><i class="icon-save"></i></button>
				</li>
			</ul>
		</nav>

	</footer>
</form>
@stop
