@extends('templates/default')

{{-- Page title --}}
@section('title')
{{ trans('platform/pages::general.title') }} ::
@parent
@stop

{{-- Queue Assets --}}
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
jQuery(document).ready(function($) {
	$.datagrid('main', '#grid', '.grid-pagination', '.applied', {
		loader: '.loading',
		type: 'single',
		sort: {
			column: 'created_at',
			direction: 'desc'
		},
		callback: function(obj) {
			$('#total').html(obj.filterCount);
			$('[data-title]').tooltip();
		}
	});
});
</script>
@stop

{{-- Page pages --}}
@section('content')
<section id="pages">

	<header class="clearfix">
		<h1>{{ trans('platform/pages::general.title') }}</h1>

		<nav class="utilities pull-left">
			<ul>
				<li>
					<a class="btn btn-action" href="{{ URL::toAdmin('pages/create') }}" data-title="{{ trans('button.create') }}"><i class="icon-plus"></i></a>
				</li>
			</ul>
		</nav>

		<nav class="tertiary-navigation pull-right">
			@widget('platform/menus::nav.show', array(2, 1, 'nav nav-pills', admin_uri()))
		</nav>
	</header>

	<hr>

	<section class="content">

		<div class="grid-functions clearfix">

			<form method="post" action="" accept-charset="utf-8" data-search data-grid="main" class="filters pull-left">
				<div class="styled">
					<select name="column">
						<option value="all">{{ trans('general.all') }}</option>
						<option value="name">{{ trans('platform/pages::table.name') }}</option>
						<option value="slug">{{ trans('platform/pages::table.slug') }}</option>
						<option value="created_at">{{ trans('platform/content::table.created_at') }}</option>
					</select>
				</div>

				<div class="input-append pull-left">
					<input name="filter" type="text" placeholder="{{ trans('general.search') }}" class="input-large">
					<button class="btn btn-large"><i class="icon-search"></i></button>
				</div>

				<ul class="applied pull-left" data-grid="main">
					<li data-template style="display: none;" class="btn-group">
						<a class="btn btn-large" href="#">
							[? if column == undefined ?]
							[[ valueLabel ]]
							[? else ?]
							[[ valueLabel ]] {{ trans('general.in') }} [[ columnLabel ]]
							[? endif ?]
						</a>
						<a href="#" class="btn btn-large remove-filter"><i class="icon-remove-sign"></i></a>
					</li>
				</ul>
			</form>

			<div class="grid-pagination pull-right" data-grid="main">
				<div data-template style="display: none;">
					<span class="page-meta">[[ pageStart ]] - [[ pageLimit ]] {{ trans('general.of') }} <span id="total"></span></span>
					[? if prevPage !== null ?]
					<a  href="#" data-page="[[ prevPage ]]" class="btn btn-circle">
						<i class="icon-chevron-left"></i>
					</a>
					[? endif ?]
					[? if nextPage !== null ?]
					<a  href="#" data-page="[[ nextPage ]]" class="btn btn-circle">
						<i class="icon-chevron-right"></i>
					</a>
					[? endif ?]
				</div>
			</div>

		</div>

		<div class="grid-wrap clearfix">

			<div class="loading">
				<div class="loading-wrap">
					<div class="cell">
						{{ trans('general.loading') }}
						<br>
						<span class="loader"></span>
					</div>
				</div>
			</div>

			<table id="grid" data-source="{{ URL::toAdmin('pages/grid') }}" data-grid="main" class="table">
				<thead>
					<tr>
						<th data-sort="id" data-grid="main" class="span1sortable">{{ trans('platform/pages::table.id') }}</th>
						<th data-sort="name" data-grid="main" class="sortable">{{ trans('platform/pages::table.name') }}</th>
						<th data-sort="slug" data-grid="main" class="sortable">{{ trans('platform/pages::table.slug') }}</th>
						<th data-sort="enabled" data-grid="main" class="sortable">{{ trans('platform/pages::table.enabled') }}</th>
						<th data-sort="created_at" data-grid="main" class="sortable">{{ trans('platform/pages::table.created_at') }}</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<tr data-template style="display: none;">
						<td>[[ id ]]</td>
						<td>[[ name ]]</td>
						<td>[[ slug ]]</td>
						<td>
							[? if enabled ?]
								{{ trans('general.yes') }}
							[? else ?]
								{{ trans('general.no') }}
							[? endif ?]
						</td>
						<td>[[ created_at ]]</td>
						<td>
							<div class="actions">
								<a class="btn btn-action" data-toggle="modal" data-target="#platform-modal-confirm" href="{{ URL::toAdmin('pages/delete/[[ id ]]') }}" data-title="{{ trans('button.delete') }}"><i class="icon-trash"></i></a>
								<a class="btn btn-action" href="{{ URL::toAdmin('pages/edit/[[ id ]]') }}" data-title="{{ trans('button.edit') }}"><i class="icon-edit"></i></a>
							</div>
						</td>
					</tr>
					<tr data-results-fallback style="display: none;">
						<td colspan="4" class="no-results">
							{{ trans('table.no_results') }}
						</td>
					</tr>
				</tbody>
			</table>

		</div>

	</section>

</section>
@stop
