<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;515;600;700&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
  
    <title>HiMe{{-- config('app.name', 'HiMe') --}}</title>

    @yield('style')
  </head>
  <body>
    <section class="header">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="logo"><img src="{{ asset('assets/images/logo.png') }}"/></div>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="#">Home  <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#video">Video</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link " href="#footer">
                    Contact us
                    </a>
                </li>
                <li class="nav-item">
                    <button type="button" class="btn btn-outline-primary">Download App</button>
                </li>
                </ul>
            
            </div>
        </nav>
    </section>

    @yield('content')

    <section class="footer my-lg-4 my-sm-2" id="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="footer-logo">
                        <img src="{{ asset('assets/images/footer-logo.png') }}" class="img-fluid"/>
                    </div>
                    <p class="footer-logo-des">Let us get connected with people that are located at the same place, but have no information of each other. Just Say Hi!</p>
                </div>
                
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="footer-wrap">
                        <h5 class="mb-4">Contact address</h5>
                        <div class="contact-info">
                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 20 20" fill="none">
                                <path d="M3.33332 3.3335H16.6667C17.5833 3.3335 18.3333 4.0835 18.3333 5.00016V15.0002C18.3333 15.9168 17.5833 16.6668 16.6667 16.6668H3.33332C2.41666 16.6668 1.66666 15.9168 1.66666 15.0002V5.00016C1.66666 4.0835 2.41666 3.3335 3.33332 3.3335Z" stroke="#558CF1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M18.3333 5L9.99999 10.8333L1.66666 5" stroke="#558CF1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <p> apphime@gmail.com</p>
                        </div>
                    </div>
                </div>
            </div>
           
        </div>
    </section>
    <!-- <section class="footer my-lg-4 my-sm-2" id="footer">
        <div class="container">
            <div class="row">
               <div class="col-lg-12 text-center">
                <div class="footer-logo">
                    <img src="images/footer-logo.png" class="img-fluid"/>
                </div>
                <div class="about-company">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam</p>
                </div>
               </div>
            </div>
           
        </div>
    </section> -->
    <section class="Copyright text-center">
        
            <p class="m-0">Copyrights © {{ date('Y') }}. All rights reserved by HiMe    </p>
        
    </section>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
 
</body>
</html>