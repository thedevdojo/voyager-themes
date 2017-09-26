@extends('voyager::master')

@section('css')

	<style type="text/css">

		#theme_options .page-title small{
			margin-left:10px;
			color:rgba(0, 0, 0, 0.55);
		}

	</style>

@endsection

@section('content')

<div id="theme_options">	

	<div class="container-fluid">

		<h1 class="page-title">
        	<i class="voyager-params"></i> {{ $theme->name }} Theme Options
        	<small>Options and settings for the {{ $theme->name }} theme.</small>
        </h1>

        <div class="panel">
        	<div class="panel-body">
        		
	        		
	        		@if(file_exists(public_path('themes') . '/' . $theme->folder . '/options.blade.php'))
	        			@include('theme::' . $theme->folder . '.options')
	        		@else
	        			<p>No options file for {{ $theme->name }} theme.</p>
	        		@endif

        	</div>
        </div>

    </div>

</div>
	

@endsection

@section('javascript')
	<script>
		$('document').ready(function(){
			
		});
	</script>
@endsection