@extends(welcomeTheme().'layouts.app') 
@section('title')
<title>{{websiteTitle('Confirm Password')}}</title>
@endsection 
@section('SEO')
<meta name="description" content="{!!general()->meta_description!!}" />
<meta name="keywords" content="{{general()->meta_keyword}}" />
<meta property="og:title" content="{{websiteTitle('Confirm Password')}}" />
<meta property="og:description" content="{!!general()->meta_description!!}" />
<meta property="og:image" content="{!!general()->meta_description!!}" />
<meta property="og:url" content="{{route('resetPassword',$token)}}" />
<link rel="canonical" href="{{route('resetPassword',$token)}}">
@endsection 
@push('css')
<style>

</style>
@endpush 

@section('contents')

@endsection 
@push('js') 
@endpush