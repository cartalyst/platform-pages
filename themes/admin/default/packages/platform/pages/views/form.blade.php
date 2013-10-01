@extends('layouts/default')

{{-- Page title --}}
@section('title', trans("platform/pages::general.{$pageSegment}.title"))

{{-- Queue assets --}}
{{ Asset::queue('validate', 'js/platform/validate.js', 'jquery') }}
{{ Asset::queue('fseditor', 'js/burakson/jquery.fseditor-min.js', 'jquery') }}
{{ Asset::queue('fseditor', 'css/burakson/fseditor.css', 'styles') }}
{{ Asset::queue('tabs', 'js/bootstrap/tab.js', 'jquery') }}
{{ Asset::queue('slugify', 'js/platform/slugify.js', 'jquery') }}
{{ Asset::queue('pages-scripts', 'platform/pages::js/scripts.js', 'jquery') }}

{{-- Inline scripts --}}
@section('scripts')
@parent
<script type="text/javascript">
	H5F.setup(document.getElementById('pages-form'));
</script>
@stop

{{-- Page content --}}
@section('content')

<div class="row">

	<div class="col-md-12">

		{{-- Page header --}}
		<div class="page-header">

			<h1>{{{ trans("platform/pages::general.{$pageSegment}.title") }}} <small>{{{ ! empty($page) ? $page->name : null }}}</small></h1>

		</div>

		{{-- Pages form --}}
		<form id="pages-form" class="form-horizontal" action="{{ Request::fullUrl() }}" method="post" accept-char="UTF-8" autocomplete="off">

			{{-- CSRF Token --}}
			<input type="hidden" name="_token" value="{{ csrf_token() }}">

			{{-- Tabs --}}
			<ul class="nav nav-tabs" data-toogle="tab">
				<li class="active"><a href="#general">* General</a></li>
				<li><a href="#attributes">* Attributes</a></li>
			</ul>

			{{-- Tabs content --}}
			<div class="tab-content tab-bordered">

				{{-- General tab --}}
				<div class="tab-pane active" id="general">

					{{-- Name --}}
					<div class="form-group{{ $errors->first('name', ' has-error') }}">
						<label for="name" class="col-lg-2 control-label">{{{ trans('platform/pages::form.name') }}}</label>
						<div class="col-lg-10">
							<input type="text" class="form-control" name="name" id="name" placeholder="{{{ trans('platform/pages::form.name') }}}" value="{{{ Input::old('name', ! empty($page) ? $page->name : null) }}}">

							<span class="help-block">
								{{{ $errors->first('name', ':message') ?: trans('platform/pages::form.name_help') }}}
							</span>
						</div>
					</div>

					{{-- Slug --}}
					<div class="form-group{{ $errors->first('slug', ' has-error') }}">
						<label for="slug" class="col-lg-2 control-label">{{{ trans('platform/pages::form.slug') }}}</label>
						<div class="col-lg-10">
							<input type="text" class="form-control" name="slug" id="slug" placeholder="{{{ trans('platform/pages::form.slug') }}}" value="{{{ Input::old('slug', ! empty($page) ? $page->slug : null) }}}">

							<span class="help-block">
								{{{ $errors->first('slug', ':message') ?: trans('platform/pages::form.slug_help') }}}
							</span>
						</div>
					</div>

					{{-- Uri --}}
					<div class="form-group{{ $errors->first('uri', ' has-error') }}">
						<label for="uri" class="col-lg-2 control-label">{{{ trans('platform/pages::form.uri') }}}</label>
						<div class="col-lg-10">
							<input type="text" class="form-control" name="uri" id="uri" placeholder="{{{ trans('platform/pages::form.uri') }}}" value="{{{ Input::old('uri', ! empty($page) ? $page->uri : null) }}}">

							<span class="help-block">
								{{{ $errors->first('uri', ':message') ?: trans('platform/pages::form.uri_help') }}}
							</span>
						</div>
					</div>

					{{-- Meta title --}}
					<div class="form-group{{ $errors->first('meta_title', ' has-error') }}">
						<label for="meta_title" class="col-lg-2 control-label">{{{ trans('platform/pages::form.meta_title') }}}</label>
						<div class="col-lg-10">
							<input type="text" class="form-control" name="meta_title" id="meta_title" placeholder="{{{ trans('platform/pages::form.meta_title') }}}" value="{{{ Input::old('meta_title', ! empty($page) ? $page->meta_title : null) }}}">

							<span class="help-block">
								{{{ $errors->first('meta_title', ':message') ?: trans('platform/pages::form.meta_title_help') }}}
							</span>
						</div>
					</div>

					{{-- Meta description --}}
					<div class="form-group{{ $errors->first('meta_description', ' has-error') }}">
						<label for="meta_description" class="col-lg-2 control-label">{{{ trans('platform/pages::form.meta_description') }}}</label>
						<div class="col-lg-10">
							<input type="text" class="form-control" name="meta_description" id="meta_description" placeholder="{{{ trans('platform/pages::form.meta_description') }}}" value="{{{ Input::old('meta_description', ! empty($page) ? $page->meta_description : null) }}}">

							<span class="help-block">
								{{{ $errors->first('meta_description', ':message') ?: trans('platform/pages::form.meta_description_help') }}}
							</span>
						</div>
					</div>

					{{-- Enabled --}}
					<div class="form-group{{ $errors->first('enabled', ' has-error') }}">
						<label for="enabled" class="col-lg-2 control-label">{{{ trans('platform/pages::form.enabled') }}}</label>
						<div class="col-lg-4">
							<select class="form-control" name="enabled" id="enabled">
								<option value="1"{{ (Input::old('enabled', ! empty($page) ? (int) $page->enabled : 1) == 1 ? ' selected="selected"' : null) }}>{{{ trans('general.enabled') }}}</option>
								<option value="0"{{ (Input::old('enabled', ! empty($page) ? (int) $page->enabled : 1) == 0 ? ' selected="selected"' : null) }}>{{{ trans('general.disabled') }}}</option>
							</select>

							<span class="help-block">
								{{{ $errors->first('enabled', ':message') ?: trans('platform/pages::form.enabled_help') }}}
							</span>
						</div>
					</div>

					{{-- Visibility --}}
					<div class="form-group{{ $errors->first('visibility', ' has-error') }}">
						<label for="visibility" class="col-lg-2 control-label">{{{ trans('platform/pages::form.visibility.legend') }}}</label>
						<div class="col-lg-4">
							<select class="form-control" name="visibility" id="visibility">
								<option value="always"{{ Input::old('visibility', ! empty($page) ? $page->visibility : 'always') == 'always' ? ' selected="selected"' : null }}>{{{ trans('platform/pages::form.visibility.always') }}}</option>
								<option value="logged_in"{{ Input::old('visibility', ! empty($page) ? $page->visibility : 'always') == 'logged_in' ? ' selected="selected"' : null }}>{{{ trans('platform/pages::form.visibility.logged_in') }}}</option>
								<option value="admin"{{ Input::old('visibility', ! empty($page) ? $page->visibility : 'always') == 'admin' ? ' selected="selected"' : null }}>{{{ trans('platform/pages::form.visibility.admin') }}}</option>
							</select>

							<span class="help-block">
								{{{ $errors->first('visibility', ':message') ?: trans('platform/pages::form.visibility_help') }}}
							</span>
						</div>
					</div>

					{{-- Groups --}}
					<div class="form-group{{ $errors->first('groups', ' has-error') }}{{ Input::old('visibility', ! empty($page) ? $page->visibility : 'always') == 'always' ? ' hide' : null }}">
						<label for="groups" class="col-lg-2 control-label">{{{ trans('platform/pages::form.groups') }}}</label>
						<div class="col-lg-4">
							<select class="form-control" name="groups[]" id="groups" multiple="multiple">
							@foreach ($groups as $group)
								<option value="{{ $group->id }}"{{ array_key_exists($group->id, $pageGroups) ? ' selected="selected"' : null }}>{{ $group->name }}</option>
							@endforeach
							</select>

							<span class="help-block">
								{{{ $errors->first('groups', ':message') ?: trans('platform/pages::form.groups_help') }}}
							</span>
						</div>
					</div>

					{{-- Type --}}
					<div class="form-group{{ $errors->first('type', ' has-error') }}">
						<label for="type" class="col-lg-2 control-label">{{{ trans('platform/pages::form.type') }}}</label>
						<div class="col-lg-4">
							<select class="form-control" name="type" id="type">
								<option value="database"{{ Input::old('type', ! empty($page) ? $page->type : 'database') == 'database' ? ' selected="selected"' : null }}>{{{ trans('platform/content::form.database') }}}</option>
								<option value="filesystem"{{ Input::old('type', ! empty($page) ? $page->type : 'database') == 'filesystem' ? ' selected="selected"' : null }}>{{{ trans('platform/content::form.filesystem') }}}</option>
							</select>

							<span class="help-block">
								{{{ $errors->first('type', ':message') ?: trans('platform/pages::form.type_help') }}}
							</span>
						</div>
					</div>

					{{-- Type : Database --}}
					<div class="type-database{{ Input::old('type', ! empty($page) ? $page->type : 'database') == 'filesystem' ? ' hide' : null }}">

						<div class="form-group{{ $errors->first('value', ' has-error') }}">
							<label for="value" class="col-lg-2 control-label">{{{ trans('platform/pages::form.value') }}}</label>
							<div class="col-lg-10">
								<textarea style="height: 160px;" class="form-control" name="value" id="value">{{{ Input::old('value', ! empty($page) ? $page->value : null) }}}</textarea>

								<span class="help-block">
									{{{ $errors->first('value', ':message') ?: trans('platform/pages::form.value_help') }}}
								</span>
							</div>
						</div>

					</div>

					{{-- Type : Filesystem --}}
					<div class="type-filesystem{{ Input::old('type', ! empty($page) ? $page->type : 'database') == 'database' ? ' hide' : null }}">

						{{-- File --}}
						<div class="form-group{{ $errors->first('file', ' error') }}">
							<label for="file" class="col-lg-2 control-label">{{{ trans('platform/pages::form.file') }}}</label>
							<div class="col-lg-4">
								<select class="form-control" name="file" id="file"{{ Input::old('type', ! empty($page) ? $page->type : null) == 'filesystem' ? ' required' : null }}>
								@foreach ($files as $value => $name)
									<option value="{{ $value }}"{{ Input::old('file', ! empty($page) ? $page->file : null) == $value ? ' selected="selected"' : null}}>{{ $name }}</option>
								@endforeach
								</select>

								<span class="help-block">
									{{{ $errors->first('file', ':message') ?: trans('platform/pages::form.file_help') }}}
								</span>
							</div>
						</div>

					</div>

					{{-- Form actions --}}
					<div class="form-group">

						<div class="col-lg-offset-2 col-lg-10">
							<button class="btn btn-success" type="submit">{{{ trans('button.save') }}}</button>
							<a class="btn btn-default" href="{{{ URL::toAdmin('content') }}}">{{{ trans('button.cancel') }}}</a>

							@if ( ! empty($page) and $pageSegment != 'copy')
							<div class="pull-right">
								<a class="btn btn-danger" data-toggle="modal" data-target="modal-confirm" href="{{ URL::toAdmin("pages/delete/{$page->slug}") }}">{{{ trans('button.delete') }}}</a>

								<a class="btn btn-info" href="{{ URL::toAdmin("pages/copy/{$page->slug}") }}">{{{ trans('button.copy') }}}</a>
							</div>
							@endif
						</div>

					</div>

				</div>

				{{-- Attributes tab --}}
				<div class="tab-pane" id="attributes">

				</div>

			</div>

		</form>

	</div>

</div>

@stop
