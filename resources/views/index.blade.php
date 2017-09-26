@extends('voyager::master')

@section('css')
	<style type="text/css">
		
		#themes{
			margin-top:20px;
		}

		#themes .page-title{
			background-image: linear-gradient(120deg, #f093fb 0%, #f5576c 100%);
			color:#fff;
			width:100%;
			border-radius:3px;
			margin-bottom:15px;
			overflow:hidden;
		}
		#themes .page-title small{
			margin-left:10px;
			color:rgba(255, 255, 255, 0.85);
		}

		#themes .page-title:after {
		    content: '';
		    width: 110%;
		    background: rgba(255, 255, 255, 0.1);
		    position: absolute;
		    bottom: -24px;
		    z-index: 9;
		    display: block;
		    transform: rotate(-2deg);
		    height: 50px;
		    right: 0px;
		}

		#themes .page-title:before {
		    content: '';
		    width: 110%;
		    background: rgba(0, 0, 0, 0.04);
		    position: absolute;
		    top: -20px;
		    z-index: 9;
		    display: block;
		    transform: rotate(2deg);
		    height: 50px;
		    left: 0px;
		}

		.theme_thumb{
			width:100%;
			height:auto;
		}

		.theme{
			border:1px solid #f1f1f1;
			border-radius:3px;
		}

		.theme_details{
			border-top: 1px solid #eaeaea;
    		padding: 15px;
		}

		.theme_details:after{
			display:block;
			clear:both;
			content:'';
			width:100%;
		}

		.panel-body .theme_details h4{
			margin-top:10px;
			float:left;
		}

		.theme_details a i, .theme_details span i{
			position:relative;
			top:2px;
			margin-bottom:0px;
		}

		.theme_details a.btn{
			color:#79797f;
			border:1px solid #e1e1e1;
		}

		.theme_details a.btn:hover{
			background:#2ecc71;
			border-color:#2ecc71;
			color:#fff;
		}

		.theme_details span{
			cursor:default;
		}
		.theme-options{
			padding: 8px 10px;
		    border: 1px solid #e1e1e1;
		    border-radius: 3px;
		    float: right;
		    width: 36px;
		    height: 36px;
		    margin-top: 5px;
		    margin-right: 10px;
		    cursor: pointer;
		    transition:all 0.3s ease;
		}
		.theme-options:hover{
			background:#fcfcfc;
			border: 1px solid #ddd;
		}

	</style>
@endsection

@section('content')

<div id="themes">	

	<div class="container-fluid">

		<h1 class="page-title">
        	<i class="voyager-paint-bucket"></i> Themes
        	<small>Choose a theme below</small>
        </h1>

        <div class="panel">
        	<div class="panel-body">
        		
        		<div class="row">
	        		@foreach($themes as $theme)

	        			<div class="col-md-4">
	        				<div class="theme">
		        				<img class="theme_thumb" src="/themes/{{ $theme->folder }}/{{ $theme->folder }}.jpg">
		        				<div class="theme_details">
		        					<h4>{{ $theme->name }}</h4>
		        					@if($theme->active)
		        						<span class="btn btn-success pull-right"><i class="voyager-check"></i> Active</span>
		        					@else
		        						<a class="btn btn-outline pull-right" href="{{ route('voyager.theme.activate', $theme->folder) }}"><i class="voyager-check"></i> Activate Theme</a>
		        					@endif
		        					<a href="{{ route('voyager.theme.options', $theme->folder) }}" class="voyager-params theme-options"></a>
		        				</div>
		        			</div>
	        			</div>

	        		@endforeach
        		</div>

        	</div>
        </div>

    </div>

</div>
	

@endsection