<div class="form-group{{ Alert::form('tags', ' has-error') }}">

	<select class="form-control" name="tags[]" id="tags" data-parsley-trigger="change" multiple>
		@foreach ($availableTags as $tag)
		<option value="{{ $tag->slug }}"{{ in_array($tag->slug, $tags) ? ' selected' : null }}>{{ $tag->name }}</option>
		@endforeach
	</select>

	<span class="help-block">{{{ Alert::form('tags') }}}</span>

</div>
