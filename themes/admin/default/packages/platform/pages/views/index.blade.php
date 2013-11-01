@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{{ trans('platform/pages::general.title') }}} ::
@parent
@stop

{{-- Queue assets --}}
{{ Asset::queue('underscore', 'js/underscore/underscore.js', 'jquery') }}
{{ Asset::queue('data-grid', 'js/cartalyst/data-grid.js', 'tempo') }}

{{-- Inline scripts --}}
@section('scripts')
@parent
<script>
$(function() {

	$.datagrid('main', '.data-grid', '.data-grid_pagination', '.data-grid_applied', {
		loader: '.loading',
		type: 'single',
		sort: {
			column: 'created_at',
			direction: 'desc'
		},
		callback: function(obj) {

			$('.total').html(obj.filterCount);
			$('.tip').tooltip();

		}
	});

});
</script>
@stop

{{-- Page content --}}
@section('content')

<div class="row">

	<div class="col-md-12">

		{{-- Page header --}}
		<div class="page-header">

			<span class="pull-right">

				<form method="post" action="" accept-charset="utf-8" data-search data-grid="main" class="form-inline" role="form">

					<div class="form-group">

						<div class="loading"></div>

					</div>

					<div class="form-group">
						<select class="form-control" name="column">
							<option value="all">{{{ trans('general.all') }}}</option>
							<option value="name">{{{ trans('platform/pages::table.name') }}}</option>
							<option value="slug">{{{ trans('platform/pages::table.slug') }}}</option>
							<option value="created_at">{{{ trans('platform/pages::table.created_at') }}}</option>
						</select>
					</div>

					<div class="form-group">
						<input name="filter" type="text" placeholder="{{{ trans('general.search') }}}" class="form-control">
					</div>

					<button class="btn btn-default"><i class="fa fa-search"></i></button>
				</form>

			</span>

			<h1>{{{ trans('platform/pages::general.title') }}}</h1>

		</div>

		<div class="row">

			{{-- Data Grid : Applied Filters --}}
			<div class="col-lg-10">

				<div class="data-grid_applied" data-grid="main"></div>

			</div>

			<div class="col-lg-2 text-right">
				<a class="btn btn-warning" href="{{ URL::toAdmin('pages/create') }}"><i class="fa fa-plus"></i> {{{ trans('platform/pages::button.create') }}}</a>
			</div>

		</div>

		<br />

		<table data-source="{{ URL::toAdmin('pages/grid') }}" data-grid="main" class="data-grid table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th data-sort="name" data-grid="main" class="col-md-3 sortable">{{{ trans('platform/pages::table.name') }}}</th>
					<th data-sort="slug" data-grid="main" class="col-md-2 sortable">{{{ trans('platform/pages::table.slug') }}}</th>
					<th data-sort="enabled" data-grid="main" class="col-md-2 sortable">{{{ trans('platform/pages::table.enabled') }}}</th>
					<th data-sort="created_at" data-grid="main" class="col-md-3 sortable">{{{ trans('platform/pages::table.created_at') }}}</th>
					<th class="col-md-2"></th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>

		{{-- Data Grid : Pagination --}}
		<div class="data-grid_pagination" data-grid="main"></div>

	</div>

</div>

@include('platform/pages::data-grid-tmpl')
@include('platform/pages::data-grid_pagination-tmpl')
@include('platform/pages::data-grid_applied-tmpl')
@include('platform/pages::data-grid_no-results-tmpl')

@stop
