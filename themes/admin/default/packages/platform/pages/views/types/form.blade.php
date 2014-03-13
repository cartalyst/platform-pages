<?php
	$childId   = ! empty($child) ? "{$child->id}_%s" : 'new-child_%s';
	$childName = ! empty($child) ? "children[{$child->id}]%s" : 'new-child_%s';
?>

<div class="form-group{{ (empty($child) or ( ! empty($child) and $child->type != 'page')) ? ' hide' : null }}" data-item-type="page">
	<label class="control-label" for="{{ sprintf($childId, 'page_id') }}">Select a page</label>

	<select data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" name="{{ sprintf($childName, '[page][page_id]') }}" id="{{ sprintf($childId, 'page_uri') }}" class="form-control">
		@foreach ($pages as $page)
		<option value="{{ $page->id }}"{{ Input::old('page.page_id', ! empty($child) ? $child->page_id : null) == $page->id ? ' selected="selected"' : null }}>/{{ $page->uri }}</option>
		@endforeach
	</select>
</div>
