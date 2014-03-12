<div class="form-group<%= type != 'page' ? ' hide' : null %>" data-item-type="page">
	<label class="control-label" for="<%= slug %>_page_uri">Select a page</label>

	<select data-item-form="<%= slug %>" name="children[<%= slug %>][page][page_id]" id="<%= slug %>_page_uri" class="form-control">
		@foreach ($pages as $page)
		<option value="{{ $page->id }}"<%= page_uri == '{{ $page->id }}' ? ' selected="selected"' : null %>>/{{ $page->uri }}</option>
		@endforeach
	</select>
</div>
