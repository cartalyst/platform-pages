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
	$.datagrid('main', '#grid', '#pagination', '#applied', {
		loader: '#loader'
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

	<section class="pages">

		<div class="actions clearfix">
			<a href="{{ URL::toAdmin('pages/create') }}" class="btn btn-large btn-primary pull-right">@lang('button.create')</a>
		</div>

		<form method="post" action="" accept-charset="utf-8" data-search data-key="main" class="form-inline">
			<select name="column" class="input-small">
				<option value="all">All</option>
				<option value="name">Name</option>
			</select>
			<input name="filter" type="text" placeholder="Filter All" class="input-large">
			<button class="btn add-global-filter">
				Add
			</button>
		</form>

		<ul id="applied" data-key="main">
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

				<ul id="pagination" class="nav nav-tabs" data-key="main">
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

					<table id="grid" data-source="{{ URL::toAdmin('pages/grid') }}" data-key="main" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th data-sort="id" data-key="main" class="sortable">@lang('platform/pages::table.id')</th>
								<th data-sort="name" data-key="main" class="sortable">@lang('platform/pages::table.name')</th>
								<th data-sort="slug" data-key="main" class="sortable">@lang('platform/pages::table.slug')</th>
								<th data-sort="status" data-key="main" class="sortable">@lang('platform/pages::table.status')</th>
								<th data-sort="created_at" data-key="main" class="sortable">@lang('platform/pages::table.created_at')</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<tr data-template>
								<td class="span1">[[id]]</td>
								<td>[[name]]</td>
								<td>[[slug]]</td>
								<td>
									[? if status == 1 ?]
										@lang('general.enabled')
									[? else ?]
										@lang('general.disabled')
									[? endif ?]
								</td>
								<td>[[created_at]]</td>
								<td class="span1">

									<div class="btn-group">
										<a href="{{ URL::toAdmin('pages/edit/[[id]]') }}" class="btn btn-small">
											@lang('button.edit')
										</a>

										<a href="{{ URL::toAdmin('pages/delete/[[id]]') }}" class="btn btn-small btn-danger">
											@lang('button.delete')
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
@stop
