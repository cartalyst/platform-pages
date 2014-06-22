@extends('layouts/default')

{{-- Page title --}}
@section('title')
	@parent
	: {{{ trans("platform/pages::general.{$mode}") }}} {{{ $page->exists ? '- ' . $page->name : null }}}
@stop

{{-- Queue assets --}}
{{ Asset::queue('redactor', 'imperavi/css/redactor.css', 'styles') }}

{{ Asset::queue('slugify', 'platform/js/slugify.js', 'jquery') }}
{{ Asset::queue('validate', 'platform/js/validate.js', 'jquery') }}
{{ Asset::queue('bootstrap.tabs', 'bootstrap/js/tab.js', 'jquery') }}
{{ Asset::queue('redactor', 'imperavi/js/redactor.min.js', 'jquery') }}
{{ Asset::queue('pages', 'platform/pages::js/scripts.js', 'jquery') }}

{{-- Inline scripts --}}
@section('scripts')
@parent
@stop

{{-- Inline styles --}}
@section('styles')
@parent
@stop

{{-- Page content --}}
@section('content')

{{-- Page header --}}
<div class="page-header">

	<h1>{{{ trans("platform/pages::general.{$mode}") }}} <small>{{{ $page->name }}}</small></h1>

</div>

{{-- Pages form --}}
<form id="pages-form" action="{{ Request::fullUrl() }}" method="post" accept-char="UTF-8" autocomplete="off">

	{{-- CSRF Token --}}
	<input type="hidden" name="_token" value="{{ csrf_token() }}">

	{{-- Tabs --}}
	<ul class="nav nav-tabs">
		<li class="active"><a href="#general" data-toggle="tab">{{{ trans('platform/pages::general.tabs.general') }}}</a></li>
		<li><a href="#attributes" data-toggle="tab">{{{ trans('platform/pages::general.tabs.attributes') }}}</a></li>
	</ul>

	{{-- Tabs content --}}
	<div class="tab-content tab-bordered">

		{{-- General tab --}}
		<div class="tab-pane active" id="general">

			<div class="row">

				<div class="col-md-8">

					{{-- Name --}}
					<div class="form-group{{ $errors->first('name', ' has-error') }}">
						<label for="name" class="control-label">{{{ trans('platform/pages::form.name') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::form.name_help') }}}"></i></label>

						<input type="text" class="form-control" name="name" id="name" placeholder="{{{ trans('platform/pages::form.name') }}}" value="{{{ Input::old('name', $page->name) }}}" required>

						<span class="help-block">{{{ $errors->first('name', ':message') }}}</span>
					</div>

					<div class="row">

						{{-- Slug --}}
						<div class="col-md-4">

							<div class="form-group{{ $errors->first('slug', ' has-error') }}">
								<label for="slug" class="control-label">{{{ trans('platform/pages::form.slug') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::form.slug_help') }}}"></i></label>

								<input type="text" class="form-control" name="slug" id="slug" placeholder="{{{ trans('platform/pages::form.slug') }}}" value="{{{ Input::old('slug', $page->slug) }}}" required>

								<span class="help-block">{{{ $errors->first('slug', ':message') }}}</span>
							</div>

						</div>

						{{-- SSL --}}
						<div class="col-md-4">

							<div class="form-group{{ $errors->first('https', ' has-error') }}">
								<label for="https" class="control-label">{{{ trans('platform/pages::form.https') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::form.https_help') }}}"></i></label>

								<select class="form-control" name="https" id="https" required>
									<option value="1"{{ Input::old('https', $page->https) == 1 ? ' selected="selected"' : null }}>{{{ trans('general.yes') }}}</option>
									<option value="0"{{ Input::old('https', $page->https) == 0 ? ' selected="selected"' : null }}>{{{ trans('general.no') }}}</option>
								</select>

								<span class="help-block">{{{ $errors->first('https', ':message') }}}</span>
							</div>

						</div>

						{{-- Enabled --}}
						<div class="col-md-4">

							<div class="form-group{{ $errors->first('enabled', ' has-error') }}">
								<label for="enabled" class="control-label">{{{ trans('platform/pages::form.enabled') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::form.enabled_help') }}}"></i></label>

								<select class="form-control" name="enabled" id="enabled" required>
									<option value="1"{{ Input::old('enabled', $page->enabled) == 1 ? ' selected="selected"' : null }}>{{{ trans('general.enabled') }}}</option>
									<option value="0"{{ Input::old('enabled', $page->enabled) == 0 ? ' selected="selected"' : null }}>{{{ trans('general.disabled') }}}</option>
								</select>

								<span class="help-block">{{{ $errors->first('enabled', ':message') }}}</span>
							</div>

						</div>

					</div>

					{{-- Uri --}}
					<div class="form-group{{ $errors->first('uri', ' has-error') }}">
						<label for="uri" class="control-label">{{{ trans('platform/pages::form.uri') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::form.uri_help') }}}"></i></label>

						<div class="input-group">
							<span class="input-group-addon">{{ URL::to('/') }}/</span>
							<input type="text" class="form-control" name="uri" id="uri" placeholder="{{{ trans('platform/pages::form.uri') }}}" value="{{{ Input::old('uri', $page->uri) }}}" required>
						</div>

						<span class="help-block">{{{ $errors->first('uri', ':message') }}}</span>
					</div>

					<div class="row">

						<div class="col-md-6">

							{{-- Type --}}
							<div class="form-group{{ $errors->first('type', ' has-error') }}">
								<label for="type" class="control-label">{{{ trans('platform/pages::form.type') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::form.type_help') }}}"></i></label>

								<select class="form-control" name="type" id="type" required>
									<option value="database"{{ Input::old('type', $page->type) == 'database' ? ' selected="selected"' : null }}>{{{ trans('platform/content::form.database') }}}</option>
									<option value="filesystem"{{ Input::old('type', $page->type) == 'filesystem' ? ' selected="selected"' : null }}>{{{ trans('platform/content::form.filesystem') }}}</option>
								</select>

								<span class="help-block">{{{ $errors->first('type', ':message') }}}</span>
							</div>

						</div>

						<div class="col-md-6">

							{{-- Type : Database --}}
							<div data-type="database" class="{{ Input::old('type', $page->type) != 'database' ? ' hide' : null }}">

								{{-- Template --}}
								<div class="form-group{{ $errors->first('template', ' has-error') }}">
									<label for="template" class="control-label">{{{ trans('platform/pages::form.template') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::form.template_help') }}}"></i></label>

									@if (empty($templates))
									<p class="form-control-static">
										<i>No templates available for the current frontend theme.</i>
									</p>
									@else
									<select class="form-control" name="template" id="template"{{ Input::old('type', $page->type) == 'database' ? ' required' : null }}>
									@foreach ($templates as $value => $name)
										<option value="{{ $value }}"{{ Input::old('template', $page->template) == $value ? ' selected="selected"' : null}}>{{ $name }}</option>
									@endforeach
									</select>
									@endif

									<span class="help-block">{{{ $errors->first('template', ':message') }}}</span>
								</div>

							</div>

							{{-- Type : Filesystem --}}
							<div data-type="filesystem" class="{{ Input::old('type', $page->type) != 'filesystem' ? ' hide' : null }}">

								{{-- File --}}
								<div class="form-group{{ $errors->first('file', ' has-error') }}">
								<label for="file" class="control-label">{{{ trans('platform/pages::form.file') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::form.file_help') }}}"></i></label>

									@if (empty($files))
									<p class="form-control-static">
										<i>No pages available for the current frontend theme.</i>
									</p>
									@else
									<select class="form-control" name="file" id="file"{{ Input::old('type', $page->type) == 'filesystem' ? ' required' : null }}>
									@foreach ($files as $value => $name)
										<option value="{{ $value }}"{{ Input::old('file', $page->file) == $value ? ' selected="selected"' : null}}>{{ $name }}</option>
									@endforeach
									</select>
									@endif

									<span class="help-block">{{{ $errors->first('file', ':message') }}}</span>
								</div>

							</div>

						</div>

					</div>

					{{-- Type : Database --}}
					<div data-type="database" class="{{ Input::old('type', $page->type) != 'database' ? ' hide' : null }}">

						{{-- Section --}}
						<div class="form-group{{ $errors->first('section', ' has-error') }}">
							<label for="section" class="control-label">{{{ trans('platform/pages::form.section') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::form.section_help') }}}"></i></label>

							<div class="input-group">
									<span class="input-group-addon">@</span>
								<input type="text" class="form-control" name="section" id="section" placeholder="{{{ trans('platform/pages::form.section') }}}" value="{{{ Input::old('section', $page->section) }}}">
							</div>

							<span class="help-block">{{{ $errors->first('section', ':message') }}}</span>
						</div>

						{{-- Value --}}
						<div class="form-group{{ $errors->first('value', ' has-error') }}">
							<label for="value" class="control-label">{{{ trans('platform/pages::form.value') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::form.value_help') }}}"></i></label>

							<textarea class="form-control redactor" name="value" id="value">{{{ Input::old('value', $page->value) }}}</textarea>

							<span class="help-block">{{{ $errors->first('value', ':message') }}}</span>
						</div>

					</div>

				</div>

				<div class="col-md-4">

					{{-- Visibility --}}
					<div class="well well-borderless">

						<fieldset>

							<legend>{{{ trans('platform/pages::form.visibility.legend') }}}</legend>

							<div class="form-group{{ $errors->first('visibility', ' has-error') }}">
								<label for="visibility" class="control-label">{{{ trans('platform/pages::form.visibility.legend') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::form.visibility_help') }}}"></i></label>

								<select class="form-control" name="visibility" id="visibility" required>
									<option value="always"{{ Input::old('visibility', $page->visibility) == 'always' ? ' selected="selected"' : null }}>{{{ trans('platform/pages::form.visibility.always') }}}</option>
									<option value="logged_in"{{ Input::old('visibility', $page->visibility) == 'logged_in' ? ' selected="selected"' : null }}>{{{ trans('platform/pages::form.visibility.logged_in') }}}</option>
									<option value="admin"{{ Input::old('visibility', $page->visibility) == 'admin' ? ' selected="selected"' : null }}>{{{ trans('platform/pages::form.visibility.admin') }}}</option>
								</select>

								<span class="help-block">
									{{{ $errors->first('visibility', ':message') }}}
								</span>
							</div>

							<div class="form-group{{ $errors->first('groups', ' has-error') }}">
								<label for="groups" class="control-label">{{{ trans('platform/pages::form.groups') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::form.groups_help') }}}"></i></label>

								<select class="form-control" name="groups[]" id="groups" multiple="multiple"{{ Input::old('visibility', $page->visibility) !== 'logged_in' ? ' disabled="disabled"' : null }}>
								@foreach ($groups as $group)
									<option value="{{ $group->id }}"{{ in_array($group->id, Input::get('groups', $page->groups)) ? ' selected="selected"' : null }}>{{ $group->name }}</option>
								@endforeach
								</select>

								<span class="help-block">
									{{{ $errors->first('groups', ':message') }}}
								</span>
							</div>

						</fieldset>

					</div>

					<div class="well well-borderless">

						<fieldset>

							<legend>{{{ trans('platform/pages::form.navigation.legend') }}}</legend>

							<p>{{{ trans('platform/pages::form.navigation_help') }}}</p>

							<div class="form-group{{ $errors->first('menu', ' has-error') }}">

								<label for="menu" class="control-label">{{{ trans('platform/pages::form.navigation.menu') }}}</label>

								<select class="form-control" name="menu" id="menu">
								<option value="-">{{{ trans('platform/pages::form.navigation.select_menu') }}}</option>
								@foreach ($menus as $item)
									<option value="{{ $item->menu }}"{{ ( ! empty($menu) and $menu->menu == $item->menu) ? ' selected="selected"' : null }}>{{ $item->name }}</option>
								@endforeach
								</select>

							</div>

							@foreach ($menus as $item)
							<div{{ ( ! empty($menu) and $menu->menu == $item->menu) ? null : ' class="hide"' }} data-menu-parent="{{{ $item->menu }}}">
								@widget('platform/menus::dropdown.show', [$item->slug, 0, $menu->exists ? $menu->getParent()->id : null, ['id' => 'parent_id', 'name' => "parent[{$item->menu}]", 'class' => 'form-control'], ['0' => trans('platform/pages::form.navigation.top_level')]])
							</div>
							@endforeach

						</fieldset>

					</div>

				</div>

			</div>

		</div>

		{{-- Attributes tab --}}
		<div class="tab-pane clearfix" id="attributes">

			@widget('platform/attributes::entity.form', [$page])

		</div>

	</div>

	{{-- Form actions --}}
	<div class="row">

		<div class="col-lg-12 text-right">

			{{-- Form actions --}}
			<div class="form-group">

				<button class="btn btn-success" type="submit">{{{ trans('button.save') }}}</button>

				<a class="btn btn-default" href="{{{ URL::toAdmin('pages') }}}">{{{ trans('button.cancel') }}}</a>

				@if ($page->exists and $mode != 'copy')
				<a class="btn btn-info" href="{{ URL::toAdmin("pages/{$page->slug}/copy") }}">{{{ trans('button.copy') }}}</a>

				<a class="btn btn-danger" data-toggle="modal" data-target="modal-confirm" href="{{ URL::toAdmin("pages/{$page->slug}/delete") }}">{{{ trans('button.delete') }}}</a>
				@endif

			</div>

		</div>

	</div>

</form>

@stop
