<div class="form-group[? if type != 'page' ?] hide[? endif ?]" data-item-type="page">
	<label class="control-label" for="[[ slug ]]_page_uri">Select a page</label>

	<select data-item-form="[[ slug ]]" name="children[[[ slug ]]][page_uri]" id="[[ slug ]]_page_uri" class="form-control">
		@foreach ($pages as $page)
		<option value="{{ $page->id }}"[? if page_uri == '{{ $page->id }}' ?] selected="selected"[? endif ?]>/{{ $page->uri }}</option>
		@endforeach
	</select>
</div>
