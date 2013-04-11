@extends('templates/default')

@section('title')
{{ Lang::get('platform/pages::general.title') }}
@stop

@section('assets')

@stop

@section('scripts')

@stop

@section('content')
<div class="page-header">
	<h3>
		{{ Lang::get('platform/pages::general.title') }}

		<div class="pull-right">
			<a href="{{ URL::to(ADMIN_URI . '/pages/create') }}" class="btn btn-info btn-small">{{ Lang::get('button.create') }}</a>
		</div>
	</h3>
</div>

<table class="table table-bordered">
	<thead>
		<tr>
			<th>{{ Lang::get('platform/pages::table.id') }}</th>
			<th>{{ Lang::get('platform/pages::table.name') }}</th>
			<th>{{ Lang::get('platform/pages::table.slug') }}</th>
			<th class="span2">{{ Lang::get('platform/pages::table.type') }}</th>
			<th class="span2">{{ Lang::get('table.status') }}</th>
			<th class="span2">{{ Lang::get('table.actions') }}</th>
		</tr>
	</thead>
	<tbody>
		@foreach ($pages as $page)
			<tr>
				<td class="span1">
					{{ $page->id }}
				</td>
				<td>
					{{ $page->name }}
				</td>
				<td>
					{{ $page->slug }}
				</td>
				<td>
					{{ ($page->type ? 'Database' : 'File') }}
				</td>
				<td>
					{{ Lang::get('general.' . ($page->status ? 'enabled' : 'disabled')) }}
				</td>
				<td>
					<div class="btn-group">
						<a href="{{ URL::to(ADMIN_URI . "/pages/edit/{$page->id}") }}" class="btn btn-mini">
							{{ Lang::get('button.edit') }}
						</a>
						<a href="{{ URL::to(ADMIN_URI . "/pages/clone/{$page->id}") }}" class="btn btn-mini btn-info">
							{{ Lang::get('button.clone') }}
						</a>
						<a href="{{ URL::to(ADMIN_URI . "/pages/delete/{$page->id}") }}" class="btn btn-mini btn-danger">
							{{ Lang::get('button.delete') }}
						</a>
					</div>
				</td>
			</tr>
		@endforeach
	</tbody>
	<tfoot>
		<tr>
			<td colspan="6">
				<div class="pull-right">
					{{ $pages->links() }}
				</div>
			</td>
		</tr>
	</tfoot>
</table>


@stop
