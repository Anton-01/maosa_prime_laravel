@extends('frontend.layouts.master')

@section('contents')
    <!--==========================
        BREADCRUMB PART START
    ===========================-->
    <div id="breadcrumb_part" style="
    background: url({{ $listing->thumbnail_image }});
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    ">
        <div class="bread_overlay">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 text-center text-white">
                        <h4>{{ $listing->title }}</h4>
                        <nav style="--bs-breadcrumb-divider: '';" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item" data-=""><a  href="{{ url('/') }}" > Inicio </a></li>
                                <li class="breadcrumb-item active" aria-current="page"> Detalles - Proveedor </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--==========================
        BREADCRUMB PART END
    ===========================-->


    <!--==========================
        LISTING DETAILS START
    ===========================-->
    <section id="listing_details">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 col-lg-7">
                    <div class="listing_details_text">
                        <div class="listing_det_header">
                            <div class="listing_det_header_text">
                                <h6>{{ $listing->title }}</h6>
                                <p class="host_name">Organizado por: <a href="agent_public_profile.html">{{ $listing->user->name }}</a></p>
                                <ul>
                                    @if ($listing->is_verified)
                                    <li><a href="#"><i class="far fa-check"></i>Verificado</a></li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                        <div class="listing_det_text">
                            {!! $listing->description !!}
                        </div>
                        <div class="listing_det_feature">
                            <div class="row">
                                @foreach ($listing->amenities as $amenity)
                                <div class="col-xl-6 col-sm-4">
                                    <div class="listing_det_feature_single">
                                        <i class="{{ $amenity->amenity->icon }}"></i>
                                        <span>{{ $amenity->amenity->name }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @if ($listing->google_map_embed_code)
                        <div class="listing_det_location">
                            {!! $listing->google_map_embed_code !!}
                        </div>
                        @endif
                    </div>
                </div>
                <div class="col-xl-4 col-lg-5">
                    <div class="listing_details_sidebar">
                        <div class="row">
                            <div class="col-12">
                                <div class="listing_det_side_address">
                                    <a href="callto:{{ $listing->phone }}"><i class="fal fa-phone-alt"></i>
                                        {{ $listing->phone }}</a>
                                    <a href="mailto:{{ $listing->email }}"><i class="fal fa-envelope"></i>
                                        {{ $listing->email }}</a>
                                    <p><i class="fal fa-map-marker-alt"></i> {{ $listing->address }}, {{ $listing->location->name }}</p>
                                    @if ($listing->website)
                                    <p><i class="fal fa-globe"></i> <a href="">{{ $listing->website }}</a></p>
                                    @endif
                                    <ul>
                                        @if ($listing->facebook_link)
                                        <li><a href="{{ $listing->facebook_link }}"><i class="fab fa-facebook-f"></i></a></li>
                                        @endif
                                        @if ($listing->x_link)
                                        <li><a href="{{ $listing->x_link }}"><i class="fab fa-twitter"></i></a></li>
                                        @endif
                                        @if ($listing->linkedin_link)
                                        <li><a href="{{ $listing->linkedin_link }}"><i class="fab fa-linkedin-in"></i></a></li>
                                        @endif
                                        @if ($listing->whatsapp_link)
                                        <li><a href="{{ $listing->whatsapp_link }}"><i class="fab fa-whatsapp"></i></a></li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            @if (count($listing->schedules) > 0)
                            <div class="col-12">
                                <div class="listing_det_side_open_hour">
                                    <h5>Horario de atención al público</h5>
                                    @foreach ($listing->schedules as $schedule)
                                    <p>{{ $schedule->day }} <span>{{ $schedule->start_time }} - {{ $schedule->end_time }}</span></p>
                                    @endforeach

                                </div>
                            </div>
                            @endif

                            @if (count($smellerListings) > 0)
                            <div class="col-12">
                                <div class="listing_det_side_list">
                                    <h5>Proveedores Similares</h5>
                                    @foreach ($smellerListings as $smellerListing)
                                    <a href="{{ route('listing.show', $smellerListing->slug) }}" class="sidebar_blog_single">
                                        <div class="sidebar_blog_img">
                                            <img src="{{ asset($smellerListing->image) }}" alt="{{ $smellerListing->title }}" class="imgofluid w-100">
                                        </div>
                                        <div class="sidebar_blog_text">
                                            <h5>{{ truncate($smellerListing->title) }}</h5>
                                            <p> <span>{{ date('m d Y', strtotime($smellerListing->created_at)) }} </span></p>
                                        </div>
                                    </a>
                                    @endforeach

                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--==========================
        LISTING DETAILS END
    ===========================-->
@endsection
