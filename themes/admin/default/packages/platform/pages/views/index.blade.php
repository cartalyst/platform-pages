@extends('templates/default')

{{-- Page title --}}
@section('title')
{{ trans('platform/pages::general.title') }} ::
@parent
@stop

{{-- Queue Assets --}}
{{ Asset::queue('tempo', 'js/vendor/tempo/tempo.js', 'jquery') }}
{{ Asset::queue('data-grid', 'js/vendor/cartalyst/data-grid.js', 'tempo') }}

{{-- Inline Styles --}}
@section('styles')
@parent
@stop

{{-- Inline Scripts --}}
@section('scripts')
@parent
<script>
$(function() {

	$.datagrid('main', '.data-grid__table', '.data-grid__pagination', '.data-grid__applied', {
		loader: '.loading',
		type: 'single',
		sort: {
			column: 'created_at',
			direction: 'desc'
		},
		callback: function(obj) {

			$('.total').html(Platform.Utils.shorten(obj.filterCount));
			$('[data-title]').tooltip();

		}
	});

});
</script>
@stop

{{-- Page pages --}}
@section('content')
<header class="page__header">

	<nav class="page__navigation">
		@widget('platform/menus::nav.show', array(1, 1, 'navigation nav nav-tabs', admin_uri()))
	</nav>

	<div class="page__actions">
		<h1><span class="total"></span> {{ trans('platform/pages::general.title') }}</h1>

		<nav class="actions">
			<ul class="navigation navigation--inline-circle">
				<li><a data-placement="right" href="{{ URL::toAdmin('pages/create') }}" data-title="{{ trans('button.create') }}"><i class="icon-plus"></i></a></li>
			</ul>
		</nav>
	</div>

</header>

<section class="page__content">

	<div class="data-grid">

		<header class="clearfix">

			<form method="post" action="" accept-charset="utf-8" data-search data-grid="main" class="data-grid__search">
				<div class="select">
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
			</form>

			<ul class="data-grid__applied navigation navigation--inline" data-grid="main">
				<li data-template style="display: none;">
					<a href="#">
						[? if column == undefined ?]
						[[ valueLabel ]]
						[? else ?]
						[[ valueLabel ]] {{ trans('general.in') }} [[ columnLabel ]]
						[? endif ?]
						<i class="icon-remove-sign"></i>
					</a>
				</li>
			</ul>

			<div class="data-grid__loader">
				<div class="loading">
					<span class="loading__loader"></span>
				</div>
			</div>

		</header>

		<table data-source="{{ URL::toAdmin('pages/grid') }}" data-grid="main" class="data-grid__table">
			<thead>
				<tr>
					<th data-sort="name" data-grid="main" class="sortable">{{ trans('platform/pages::table.name') }}</th>
					<th data-sort="slug" data-grid="main" class="sortable">{{ trans('platform/pages::table.slug') }}</th>
					<th data-sort="enabled" data-grid="main" class="sortable">{{ trans('platform/pages::table.enabled') }}</th>
					<th data-sort="created_at" data-grid="main" class="span2 sortable">{{ trans('platform/pages::table.created_at') }}</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<tr data-template style="display: none;">
					<td>[[ name ]]</td>
					<td>[[ slug ]]</td>
					<td>
						[? if enabled ?]
							{{ trans('general.yes') }}
						[? else ?]
							{{ trans('general.no') }}
						[? endif ?]
					</td>
					<td>[[ created_at | date 'DD MMMM YYYY' ]]</td>
					<td>
						<nav class="actions actions--hidden">
							<ul class="navigation navigation--inline-circle">
								<li>
									<a target="_blank" href="{{ URL::to('[[ uri ]]') }}" data-title="{{ trans('platform/pages::button.view') }}"><i class="icon-eye-open"></i></a>
								</li>
								<li>
									<a data-toggle="modal" data-target="#platform-modal-confirm" href="{{ URL::toAdmin('pages/delete/[[ slug ]]') }}" data-title="{{ trans('button.delete') }}"><i class="icon-trash"></i></a>
								</li>
								<li>
									<a href="{{ URL::toAdmin('pages/copy/[[ slug ]]') }}" data-title="{{ trans('button.copy') }}"><i class="icon-copy"></i></a>
								</li>
								<li>
									<a href="{{ URL::toAdmin('pages/edit/[[ slug ]]') }}" data-title="{{ trans('button.edit') }}"><i class="icon-pencil"></i></a>
								</li>
							</ul>
						</nav>
					</td>
				</tr>
				<tr data-results-fallback style="display: none;">
					<td colspan="5" class="no-results">
						{{ trans('table.no_results') }}
					</td>
				</tr>
			</tbody>
		</table>

	</div>

</section>

<footer class="page__footer">

	<div class="data-grid__pagination clearfix" data-grid="main">
		<div data-template style="display: none;">
			<div class="count">[[ pageStart ]] - [[ pageLimit ]] {{ trans('general.of') }} <span class="total"></span></div>
			<nav class="actions actions--right">
				<ul class="navigation navigation--inline-circle">
					[? if prevPage !== null ?]
					<li>
						<a href="#" data-page="[[ prevPage ]]">
							<i class="icon-chevron-left"></i>
						</a>
					</li>
					[? endif ?]

					[? if nextPage !== null ?]
					<li>
						<a href="#" data-page="[[ nextPage ]]">
							<i class="icon-chevron-right"></i>
						</a>
					</li>
					[? endif ?]
				</ul>
			</nav>
		</div>
	</div>

</footer>
@stop
