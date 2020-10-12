@extends(config('forum.master_file_extend'))

@section(config('forum.yields.head'))
    <link href="{{ url('/forum/assets/vendor/spectrum/spectrum.css') }}" rel="stylesheet">
	<link href="{{ url('/forum/assets/css/forum.css') }}" rel="stylesheet">
	@if($forum_editor == 'simplemde')
		<link href="{{ url('/forum/assets/css/simplemde.min.css') }}" rel="stylesheet">
	@elseif($forum_editor == 'trumbowyg')
		<link href="{{ url('/forum/assets/vendor/trumbowyg/ui/trumbowyg.css') }}" rel="stylesheet">
		<style>
			.trumbowyg-box, .trumbowyg-editor {
				margin: 0px auto;
			}
		</style>
	@endif
@stop

@section('content')

<div id="forum" class="forum_home">

{{--	<div id="forum_header" style="background-color:#263238;">--}}
{{--		<div class="container">--}}
{{--			<h1>@lang('forum::intro.headline')</h1>--}}
{{--		</div>--}}
{{--	</div>--}}

	@if(config('forum.errors'))
		@if(Session::has('forum_alert'))
			<div class="forum-alert alert alert-{{ Session::get('forum_alert_type') }}">
				<div class="container">
					<strong><i class="forum-alert-{{ Session::get('forum_alert_type') }}"></i> {{ config('forum.alert_messages.' . Session::get('forum_alert_type')) }}</strong>
					{{ Session::get('forum_alert') }}
					<i class="forum-close"></i>
				</div>
			</div>
			<div class="forum-alert-spacer"></div>
		@endif

		@if (count($errors) > 0)
			<div class="forum-alert alert alert-danger">
				<div class="container">
					<p><strong><i class="forum-alert-danger"></i> @lang('forum::alert.danger.title')</strong> @lang('forum::alert.danger.reason.errors')</p>
					<ul>
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			</div>
		@endif
	@endif

	<div class="px:0 container forum_container">

	    <div class="row">

	    	<div class="col-md-3 left-column">
	    		<!-- SIDEBAR -->
	    		<div class="forum_sidebar">
					@auth
					<a class="mb-3 text-center p-3 font-weight-bold rounded" style="background-color: rgba(255, 255, 255, 0.03);" href="#" id="new_discussion_btn"><i class="forum-new"></i> @lang('forum::messages.discussion.new')</a>
					@endauth
					<div class="hidden md:block w-full md:w-auto">
						<ul class="nav nav-pills nav-stacked" style="display: block;">
							<li style="display: block;">
								<a href="{{ route('forum.home') }}"><div class="forum-box bg-secondary"></div>All discussions
								</a>
							</li>
						</ul>
          				{!! $categoriesMenu !!}
					</div>

					<div class="block md:hidden w-full">
						<div class="block relative">
							<select id="selectCategory" class="block appearance-none w-full bg-secondary text-gray-200 border border-secondary hover:border-gray-500 px-4 py-2 pr-8 rounded-top shadow leading-tight focus:outline-none focus:shadow-outline">
								@php
									$currentCategory = \MeinderA\Forum\Models\Category::find($current_category_id);
									if (! $currentCategory) {
										$allCategoriesSelected = true;
									} else {
										$allCategoriesSelected = false;
									}
								@endphp
								<option value="null" @php if ($allCategoriesSelected) { echo 'selected'; } @endphp>All categories</option>
								@foreach (\MeinderA\Forum\Models\Category::all() as $category)
									<option value="{{ $category->slug }}"
										@php if($currentCategory && $category->id === $currentCategory->id) {
												echo ' selected ';
											}
										@endphp
									>{{ $category->name }}</option>
								@endforeach
							</select>
							<script>
								$("#selectCategory").change(function () {
									var selectedCategory = $(this).find("option:selected").attr('value');
									if (selectedCategory === 'null') {
										window.location = "{{ route('forum.home') }}";
									} else {
										window.location = "{{ route('forum.category.show', '') }}/" + selectedCategory;
									}
								});
							</script>
							<div class="pointer-events-none absolute inset-y-0 right-0 flex items-center  px-2 text-gray-400">
								<svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
							</div>
						</div>
					</div>
				</div>
				<!-- END SIDEBAR -->
	    	</div>
	        <div class="col-md-9 right-column">
	        	<div class="panel">
		        	<ul class="discussions">
		        		@foreach($discussions as $discussion)
				        	<li style="height:75px;">
								<a href="/{{ config('forum.routes.home') }}/{{ config('forum.routes.discussion') }}/{{ $discussion->category->slug }}/{{ $discussion->slug }}">
									<div class="text-gray-300 border-bottom bg-tertiary border-secondary flex justify-content-between items-center text-sm md:text-md h-100">
										<div class="mr-1 h-100" style="background-color: {{ $discussion->category->color }}; width: 3px;"></div>
										<div class="w-auto mt-3 h-100">
											<button class="flex text-sm md:text-md border-2 border-transparent rounded-full focus:outline-none focus:border-gray-200 transition duration-150 ease-in-out">
												<img class="h-8 w-8 rounded-full object-cover" src="{{ $discussion->user->profile_photo_url }}" alt="{{ $discussion->user->name }}" />
											</button>
											<div class="text-muted text-xs">{{ $discussion->user->name }}</div>
										</div>
										<div class="w-4/6 block mt-3 pl-3">
											<span class="font-bold">{{ $discussion->title }}</span>
											@if($discussion->post[0]->markdown)
												<?php $discussion_body = GrahamCampbell\Markdown\Facades\Markdown::convertToHtml( $discussion->post[0]->body ); ?>
											@else
												<?php $discussion_body = $discussion->post[0]->body; ?>
											@endif
											<p class="block text-xs md:text-sm text-muted overflow-hidden break-words">{{ substr(strip_tags($discussion_body), 0, 120) }}@if(strlen(strip_tags($discussion_body)) > 200){{ '...' }}@endif</p>
										</div>
										<div class="w-auto h-100 mt-3 px-3">
											<span class="font-bold text-xs md:text-sm text-gray-500 block">{{ $discussion->postsCount[0]->total }} <i class="forum-bubble text-muted"></i></span>
										</div>
									</div>
								</a>
				        	</li>
			        	@endforeach
		        	</ul>
	        	</div>

	        	<div id="pagination">
	        		{{ $discussions->links() }}
	        	</div>

	        </div>
	    </div>
	</div>

	<div id="new_discussion">


    	<div class="forum_loader dark" id="new_discussion_loader">
		    <div></div>
		</div>

    	<form id="forum_form_editor" action="/{{ config('forum.routes.home') }}/{{ config('forum.routes.discussion') }}" method="POST">
        	<div class="row">
	        	<div class="col-md-7">
		        	<!-- TITLE -->
	                <input type="text" class="form-control" id="title" name="title" placeholder="@lang('forum::messages.editor.title')" value="{{ old('title') }}" >
	            </div>

	            <div class="col-md-4">
		            <!-- CATEGORY -->
					<select id="forum_category_id" class="form-control" name="forum_category_id">
						<option value="">@lang('forum::messages.editor.select')</option>
						@foreach($categories as $category)
							@if(old('forum_category_id') == $category->id)
								<option value="{{ $category->id }}" selected>{{ $category->name }}</option>
							@elseif(!empty($current_category_id) && $current_category_id == $category->id)
								<option value="{{ $category->id }}" selected>{{ $category->name }}</option>
							@else
								<option value="{{ $category->id }}">{{ $category->name }}</option>
							@endif
						@endforeach
					</select>
		        </div>

		        <div class="col-md-1">
		        	<i class="forum-close"></i>
		        </div>
	        </div><!-- .row -->

            <!-- BODY -->
        	<div id="editor">
        		@if( $forum_editor == 'tinymce' || empty($forum_editor) )
					<label id="tinymce_placeholder">@lang('forum::messages.editor.tinymce_placeholder')</label>
    				<textarea id="body" class="richText" name="body" placeholder="">{{ old('body') }}</textarea>
    			@elseif($forum_editor == 'simplemde')
    				<textarea id="simplemde" name="body" placeholder="">{{ old('body') }}</textarea>
				@elseif($forum_editor == 'trumbowyg')
					<textarea class="trumbowyg" name="body" placeholder="@lang('forum::messages.editor.tinymce_placeholder')">{{ old('body') }}</textarea>
				@endif
    		</div>

            <input type="hidden" name="_token" id="csrf_token_field" value="{{ csrf_token() }}">

            <div id="new_discussion_footer" class="float-right">
				<div class="pull-right">
					<button id="submit_discussion" class="btn btn-primary pull-right"><i class="forum-new"></i> @lang('forum::messages.discussion.create')</button>
					<a href="/{{ config('forum.routes.home') }}" class="btn btn-default pull-right" id="cancel_discussion">@lang('forum::messages.words.cancel')</a>
					<div style="clear:both"></div>
				</div>
            </div>
        </form>

    </div><!-- #new_discussion -->

</div>

@if( $forum_editor == 'tinymce' || empty($forum_editor) )
	<input type="hidden" id="forum_tinymce_toolbar" value="{{ config('forum.tinymce.toolbar') }}">
	<input type="hidden" id="forum_tinymce_plugins" value="{{ config('forum.tinymce.plugins') }}">
@endif
<input type="hidden" id="current_path" value="{{ Request::path() }}">

@endsection

@section(config('forum.yields.footer'))


@if( $forum_editor == 'tinymce' || empty($forum_editor) )
	<script src="{{ url('/forum/assets/vendor/tinymce/tinymce.min.js') }}"></script>
	<script src="{{ url('/forum/assets/js/tinymce.js') }}"></script>
	<script>
		var my_tinymce = tinyMCE;
		$('document').ready(function(){
			$('#tinymce_placeholder').click(function(){
				my_tinymce.activeEditor.focus();
			});
		});
	</script>
@elseif($forum_editor == 'simplemde')
	<script src="{{ url('/forum/assets/js/simplemde.min.js') }}"></script>
	<script src="{{ url('/forum/assets/js/forum_simplemde.js') }}"></script>
@elseif($forum_editor == 'trumbowyg')
	<script src="{{ url('/forum/assets/vendor/trumbowyg/trumbowyg.min.js') }}"></script>
	<script src="{{ url('/forum/assets/vendor/trumbowyg/plugins/preformatted/trumbowyg.preformatted.min.js') }}"></script>
	<script src="{{ url('/forum/assets/js/trumbowyg.js') }}"></script>
@endif

<script src="{{ url('/forum/assets/vendor/spectrum/spectrum.js') }}"></script>
<script src="{{ url('/forum/assets/js/forum.js') }}"></script>
<script>
	$('document').ready(function(){

		$('.forum-close, #cancel_discussion').click(function(){
			$('#new_discussion').slideUp();
		});
		$('#new_discussion_btn').click(function(){
			@if(Auth::guest())
				window.location.href = "{{ route('login') }}";
			@else
				$('#new_discussion').slideDown();
				$('#title').focus();
			@endif
		});

		$("#color").spectrum({
		    color: "#333639",
		    preferredFormat: "hex",
		    containerClassName: 'forum-color-picker',
		    cancelText: '',
    		chooseText: 'close',
		    move: function(color) {
				$("#color").val(color.toHexString());
			}
		});

		@if (count($errors) > 0)
			$('#new_discussion').slideDown();
			$('#title').focus();
		@endif


	});
</script>
@stop
