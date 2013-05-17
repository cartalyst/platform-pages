@extends('templates/default')

{{-- Page title --}}
@section('title')
@lang('platform/pages::general.title') ::
@parent
@stop

{{-- Queue Assets --}}
{{ Asset::queue('tab', 'js/vendor/bootstrap/tab.js', 'jquery') }}
{{ Asset::queue('tempo', 'js/vendor/tempo/tempo.js', 'jquery') }}
{{ Asset::queue('data-grid', 'js/vendor/cartalyst/data-grid.js', 'tempo') }}

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
<script>
jQuery(document).ready(function($){
	$.datagrid('main', '.table', '.pagination', '.applied', {
		loader: '.table-processing'
	});
});
</script>
@stop

{{-- Page pages --}}
@section('content')
<section id="pages">

	<header class="clearfix">
		<h1>@lang('platform/pages::general.title')</h1>
		<nav class="tertiary-navigation">
			@widget('platform/ui::nav.show', array(2, 1, 'nav nav-pills', app('platform.admin.uri')))
		</nav>
	</header>

	<hr>

	<section class="content">

		<div class="clearfix">

			<form method="post" action="" accept-charset="utf-8" data-search data-grid="main" class="form-inline pull-left">
				<select name="column" class="input-medium">
					<option value="all">@lang('general.all')</option>
					<option value="name">@lang('platform/pages::table.name')</option>
					<option value="slug">@lang('platform/pages::table.slug')</option>
					<option value="created_at">@lang('platform/pages::table.created_at')</option>
				</select>
				<input name="filter" type="text" placeholder="Filter All" class="input-large">
				<button class="btn btn-medium">Add Filter</button>
				<button class="btn btn-medium" data-reset data-grid="main">Reset</button>
			</form>

			<div class="processing pull-left">
				<div class="table-processing" style="display: none;">Processing...</div>
			</div>

		</div>

		<ul class="applied" data-grid="main">
			<li data-template>
				<a href="#" class="remove-filter btn btn-small">
					[? if column == undefined ?]
					[[ valueLabel ]]
					[? else ?]
					[[ valueLabel ]] in [[ columnLabel ]]
					[? endif ?]
					<span class="close" style="float: none;">&times;</span>
				</a>
			</li>
		</ul>

		<div id="table">

			<div class="tabbable tabs-right">

				<a href="{{ URL::toAdmin('pages/create') }}" class="btn btn-large btn-primary pull-right create">@lang('button.create')</a>

				<ul class="pagination nav nav-tabs" data-grid="main">
					<li data-template data-if-infiniteload>
						<a href="#" class="goto-page" data-page="[[ page ]]">
							Load More
						</a>
					</li>
					<li data-template data-if-throttle>
						<a href="#" class="goto-page" data-throttle>
							[[ label ]]
						</a>
					</li>
					<li data-template class="[? if active ?]active[? endif ?]">
						<a  href="#" data-page="[[ page ]]" class="goto-page">
							[[ pageStart ]] - [[ pageLimit ]]
						</a>
					</li>
				</ul>

				<div class="tab-content">

					<table class="table table-striped table-bordered" data-grid="main" data-source="{{ URL::toAdmin('pages/grid') }}">
						<thead>
							<tr>
								<th data-sort="id" data-grid="main" class="span1sortable">@lang('platform/pages::table.id')</th>
								<th data-sort="name" data-grid="main" class="sortable">@lang('platform/pages::table.name')</th>
								<th data-sort="slug" data-grid="main" class="sortable">@lang('platform/pages::table.slug')</th>
								<th data-sort="enabled" data-grid="main" class="sortable">@lang('platform/pages::table.enabled')</th>
								<th data-sort="created_at" data-grid="main" class="sortable">@lang('platform/pages::table.created_at')</th>
								<th class="span1">@lang('table.actions')</th>
							</tr>
						</thead>
						<tbody>
							<tr data-template>
								<td>[[ id ]]</td>
								<td>[[ name ]]</td>
								<td>[[ slug ]]</td>
								<td data-type="select" data-column="enabled" data-mappings="Yes:1|No:0">
									[? if enabled ?]
										@lang('general.yes')
									[? else ?]
										@lang('general.no')
									[? endif ?]
								</td>
								<td data-column="created_at">[[ created_at ]]</td>
								<td>

									<div class="btn-group">
										<a href="{{ URL::toAdmin('pages/edit/[[ id ]]') }}" class="btn" title="@lang('button.edit')">
											<i class="icon-edit"></i>
										</a>

										<a data-toggle="modal" data-target="#platform-modal-confirm" href="{{ URL::toAdmin('pages/delete/[[ id ]]') }}" class="btn btn-danger" title="@lang('button.delete')">
											<i class="icon-trash"></i>
										</a>
									</div>

								</td>
							</tr>
						</tbody>
					</table>

				</div>

			</div>

		</div>

	</section>

</section>

@widget('platform/ui::modal.confirm')
@stop
