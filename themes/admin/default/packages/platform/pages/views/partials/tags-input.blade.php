<div class="form-group{{ Alert::form('tags', ' has-error') }}">

	<label for="tags" class="control-label">
		<i class="fa fa-info-circle" data-toggle="popover" data-placement="left" data-content="{{{ trans('platform/pages::model.tags_help') }}}"></i>
		{{{ trans('platform/pages::model.tags') }}}
	</label>

	<select class="form-control" name="tags[]" id="tags" data-parsley-trigger="change" multiple>
		@foreach ($availableTags as $tag)
		<option value="{{ $tag->slug }}"{{ in_array($tag->slug, $tags) ? ' selected' : null }}>{{ $tag->name }}</option>
		@endforeach
	</select>

	<span class="help-block">
		{{{ Alert::form('tags') ?: trans('platform/pages::model.tags_help') }}}
	</span>

</div>
