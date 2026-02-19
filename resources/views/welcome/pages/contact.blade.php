@extends(welcomeTheme().'layouts.app') @section('title')
<title>{{$page->seo_title?:websiteTitle($page->name)}}</title>
@endsection @section('SEO')
<meta name="title" property="og:title" content="{{$page->seo_title?:websiteTitle($page->name)}}" />
<meta name="description" property="og:description" content="{!!$page->seo_description?:general()->meta_description!!}" />
<meta name="keywords" content="{{$page->seo_keyword?:general()->meta_keyword}}" />
<meta name="image" property="og:image" content="{{asset($page->image())}}" />
<meta name="url" property="og:url" content="{{route('pageView',$page->slug?:'no-title')}}" />
<link rel="canonical" href="{{route('pageView',$page->slug?:'no-title')}}">
@endsection
@push('css')
<style>

</style>
@endpush 

@section('contents')
<div class="breadcrumb-area"
@if($page->bannerFile)
style="background-image:url({{asset($page->banner())}});background-repeat: no-repeat;
    background-size: cover;padding: 50px 0;"
@endif
>
    <div class="container">
        <div class="title">
            <h1>{{$page->name}}</h1>
            <ul>
                <li><a href="{{route('index')}}">Home</a></li>
                <li>{{$page->name}}</li>
            </ul>
        </div>
    </div>
</div>

<div class="contact-page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="sitemap">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d29186.92021831534!2d90.35768830596885!3d23.876671472592303!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755c5d05e7074dd%3A0xd1c58803049f00c7!2sUttara%2C%20Dhaka!5e0!3m2!1sen!2sbd!4v1699677238195!5m2!1sen!2sbd" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>

        <div class="contact-form">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-info">
                        <h3>Send Messege</h3>
                        <p>Feel Free To Contact Us</p>
                        @if(Session::has('success'))
                        <div class="alert alert-success alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <strong>Success! </strong> {{Session::get('success')}}.
                        </div>
                        @endif
                        <form action="{{route('contactMail')}}" method="post">
                            @csrf
                            <div class="form-group form-group-section">
                                @if ($errors->has('name'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
                                @endif
                                <input type="name" name="name" value="" class="form-control control-section" placeholder="Enter Name" required="" />
                            </div>
                            <!-- <span class="required">This field is required</span> -->
                            <div class="form-group form-group-section">
                                @if ($errors->has('email'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('email') }}</p>
                                @endif
                                <input type="email" name="email" value="" class="form-control control-section" placeholder="Email Address" required="" />
                            </div>
                            <div class="form-group form-group-section">
                                @if ($errors->has('phone'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('phone') }}</p>
                                @endif
                                <input type="phone" name="phone" value="" class="form-control control-section" placeholder="Phone Number" required="" />
                            </div>
                            <div class="form-group form-group-section">
                                @if ($errors->has('subject'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('subject') }}</p>
                                @endif
                                <input type="subject" name="subject" value="" class="form-control control-section" placeholder="Subject" required="" />
                            </div>
                            <div class="form-group form-group-section">
                                @if ($errors->has('message'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('message') }}</p>
                                @endif
                                <textarea name="message" rows="5" value="" class="form-control control-section" placeholder="Write Your Massege" required=""></textarea>
                            </div>
                            <div>
                                <button type="submit" class="btn submitbutton">SUBMIT</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="website-info">
                        <div class="media">
                            <i class="fa fa-map-signs" aria-hidden="true"></i>
                            <div class="media-body">
                                <h5>Head Office</h5>
                                <p>{{general()->address_one}}</p>
                            </div>
                        </div>
                    </div>

                    <div class="website-info">
                        <div class="media">
                            <i class="fa fa-envelope" aria-hidden="true"></i>
                            <div class="media-body">
                                <h5>Email Address</h5>
                                <p>{{general()->email}}</p>
                            </div>
                        </div>
                    </div>

                    <div class="website-info">
                        <div class="media">
                            <i class="fa fa-phone" aria-hidden="true"></i>
                            <div class="media-body">
                                <h5>Phone Number</h5>
                                <p>+880 1677717525<br>+880 1917570742<br>+8801712589730</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection 

@push('js') 

@endpush


