<script type="text/template" data-grid="main" id="data-grid_applied-tmpl">

	<% _.each(filters, function(f) { %>

		<span>

			<button type="button" class="btn btn-info tip" title="Remove filter">

				<% if( f.column === 'all') { %>

					<%= f.valueLabel %>

				<% }else{ %>

					<%= f.valueLabel %> {{{ trans('general.in') }}} <em><%= f.columnLabel %></em>

				<% } %>

				<i class="fa fa-times"></i>
			</button>

		</span>

	<% }); %>

</script>
