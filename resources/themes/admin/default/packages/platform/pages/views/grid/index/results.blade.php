<script type="text/template" data-grid="main" data-grid-template="results">

	<% var results = response.results; %>

	<% if (_.isEmpty(results)) { %>

		<tr>
			<td colspan="5">{{{ trans('common.no_results') }}}</td>
		</tr>

	<% } else { %>

		<% _.each(results, function(r) { %>

			<tr data-grid-row>
				<td><input data-grid-checkbox type="checkbox" name="row[]" value="<%= r.id %>"></td>
				<td><a href="<%= r.edit_uri %>"><%= r.name %></a></td>
				<td class="hidden-xs"><%= r.slug %></td>
				<td>
					<% if (r.enabled == 1) { %>
						{{{ trans('common.enabled') }}}
					<% } else { %>
						{{{ trans('common.disabled') }}}
					<% } %>
				</td>
				<td class="hidden-xs"><%= moment(r.created_at).format('MMM DD, YYYY') %></td>
			</tr>

		<% }); %>

	<% } %>

</script>
