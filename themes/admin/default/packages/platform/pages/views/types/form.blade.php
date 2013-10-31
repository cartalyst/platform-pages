<?php
	$childId   = ! empty($child) ? "{$child->id}_%s" : 'new-child_%s';
	$childName = ! empty($child) ? "children[{$child->id}][%s]" : 'new-child_%s';
?>

<div class="form-group{{ ! empty($child) ? ( $child->type != 'page' ? ' hide' : null ) : ' hide' }}" data-item-type="page">
	<label class="control-label" for="{{ sprintf($childId, 'page_uri') }}">Select a page</label>

	<select data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" name="{{ sprintf($childName, 'page_uri') }}" id="{{ sprintf($childId, 'page_uri') }}" class="form-control">
		@foreach ($pages as $page)
		<option value="{{ $page->id }}">/{{ $page->uri }}</option>
		@endforeach
	</select>
</div>
