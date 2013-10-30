<div class="form-group hide" data-item-type="page">
	<label class="control-label" for="[[ slug ]]_page_uri">Select a page</label>

	<select data-item-form="[[ slug ]]" name="children[[[ slug ]]][page_uri]" id="[[ slug ]]_page_uri" class="form-control">
		@foreach ($pages as $page)
		<option value="{{ $page->id }}">/{{ $page->uri }}</option>
		@endforeach
	</select>
</div>
