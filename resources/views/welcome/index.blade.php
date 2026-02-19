@extends(welcomeTheme().'layouts.app') 
@section('title')
<title>{{websiteTitle()}}</title>
@endsection 
@section('SEO')
<meta name="title" property="og:title" content="{{general()->meta_title}}" />
<meta name="description" property="og:description" content="{!!general()->meta_description!!}" />
<meta name="keyword" property="og:keyword" content="{{general()->meta_keyword}}" />
<meta name="image" property="og:image" content="{{asset(general()->logo())}}" />
<meta name="url" property="og:url" content="{{route('index')}}" />
<link rel="canonical" href="{{route('index')}}">
@endsection 
@push('css')
<script type="application/ld+json">
    { 
    "@context": "https://schema.org", 
    "@type": "WebPage", 
    "url": "{{route('index')}}", 
    "name": "{{websiteTitle()}}",
    "author": {
        "@type": "Webpage",
        "name": "{{websiteTitle()}}"
    },
    "description": "{!!general()->meta_description!!}"
    }
</script>
@endpush 

@section('contents')

<!--Slider Part Include Start-->
@include(welcomeTheme().'layouts.slider')

<div class="serviceFeatured">
    <div class="container">
        <div class="row" style="margin:0 -10px;">
            <div class="col-md-4" style="padding:10px;">
                <div class="featuredGrid" style="background-image: url({{asset(assetLink().'/images/financial__item-bg.png')}});">
                    <div class="row">
                        <div class="col-3">
                            <img src="{{asset(assetLink().'/images/growth.svg')}}" alt="featured">
                        </div>
                        <div class="col-9 p-0">
                            <h5>Business Strategy</h5>
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit sed do eiusmod tempor.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4" style="padding:10px;">
                <div class="featuredGrid" style="background-image: url({{asset(assetLink().'/images/financial__item-bg.png')}});">
                    <div class="row">
                        <div class="col-3">
                            <img src="{{asset(assetLink().'/images/international.svg')}}" alt="featured">
                        </div>
                        <div class="col-9 p-0">
                            <h5>Financial Planning</h5>
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit sed do eiusmod tempor.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4" style="padding:10px;">
                <div class="featuredGrid" style="background-image: url({{asset(assetLink().'/images/financial__item-bg.png')}});">
                    <div class="row">
                        <div class="col-3">
                            <img src="{{asset(assetLink().'/images/planning-1.svg')}}" alt="featured">
                        </div>
                        <div class="col-9 p-0">
                            <h5>International Business</h5>
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit sed do eiusmod tempor.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="aboutCompany">
    <div class="container">
        <div class="title">
            <span><img src="{{asset(assetLink().'/images/line.svg')}}" alt="line" > KNOW OUR COMPANY</span>
            <h1>Our Company Provide High Quality Idea</h1>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="aboutCompanyImage">
                    <div class="row">
                        <div class="col-md-6">
                            <img src="{{asset(assetLink().'/images/our-company-1.png')}}" alt="about company">
                            <div class="more">
                               <span style="font-size:35px;"> 23<span style="font-size: 30px;position: absolute;">+</span></span>
                                <p style="margin: 0;font-size: 24px;">Years Experience</p>
                            </div>
                        </div>
                        <div class="col-md-6 right">
                            <img src="{{asset(assetLink().'/images/our-company-2.png')}}" alt="about company">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div>
                    <h4>About Company</h4>
                    <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    </p>
                    <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    </p>
                    <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                    </p>
                    @if($page =pageTemplate('About Us'))
                        <a href="{{route('pageView',$page->slug?:'')}}" class="btn btn-success">View More</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="serviceCompany">
    <div class="container">
        <div class="title">
            <span><img src="{{asset(assetLink().'/images/line.svg')}}" alt="line" > KNOW OUR SERVICES</span>
            <h1>Our Business Consulting Case Services</h1>
        </div>

        <div class="row">
            @foreach($latestServices as $service)
            <div class="col-md-4">
                @include(welcomeTheme().'services.includes.serviceGrid')
            </div>
            @endforeach
        </div>
        @if($page =pageTemplate('Latest Services'))
        <div class="moreServices">
            <a href="{{route('pageView',$page->slug?:'')}}" class="btn">View More</a>
        </div>
        @endif
    </div>
</div>

<div class="contactCompany">
    <div class="container">
        <div class="row">
            <div class="col-md-7">
                <div class="contactText">
                    <h2>
                        Experience The Evolution Of Your Business
                    </h2>
                    <p>
                    There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomized words which don't look
                    </p>
                </div>
                <br><br>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row m-0" style="border: 1px solid #d4d4d4;">
                            <div class="col-4 p-3" style="text-align: center;">
                                <i class="fa fa-phone" style="font-size: 40px;color: white;"></i>
                            </div>
                            <div class="col-8 p-2">
                                <p style="margin: 0;color: white;">Call Us Everyday</p>
                                <a href="tel:{{general()->mobile}}" style="color: #ef0548;">{{general()->mobile}}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row m-0" style="border: 1px solid #d4d4d4;">
                            <div class="col-4 p-3" style="text-align: center;">
                                <i class="fa fa-envelope-o" style="font-size: 40px;color: white;"></i>
                            </div>
                            <div class="col-8 p-2">
                                <p style="margin: 0;color: white;">Call Us Everyday</p>
                                <a href="mailto:{{general()->mobile}}" style="color: #ef0548;">{{general()->mobile}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="contactFrom">
                    @if(Session::has('success'))
                    <div class="alert alert-success alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        <strong>Success! </strong> {{Session::get('success')}}.
                    </div>
                    @endif
                    <form action="{{route('contactMail')}}" method="post">
                        @csrf
                        <span><img src="{{asset(assetLink().'/images/line.svg')}}" alt="line" > GET IN TOUCH</span>
                        <h2>
                        Free Consultation
                        </h2>
                        <div class="form-group form-group-section">
                            @if ($errors->has('name'))
                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
                            @endif
                            <input type="name" name="name" value="" class="form-control control-section" placeholder="Enter Name" required="" />
                        </div>
                        <div class="form-group form-group-section">
                            @if ($errors->has('email'))
                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('email') }}</p>
                            @endif
                            <input type="email" name="email" value="" class="form-control control-section" placeholder="Email Address" required="" />
                        </div>
                        <div class="form-group form-group-section">
                            @if ($errors->has('message'))
                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('message') }}</p>
                            @endif
                            <textarea name="message" rows="5" value="" class="form-control control-section" placeholder="Write Your Massege" required=""></textarea>
                        </div>
                        <button type="submit" class="btn btn-contact">Free Consultation <i class="fa fa-angle-double-right"></i></button>
                    </form>
                </div>  
            </div>
        </div>
    </div>
</div>

<div class="blogCompany">
    <div class="container">
        <div class="title">
            <span><img src="{{asset(assetLink().'/images/line.svg')}}" alt="line" > BLOG & NEWS</span>
            <h1>Our Business Latest Blog & News</h1>
        </div>

        <div class="row">
            @foreach($latestPosts as $post)
            <div class="col-md-4">
                @include(welcomeTheme().'blogs.includes.blogGrid')
            </div>
            @endforeach
        </div>
        @if($page =pageTemplate('Latest Blog'))
        <div class="moreServices">
            <a href="{{route('pageView',$page->slug?:'')}}" class="btn">View More</a>
        </div>
        @endif
    </div>
</div>

<div class="subscriptionPart">
    <div class="container">
        <div id="subscribe-css">
            <p class="subscribe-note"><span>SUBSCRIBE</span> <span class="itatu">TO</span> OUR NEWSLETTER</p>
            <div class="subscribe-wrapper">
                <div class="subscribe-form">
                    <div id="subscribeemailMsg"></div>
                    <form
                        id="subscirbeForm"
                        data-url="{{route('subscribe')}}"
                        class="subscribe-form"
                        method="post"
                    >
                        <input name="uri" type="hidden" value="ArlinaDesign" /><input name="loc" type="hidden" value="en_US" /><input autocomplete="off" class="subscribe-css-email-field" id="subscribeEmail" name="email" placeholder="Enter your Email" />
                        <input class="subscribe-css-email-button subsriberbtm" title="" type="submit" value="submit" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection @push('js') @endpush