@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{{ trans("platform/pages::general.{$pageSegment}.title") }}} {{{ ! empty($page) ? '- ' . $page->name : null }}} ::
@parent
@stop

{{-- Queue assets --}}
{{ Asset::queue('validate', 'js/platform/validate.js', 'jquery') }}
{{ Asset::queue('redactor-js', 'js/redactor/redactor.min.js', 'jquery') }}
{{ Asset::queue('redactor-css', 'css/redactor/redactor.css', 'styles') }}
{{ Asset::queue('boostrap.tabs', 'js/bootstrap/tab.js', 'jquery') }}
{{ Asset::queue('slugify', 'js/platform/slugify.js', 'jquery') }}
{{ Asset::queue('pages-scripts', 'platform/pages::js/scripts.js', 'jquery') }}

{{-- Inline scripts --}}
@section('scripts')
@parent
@stop

{{-- Call custom inline styles --}}
@section('styles')
@parent
@stop

{{-- Page content --}}
@section('content')

<div class="col-md-12">

	{{-- Page header --}}
	<div class="page-header">

		<h1>{{{ trans("platform/pages::general.{$pageSegment}.title") }}} <small>{{{ ! empty($page) ? $page->name : null }}}</small></h1>

	</div>

	{{-- Pages form --}}
	<form id="pages-form" action="{{ Request::fullUrl() }}" method="post" accept-char="UTF-8" autocomplete="off">

		{{-- CSRF Token --}}
		<input type="hidden" name="_token" value="{{ csrf_token() }}">

		<div class="row">

			<div class="col-md-8">

				<div class="row">

					<div class="col-md-8">

						{{-- Name --}}
						<div class="form-group{{ $errors->first('name', ' has-error') }}">
							<label for="name" class="control-label">{{{ trans('platform/pages::form.name') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::form.name_help') }}}"></i></label>

							<input type="text" class="form-control" name="name" id="name" placeholder="{{{ trans('platform/pages::form.name') }}}" value="{{{ Input::old('name', ! empty($page) ? $page->name : null) }}}" required>

							<span class="help-block">{{{ $errors->first('name', ':message') }}}</span>
						</div>

					</div>

					<div class="col-md-4">

						{{-- Slug --}}
						<div class="form-group{{ $errors->first('slug', ' has-error') }}">
							<label for="slug" class="control-label">{{{ trans('platform/pages::form.slug') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::form.slug_help') }}}"></i></label>

							<input type="text" class="form-control" name="slug" id="slug" placeholder="{{{ trans('platform/pages::form.slug') }}}" value="{{{ Input::old('slug', ! empty($page) ? $page->slug : null) }}}" required>

							<span class="help-block">{{{ $errors->first('slug', ':message') }}}</span>
						</div>

					</div>

				</div>

				<div class="row">

					<div class="col-md-8">

						{{-- Uri --}}
						<div class="form-group{{ $errors->first('uri', ' has-error') }}">
							<label for="uri" class="control-label">{{{ trans('platform/pages::form.uri') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::form.uri_help') }}}"></i></label>

							<input type="text" class="form-control" name="uri" id="uri" placeholder="{{{ trans('platform/pages::form.uri') }}}" value="{{{ Input::old('uri', ! empty($page) ? $page->uri : null) }}}" required>

							<span class="help-block">{{{ $errors->first('uri', ':message') }}}</span>
						</div>

					</div>

					<div class="col-md-4">

						{{-- Enabled --}}
						<div class="form-group{{ $errors->first('enabled', ' has-error') }}">
							<label for="enabled" class="control-label">{{{ trans('platform/pages::form.enabled') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::form.enabled_help') }}}"></i></label>
							<div class="xcol-lg-4">
								<select class="form-control" name="enabled" id="enabled" required>
									<option value="1"{{ (Input::old('enabled', ! empty($page) ? (int) $page->enabled : 1) == 1 ? ' selected="selected"' : null) }}>{{{ trans('general.enabled') }}}</option>
									<option value="0"{{ (Input::old('enabled', ! empty($page) ? (int) $page->enabled : 1) == 0 ? ' selected="selected"' : null) }}>{{{ trans('general.disabled') }}}</option>
								</select>

								<span class="help-block">{{{ $errors->first('enabled', ':message') }}}</span>
							</div>
						</div>

					</div>
				</div>


				{{-- Meta title --}}
				<div class="form-group{{ $errors->first('meta_title', ' has-error') }}">
					<label for="meta_title" class="control-label">{{{ trans('platform/pages::form.meta_title') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::form.meta_title_help') }}}"></i></label>

					<input type="text" class="form-control" name="meta_title" id="meta_title" placeholder="{{{ trans('platform/pages::form.meta_title') }}}" value="{{{ Input::old('meta_title', ! empty($page) ? $page->meta_title : null) }}}">

					<span class="help-block">{{{ $errors->first('meta_title', ':message') }}}</span>
				</div>

				{{-- Meta description --}}
				<div class="form-group{{ $errors->first('meta_description', ' has-error') }}">
					<label for="meta_description" class="control-label">{{{ trans('platform/pages::form.meta_description') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::form.meta_description_help') }}}"></i></label>

					<input type="text" class="form-control" name="meta_description" id="meta_description" placeholder="{{{ trans('platform/pages::form.meta_description') }}}" value="{{{ Input::old('meta_description', ! empty($page) ? $page->meta_description : null) }}}">

					<span class="help-block">{{{ $errors->first('meta_description', ':message') }}}</span>
				</div>

				<div class="row">

					<div class="col-md-6">

						{{-- Type --}}
						<div class="form-group{{ $errors->first('type', ' has-error') }}">
							<label for="type" class="control-label">{{{ trans('platform/pages::form.type') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::form.type_help') }}}"></i></label>

							<select class="form-control" name="type" id="type" required>
								<option value="database"{{ Input::old('type', ! empty($page) ? $page->type : 'database') == 'database' ? ' selected="selected"' : null }}>{{{ trans('platform/content::form.database') }}}</option>
								<option value="filesystem"{{ Input::old('type', ! empty($page) ? $page->type : 'database') == 'filesystem' ? ' selected="selected"' : null }}>{{{ trans('platform/content::form.filesystem') }}}</option>
							</select>

							<span class="help-block">{{{ $errors->first('type', ':message') }}}</span>
						</div>

					</div>

					<div class="col-md-6">

						{{-- Type : Database --}}
						<div data-storage="database" class="{{ Input::old('type', ! empty($page) ? $page->type : 'database') == 'filesystem' ? ' hide' : null }}">

							{{-- Template --}}
							<div class="form-group{{ $errors->first('template', ' error') }}">
								<label for="template" class="control-label">{{{ trans('platform/pages::form.template') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::form.template_help') }}}"></i></label>

								<select class="form-control" name="template" id="template"{{ Input::old('type', ! empty($page) ? $page->type : null) == 'templatesystem' ? ' required' : null }}>
								@foreach ($templates as $value => $name)
									<option value="{{ $value }}"{{ Input::old('template', ! empty($page) ? $page->template : $defaultTemplate) == $value ? ' selected="selected"' : null}}>{{ $name }}</option>
								@endforeach
								</select>

								<span class="help-block">{{{ $errors->first('template', ':message') }}}</span>
							</div>

						</div>

						{{-- Type : Filesystem --}}
						<div data-storage="filesystem" class="{{ Input::old('type', ! empty($page) ? $page->type : 'database') == 'database' ? ' hide' : null }}">

							{{-- File --}}
							<div class="form-group{{ $errors->first('file', ' error') }}">
							<label for="file" class="control-label">{{{ trans('platform/pages::form.file') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::form.file_help') }}}"></i></label>

								<select class="form-control" name="file" id="file"{{ Input::old('type', ! empty($page) ? $page->type : null) == 'filesystem' ? ' required' : null }}>
								@foreach ($files as $value => $name)
									<option value="{{ $value }}"{{ Input::old('file', ! empty($page) ? $page->file : null) == $value ? ' selected="selected"' : null}}>{{ $name }}</option>
								@endforeach
								</select>

								<span class="help-block">{{{ $errors->first('file', ':message') }}}</span>
							</div>

						</div>

					</div>

				</div>

				{{-- Type : Database --}}
				<div data-storage="database" class="{{ Input::old('type', ! empty($page) ? $page->type : 'database') == 'filesystem' ? ' hide' : null }}">

					{{-- Section --}}
					<div class="form-group{{ $errors->first('section', ' has-error') }}">
						<label for="section" class="control-label">{{{ trans('platform/pages::form.section') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::form.section_help') }}}"></i></label>

						<input type="text" class="form-control" name="section" id="section" placeholder="{{{ trans('platform/pages::form.section') }}}" value="{{{ Input::old('section', ! empty($page) ? $page->section : null) }}}">

						<span class="help-block">{{{ $errors->first('section', ':message') }}}</span>
					</div>

					{{-- Value --}}
					<div class="form-group{{ $errors->first('value', ' has-error') }}">
						<label for="value" class="control-label">{{{ trans('platform/pages::form.value') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::form.value_help') }}}"></i></label>

						<textarea class="form-control redactor" name="value" id="value">{{{ Input::old('value', ! empty($page) ? $page->value : null) }}}</textarea>

						<span class="help-block">{{{ $errors->first('value', ':message') }}}</span>
					</div>

				</div>

			</div>

			<div class="col-md-4">

				{{-- Visibility --}}
				<div class="well well-borderless">

					<fieldset>

						<legend>Visibility</legend>

						<div class="form-group{{ $errors->first('visibility', ' has-error') }}">
							<label for="visibility" class="control-label">{{{ trans('platform/pages::form.visibility.legend') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::form.visibility_help') }}}"></i></label>

							<select class="form-control" name="visibility" id="visibility" required>
								<option value="always"{{ Input::old('visibility', ! empty($page) ? $page->visibility : 'always') == 'always' ? ' selected="selected"' : null }}>{{{ trans('platform/pages::form.visibility.always') }}}</option>
								<option value="logged_in"{{ Input::old('visibility', ! empty($page) ? $page->visibility : 'always') == 'logged_in' ? ' selected="selected"' : null }}>{{{ trans('platform/pages::form.visibility.logged_in') }}}</option>
								<option value="admin"{{ Input::old('visibility', ! empty($page) ? $page->visibility : 'always') == 'admin' ? ' selected="selected"' : null }}>{{{ trans('platform/pages::form.visibility.admin') }}}</option>
							</select>

							<span class="help-block">
								{{{ $errors->first('visibility', ':message') }}}
							</span>
						</div>

						<div class="form-group{{ $errors->first('groups', ' has-error') }}">
							<label for="groups" class="control-label">{{{ trans('platform/pages::form.groups') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::form.groups_help') }}}"></i></label>

							<select class="form-control" name="groups[]" id="groups" multiple="multiple"{{ Input::old('visibility', ! empty($page) ? $page->visibility : 'always') == 'always' ? ' disabled="disabled"' : null }}>
							@foreach ($groups as $group)
								<option value="{{ $group->id }}"{{ in_array($group->id, Input::get('groups', ! empty($page) ? $page->groups : array())) ? ' selected="selected"' : null }}>{{ $group->name }}</option>
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

						<legend>Navigation</legend>

						<p>Add this page to your navigation.</p>

						<div class="form-group{{ $errors->first('menu', ' has-error') }}">
							<label for="menu" class="control-label">Menu <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/pages::form.groups_help') }}}"></i></label>

							<select class="form-control" name="menu" id="menu">
							<option value="-">-- Select a menu --</option>
							@foreach ($menus as $item)
								<option value="{{ $item->id }}"{{ ( ! empty($menu) and $menu->menu == $item->menu ) ? ' selected="selected"' : null }}>{{ $item->name }}</option>
							@endforeach
							</select>

						</div>

						@foreach ($menus as $item)
							<div{{ ( ! empty($menu) and $menu->menu == $item->menu ) ? null : ' class="hide"' }} data-menu-parent="{{{ $item->id }}}">
							@widget('platform/menus::dropdown.show', array($item->slug, 0, ! empty($menu) ? $menu->getParent()->id : null, array('id' => 'parent_id', 'name' => "parent[{$item->id}]", 'class' => 'form-control'), array('0' => '-- Top Level --')))
							</div>
						@endforeach

					</fieldset>

				</div>

			</div>

		</div>

		<div class="row">

			<div class="col-md-12">

				{{-- Form actions --}}
				<div class="form-group">

					<button class="btn btn-success" type="submit">{{{ trans("platform/pages::button.{$pageSegment}") }}}</button>

					<a class="btn btn-default" href="{{{ URL::toAdmin('pages') }}}">{{{ trans('button.cancel') }}}</a>

					@if ( ! empty($page) and $pageSegment != 'copy')
					<div class="pull-right">
						<a class="btn btn-danger" data-toggle="modal" data-target="modal-confirm" href="{{ URL::toAdmin("pages/delete/{$page->slug}") }}">{{{ trans('platform/pages::button.delete') }}}</a>

						<a class="btn btn-info" href="{{ URL::toAdmin("pages/copy/{$page->slug}") }}">{{{ trans('platform/pages::button.copy') }}}</a>
					</div>
					@endif

				</div>

			</div>

		</div>

	</form>

</div>

@stop
