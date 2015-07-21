@extends('layouts/default')

{{-- Page title --}}
@section('title')
@parent
 {{{ trans("action.{$mode}") }}} {{{ trans('platform/pages::common.title') }}}
@stop

{{-- Queue assets --}}
{{ Asset::queue('selectize', 'selectize/css/selectize.bootstrap3.css', 'styles') }}
{{ Asset::queue('redactor', 'redactor/css/redactor.css', 'styles') }}

{{ Asset::queue('slugify', 'platform/js/slugify.js', 'jquery') }}
{{ Asset::queue('validate', 'platform/js/validate.js', 'jquery') }}
{{ Asset::queue('selectize', 'selectize/js/selectize.js', 'jquery') }}
{{ Asset::queue('redactor', 'redactor/js/redactor.min.js', 'jquery') }}
{{ Asset::queue('form', 'platform/pages::js/form.js', 'platform') }}

{{-- Inline styles --}}
@section('styles')
@parent
@stop

{{-- Inline scripts --}}
@section('scripts')
@parent
@stop

{{-- Page content --}}
@section('page')
<section class="panel panel-default panel-tabs">

	{{-- Form --}}
	<form id="pages-form" action="{{ request()->fullUrl() }}" role="form" method="post" accept-char="UTF-8" autocomplete="off" data-parsley-validate>

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

						<a class="btn btn-navbar-cancel navbar-btn pull-left tip" href="{{ route('admin.pages.all') }}" data-toggle="tooltip" data-original-title="{{{ trans('action.cancel') }}}">
							<i class="fa fa-reply"></i> <span class="visible-xs-inline">{{{ trans('action.cancel') }}}</span>
						</a>

						<span class="navbar-brand">{{{ trans("action.{$mode}") }}} <small>{{{ $page->exists ? $page->name : null }}}</small></span>
					</div>

					{{-- Form: Actions --}}
					<div class="collapse navbar-collapse" id="actions">

						<ul class="nav navbar-nav navbar-right">

							@if ($page->exists and $mode != 'copy')
							<li>
								<a href="{{ route('admin.pages.delete', $page->id) }}" class="tip" data-action-delete data-toggle="tooltip" data-original-title="{{{ trans('action.delete') }}}" type="delete">
									<i class="fa fa-trash-o"></i> <span class="visible-xs-inline">{{{ trans('action.delete') }}}</span>
								</a>
							</li>

							<li>
								<a href="{{ route('admin.pages.copy', $page->id) }}" data-toggle="tooltip" data-original-title="{{{ trans('action.copy') }}}">
									<i class="fa fa-copy"></i> <span class="visible-xs-inline">{{{ trans('action.copy') }}}</span>
								</a>
							</li>
							@endif

							<li>
								<button class="btn btn-primary navbar-btn" data-toggle="tooltip" data-original-title="{{{ trans('action.save') }}}">
									<i class="fa fa-save"></i> <span class="visible-xs-inline">{{{ trans('action.save') }}}</span>
								</button>
							</li>

						</ul>

					</div>

				</div>

			</nav>

		</header>

		<div class="panel-body">

			<div role="tabpanel">

				{{-- Form: Tabs --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="active" role="presentation"><a href="#general-tab" aria-controls="general-tab" role="tab" data-toggle="tab">{{{ trans('platform/pages::common.tabs.general') }}}</a></li>
					<li role="presentation"><a href="#visiblity-tab" aria-controls="visiblity-tab" role="tab" data-toggle="tab">{{{ trans('platform/pages::common.tabs.access') }}}</a></li>
					<li role="presentation"><a href="#navigation-tab" aria-controls="navigation-tab" role="tab" data-toggle="tab">{{{ trans('platform/pages::common.tabs.navigation') }}}</a></li>
					<li role="presentation"><a href="#tags-tab" aria-controls="tag" role="tabs-tab" data-toggle="tab">{{{ trans('platform/pages::common.tabs.tags') }}}</a></li>
					<li role="presentation"><a href="#attributes-tab" aria-controls="attributes-tab" role="tab" data-toggle="tab">{{{ trans('platform/pages::common.tabs.attributes') }}}</a></li>
				</ul>

				<div class="tab-content">

					{{-- Tab: General --}}
					<div role="tabpanel" class="tab-pane fade in active" id="general-tab">

						<fieldset>

							<legend>{{{ trans('platform/pages::model.general.legend') }}}</legend>

							<div class="row">

								<div class="col-md-3">

									{{-- Name --}}
									<div class="form-group{{ Alert::onForm('name', ' has-error') }}">

										<label for="name" class="control-label">
											<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::model.general.name_help') }}}"></i>
											{{{ trans('platform/pages::model.general.name') }}}
										</label>

										<input type="text" class="form-control" name="name" id="name" data-slugify="#slug" placeholder="{{{ trans('platform/pages::model.general.name') }}}" value="{{{ input()->old('name', $page->name) }}}" required autofocus data-parsley-trigger="change">

										<span class="help-block">{{{ Alert::onForm('name') }}}</span>

									</div>

								</div>

								<div class="col-md-3">

									{{-- Slug --}}
									<div class="form-group{{ Alert::onForm('slug', ' has-error') }}">

										<label for="slug" class="control-label">
											<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::model.general.slug_help') }}}"></i>
											{{{ trans('platform/pages::model.general.slug') }}}
										</label>

										<input type="text" class="form-control" name="slug" id="slug" placeholder="{{{ trans('platform/pages::model.general.slug') }}}" value="{{{ input()->old('slug', $page->slug) }}}" required data-parsley-trigger="change">

										<span class="help-block">{{{ Alert::onForm('slug') }}}</span>

									</div>

								</div>

								<div class="col-md-3">

									{{-- HTTPS --}}
									<div class="form-group{{ Alert::onForm('https', ' has-error') }}">

										<label for="https" class="control-label">
											<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::model.general.https_help') }}}"></i>
											{{{ trans('platform/pages::model.general.https') }}}
										</label>

										<select class="form-control" name="https" id="https" required>
											<option value="1"{{ request()->old('https', $page->https) == 1 ? ' selected="selected"' : null }}>{{{ trans('common.yes') }}}</option>
											<option value="0"{{ request()->old('https', $page->https) == 0 ? ' selected="selected"' : null }}>{{{ trans('common.no') }}}</option>
										</select>

										<span class="help-block">{{{ Alert::onForm('https') }}}</span>

									</div>

								</div>

								<div class="col-md-3">

									{{-- Enabled --}}
									<div class="form-group{{ Alert::onForm('enabled', ' has-error') }}">

										<label for="enabled" class="control-label">
											<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::model.general.enabled_help') }}}"></i>
											{{{ trans('platform/pages::model.general.enabled') }}}
										</label>

										<select class="form-control" name="enabled" id="enabled" required>
											<option value="1"{{ request()->old('enabled', $page->enabled) == 1 ? ' selected="selected"' : null }}>{{{ trans('common.enabled') }}}</option>
											<option value="0"{{ request()->old('enabled', $page->enabled) == 0 ? ' selected="selected"' : null }}>{{{ trans('common.disabled') }}}</option>
										</select>

										<span class="help-block">{{{ Alert::onForm('enabled') }}}</span>

									</div>

								</div>

							</div>

							<div class="row">

								<div class="col-md-12">

									{{-- Uri --}}
									<div class="form-group{{ Alert::onForm('uri', ' has-error') }}">

										<label for="uri" class="control-label">
											<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::model.general.uri_help') }}}"></i>
											{{{ trans('platform/pages::model.general.uri') }}}
										</label>

										<div class="input-group">
											<span class="input-group-addon">{{ url('') }}</span>
											<input type="text" class="form-control" name="uri" id="uri" placeholder="{{{ trans('platform/pages::model.general.uri') }}}" value="{{{ request()->old('uri', $page->uri) }}}" required>
										</div>

										<span class="help-block">{{{ Alert::onForm('uri') }}}</span>

									</div>

								</div>

							</div>

							<div class="row">

								<div class="col-md-6">

									{{-- Type --}}
									<div class="form-group{{ Alert::onForm('type', ' has-error') }}">

										<label for="type" class="control-label">
											<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::model.general.type_help') }}}"></i>
											{{{ trans('platform/pages::model.general.type') }}}
										</label>

										<select class="form-control" name="type" id="type" required>
											<option value="database"{{ request()->old('type', $page->type) == 'database' ? ' selected="selected"' : null }}>{{{ trans('platform/pages::model.general.database') }}}</option>
											<option value="filesystem"{{ request()->old('type', $page->type) == 'filesystem' ? ' selected="selected"' : null }}>{{{ trans('platform/pages::model.general.filesystem') }}}</option>
										</select>

										<span class="help-block">{{{ Alert::onForm('type') }}}</span>

									</div>

								</div>

								<div class="col-md-6">

									{{-- Type : Database --}}
									<div data-type="database" class="{{ request()->old('type', $page->type) != 'database' ? ' hide' : null }}">

										{{-- Template --}}
										<div class="form-group{{ Alert::onForm('template', ' has-error') }}">

											<label for="template" class="control-label">
												<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::model.general.template_help') }}}"></i>
												{{{ trans('platform/pages::model.general.template') }}}
											</label>

											@if (empty($templates))
											<p class="form-control-static">
												<i>No templates available for the current frontend theme.</i>
											</p>
											@else
											<select class="form-control" name="template" id="template"{{ request()->old('type', $page->type) == 'database' ? ' required' : null }}>
												@foreach ($templates as $value => $name)
												<option value="{{ $value }}"{{ request()->old('template', $page->template) == $value ? ' selected="selected"' : null}}>{{ $name }}</option>
												@endforeach
											</select>
											@endif

											<span class="help-block">{{{ Alert::onForm('template') }}}</span>

										</div>

									</div>

									{{-- Type : Filesystem --}}
									<div data-type="filesystem" class="{{ request()->old('type', $page->type) != 'filesystem' ? ' hide' : null }}">

										{{-- File --}}
										<div class="form-group{{ Alert::onForm('file', ' has-error') }}">

											<label for="file" class="control-label">
												<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::model.general.file_help') }}}"></i>
												{{{ trans('platform/pages::model.general.file') }}}
											</label>

											@if (empty($files))
											<p class="form-control-static">
												<i>No pages available for the current frontend theme.</i>
											</p>
											@else
											<select class="form-control" name="file" id="file"{{ request()->old('type', $page->type) == 'filesystem' ? ' required' : null }}>
												@foreach ($files as $value => $name)
												<option value="{{ $value }}"{{ request()->old('file', $page->file) == $value ? ' selected="selected"' : null}}>{{ $name }}</option>
												@endforeach
											</select>
											@endif

											<span class="help-block">{{{ Alert::onForm('file') }}}</span>

										</div>

									</div>

								</div>

							</div>

							<div class="row">

								<div class="col-md-12">

									{{-- Type : Database --}}
									<div data-type="database" class="{{ request()->old('type', $page->type) != 'database' ? ' hide' : null }}">

										{{-- Section --}}
										<div class="form-group{{ Alert::onForm('section', ' has-error') }}">

											<label for="section" class="control-label">
												<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::model.general.section_help') }}}"></i>
												{{{ trans('platform/pages::model.general.section') }}}
											</label>

											<div class="input-group">
												<span class="input-group-addon">@</span>
												<input type="text" class="form-control" name="section" id="section" placeholder="{{{ trans('platform/pages::model.general.section') }}}" value="{{{ request()->old('section', $page->section) }}}">
											</div>

											<span class="help-block">{{{ Alert::onForm('section') }}}</span>

										</div>

										{{-- Value --}}
										<div class="form-group{{ Alert::onForm('value', ' has-error') }}">

											<label for="value" class="control-label">
												<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::model.general.value_help') }}}"></i>
												{{{ trans('platform/pages::model.general.value') }}}
											</label>

											<textarea class="form-control redactor" name="value" id="value">{{{ request()->old('value', $page->value) }}}</textarea>

											<span class="help-block">{{{ Alert::onForm('value') }}}</span>

										</div>

									</div>

								</div>

							</div>

						</fieldset>

					</div>

					{{-- Tab: Access --}}
					<div role="tabpanel" class="tab-pane fade" id="visiblity-tab">

						<fieldset>

							<legend>{{{ trans('platform/pages::model.access.legend') }}}</legend>

							<div class="form-group{{ Alert::onForm('visibility', ' has-error') }}">

								<label for="visibility" class="control-label">
									<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::model.access.visibility_help') }}}"></i>
									{{{ trans('platform/pages::model.access.visibility') }}}
								</label>

								<select class="form-control" name="visibility" id="visibility" required>
									<option value="always"{{ request()->old('visibility', $page->visibility) == 'always' ? ' selected="selected"' : null }}>{{{ trans('platform/pages::model.access.always') }}}</option>
									<option value="logged_in"{{ request()->old('visibility', $page->visibility) == 'logged_in' ? ' selected="selected"' : null }}>{{{ trans('platform/pages::model.access.logged_in') }}}</option>
									<option value="admin"{{ request()->old('visibility', $page->visibility) == 'admin' ? ' selected="selected"' : null }}>{{{ trans('platform/pages::model.access.admin') }}}</option>
								</select>

								<span class="help-block">{{{ Alert::onForm('visibility') }}}</span>

							</div>

							<div class="form-group{{ Alert::onForm('roles', ' has-error') }}">

								<label for="roles" class="control-label">
									<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::model.access.roles_help') }}}"></i>
									{{{ trans('platform/pages::model.access.roles') }}}
								</label>

								<select class="form-control" name="roles[]" id="roles" multiple="multiple"{{ request()->old('visibility', $page->visibility) !== 'logged_in' ? ' disabled="disabled"' : null }}>
									@foreach ($roles as $role)
									<option value="{{ $role->id }}"{{ in_array($role->id, request()->get('roles', $page->roles)) ? ' selected="selected"' : null }}>{{ $role->name }}</option>
									@endforeach
								</select>

								<span class="help-block">{{{ Alert::onForm('roles') }}}</span>

							</div>

						</fieldset>

					</div>

					{{-- Tab: Navigation --}}
					<div role="tabpanel" class="tab-pane fade" id="navigation-tab">

						<fieldset>

							<legend>{{{ trans('platform/pages::model.navigation.legend') }}}</legend>

							<div class="form-group{{ Alert::onForm('menu', ' has-error') }}">

								<label for="menu" class="control-label">
									<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::model.navigation.menu_help') }}}"></i>
									{{{ trans('platform/pages::model.navigation.menu') }}}
								</label>

								<select class="form-control" name="menu" id="menu">
									<option value="-">{{{ trans('platform/pages::model.navigation.select_menu') }}}</option>
									@foreach ($menus as $item)
									<option value="{{ $item->menu }}"{{ ( ! empty($menu) and $menu->menu == $item->menu) ? ' selected="selected"' : null }}>{{ $item->name }}</option>
									@endforeach
								</select>

							</div>

							@foreach ($menus as $item)
							<div{!! ($menu->menu == $item->menu) ? null : ' class="hide"' !!} data-menu-parent="{!! $item->menu !!}">
								@dropdown($item->slug, 0, $menu->exists ? $menu->getParent()->id : null, ['id' => 'parent_id', 'name' => "parent[{$item->menu}]", 'class' => 'form-control'], ['0' => trans('platform/pages::model.navigation.top_level')])
							</div>
							@endforeach

					</fieldset>

				</div>

				{{-- Tab: Tags --}}
				<div role="tabpanel" class="tab-pane fade" id="tags-tab">

					<fieldset>

						<legend>{{{ trans('platform/tags::model.tag.legend') }}}</legend>

						@tags($page, 'tags')

					</fieldset>

				</div>

				{{-- Tab: Attributes --}}
				<div role="tabpanel" class="tab-pane fade" id="attributes-tab">

					@attributes($page)

				</div>

			</div>

		</div>

	</div>

</form>

</section>
@stop
