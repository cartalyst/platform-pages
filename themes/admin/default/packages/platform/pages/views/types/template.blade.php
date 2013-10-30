<div class="form-group<% if (type != 'page') { %> hide<% } %>" data-item-type="page">
	<label class="control-label" for="<%= slug %>_page_uri">Select a page</label>

	<select data-item-form="<%= slug %>" name="children[<%= slug %>][page_uri]" id="<%= slug %>_page_uri" class="form-control">
		@foreach ($pages as $page)
		[? if page_uri == '{{ $page->id }}' ?]
		<option value="{{ $page->id }}" selected="selected">/{{ $page->uri }}</option>
		[? else ?]
		<option value="{{ $page->id }}">/{{ $page->uri }}</option>
		[? endif ?]
		@endforeach
	</select>
</div>
