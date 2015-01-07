@extends('layouts/default')

{{-- Page title --}}
@section('title')
@parent
: {{{ trans("action.{$mode}") }}} {{{ $page->exists ? '- ' . $page->name : null }}}
@stop

{{-- Queue assets --}}
{{ Asset::queue('selectize', 'selectize/css/selectize.bootstrap3.css', 'styles') }}
{{ Asset::queue('redactor', 'redactor/css/redactor.css', 'styles') }}

{{ Asset::queue('slugify', 'platform/js/slugify.js', 'jquery') }}
{{ Asset::queue('validate', 'platform/js/validate.js', 'jquery') }}
{{ Asset::queue('selectize', 'selectize/js/selectize.js', 'jquery') }}
{{ Asset::queue('redactor', 'redactor/js/redactor.min.js', 'jquery') }}
{{ Asset::queue('form', 'platform/pages::js/form.js', 'platform') }}

{{-- Inline scripts --}}
@section('scripts')
@parent
@stop

{{-- Inline styles --}}
@section('styles')
@parent
@stop

{{-- Page content --}}
@section('page')
<section class="panel panel-default panel-tabs">

	{{-- Form --}}
	<form id="content-form" action="{{ request()->fullUrl() }}" role="form" method="post" accept-char="UTF-8" autocomplete="off" data-parsley-validate>

		{{-- Form: CSRF Token --}}
		<input type="hidden" name="_token" value="{{ csrf_token() }}">

		<header class="panel-heading">

			<nav class="navbar navbar-default navbar-actions">

				<div class="container-fluid">

					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#actions">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>

						<ul class="nav navbar-nav navbar-cancel">
							<li>
								<a class="tip" href="{{ route('admin.pages.all') }}" data-toggle="tooltip" data-original-title="{{{ trans('action.cancel') }}}">
									<i class="fa fa-reply"></i>  <span class="visible-xs-inline">{{{ trans('action.cancel') }}}</span>
								</a>
							</li>
						</ul>

						<span class="navbar-brand">{{{ trans("action.{$mode}") }}} <small>{{{ $page->exists ? $page->name : null }}}</small></span>
					</div>

					{{-- Form: Actions --}}
					<div class="collapse navbar-collapse" id="actions">

						<ul class="nav navbar-nav navbar-right">

							@if ($page->exists and $mode != 'copy')
								<li>
									<a href="{{ route('admin.pages.delete', $page->id) }}" class="tip" data-action-delete data-toggle="tooltip" data-original-title="{{{ trans('action.delete') }}}" type="delete">
										<i class="fa fa-trash-o"></i>  <span class="visible-xs-inline">{{{ trans('action.delete') }}}</span>
									</a>
								</li>

								<li>
									<a href="{{ route('admin.pages.copy', $page->id) }}" data-toggle="tooltip" data-original-title="{{{ trans('action.copy') }}}">
										<i class="fa fa-copy"></i>  <span class="visible-xs-inline">{{{ trans('action.copy') }}}</span>
									</a>
								</li>
							@endif

							<li>
								<button class="btn btn-primary navbar-btn" data-toggle="tooltip" data-original-title="{{{ trans('action.save') }}}">
									<i class="fa fa-save"></i>  <span class="visible-xs-inline">{{{ trans('action.save') }}}</span>
								</button>
							</li>

						</ul>

					</div>

				</div>

			</nav>

		</header>

		<main class="panel-body">

			<div role="tabpanel">

				{{-- Form: Tabs --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="active" role="presentation"><a href="#general" aria-controls="general" role="tab" data-toggle="tab">{{{ trans('common.tabs.general') }}}</a></li>
					<li role="presentation"><a href="#attributes" aria-controls="attributes" role="tab" data-toggle="tab">{{{ trans('common.tabs.attributes') }}}</a></li>
				</ul>

				<div class="tab-content">

					{{-- Form: General --}}
					<div role="tabpanel" class="tab-pane fade in active" id="general">

						<fieldset>

							<div class="row">

								<div class="col-md-8">

									{{-- Name --}}
									<div class="form-group{{ Alert::form('name', ' has-error') }}">

										<label for="name" class="control-label">
											<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::model.name_help') }}}"></i>
											{{{ trans('platform/pages::model.name') }}}
										</label>

										<input type="text" class="form-control" name="name" id="name" placeholder="{{{ trans('platform/pages::model.name') }}}" value="{{{ input()->old('name', $page->name) }}}" required autofocus data-parsley-trigger="change">

										<span class="help-block"></span>

									</div>

								</div>

								<div class="col-md-4">

									{{-- Slug --}}
									<div class="form-group{{ Alert::form('slug', ' has-error') }}">

										<label for="slug" class="control-label">
											<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/page::model.slug_help') }}}"></i>
											{{{ trans('platform/pages::model.slug') }}}
										</label>

										<input type="text" class="form-control" name="slug" id="slug" placeholder="{{{ trans('platform/page::model.slug') }}}" value="{{{ input()->old('slug', $page->slug) }}}" required data-parsley-trigger="change">

										<span class="help-block"></span>

									</div>
		
								</div>

							</div>

							<div class="row">

								<div class="col-md-3">

									{{-- SSL --}}
									<div class="form-group{{ Alert::form('https', ' has-error') }}">

										<label for="https" class="control-label">
											<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::model.https_help') }}}"></i>
											{{{ trans('platform/pages::model.https') }}}
										</label>

										<select class="form-control" name="https" id="https" required>
											<option value="1"{{ Input::old('https', $page->https) == 1 ? ' selected="selected"' : null }}>{{{ trans('common.yes') }}}</option>
											<option value="0"{{ Input::old('https', $page->https) == 0 ? ' selected="selected"' : null }}>{{{ trans('common.no') }}}</option>
										</select>

										<span class="help-block"></span>
									</div>

								</div>

								<div class="col-md-3">

									{{-- Enabled --}}
									<div class="form-group{{ Alert::form('enabled', ' has-error') }}">

										<label for="enabled" class="control-label">
											<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::model.enabled_help') }}}"></i>
											{{{ trans('platform/pages::model.enabled') }}}
										</label>

										<select class="form-control" name="enabled" id="enabled" required>
											<option value="1"{{ Input::old('enabled', $page->enabled) == 1 ? ' selected="selected"' : null }}>{{{ trans('common.enabled') }}}</option>
											<option value="0"{{ Input::old('enabled', $page->enabled) == 0 ? ' selected="selected"' : null }}>{{{ trans('common.disabled') }}}</option>
										</select>

										<span class="help-block"></span>
									</div>

								</div>
								<div class="col-md-6">

									{{-- Uri --}}
									<div class="form-group{{ Alert::form('uri', ' has-error') }}">
										<label for="uri" class="control-label">
											<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::model.uri_help') }}}"></i>
											{{{ trans('platform/pages::model.uri') }}}
										</label>

										<div class="input-group">
											<span class="input-group-addon">{{ url('/') }}/</span>
											<input type="text" class="form-control" name="uri" id="uri" placeholder="{{{ trans('platform/pages::model.uri') }}}" value="{{{ Input::old('uri', $page->uri) }}}" required>
										</div>

										<span class="help-block">{{{ Alert::form('uri') }}}</span>
									</div>

								</div>

							</div>


					<div class="row">

						<div class="col-md-6">

							{{-- Type --}}
							<div class="form-group{{ Alert::form('type', ' has-error') }}">
								<label for="type" class="control-label">{{{ trans('platform/pages::model.type') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::model.type_help') }}}"></i></label>

								<select class="form-control" name="type" id="type" required>
									<option value="database"{{ Input::old('type', $page->type) == 'database' ? ' selected="selected"' : null }}>{{{ trans('platform/pages::model.database') }}}</option>
									<option value="filesystem"{{ Input::old('type', $page->type) == 'filesystem' ? ' selected="selected"' : null }}>{{{ trans('platform/pages::model.filesystem') }}}</option>
								</select>

								<span class="help-block">{{{ Alert::form('type') }}}</span>
							</div>

						</div>

						<div class="col-md-6">

							{{-- Type : Database --}}
							<div data-type="database" class="{{ Input::old('type', $page->type) != 'database' ? ' hide' : null }}">

								{{-- Template --}}
								<div class="form-group{{ Alert::form('template', ' has-error') }}">
									<label for="template" class="control-label">{{{ trans('platform/pages::model.template') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::model.template_help') }}}"></i></label>

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

									<span class="help-block">{{{ Alert::form('template') }}}</span>
								</div>

							</div>

							{{-- Type : Filesystem --}}
							<div data-type="filesystem" class="{{ Input::old('type', $page->type) != 'filesystem' ? ' hide' : null }}">

								{{-- File --}}
								<div class="form-group{{ Alert::form('file', ' has-error') }}">
								<label for="file" class="control-label">{{{ trans('platform/pages::model.file') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::model.file_help') }}}"></i></label>

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

									<span class="help-block">{{{ Alert::form('file') }}}</span>
								</div>

							</div>

						</div>

					</div>

					{{-- Type : Database --}}
					<div data-type="database" class="{{ Input::old('type', $page->type) != 'database' ? ' hide' : null }}">

						{{-- Section --}}
						<div class="form-group{{ Alert::form('section', ' has-error') }}">
							<label for="section" class="control-label">{{{ trans('platform/pages::model.section') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::model.section_help') }}}"></i></label>

							<div class="input-group">
									<span class="input-group-addon">@</span>
								<input type="text" class="form-control" name="section" id="section" placeholder="{{{ trans('platform/pages::model.section') }}}" value="{{{ Input::old('section', $page->section) }}}">
							</div>

							<span class="help-block">{{{ Alert::form('section') }}}</span>
						</div>

						{{-- Value --}}
						<div class="form-group{{ Alert::form('value', ' has-error') }}">
							<label for="value" class="control-label">{{{ trans('platform/pages::model.value') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::model.value_help') }}}"></i></label>

							<textarea class="form-control redactor" name="value" id="value">{{{ Input::old('value', $page->value) }}}</textarea>

							<span class="help-block">{{{ Alert::form('value') }}}</span>
						</div>

					</div>

				</div>

				<div class="col-md-4">

					{{-- Visibility --}}
					<div class="well well-borderless">

						<fieldset>

							<legend>{{{ trans('platform/pages::model.visibility.legend') }}}</legend>

							<div class="form-group{{ Alert::form('visibility', ' has-error') }}">
								<label for="visibility" class="control-label">{{{ trans('platform/pages::model.visibility.legend') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::model.visibility_help') }}}"></i></label>

								<select class="form-control" name="visibility" id="visibility" required>
									<option value="always"{{ Input::old('visibility', $page->visibility) == 'always' ? ' selected="selected"' : null }}>{{{ trans('platform/pages::model.visibility.always') }}}</option>
									<option value="logged_in"{{ Input::old('visibility', $page->visibility) == 'logged_in' ? ' selected="selected"' : null }}>{{{ trans('platform/pages::model.visibility.logged_in') }}}</option>
									<option value="admin"{{ Input::old('visibility', $page->visibility) == 'admin' ? ' selected="selected"' : null }}>{{{ trans('platform/pages::model.visibility.admin') }}}</option>
								</select>

								<span class="help-block">
									{{{ Alert::form('visibility') }}}
								</span>
							</div>

							<div class="form-group{{ Alert::form('roles', ' has-error') }}">
								<label for="roles" class="control-label">{{{ trans('platform/pages::model.roles') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::model.roles_help') }}}"></i></label>

								<select class="form-control" name="roles[]" id="roles" multiple="multiple"{{ Input::old('visibility', $page->visibility) !== 'logged_in' ? ' disabled="disabled"' : null }}>
								@foreach ($roles as $role)
									<option value="{{ $role->id }}"{{ in_array($role->id, Input::get('roles', $page->roles)) ? ' selected="selected"' : null }}>{{ $role->name }}</option>
								@endforeach
								</select>

								<span class="help-block">
									{{{ Alert::form('roles') }}}
								</span>
							</div>

						</fieldset>

					</div>

					<div class="well well-borderless">

						<fieldset>

							<legend>{{{ trans('platform/pages::model.navigation.legend') }}}</legend>

							<p>{{{ trans('platform/pages::model.navigation_help') }}}</p>

							<div class="form-group{{ Alert::form('menu', ' has-error') }}">

								<label for="menu" class="control-label">{{{ trans('platform/pages::model.navigation.menu') }}}</label>

								<select class="form-control" name="menu" id="menu">
								<option value="-">{{{ trans('platform/pages::model.navigation.select_menu') }}}</option>
								@foreach ($menus as $item)
									<option value="{{ $item->menu }}"{{ ( ! empty($menu) and $menu->menu == $item->menu) ? ' selected="selected"' : null }}>{{ $item->name }}</option>
								@endforeach
								</select>

							</div>

							@foreach ($menus as $item)
							<div{{ ($menu->menu == $item->menu) ? null : ' class="hide"' }} data-menu-parent="{{{ $item->menu }}}">
								@widget('platform/menus::dropdown.show', [$item->slug, 0, $menu->exists ? $menu->getParent()->id : null, ['id' => 'parent_id', 'name' => "parent[{$item->menu}]", 'class' => 'form-control'], ['0' => trans('platform/pages::model.navigation.top_level')]])
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

				<a class="btn btn-default" href="{{{ url()->toAdmin('pages') }}}">{{{ trans('button.cancel') }}}</a>

				@if ($page->exists and $mode != 'copy')
				<a class="btn btn-info" href="{{ url()->toAdmin("pages/{$page->slug}/copy") }}">{{{ trans('button.copy') }}}</a>

				<a class="btn btn-danger" data-toggle="modal" data-target="modal-confirm" href="{{ url()->toAdmin("pages/{$page->slug}/delete") }}">{{{ trans('button.delete') }}}</a>
				@endif

			</div>

		</div>

	</div>

</form>
	
</section>
@stop
