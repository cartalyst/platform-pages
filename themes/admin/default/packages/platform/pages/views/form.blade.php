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
@stop

{{-- Page content --}}
@section('page')
<form id="page-form" class="form-horizontal" action="{{ Request::fullUrl() }}" method="POST" accept-char="UTF-8" autocomplete="off">

	{{-- CSRF Token --}}
	<input type="hidden" name="_token" value="{{ csrf_token() }}">

	<header class="page__header">

		<div class="page__title">
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
					<input type="text" name="name" id="name" value="{{{ Input::old('name', ! empty($page) ? $page->name : null) }}}" required>
					@if ($errors->has('name'))
					{{ $errors->first('name', '<span class="help-block">:message</span>') }}
					@else
					<span class="help-block">{{ trans('platform/pages::form.name_help') }}</span>
					@endif
				</div>
			</div>

			{{-- Slug --}}
			<div class="control-group{{ $errors->first('slug', ' error') }}" required>
				<label class="control-label" for="slug">{{ trans('platform/pages::form.slug') }}</label>
				<div class="controls">
					<input type="text" name="slug" id="slug" value="{{{ Input::old('slug', ! empty($page) ? $page->slug : null) }}}" required>
					@if ($errors->has('slug'))
					{{ $errors->first('slug', '<span class="help-block">:message</span>') }}
					@else
					<span class="help-block">{{ trans('platform/pages::form.slug_help') }}</span>
					@endif
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
						<input type="text" name="uri" id="uri" value="{{{ Input::old('uri', ! empty($page) ? $page->uri : null) }}}" required>
					</div>
					@if ($errors->has('uri'))
					{{ $errors->first('uri', '<span class="help-block">:message</span>') }}
					@else
					<span class="help-block">{{ trans('platform/pages::form.uri_help') }}</span>
					@endif
				</div>
			</div>

			{{-- Meta Title --}}
			<div class="control-group{{ $errors->first('meta_title', ' error') }}">
				<label class="control-label" for="meta_title">{{ trans('platform/pages::form.meta_title') }}</label>
				<div class="controls">
					<input type="text" name="meta_title" id="meta_title" value="{{{ Input::old('meta_title', ! empty($page) ? $page->meta_title : null) }}}">
					@if ($errors->has('meta_title'))
					{{ $errors->first('meta_title', '<span class="help-block">:message</span>') }}
					@else
					<span class="help-block">{{ trans('platform/pages::form.meta_title_help') }}</span>
					@endif
				</div>
			</div>

			{{-- Meta Description --}}
			<div class="control-group{{ $errors->first('meta_description', ' error') }}">
				<label class="control-label" for="meta_description">{{ trans('platform/pages::form.meta_description') }}</label>
				<div class="controls">
					<input type="text" name="meta_description" id="meta_description" value="{{{ Input::old('meta_description', ! empty($page) ? $page->meta_description : null) }}}">
					@if ($errors->has('meta_description'))
					{{ $errors->first('meta_description', '<span class="help-block">:message</span>') }}
					@else
					<span class="help-block">{{ trans('platform/pages::form.meta_description_help') }}</span>
					@endif
				</div>
			</div>

			{{-- Enabled --}}
			<div class="control-group{{ $errors->first('enabled', ' error') }}" required>
				<label class="control-label" for="enabled">{{ trans('platform/pages::form.enabled') }}</label>
				<div class="controls">
					<select name="enabled" id="enabled" required>
						<option value="1"{{ Input::old('enabled', ! empty($page) ? (int) $page->enabled : 1) === 1 ? ' selected="selected"' : null }}>{{ trans('general.enabled') }}</option>
						<option value="0"{{ Input::old('enabled', ! empty($page) ? (int) $page->enabled : 1) === 0 ? ' selected="selected"' : null }}>{{ trans('general.disabled') }}</option>
					</select>
					@if ($errors->has('enabled'))
					{{ $errors->first('enabled', '<span class="help-block">:message</span>') }}
					@else
					<span class="help-block">{{ trans('platform/pages::form.enabled_help') }}</span>
					@endif
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
					@if ($errors->has('visibility'))
					{{ $errors->first('visibility', '<span class="help-block">:message</span>') }}
					@else
					<span class="help-block">{{ trans('platform/pages::form.visibility_help') }}</span>
					@endif
				</div>
			</div>

			{{-- Groups --}}
			<div class="control-group{{ $errors->first('groups', ' error') }}{{ Input::old('visibility', ! empty($page) ? $page->visibility : 'always') == 'always' ? ' hide' : null }}" required>
				<label for="groups" class="control-label">{{ trans('platform/pages::form.groups') }}</label>
				<div class="controls">
					<select name="groups[]" id="groups" multiple="multiple">
					@foreach ($groups as $group)
						<option value="{{ $group->id }}"{{ array_key_exists($group->id, $pageGroups) ? ' selected="selected"' : null }}>{{ $group->name }}</option>
					@endforeach
					</select>
					@if ($errors->has('groups'))
					{{ $errors->first('groups', '<span class="help-block">:message</span>') }}
					@else
					<span class="help-block">{{ trans('platform/pages::form.groups_help') }}</span>
					@endif
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
					@if ($errors->has('type'))
					{{ $errors->first('type', '<span class="help-block">:message</span>') }}
					@else
					<span class="help-block">{{ trans('platform/pages::form.type_help') }}</span>
					@endif
				</div>
			</div>

			<div class="type-database{{ Input::old('type', ! empty($page) ? $page->type : 'database') == 'filesystem' ? ' hide' : null }}">

				{{-- Templates --}}
				<div class="control-group{{ $errors->first('template', ' error') }}" required>
					<label class="control-label" for="template">{{ trans('platform/pages::form.template') }}</label>
					<div class="controls">
						<select name="template" id="template" required>
						@foreach ($templates as $value => $name)
							<option value="{{ $value }}"{{ Input::old('template', ! empty($page) ? $page->template : $defaultTemplate) == $value ? ' selected="selected"' : null}}>{{ $name }}</option>
						@endforeach
						</select>
						@if ($errors->has('template'))
						{{ $errors->first('template', '<span class="help-block">:message</span>') }}
						@else
						<span class="help-block">{{ trans('platform/pages::form.template_help') }}</span>
						@endif
					</div>
				</div>

				{{-- Section --}}
				<div class="control-group{{ $errors->first('section', ' error') }}" required>
					<label class="control-label" for="section">{{ trans('platform/pages::form.section') }}</label>
					<div class="controls">
						<div class="input-prepend">
							<i class="add-on">@</i>
							<input type="text" name="section" value="{{{ Input::old('section', ! empty($page) ? $page->section : null) }}}">
						</div>
						@if ($errors->has('section'))
						{{ $errors->first('section', '<span class="help-block">:message</span>') }}
						@else
						<span class="help-block">{{ trans('platform/pages::form.section_help') }}</span>
						@endif
					</div>
				</div>

				{{-- Value --}}
				<div class="control-group{{ $errors->first('value', ' error') }}" required>
					<label class="control-label" for="value">{{ trans('platform/pages::form.value') }}</label>
					<div class="controls">
						<textarea rows="10" name="value" id="value"{{{ Input::old('value', ! empty($page) ? $page->type : null) == 'database' ? ' required' : null }}}>{{ Input::old('value', ! empty($page) ? $page->value : null) }}</textarea>
						@if ($errors->has('value'))
						{{ $errors->first('value', '<span class="help-block">:message</span>') }}
						@else
						<span class="help-block">{{ trans('platform/pages::form.value_help') }}</span>
						@endif
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
						@if ($errors->has('file'))
						{{ $errors->first('file', '<span class="help-block">:message</span>') }}
						@else
						<span class="help-block">{{ trans('platform/pages::form.file_help') }}</span>
						@endif
					</div>
				</div>

			</div>

		</fieldset>

	</section>
@stop

@section('page__footer')
	<nav class="actions actions--right">
		<ul class="navigation navigation--inline-circle">
			@if( ! empty($page) and $pageSegment != 'copy')
			<li>
				<a class="tip" data-placement="bottom" target="_blank" href="{{ URL::to($page->uri) }}" title="{{ trans('platform/pages::button.view') }}"><i class="icon-eye-open"></i></a>
			</li>
			<li>
				<a class="danger tip" data-placement="bottom" data-toggle="modal" data-target="#platform-modal-confirm" href="{{ URL::toAdmin("pages/delete/{$page->slug}") }}" title="{{ trans('button.delete') }}"><i class="icon-trash"></i></a>
			</li>
			<li>
				<a class="tip" data-placement="bottom" href="{{ URL::toAdmin("pages/copy/{$page->slug}") }}" title="{{ trans('button.copy') }}"><i class="icon-copy"></i></a>
			</li>
			@endif
			<li>
				<button class="tip" data-placement="bottom" title="{{ trans('button.save') }}" type="submit"><i class="icon-save"></i></button>
			</li>
		</ul>
	</nav>

</form>
@stop
