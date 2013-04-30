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
	$('#grid').dataGrid();
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

		<div id="grid" data-source="{{ URL::toAdmin('pages/grid') }}" data-results=".grid-results" data-filters=".grid-filters" data-applied-filters=".grid-applied-filters" data-pagination=".grid-pagination">

			<div class="grid-filters">

				<div class="clearfix">
					<div class="form-inline">

						<div class="pull-left">
							<div class="input-append">
								<input type="text" placeholder="Filter All">
								<button class="btn add-global-filter">
									Add
								</button>
							</div>
							&nbsp;
						</div>

						<div class="pull-left" data-template>

							{{-- Build different HTML based on the type --}}
							[? if type == 'select' ?]
								<select class="input-small" id="grid-filters-[[column]]" data-column="[[column]]">
									<option>
										-- [[label]] --
									</option>

									{{-- Need to work out how to embed each <option> inside the <optgroup> data-template... --}}
									<option data-template-for="mappings" value="[[value]]">
										[[label]]
									</option>
								</select>

								<button class="btn add-filter">
									Add
								</button>
							[? else ?]
								<div class="input-append">
									<input type="text" class="input-small" id="grid-filters-[[column]]" data-column="[[column]]" placeholder="[[label]]">

									<button class="btn add-filter">
										Add
									</button>
								</div>
								&nbsp;
							[? endif ?]

						</div>

					</div>
				</div>

			</div>

			<br>

			<ul class="nav nav-tabs grid-applied-filters">
				<li data-template>
					<a href="#" class="remove-filter">
						[? if type == 'global' ?]
							<strong>[[value]]</strong>
						[? else ?]
							<small><em>([[column]])</em></small> <strong>[[value]]</strong>
						[? endif ?]
						<span class="close" style="float: none;">&times;</span>
					</a>
				</li>
			</ul>

			<div class="tabbable tabs-right">

				<ul class="nav nav-tabs grid-pagination">
					<li data-template class="[? if active ?] active [? endif ?]">
						<a href="#" data-page="[[page]]" data-toggle="tab" class="goto-page">
							Page #[[page]]
						</a>
					</li>
				</ul>

				<div class="tab-pages">

					<table class="table table-striped table-bordered grid-results">
						<thead>
							<tr>
								<th data-column="id">@lang('platform/pages::table.id')</th>
								<th data-column="name">@lang('platform/pages::table.name')</th>
								<th data-column="slug">@lang('platform/pages::table.slug')</th>
								<th data-column="enabled">@lang('platform/pages::table.enabled')</th>
								<th data-column="created_at">@lang('platform/pages::table.created_at')</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<tr data-template>
								<td data-column="id" class="span1">[[id]]</td>
								<td data-column="name">[[name]]</td>
								<td data-column="slug">[[slug]]</td>
								<td data-type="select" data-column="enabled" data-mappings="Yes:1|No:0">
									[? if enabled ?]
										@lang('general.yes')
									[? else ?]
										@lang('general.no')
									[? endif ?]
								</td>
								<td data-column="created_at">[[created_at]]</td>
								<td data-static class="span1">

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
