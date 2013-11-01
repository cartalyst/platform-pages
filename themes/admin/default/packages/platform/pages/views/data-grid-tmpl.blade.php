<script type="text/template" data-grid="main" id="data-grid-tmpl">

	<% _.each(results, function(r) { %>

		<tr>
			<td><%= r.name %></td>
			<td><%= r.slug %></td>
			<td>
				<% if( r.enabled == 1) { %>
					{{{ trans('general.enabled') }}}
				<% }else{ %>
					{{{ trans('general.disabled') }}}
				<% } %>
			</td>
			<td><%= r.created_at %></td>
			<td>
				<a class="btn btn-primary tip" href="{{ URL::toAdmin('pages/edit/<%= r.slug %>') }}" title="{{{ trans('platform/pages::button.edit') }}}"><i class="fa fa-edit"></i></a>

				<a class="btn btn-warning tip" href="{{ URL::toAdmin('pages/copy/<%= r.slug %>') }}" title="{{{ trans('platform/pages::button.copy') }}}"><i class="fa fa-copy"></i></a>

				<a class="btn btn-danger tip" data-toggle="modal" data-target="modal-confirm" href="{{ URL::toAdmin('pages/delete/<%= r.slug %>') }}" title="{{{ trans('platform/pages::button.delete') }}}"><i class="fa fa-trash-o"></i></a>
			</td>
		</tr>

	<% }); %>

</script>
