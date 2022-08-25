@extends('layouts.appLayout')
@section('content')
<section class="header">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="header-main-div">
                <h2 class="banner-heading">Let's Get Connected</h2>
                <p>Connecting people that are located at the same place and want to get know each other.</p>
                <button type="button" class="btn btn-primary"><p>Download App</p> <svg width="30" height="30" viewBox="0 0 67 67" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="33.5" cy="33.5" r="33.5" fill="url(#paint0_linear)"/>
                    <path d="M45 37V42.3333C45 43.0406 44.7191 43.7189 44.219 44.219C43.7189 44.7191 43.0406 45 42.3333 45H23.6667C22.9594 45 22.2811 44.7191 21.781 44.219C21.281 43.7189 21 43.0406 21 42.3333V37" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M26.3335 30.3333L33.0002 37L39.6668 30.3333" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M33 37V21" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <defs>
                    <linearGradient id="paint0_linear" x1="33.5" y1="0" x2="33.5" y2="67" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#21B2F8"/>
                    <stop offset="1" stop-color="#7F6DEC"/>
                    <stop offset="1" stop-color="#7F6DEC"/>
                    </linearGradient>
                    </defs>
                    </svg>
                </button>
            </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="img text-center">
                    <img src="{{ asset('assets/images/phone-img.png') }}"/>
                </div>
            </div>
        </div> 
    </div>
</section>
<section class="how-we-work my-4">
    <div class="container">
        <div class="row">
        <div class="col-lg-12 internal-headings text-center">
            <h2> How We work?</h2>
            <p class="content">People located at the same place show up in the app</p>
            <p class="content">Send a Hi to the person you want to meet</p>
            <p class="content">Receiver of the Hi has the option to accept it, share his/her profile and get connected, or reject the Hi and meet someone else.</p>
        </div>
        </div>
        <div class="row mt-4">
            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mt-lg-4 mt-sm-2 mt-xs-2">
                <div class="services-wrap text-center">
                    <img src="{{ asset('assets/images/account.svg') }}"/>
                    <div class="emi-cal">
                        <p>Create an account</p>
                    </div>
                    <div class="emi-des">
                        <p><br>Sign-up for FREE</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mt-lg-4 mt-sm-2 mt-xs-2">
                <div class="services-wrap text-center">
                    <img src="{{ asset('assets/images/interface 1.svg') }}"/>
                    <div class="emi-cal">
                        <p>See nearby people</p>
                    </div>
                    <div class="emi-des">
                        <p>People with the app that are at the same place will show up</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mt-lg-4 mt-sm-2 mt-xs-2">
                <div class="services-wrap text-center">
                    <img src="{{ asset('assets/images/multimedia.svg') }}"/>
                    <div class="emi-cal">
                        <p>Just Say Hi!</p>
                    </div>
                    <div class="emi-des">
                        <p>Send a Hi to the person you want to meet</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="video" id="video">
    <div class="container">
        <div class="row py-lg-5">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="div-wrapper ">
                    <h2>Watch our demo</h2>
                    <p>Check-out how the app works and understand  how to get connected with people nearby.. Just say Hii</p>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="div-wrap">
                    <img src="{{ asset('assets/images/video.png') }}"/>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="download-app" id="download_section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="wrap">
                    <p class="title">Download</p>
                    <h2>Available for all devices</h2>
                    <p>Download on every device and check the functionality and please share your suggestion on how to improve it</p>
                    <div class="available d-flex">
                        <a href="https://play.google.com/store/apps/details?id=com.roberto.hime"><img src="{{ asset('assets/images/play-store.png') }}" class="play-store"/></a> 
                        <a href="https://apps.apple.com/in/app/hime-app/id1523251098"><img src="{{ asset('assets/images/iphone.png') }}" class=""/></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 text-center">
                <div class="splash-img">
                    <img src="{{ asset('assets/images/splash.png') }}">
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
