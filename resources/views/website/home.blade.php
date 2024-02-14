@extends('layout.mainlayout',['activePage' => 'home'])

@section('css')
{{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"> --}}
<link rel="stylesheet" href="{{ url('assets/plugins/slick-carousel/slick.min.css') }}" />
<link rel="stylesheet" href="{{ url('assets/plugins/jquery-ui/jquery-ui.min.css') }}" />
<link rel="stylesheet" href="{{ url('assets/css/animate.min.css') }}" />
<style>
    body[dir='rtl'] .btn-appointment {
        margin-right: 10px;
    }

    .imagePopup {
        width: 100%;
        max-width: 70vw;
        height: 100%;
        max-height: 70vh;
    }

    .slick-slider .element {
        color: #fff;
        border-radius: 5px;
        display: inline-block;
        margin: 0px 10px;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        font-size: 20px;
    }

    .slick-disabled {
        pointer-events: none;
        border-color: var(--site_color_hover);
    }

    .slick-disabled svg {
        fill: var(--site_color_hover);
    }

    .slick-dots {
        display: flex;
        justify-content: center;
        margin: 0;
        padding: 1rem 0;
        list-style-type: none;
    }

    .slick-dots li {
        margin: 0 0.25rem;
    }

    .slick-dots button {
        display: block;
        width: 10px;
        height: 10px;
        padding: 0;
        border: none;
        border-radius: 100%;
        background-color: #D9D9D9;
        text-indent: -9999px;
    }

    .slick-dots li.slick-active button {
        background-color: var(--site_color);
    }

    /** home page **/
    .animated-element, .comment-group{
        opacity: 0;
    }
    .search-box-fields {
        background: #377ef7;
        border-radius: 30px 0 0 30px;
        padding:0 0 0 15px;
        width: calc(100% - 90px);
        z-index: 9;
    }
    .search-box-btn {
        display: inline-block;
        color: #fff;
        background: #377ef7;
        border-radius: 0 30px 30px 0;
        padding: 10px 30px 0px 30px;
        width: 80px;
        z-index: 9;
    }
    .inputgroup {
        position: relative;
        display: inline-block;
        height: 30px;
        line-height: 30px;
        vertical-align: middle;
        color: #fff;
        margin: 10px 0;
        width: 32%;
        min-width: 120px;
        border-right: 2px solid #5a95f8;
    }
    .inputgroup>input {
        height: 32px;
        line-height: 32px;
        padding: 0px 6px 3px 38px;
        outline: none;
        border: none;
        background: transparent;
        font-size: 14px;
        display: inline-block;
        vertical-align: middle;
    }
    .inputgroup>input::placeholder {
        color: #d2d2d2;
        opacity: 0.9; /* Firefox */
    }
    .inputgroup .innericon {
        position: absolute;
        top: 6px;
        left: 8px;
        z-index: 2;
        display: block;
        text-align: center;
        pointer-events: none;
    }
    .toggle-switch {
        display: inline-block;
        border-radius: 18px;
        border: 1px solid #377ef7;
        background:#fff;
    }
    .toggle-switch a {
        display: inline-block;
        border-radius: 15px;
        padding: 3px 24px;
    }
    .toggle-switch a.active {
        background: #7ba4db;
        color: #fff;
    }
    .banner-bg {
        background: rgb(136,135,140);
        background: linear-gradient(90deg, rgba(136,135,140,1) 0%, rgba(200,200,202,1) 50%, rgba(255,255,255,1) 100%);
        font-size:1.25em;
        overflow: hidden;
    }
    .banner-img {
        position: absolute;
        z-index: 0;
        right: 0;
        bottom: 0;
    }
    .slider-box {
        background: rgba(123, 164, 219, 0.6);
        border: 1px solid #3f82f4;
        border-radius: 10px;
        color:#fff;
        z-index: 3;
    }
    .call-action-btn {
        display: inline-block;
        border: 1px solid #2b2b2b;
        color: #2b2b2b;
        padding: 10px 60px;
        border-radius: 30px;
        box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        -webkit-transition: 0.3s;
        -moz-transition: 0.3s;
        -o-transition: 0.3s;
        transition: 0.3s;
    }
    .call-action-btn:hover {
        -webkit-transform: translateY(-2px);
        -moz-transform: translateY(-2px);
        -ms-transform: translateY(-2px);
        -o-transform: translateY(-2px);
        transform: translateY(-2px);
    }
    .btn-small {
        font-size:13px;
    }
    .action-box {
        display: inline-block;
        text-align:center;
        -webkit-box-shadow: 0px 2px 8px 0px rgba(0, 0, 0, 0.2);
        box-shadow: 0px 2px 8px 0px rgba(0, 0, 0, 0.2);
        padding: 30px 30px 40px 30px;
    }
    .action-box.active {
        z-index: 5;
        background:#377ef7;
        color:#fff;
        -webkit-box-shadow: 0px 2px 8px 0px rgba(55, 126, 247, 0.2);
        box-shadow: 0px 2px 8px 0px rgba(55, 126, 247, 0.2);
    }
    .action-box.active .call-action-btn {
        color:#fff;
        border-color:#fff;
    }
    .action-box.active .call-action-btn:hover {
        color: #fff;
    }
    .action-box img {
        display: inline-block;
        margin:20px 0 0;
    }
    .parallax {
        background-image: url(images/upload_empty/parallax.jpg);
        min-height: 500px;
        background-attachment: fixed;
        background-position: bottom right;
        background-repeat: no-repeat;
        background-size: contain;
        background-color: #e3f6fd;
    }
    .parallax-box0 {
        background: rgba(255, 255, 255, 0.6);
        color:#2b2b2b;
        padding:20px 10px;
        text-align: center;
    }
    .parallax-box1 {
        background: rgba(55, 126, 247, 0.6);
        color:#fff;
        padding:20px 10px;
        text-align: center;
    }
    .comment-box {
        color: #2b2b2b;
        font-size:1.5em;
    }
    .rounded-mark {
        display: inline-block;
        font-size: 52px;
        font-weight: 700;
        border: 1px solid #2b2b2b;
        border-radius: 50%;
        width: 71px;
        height: 71px;
        line-height: 70px;
        text-align: center;
    }
    .comment-bg {
        margin-left: -130px;
        margin-right:-20px;
        width:370px;
    }
    .comment-box-1 {
        margin-right: -40px;
    }
    .comment-box-2 {
        margin-left: 60px;
        margin-right: -130px;
    }
    .comment-box-3 {
        margin-left: 150px;
        margin-right: -150px;
    }
    ul.ui-autocomplete {
        background-color: rgb(255, 255, 255);
        box-sizing: border-box;
        left: 0px;
        position: absolute;
        /* width: 100%; */
        width: 288px !important;
        z-index: 99;
        --webkit-overflow-scrolling: touch;
        box-shadow: none;
        overflow-y: auto;
        padding: 11px 0px 52px;
        height: 100%;
    }
    .atc-box {
        background: none;
        cursor: pointer;
        padding: 3px 12px;
        transition: all 0.2s ease-in-out 0s;
        display:block;
        overflow: hidden;
    }
    .atc-box.ui-state-active {
        border:none !important;
        background:#e3f6fd;
    }
    .atc-img {
        border-color: white;
        border-radius: 50%;
        height: 40px;
        margin-top: 1px;
        overflow: hidden;
        position: absolute;
    }
    .atc-img img {
        max-width: 100%;
        height: auto;
        -ms-interpolation-mode: bicubic;
        display: inline-block;
        vertical-align: middle;
        width: 40px;
    }
    .atc-desc {
        padding-left: 50px;
        padding-top: 4px;
    }
    .atc-title {
        color: #333333;
        font-size: 16px;
        line-height: 24px;
        letter-spacing: 0em;
        text-transform: none;
        padding: 0px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-size-adjust: none;
    }
    .atc-categ {
        color: #707070;
        font-size: 12px;
        line-height: 16px;
        letter-spacing: 0em;
        text-transform: none;
        overflow: hidden;
        text-overflow: ellipsis;
        text-size-adjust: none;
        white-space: nowrap;
    }
    .atc-title b {
        color: #ff7467 !important;
    }
    .atc-categ span {
        margin-left:4px;
        display:inline-block;
        padding-left:3px;
        color:#184394;
    }
</style>
@endsection

@section('content')

<!-- Modal toggle -->
<button id="modalBtn" data-modal-target="staticModal" data-modal-toggle="staticModal" class="hidden text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" type="button">
    Toggle modal
</button>

<!-- Main modal -->
<div id="staticModal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-hidden md:inset-0">
    <div class="relative w-auto max-w-2xl">
<!-- Modal content -->
        <div class="relative text-white rounded-lg shadow">
<!-- Modal header -->
            <div class="flex items-start justify-between rounded-t">
                <button type="button" class="absolute top-0 right-0 mt-2 mr-2 text-white cursor-pointer focus:outline-none" data-modal-hide="staticModal">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
<!-- Modal body -->
            <div class="">
                <a class="" href="{{ url($setting->popup_target_url) }}">
                    <img src="{{ url('images/upload/'.$setting->landing_popup_image) }}" alt="image not found" class="imagePopup object-cover">
                    <input type="hidden" name="landing_popup_switch" id="landing_popup_switch" value="{{ $setting->landing_popup_switch }}">
                    <input type="hidden" name="popup_timer_seconds" id="popup_timer_seconds" value="{{ $setting->popup_timer_seconds }}">
                </a>
            </div>
        </div>
    </div>
</div>

<div class="banner-bg w-full p-10 relative">
    <img src="{{ url('images/upload_empty/slider_img1.png') }}" id="guide_doctor" class="animate__animated animate__fadeInUp animate__delay-1s banner-img" alt="guide doctor" />
    <div class="xlg:mx-20 xxsm:mx-4 xsm:mx-5 pt-20">
        <div class="!1xl:w-2/4 2xl:w-2/3 md:w-3/4 xxsm:w-full">
            <h1 class="font-fira-sans text-black font-medium text-6xl leading-snug mb-10">
                Trouvez votre prestataire de soins et <span class="text-white">prenez rendez-vous.</span></h1>
            <p class="font-fira-sans font-normal text-lg text-white mb-10">Facile, gratuit et rapide.</p>
            <div class="text-center mt-20">
                <div class="toggle-switch">
                    <a href="javascript:change_search_option(0)" id="search_option_0" class="active">En personne</a>
                    <a href="javascript:change_search_option(1)" id="search_option_1">En ligne</a>
                </div>
                <input type="hidden" id="search_option" value="0" />
                <input type="hidden" id="search_type" value="doctor" />
            </div>
            <div class="search-box justify-between flex mt-4">
                <div class="search-box-fields">
                    <div class="inputgroup"> 
                        <span class="innericon fa fa-user-md"></span>
                        <input type="text" id="search_doctor" class="search" placeholder="search..." />
                    </div>
                    <div class="inputgroup"> 
                        <span class="innericon fa fa-street-view"></span>
                        <input type="search" onFocus="geolocate()" class="search" id="search_address"
                        class="block p-2 pl-10 text-sm text-black-700 bg-white-50 border border-white-light 2xl:w-96 xmd:w-72 !sm:w-32 msm:w-40 h-12"
                        placeholder="{{ __('Set your location') }}">
                        <input type="hidden" name="search_lat" id="search_lat">
                        <input type="hidden" name="search_lng" id="search_lng">
                    </div>
                    <div class="inputgroup border-none"> 
                        <span class="innericon fa fa-calendar"></span>
                        <input type="text" id="search_date" class="search" placeholder="search..." />
                    </div>
                </div>
                <a href="#" class="search-box-btn"><i class="fa fa-search"></i></a>
            </div>
        </div>
        <div class="w-full mt-32">
            <p class="mb-5 text-white">Trouvez un médecin et des centres spécialisés près de chez vous.</p>
            <div class="grid gap-6 xxsm:grid-cols-1 xsm:grid-cols-1 msm:grid-cols-1 sm:grid-cols-3 lg:grid-cols-3 w-full">
                <div class="slider-box p-5">
                    <h3 class="text-2xl"><i class="fa fa-user-md"></i>&nbsp; Trouvez un médecin</h3>
                    <p class="pt-5">
                    À la recherche d'un médecin généraliste ou d'un <br>
                    spécialiste? Commencez ici.
                    </p>
                </div>
                <div class="slider-box p-5">
                    <h3 class="text-2xl"><i class="fa fa-user-md"></i>&nbsp; Trouvez un médecin</h3>
                    <p class="pt-5">
                    À la recherche d'un médecin généraliste ou d'un <br>
                    spécialiste? Commencez ici.
                    </p>
                </div>
                <div class="slider-box p-5">
                    <h3 class="text-2xl"><i class="fa fa-user-md"></i>&nbsp; Trouvez un médecin</h3>
                    <p class="pt-5">
                    À la recherche d'un médecin généraliste ou d'un <br>
                    spécialiste? Commencez ici.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="xxsm:mx-5 xl:mx-0 2xl:mx-0">
    <div class="mt-20 xl:w-3/4 mx-auto mb-0">
        <h2 class="font-medium text-center 2xl:text-4xl xl:text-4xl xlg:text-4xl lg:text-4xl xmd:text-4xl md:text-4xl msm:text-4xl sm:text-4xl xsm:text-4xl xxsm:text-2xl leading-10 font-fira-sans text-black">
            {{__('How it works')}}
        </h2>
        <div class="justify-between flex sm:flex-row xxsm:flex-col 2xl:mt-0 mb-0 xxsm:mt-10 lg:mt-0">
            <img src="{{ url('images/upload_empty/bg_man3.jpg') }}" class="animated-element animate__animated comment-bg" alt="guide doctor" />
            <div class="grid grid-rows-2 comment-group animate__animated pt-14">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 xlg:grid-cols-3 lg:grid-cols-3">
                    <div class="comment-box comment-box-1">
                        <h2>
                            <span class="rounded-mark">1</span>
                        </h2>
                        <h5 class="font-fira-sans text-black font-bold text-2xl md:text-md pt-5">
                            Trouvez un médecin près de chez vous.
                        </h5>
                        <p class="font-normal text-lg md:text-md pt-5">
                            Doctipro simplifie la découverte de médecins dans votre région. Grâce à une fonction de recherche conviviale, il vous suffit d'entrer votre emplacement ou vos préférences pour accéder à une liste de médecins qualifiés à proximité.
                        </p>
                    </div>
                    <div class="comment-box comment-box-2">
                        <h2>
                            <span class="rounded-mark">2</span>
                        </h2>
                        <h5 class="font-fira-sans text-black font-bold text-2xl md:text-md pt-5">
                            Réservez facilement un RDV en personne ou en ligne.
                        </h5>
                        <p class="font-normal text-lg md:text-md pt-5">
                            Que vous préfériez une consultation traditionnelle en personne ou la commodité d'une vidéo consultation, Doctipro répond à vos besoins. Parcourez les créneaux horaires disponibles et sélectionnez le type de rendez-vous qui vous convient le mieux.
                        </p>
                    </div>
                    <div class="comment-box comment-box-3">
                        <h2>
                            <span class="rounded-mark">3</span>
                        </h2>
                        <h5 class="font-fira-sans text-black font-bold text-2xl md:text-md pt-5">
                            Ajoutez vos informations et voilà!
                        </h5>
                        <p class="font-normal text-lg md:text-md pt-5">
                            Après avoir choisi votre médecin préféré et le type de rendez-vous souhaité, ajoutez vos informations en toute sécurité. En quelques clics, votre rendez-vous est confirmé, et vous êtes prêt.
                        </p>
                    </div>
                </div>
                <div class="text-center">
                    <a href="{{ url('show-doctors') }}" class="font-bold mb-10 call-action-btn">{{__('Find an appointment')}}</a>
                </div>
            </div>
        </div>
    </div>
    <div class="p-10 w-full mb-10 bg-light-gray">
        <div class="xl:w-3/4 mx-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 xlg:grid-cols-2 lg:grid-cols-2">
                <div class="sm:flex-row xxsm:flex-col text-center">
                    <img src="{{ url('images/upload_empty/company_mark1.png') }}" class="inline-block" alt="sponsored company" />
                </div>
                <div class="sm:flex-row xxsm:flex-col text-center 2xl:mt-28 mb-8 xxsm:mt-10 lg:mt-20">
                    <img src="{{ url('images/upload_empty/company_mark2.png') }}" class="inline-block" alt="sponsored company" />
                </div>
            </div>
        </div>
    </div>

    <div class="mt-20 xl:w-3/4 mx-auto mb-20">
        <h2 class="mt-20 font-medium text-center 2xl:text-4xl xl:text-4xl xlg:text-4xl lg:text-4xl xmd:text-4xl md:text-4xl msm:text-4xl sm:text-4xl xsm:text-4xl xxsm:text-2xl font-fira-sans text-black">
            {{__('How it works')}}
        </h2>
        <div class="mt-20 grid grid-cols-1 sm:grid-cols-1 md:grid-cols-3 xlg:grid-cols-3 lg:grid-cols-3">
            <div class="action-box">
                <img src="{{ url('images/upload_empty/block_icon1.png') }}" />
                <p class="my-10 font-fira-sans font-bold text-2xl md:text-md">Rendez-vous gratuit en personne ou en ligne.</p>
                <a class="call-action-btn btn-small">Discover More</a>
            </div>
            <div class="action-box active">
                <img src="{{ url('images/upload_empty/block_icon2.png') }}" />
                <p class="my-10 font-fira-sans font-bold text-2xl md:text-md">Paiement immédiat direct.</p>
                <a class="call-action-btn btn-small">Discover More</a>
            </div>
            <div class="action-box">
                <img src="{{ url('images/upload_empty/block_icon3.png') }}" />
                <p class="my-10 font-fira-sans font-bold text-2xl md:text-md">Portail patient</p>
                <a class="call-action-btn btn-small">Discover More</a>
            </div>
        </div>
    </div>
    <div class="mt-32 text-center">
        <h2 class="font-medium text-center 2xl:text-4xl xl:text-4xl xlg:text-4xl lg:text-4xl xmd:text-4xl md:text-4xl msm:text-4xl sm:text-4xl xsm:text-4xl xxsm:text-2xl font-fira-sans text-black">
            Êtes-vous un professionnel de santé ?
        </h2>
        <p class="font-fira-sans font-medium text-xl md:text-md">
            Débloquez une multitude d'avantages avec Doctipro
        </p>
    </div>
    <div class="parallax mt-20 xxsm:mx-5 xl:mx-0 2xl:mx-0">
        <div class="pt-32 xl:w-3/4 mx-auto mb-0">
            <div class="grid gap-6 xxsm:grid-cols-1 xsm:grid-cols-1 sm:grid-cols-2 md:grid-cols-2 xlg:grid-cols-4 w-full">
                <div class="parallax-box0">
                    <h5 class="my-10 font-fira-sans font-bold text-2xl md:text-md">
                        365 jours d’essai gratuit.
                    </h5>
                    <p class="my-10 font-fira-sans font-medium text-xl md:text-md">
                        Bénéficiez d'une période d'essai gratuite d'un an.
                    </p>
                </div>
                <div class="parallax-box1">
                    <h5 class="my-10 font-fira-sans font-bold text-2xl md:text-md">
                        Orienté vers une solution cloud
                    </h5>
                    <p class="my-10 font-fira-sans font-medium text-xl md:text-md">
                        Accédez à une solution cloud adaptée à vos besoins.
                    </p>
                </div>
                <div class="parallax-box0">
                    <h5 class="my-10 font-fira-sans font-bold text-2xl md:text-md">
                        Une Assistance 24h/7j
                    </h5>
                    <p class="my-10 font-fira-sans font-medium text-xl md:text-md">
                        Recevez à tout moment une assistance et un suivi gratuits.
                    </p>
                </div>
                <div class="parallax-box1">
                    <h5 class="my-10 font-fira-sans font-bold text-2xl md:text-md">
                        Consultez où que vous soyez.
                    </h5>
                    <p class="my-10 font-fira-sans font-medium text-xl md:text-md">
                        Consultez facilement de n'importe où.
                    </p>
                </div>
            </div>
            <div class="text-center mt-10 mb-20">
                <a href="{{ url('show-doctors') }}" class="bg-white font-bold mb-10 call-action-btn">{{__('Find an appointment')}}</a>
            </div>
        </div>
    </div>

    {{-- our doctor--}}
    {{-- <div class="mt-20 xl:w-3/4 mx-auto mb-20">
        <div class="justify-between flex sm:flex-row xxsm:flex-col 2xl:mt-28 mb-8 xxsm:mt-10 lg:mt-40">
            <div class="sm:py-3 md:py-0 msm:py-3 xsm:py-3 xxsm:py-3">
                <h2 class="font-medium 2xl:text-4xl xl:text-4xl xlg:text-4xl lg:text-4xl xmd:text-4xl md:text-4xl msm:text-4xl sm:text-4xl xsm:text-4xl xxsm:text-2xl leading-10 font-fira-sans text-black">
                    {{__('Our Doctors')}}
                </h2>
            </div>
            @if(count($doctors)>0)
            <div class="sm:py-3 md:py-0 msm:py-3 xsm:py-3 xxsm:py-3">
                <a href="{{ url('show-doctors') }}" class="text-sm font-normal font-fira-sans leading-4 text-primary border border-primary text-center md:text-sm py-3.5 px-7">{{__('View All Doctors')}}</a>
            </div>
            @else
            @endif
        </div>
        @if(count($doctors)>0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xlg:grid-cols-4 lg:grid-cols-3">
            @foreach ($doctors as $doctor)
            <a href="{{ url('doctor-profile/'.$doctor['id'].'/'.Str::slug($doctor['name'])) }}">
                <div class="border border-white-light p-10">
                    <div class="border-2 border-primary rounded-full w-36 h-36 mx-auto overflow-hidden">
                        <img class="w-36 h-36 object-cover rounded-full" src="{{ url($doctor->fullImage) }}" alt="" />
                    </div>

                    <h5 class="font-fira-sans font-normal text-lg leading-6 text-black text-center md:text-md pt-5">
                        {{ $doctor->name }}
                    </h5>
                    <p class="font-normal leading-4 text-sm text-primary text-center font-fira-sans md:text-md py-2">
                        {{isset($doctor['category']) ? $doctor['category']['name']:'No Set' }}
                    </p>
                    <p class="font-normal leading-4 text-sm text-gray text-center md:text-md"><i class="fa-solid fa-star text-yellow"></i> {{ $doctor['rate'] }} ({{$doctor['review'] }} {{__('reviews') }})</p>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="flex justify-center mt-44 font-fira-sans font-normal text-base text-gray">
            {{__('No Data Avalaible')}}
        </div>
        @endif

    </div> --}}

    {{-- Read top articles from health experts --}}
    <div class="py-10 xl:w-3/4 mx-auto 2xl:mb-20">
        <div class="flex justify-between md:flex-row sm:flex-row xxsm:flex-col">
            <div class="sm:py-3 md:py-0 msm:py-3 xsm:py-3 xxsm:py-3">
                <h2 class="font-medium 2xl:text-4xl xl:text-4xl xlg:text-4xl lg:text-4xl xmd:text-4xl md:text-3xl msm:text-2xl sm:text-2xl xsm:text-2xl xxsm:text-2xl leading-10 font-fira-sans text-black">
                    {{__('Read top articles from health experts')}}
                </h2>
            </div>
            <div class="flex">
                <button type="button" class="prev w-10 md:px-2 lg:text-base lg:py-2 md:text-sm md:py-2 sm:py-2 sm:px-3 msm:py-2 msm:px-3 xsm:py-2 xsm:px-3 xxsm:py-2 xxsm:px-3 text-primary border border-primary text-center">
                    <svg class="m-auto" width="8" height="12" viewBox="0 0 8 12" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6.29303 11.707L0.586032 5.99997L6.29303 0.292969L7.70703 1.70697L3.41403 5.99997L7.70703 10.293L6.29303 11.707Z" />
                    </svg>
                </button>
                <button type="button" class="ml-2 next w-10 md:px-2 lg:text-base lg:py-2 md:text-sm md:py-2 sm:py-2 sm:px-3 msm:py-2 msm:px-3 xsm:py-2 xsm:px-3 xxsm:py-2 xxsm:px-3 text-primary border border-primary text-center">
                    <svg class="m-auto" width="8" height="12" viewBox="0 0 8 12" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.70697 11.707L7.41397 5.99997L1.70697 0.292969L0.292969 1.70697L4.58597 5.99997L0.292969 10.293L1.70697 11.707Z" />
                    </svg>
                </button>
            </div>
        </div>
        <div class="single-item mb-5">
            @if(count($blogs) > 0)
            <div class="slick-slider-ltr" dir="ltr">
                @foreach ($blogs as $blog)
                @php
                //updated by Polaris
                $tlist = json_decode($blog->multi_language);
                $temp_title = $blog->title;
                $temp_desc = strip_tags($blog->desc);
                foreach ($tlist as $t) {
                    if (strcmp($t->lang, session('locale'))==0) {
                        $temp_title = $t->title;
                        $temp_desc = strip_tags($t->desc);
                    }
                }
                @endphp
                <a href="{{ url('blog-details/'.$blog->id.'/'.Str::slug($blog->title)) }}">
                    <div class="element element-{{ $loop->iteration }} ">
                        <div class="md:mt-12 sm:mt-11 msm:mt-11 xsm:mt-11 xxsm:mt-11 w-full">
                            <img class="w-full h-56 object-cover bg-cover" src="{{ url('images/upload/'.$blog->image) }}" alt="" />
                            <div class="w-96 text-gray text-left font-medium text-base py-2 font-fira-sans flex">
                                <div class="font-fira-sans text-primary text-base font-normal md:text-sm">{{ $blog->blog_ref }}</div>
                                <div class="ml-5">{{ Carbon\Carbon::parse($blog->created_at)->format('d M,Y') }}</div>
                            </div>
                            @if (strlen($temp_title) > 30)
                            <div class="font-fira-sans font-normal text-xl text-black text-left mb-2">{!! substr(clean($temp_title), 0, 30) !!}....</div>
                            @else
                            <div class="font-fira-sans font-normal text-xl text-black text-left mb-2">{!! clean($temp_title) !!}</div>
                            @endif
                            <div class="font-fira-sans font-normal text-sm text-gray w-[400px] h-[40px] truncate">
                                @if (strlen($temp_desc) > 190)
                                    {!! substr(clean($temp_desc), 0, 190) !!}....
                                @else
                                    {!! clean($temp_desc) !!}
                                @endif    
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <div class="flex justify-center mt-44 font-fira-sans font-normal text-base text-gray">{{__('No Data Avalaible')}}</div>
            @endif
        </div>
    </div>

    {{-- Browse by Specialities--}}
    <div class="p-5 w-full mb-10" style="background-color: aliceblue;">
        <div class="xl:w-3/4 mx-auto pt-20 pb-20">
            <div class="grid xlg:grid-cols-4 lg:grid-cols-3 md:grid-cols-3 sm:grid-cols-2 msm:grid-cols-1 xsm:grid-cols-1 xxsm:grid-cols-1">
                <div class="sm:col-span-2 msm:col-span-1 xsm:col-span-1 xxsm:col-span-1 ">
                    @if(isset($setting->home_content) || isset($setting->home_content_desc))
                    <div class="justify-center items-left md:mt-12 lg:mt-16 sm:mt-11 msm:mt-11 xsm:mt-11 xxsm:mt-11">
                        <h2 class="font-medium 2xl:text-4xl xl:text-4xl xlg:text-4xl lg:text-4xl xmd:text-4xl md:text-4xl msm:text-4xl sm:text-4xl xsm:text-4xl xxsm:text-2xl leading-10 font-fira-sans text-black ">
                            {{ $setting->home_content}}
                        </h2>
                        <p class="font-normal leading-5 text-sm text-gray text-left lg:mt-4 xmd:mt-4 md:mt-4 sm:pt-3 msm:pt-3 xsm:pt-3 xxsm:pt-3 ">{!! $setting->home_content_desc !!}</p>
                    </div>
                    @else
                    <div class="flex justify-center mt-44 font-fira-sans font-normal text-base text-gray">{{__('No Data Avalaible')}}</div>
                    @endif
                </div>
                @if(count($categories) > 0)
                @foreach($categories as $category)
                <div class="bg-white shadow-xl p-14 transform w-full h-full hover:bg-white-50 transition duration-500 hover:scale-110 xxsm:mt-10 2xl:mt-0">
                    <div class="justify-center items-center w-full">
                        <img class="lg:h-16 lg:w-16 xxmd:w-16 xxmd:h-16 md:h-10 md:w-10 sm:h-10 sm:w-10 msm:h-10 msm:w-10 xsm:h-10 xsm:w-10 xxsm:h-10 xxsm:w-10 mx-auto  bg-cover object-cover" src="{{$category->fullImage}}" alt="" />
                        <p class="font-fira-sans font-normal text-xl xxsm:text-base leading-6 text-black text-center md:text-xl py-5">{{$category->name}}</p>
                        <p class="font-fira-sans text-center md:text-xl">
                        <form action="{{ url('show-doctors') }}" method="post" class="text-center">
                            @csrf
                            <input type="hidden" name="category_id" value="{{ $category->id }}">
                            <button type="submit" class="font-medium leading-4 text-sm text-primary text-center font-fira-sans md:text-sm">{{__('Consult Now!')}}
                                <svg width="11" height="11" viewBox="0 0 11 11" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.73544 0.852912C8.6542 0.446742 8.25908 0.183329 7.85291 0.264563L1.23399 1.58835C0.827824 1.66958 0.564411 2.0647 0.645646 2.47087C0.72688 2.87704 1.122 3.14045 1.52817 3.05922L7.41165 1.88252L8.58835 7.76601C8.66958 8.17218 9.0647 8.43559 9.47087 8.35435C9.87704 8.27312 10.1405 7.878 10.0592 7.47183L8.73544 0.852912ZM2.62404 10.416L8.62404 1.41602L7.37596 0.583973L1.37596 9.58397L2.62404 10.416Z" />
                                </svg>
                            </button>
                        </form>
                        </p>
                    </div>
                </div>
                @endforeach
                @else
                <div class="flex justify-center mt-44 font-fira-sans font-normal text-base text-gray">{{__('No Data Avalaible')}}</div>
                @endif
            </div>
        </div>
    </div>
    {{-- Download the Doctro --}}
    {{-- <div class="xl:w-3/4 mx-auto rounded-lg mb-20" style="background-color: aliceblue;">
        <div class="rounded-xl">
            <div class="grid xxsm:grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 3xl:grid-cols-2 self-center relative">
                <div class="mt-20 xl:w-96 xxsm:w-full mx-auto">
                    <div class="mb-10">
                        <h1 class="font-medium leading-10 font-fira-sans text-black 2xl:text-4xl xl:text-4xl xlg:text-4xl lg:text-4xl xmd:text-4xl md:text-4xl msm:text-4xl sm:text-4xl xsm:text-2xl xxsm:text-2xl">
                            {{__('Download the ')}} {{ $setting->business_name }} {{__('app')}}
                        </h1>
                        <p class="lg:pt-7 md:pt-2 msm:pt-2 xsm:pt-2 xxsm:pt-2 leading-6 md:leading-1 md:text-xs font-fira-sans font-normal text-sm text-gray text-left">
                            {{__('Get in touch with the top-most expert Specialist Doctors for an accurate consultation on the Doctro. Connect with Doctors, that will be available 24/7 right for you.')}}
                        </p>
                    </div>
                    <div class="flex xxsm:flex-col msm:flex-row gap-6">
                        <a href="{{ $setting->playstore }}" class="store_btn">
                            <img src="{{ asset('assets/image/google pay.png')}}" style="width: 200px;height:62px">
                        </a>
                        <a href="{{ $setting->appstore }}" class="store_btn ">
                            <img src="{{ asset('assets/image/app store.png')}}" style="width: 200px;height:62px">
                        </a>
                    </div>
                </div>
                <div class="mx-auto pt-24">
                    <img src="{{asset('assets/image/Mobile.png')}}" class="bg-cover object-cover 2xl:w-[80%] 1xl:w-[70%] xl:w-[100%] lg:w-[100%] xmd:w-80 md:w-80 sm:w-full msm:w-full xsm:w-80 xxsm:w-full xlg:w-96" alt="">
                </div>
            </div>
        </div>
    </div> --}}
</div>
@endsection

@section('js')
<script src="https://maps.googleapis.com/maps/api/js?key={{ App\Models\Setting::first()->map_key }}&sensor=false&libraries=places">
</script>
<script src="{{ url('assets/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ url('assets/js/slick.min.js') }}"></script>
<script type="text/javascript">
    $(window).on('load', function() {
        const $imageValue = document.getElementById('landing_popup_switch').value;
        const $popupTime = document.getElementById('popup_timer_seconds').value;
        const $miliSeconds = $popupTime * 1000;

        var is_modal_show = sessionStorage.getItem('alreadyShow');
        if ($imageValue == 1) {
            if (is_modal_show != 'alredy shown') {
                setTimeout(function() {
                    const modal = new Modal($targetEl, options);
                    $('#modalBtn').click();
                }, $miliSeconds);

                $targetEl = document.getElementById('staticModal');
                sessionStorage.setItem('alreadyShow', 'alredy shown');
            } else {
                console.log('popup alredy shown');
            }
        } else {
            $('#staticModal').hide();
        }

        const options = {
            placement: 'center',
            backdrop: 'dynamic',
            backdropClasses: 'bg-gray-900 bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-40',
            closable: true,
            onHide: () => {
                console.log('modal is hidden');
            },
            onShow: () => {
                console.log('modal is shown');
            },
            onToggle: () => {
                console.log('modal has been toggled');
            }
        };

    });
    $('.slick-slider-rtl').slick({
        infinite: false,
        prevArrow: $('.prev'),
        nextArrow: $('.next'),
        autoplay: true,
        autoplaySpeed: 1000,
        slidesToShow: 3,
        lidesToScroll: 1,
        dots: true,
        rtl: true,
        slidesToShow: 3, // Shows a three slides at a time
        responsive: [{
                breakpoint: 1024,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1,
                },
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 2,
                },
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });

    $('.slick-slider-ltr').slick({
        infinite: false,
        prevArrow: $('.prev'),
        nextArrow: $('.next'),
        autoplay: true,
        autoplaySpeed: 1000,
        slidesToShow: 3,
        lidesToScroll: 1,
        dots: true,
        ltr: true,
        slidesToShow: 3, // Shows a three slides at a time
        responsive: [{
                breakpoint: 1024,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1,
                },
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 2,
                },
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });

    // Function to check if an element is in the viewport
    function isInViewport(element) {
        var rect = element.getBoundingClientRect();
        // console.log('rect.top', rect.top);
        // console.log('rect.bottom', rect.bottom);
        return (
            rect.top <200 || rect.bottom <= (window.innerHeight || document.documentElement.clientHeight)
        );
    }

    // Function to add animation class when element is in viewport
    function animateOnScroll() {
        var element = document.querySelector('.animated-element');
        if (isInViewport(element)) {
            element.classList.add('animate__fadeInLeft');
            document.querySelector('.comment-group').classList.add('animate__bounceIn');
        } 
    }
    function change_search_option(v) {
        $('#search_option').val(v)
        $('.toggle-switch a').removeClass('active');
        $('#search_option_'+v).addClass('active');
    }
    function geolocate()
    {
        var autocomplete = new google.maps.places.Autocomplete((document.getElementById('search_address')), { types: ['geocode'] });
        google.maps.event.addListener(autocomplete, 'place_changed', function()
        {
            var lat = autocomplete.getPlace().geometry.location.lat();
            var lng = autocomplete.getPlace().geometry.location.lng();
            $('#search_lat').val(lat);
            $('#search_lng').val(lng);
        });
    }
    window.addEventListener('scroll', animateOnScroll);
    animateOnScroll();

    $(document).ready(function () {
        // Attach scroll event listener to trigger animation
        // Initial check to see if the element is already in the viewport on page load
        $('#search_date').datepicker({
            format: "yyyy-mm-dd",
            maxViewMode: 3,
            language: "fr",
            daysOfWeekHighlighted: "0",
            autoclose: true,
            todayHighlight: true
        });
        $("#search_doctor").autocomplete({
            minLength: 1,
            source: function( request, response ) {
                $.ajax( {
                    headers:
                    {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    data:{
                        search_doctor: request.term
                    },
                    url: base_url + '/autocomplete-doctors',
                    dataType: "json",
                    success: function( data ) {
                        response( data.data );
                    }
                });
            },
            focus: function( event, ui ) {
                $( "#search_doctor" ).val( ui.item.name );
                $( "#search_type" ).val('doctor');
                return false;
            },
            select: function( event, ui ) {
                $( "#search_doctor" ).val( ui.item.name );
                $( "#search_type" ).val( ui.item.type );
                $('#gender_all').trigger('click');
                $('input[name="select_specialist"]').prop('checked', false);
                if (ui.item.type=='category') {
                    $('input[name="select_specialist"]').each(function(i)
                    {
                        if ($(this).next().html() == ui.item.name )
                            $(this).prop('checked', true);
                    });
                } else {
                    $('input[name="select_specialist"]').prop('checked', false);
                }
                searchDoctor();
                return false;
            }
        })
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            var searchInput = $( "#search_doctor" ).val();
            var emphasizedText = item.name.replace(new RegExp(searchInput, 'gi'), function(match) {
                return '<b>' + match + '</b>';
            });
            if (item.type=='doctor') {
                return $( "<li>" )
                // .append( "<a class='atc-box' href='"+base_url+item.href+"'><div class='atc-img'><img src='"+base_url+'/images/upload/'+item.img+"'/></div><div class='atc-desc'><div class='atc-title'>" + emphasizedText + "</div><div class='atc-categ'>" + item.category + "</div></div></a>" )
                .append( "<div class='atc-box'><div class='atc-img'><img src='"+base_url+'/images/upload/'+item.img+"'/></div><div class='atc-desc'><div class='atc-title'>" + emphasizedText + "</div><div class='atc-categ'>" + item.category + "</div></div></div>" )
                .appendTo( ul );
            } else {
                return $( "<li>" )
                // .append( "<div class='atc-box'><div class='atc-title'>" + emphasizedText + "</div><div class='atc-categ'>" + item.category + "</div></div>" )
                .append( "<div class='atc-box'><div class='atc-title'>" + emphasizedText + "</div><div class='atc-categ'>[Expertise]</div></div>" )
                .appendTo( ul );
            }
        };
        // Call the function to start the animation
        repeatAnimation();
    });
    function repeatAnimation() {
        var element = document.getElementById('guide_doctor');
        element.classList.remove('animate__fadeInUp'); // Remove animation class to reset animation
        void element.offsetWidth; // Trigger reflow to restart animation
        element.classList.add('animate__fadeInUp'); // Add animation class to start animation again
        setTimeout(repeatAnimation, 10000); // Repeat after 5 seconds (adjust as needed)
    }
</script>
@endsection
