@extends(welcomeTheme().'layouts.app') 
@section('title')
<title>{{websiteTitle('Register')}}</title>
@endsection 
@section('SEO')
<meta name="description" content="{!!general()->meta_description!!}" />
<meta name="keywords" content="{{general()->meta_keyword}}" />
<meta property="og:title" content="{{websiteTitle('Register')}}" />
<meta property="og:description" content="{!!general()->meta_description!!}" />
<meta property="og:image" content="{!!general()->meta_description!!}" />
<meta property="og:url" content="{{route('register')}}" />
<link rel="canonical" href="{{route('register')}}">
@endsection 
@push('css')
<style>

</style>
@endpush 

@section('contents')

@endsection 
@push('js') 
@endpush
