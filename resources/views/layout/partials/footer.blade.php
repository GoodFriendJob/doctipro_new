<div class="bg-light-gray w-full footer">
    <div class="msm:mx-0 xsm:mx-0 xxsm:mx-0 xlg:mx-20 2xl:mx-20">
        <div class="xxsm:mx-5 xl:mx-0 2xl:mx-0">
            <div class="xxsm:pt-5 xxsm:pb-5 justify-between flex sm:flex-row xxsm:flex-col">
                <div class="my-10">
                    <a href="{{ url('/') }}" class="">
                        @if(!isset($setting->companyWhite))
                        <img src="{{ $setting->companyWhite }}" width="150px" height="40px" alt="Logo">
                        @else
                        <img src="{{url('/images/upload_empty/footer_logo.png')}}" class="h-24 mr-3 md:h-28 2xl:h-28" alt="Doctro Logo" />
                        @endif
                    </a>
                    {{-- <div class="flex pt-5">
                        <a href="{{ $setting->facebook_url }}" target="_blank" class=""><i class="fa-brands fa-facebook text-black border rounded-full p-2"></i></a>
                        <a href="{{ $setting->twitter_url }}" target="_blank" class="lg:mx-4 md:mx-2 xsm:mx-1 xxsm:mx-1"><i class="fa-brands fa-twitter text-black border rounded-full p-2"></i></a>
                        <a href="{{ $setting->instagram_url }}" target="_blank" class=""><i class="fa-brands fa-instagram text-black border rounded-full p-2"></i></a>
                        <a href="{{ $setting->linkdin_url }}" target="_blank" class="lg:mx-4 md:mx-2 xsm:mx-1 xxsm:mx-1"><i class="fa-brands fa-linkedin-in text-black border rounded-full p-2"></i></a>
                    </div> --}}
                </div>
                <div class="my-10">
                    <h1 class="text-primary font-medium text-lg leading-5 font-fira-sans xxsm:pt-5 sm:pt-0">{{__('For Patients')}}</h1>
                    <ul class="2xl:pt-10 xxsm:pt-5">
                        <li><a href="{{url('/about')}}" class="text-black text-sm font-normal leading-4 font-fira-sans pt-10">Qui sommes-nous ?</a></li>
                        <li><a href="{{url('/our-offers')}}" class="text-black text-sm font-normal leading-4 font-fira-sans pt-10">Notre vision et nos valeurs</a></li>
                        <li><a href="{{url('/our_blogs')}}" class="text-black text-sm font-normal leading-4 font-fira-sans pt-10">Blog</a></li>
                        <li><a href="{{url('/join-us')}}" class="text-black text-sm font-normal leading-4 font-fira-sans pt-10">Rejoindre notre équipe</a></li>
                        <li><a href="{{url('/contact')}}" class="text-black text-sm font-normal leading-4 font-fira-sans pt-10">Nous contacter</a></li>
                    </li>
                </div>
                <div class="my-10">
                    <h1 class="text-primary font-medium text-lg leading-5 font-fira-sans xxsm:pt-5 sm:pt-0">
                        Trouvez votre praticien
                    </h1>
                    <ul class="2xl:pt-10 xxsm:pt-5">
                        <li>
                            <a href="{{url('/show-doctors')}}" class="text-black text-sm font-normal leading-4 font-fira-sans pt-10">
                                Médecin généraliste
                            </a>
                        </li>
                        <li>
                            <a href="{{url('/show-doctors')}}" class="text-black text-sm font-normal leading-4 font-fira-sans pt-10">
                                Dentiste
                            </a>
                        </li>
                        <li>
                            <a href="{{url('/show-doctors')}}" class="text-black text-sm font-normal leading-4 font-fira-sans pt-10">
                                Gynécologue
                            </a>
                        </li>
                        <li>
                            <a href="{{url('/show-doctors')}}" class="text-black text-sm font-normal leading-4 font-fira-sans pt-10">
                                Interniste
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="my-10">
                    <h1 class="text-primary font-medium text-lg leading-5 font-fira-sans msm:pt-5 sm:pt-0">{{__('Contact Us:')}}</h1>
                    <ul class="2xl:pt-10 xxsm:pt-5">
                        <li><a href="tel:{{ $setting->phone }}" class="text-black text-sm leading-4 font-fira-sans font-normal underline pt-2">{{ $setting->phone }}</a></li>
                        <li><a href="mailto:{{ $setting->email }}" class="text-black text-sm leading-4 font-fira-sans font-normal underline pt-2">{{ $setting->email }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="bg-white w-full">
    <div class="msm:mx-0 xsm:mx-0 xxsm:mx-0 xlg:mx-20 2xl:mx-20">
        <div class="xxsm:mx-5 xl:mx-0 2xl:mx-0">
            <div class="xxsm:pt-5 justify-between flex sm:flex-row xxsm:flex-col">
                <div class="flex flex-row">
                    {{-- <p class="text-black text-base font-normal leading-5 font-fira-sans mb-5">{{__('Copyright')}} &copy; {{ Carbon\Carbon::now(env('timezone'))->year }} {{ $setting->business_name }}{{__(', All rights reserved')}} </p> --}}
                    Les informations fournies sur le site ou l'application mobile de DOCTIPRO, y compris celles présentées ici, ont un caractère purement informatif et général. Elles ne constituent pas des conseils médicaux, un diagnostic, ou un traitement. Pour des préoccupations médicales spécifiques ou des conseils concernant votre santé, veuillez consulter directement votre professionnel de la santé.
                </div>
            </div>
        </div>
    </div>
</div>