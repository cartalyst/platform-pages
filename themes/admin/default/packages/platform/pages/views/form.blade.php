@extends('templates/default')

{{-- Page title --}}
@section('title')
{{ trans("platform/pages::general.{$segment}.title", array('name' => ! empty($page) ? $page->name : null)) }} ::
@parent
@stop

{{-- Queue Assets --}}
{{ Asset::queue('redactor', 'styles/css/vendor/imperavi/redactor.css', 'style') }}
{{ Asset::queue('redactor', 'js/vendor/imperavi/redactor.js', 'jquery') }}
{{ Asset::queue('slugify', 'js/vendor/platform/slugify.js', 'jquery') }}
{{ Asset::queue('validate', 'js/vendor/platform/validate.js', 'jquery') }}
{{ Asset::queue('pages', 'platform/pages::js/pages.js', 'jquery') }}
{{ Asset::queue('redactor-editor', 'platform/pages::js/editor.js', 'redactor') }}

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

	<form id="page-create-form" class="form-horizontal" action="{{ Request::fullUrl() }}" method="POST" accept-char="UTF-8" autocomplete="off">

		{{-- CSRF Token --}}
		<input type="hidden" name="csrf_token" value="{{ csrf_token() }}">

		<header class="clearfix">
			<h1><a class="icon-reply" href="{{ URL::toAdmin('pages') }}"></a> {{ trans("platform/pages::general.{$segment}.title", array('name' => ! empty($page) ? $page->name : null)) }}</h1>

			<nav class="utilities pull-right">
				<ul>
					@if( ! empty($page) and $segment != 'copy')
					<li>
						<a class="btn btn-action tip" data-placement="bottom" data-toggle="modal" data-target="#platform-modal-confirm" href="{{ URL::toAdmin("page/delete/{$page->id}") }}" title="{{ trans('button.delete') }}"><i class="icon-trash"></i></a>
					</li>
					<li>
						<a class="btn btn-action tip" data-placement="bottom" href="{{ URL::toAdmin("pages/copy/{$page->id}") }}" title="{{ trans('button.copy') }}"><i class="icon-copy"></i></a>
					</li>
					@endif
					<li>
						<button class="btn btn-action tip" data-placement="bottom" title="{{ trans('button.update') }}" type="submit"><i class="icon-save"></i></button>
					</li>
				</ul>
			</nav>
		</header>

		<hr>

		<section class="content">
			<fieldset>
				<legend>{{ trans("platform/pages::form.{$segment}.legend") }}</legend>

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
						<div class="input-prepend">
							<span class="add-on">
								{{ str_finish(URL::to('/'), '/') }}
							</span>
							<input type="text" name="slug" id="slug" value="{{{ Input::old('slug', ! empty($page) ? $page->slug : null) }}}" placeholder="{{ trans('platform/pages::form.slug_help') }}" required>
						</div>
						{{ $errors->first('slug', '<span class="help-inline">:message</span>') }}
					</div>
				</div>

				{{-- Enabled --}}
				<div class="control-group{{ $errors->first('enabled', ' error') }}" required>
					<label class="control-label" for="enabled">{{ trans('platform/pages::form.status') }}</label>
					<div class="controls">
						<select name="enabled" id="enabled" required>
							<option value="1"{{ (Input::old('enabled', ! empty($page) ? (int) $page->enabled : 1) === 1 ? ' selected="selected"' : null) }}>{{ trans('general.enabled') }}</option>
							<option value="0"{{ (Input::old('enabled', ! empty($page) ? (int) $page->enabled : 1) === 0 ? ' selected="selected"' : null) }}>{{ trans('general.disabled') }}</option>
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
					<label for="visibility" class="control-label">{{ trans('platform/pages::form.visibility') }}</label>
					<div class="controls">
						<select name="visibility" id="visibility">
							<option value="always">Show Always</option>
							<option value="logged_in">Logged In Only</option>
							<option value="admin">Admin Only</option>
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

		{{-- Form Actions --}}
		<footer>
			<nav class="utilities pull-right">
				<ul>
					@if( ! empty($page) and $segment != 'copy')
					<li>
						<a class="btn btn-action tip" data-placement="bottom" data-toggle="modal" data-target="#platform-modal-confirm" href="{{ URL::toAdmin("pages/delete/{$page->id}") }}" title="{{ trans('button.delete') }}"><i class="icon-trash"></i></a>
					</li>
					<li>
						<a class="btn btn-action tip" data-placement="bottom" href="{{ URL::toAdmin("pages/copy/{$page->id}") }}" title="{{ trans('button.copy') }}"><i class="icon-copy"></i></a>
					</li>
					@endif
					<li>
						<button class="btn btn-action tip" data-placement="bottom" title="{{ trans('button.update') }}" type="submit"><i class="icon-save"></i></button>
					</li>
				</ul>
			</nav>
		</footer>

	</form>

</section>
@stop
