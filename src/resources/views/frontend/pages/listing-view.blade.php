@extends('frontend.layouts.master')

@section('contents')
    <!--==========================
        BREADCRUMB PART START
    ===========================-->
    <div id="breadcrumb_part" class="listing-breadcrumb" style="
    background: url({{ $listing->thumbnail_image }});
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    ">
        <div class="bread_overlay listing-overlay">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 text-center text-white">
                        <h4>{{ $listing->title }}</h4>
                        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}"> Inicio </a></li>
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
    <section id="listing_details" class="listing-details-enhanced">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 col-lg-7">
                    <div class="listing_details_text listing-content-card">
                        <!-- Header -->
                        <div class="listing_det_header listing-header-enhanced">
                            <div class="listing_det_header_text">
                                <h6>{{ $listing->title }}</h6>
                                <p class="host_name">
                                    <i class="fas fa-user-tie"></i>
                                    Organizado por: <a href="#">{{ $listing->user->name }}</a>
                                </p>
                                <ul class="listing-badges">
                                    @if ($listing->is_verified)
                                    <li class="verified-badge">
                                        <i class="far fa-check-circle"></i>
                                        <span>Verificado</span>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="listing_det_text listing-description">
                            {!! $listing->description !!}
                        </div>

                        <!-- Amenities/Services -->
                        @if(count($listing->amenities) > 0)
                        <div class="listing_det_feature listing-amenities-section">
                            <div class="amenities-header">
                                <h5><i class="fas fa-concierge-bell"></i> Servicios y Amenidades</h5>
                            </div>
                            <div class="row">
                                @foreach ($listing->amenities as $amenity)
                                <div class="col-xl-6 col-sm-6 col-md-4">
                                    <div class="listing_det_feature_single amenity-card">
                                        <div class="amenity-icon">
                                            <i class="{{ $amenity->amenity->icon }}"></i>
                                        </div>
                                        <span>{{ $amenity->amenity->name }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Map -->
                        @if ($listing->google_map_embed_code)
                        <div class="listing_det_location listing-map-section">
                            <div class="map-header">
                                <h5><i class="fas fa-map-marked-alt"></i> Ubicación</h5>
                            </div>
                            <div class="map-container">
                                {!! $listing->google_map_embed_code !!}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-xl-4 col-lg-5">
                    <div class="listing_details_sidebar listing-sidebar-enhanced">
                        <div class="row">
                            <!-- Contact Info -->
                            <div class="col-12">
                                <div class="listing_det_side_address sidebar-card contact-card">
                                    <h5 class="sidebar-card-title">
                                        <i class="fas fa-address-card"></i> Información de Contacto
                                    </h5>
                                    <div class="contact-item">
                                        <a href="tel:{{ $listing->phone }}">
                                            <i class="fal fa-phone-alt"></i>
                                            <span>{{ $listing->phone }}</span>
                                        </a>
                                    </div>
                                    <div class="contact-item">
                                        <a href="mailto:{{ $listing->email }}">
                                            <i class="fal fa-envelope"></i>
                                            <span>{{ $listing->email }}</span>
                                        </a>
                                    </div>
                                    <div class="contact-item">
                                        <p>
                                            <i class="fal fa-map-marker-alt"></i>
                                            <span>{{ $listing->address }}, {{ $listing->location->name }}</span>
                                        </p>
                                    </div>
                                    @if ($listing->website)
                                    <div class="contact-item">
                                        <a href="{{ $listing->website }}" target="_blank">
                                            <i class="fal fa-globe"></i>
                                            <span>{{ $listing->website }}</span>
                                        </a>
                                    </div>
                                    @endif

                                    <!-- Social Links -->
                                    <div class="social-links">
                                        @if ($listing->facebook_link)
                                        <a href="{{ $listing->facebook_link }}" class="social-link facebook" target="_blank">
                                            <i class="fab fa-facebook-f"></i>
                                        </a>
                                        @endif
                                        @if ($listing->x_link)
                                        <a href="{{ $listing->x_link }}" class="social-link twitter" target="_blank">
                                            <i class="fab fa-twitter"></i>
                                        </a>
                                        @endif
                                        @if ($listing->linkedin_link)
                                        <a href="{{ $listing->linkedin_link }}" class="social-link linkedin" target="_blank">
                                            <i class="fab fa-linkedin-in"></i>
                                        </a>
                                        @endif
                                        @if ($listing->whatsapp_link)
                                        <a href="{{ $listing->whatsapp_link }}" class="social-link whatsapp" target="_blank">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Schedule -->
                            @if (count($listing->schedules) > 0)
                            <div class="col-12">
                                <div class="listing_det_side_open_hour sidebar-card schedule-card">
                                    <h5 class="sidebar-card-title">
                                        <i class="fas fa-clock"></i> Horario de Atención
                                    </h5>
                                    <div class="schedule-list">
                                        @foreach ($listing->schedules as $schedule)
                                        <div class="schedule-item">
                                            <span class="day">{{ $schedule->day }}</span>
                                            <span class="time">{{ $schedule->start_time }} - {{ $schedule->end_time }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Similar Listings -->
                            @if (count($smellerListings) > 0)
                            <div class="col-12">
                                <div class="listing_det_side_list sidebar-card similar-card">
                                    <h5 class="sidebar-card-title">
                                        <i class="fas fa-th-large"></i> Proveedores Similares
                                    </h5>
                                    <div class="similar-listings">
                                        @foreach ($smellerListings as $smellerListing)
                                        <a href="{{ route('listing.show', $smellerListing->slug) }}" class="similar-listing-item">
                                            <div class="similar-image">
                                                <img src="{{ asset($smellerListing->image) }}" alt="{{ $smellerListing->title }}">
                                            </div>
                                            <div class="similar-content">
                                                <h6>{{ truncate($smellerListing->title, 25) }}</h6>
                                                <span class="similar-date">
                                                    <i class="fal fa-calendar"></i>
                                                    {{ date('d M Y', strtotime($smellerListing->created_at)) }}
                                                </span>
                                            </div>
                                        </a>
                                        @endforeach
                                    </div>
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

    <style>
        /* Enhanced Listing Details */
        .listing-details-enhanced {
            background: #f8f9fa;
            padding: 60px 0;
        }

        .listing-breadcrumb .listing-overlay {
            background: linear-gradient(135deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.4) 100%);
        }

        /* Main Content Card */
        .listing-content-card {
            background: #fff;
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.08);
        }

        /* Header Enhanced */
        .listing-header-enhanced {
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 25px;
            margin-bottom: 30px;
        }

        .listing-header-enhanced h6 {
            font-size: 28px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 12px;
        }

        .listing-header-enhanced .host_name {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
            font-size: 15px;
            margin-bottom: 15px;
        }

        .listing-header-enhanced .host_name i {
            color: #f66542;
        }

        .listing-header-enhanced .host_name a {
            color: #f66542;
            font-weight: 600;
        }

        .listing-badges {
            display: flex;
            gap: 10px;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .verified-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
        }

        .verified-badge i {
            color: #28a745;
        }

        /* Description */
        .listing-description {
            color: #555;
            line-height: 1.8;
            margin-bottom: 35px;
        }

        .listing-description p {
            margin-bottom: 15px;
        }

        /* Amenities Section */
        .listing-amenities-section {
            margin-bottom: 35px;
        }

        .amenities-header,
        .map-header {
            margin-bottom: 20px;
        }

        .amenities-header h5,
        .map-header h5 {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 20px;
            font-weight: 600;
            color: #1a1a2e;
        }

        .amenities-header h5 i,
        .map-header h5 i {
            color: #f66542;
        }

        /* Amenity Card with Shadows and Hover */
        .amenity-card {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 18px 20px;
            background: #fff;
            border-radius: 15px;
            margin-bottom: 15px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.06);
            border: 1px solid #f0f0f0;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            cursor: default;
        }

        .amenity-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(246, 101, 66, 0.15);
            border-color: #f66542;
        }

        .amenity-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #fff5f2 0%, #ffe8e2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .amenity-card:hover .amenity-icon {
            background: linear-gradient(135deg, #f66542 0%, #ff8a65 100%);
        }

        .amenity-icon i {
            font-size: 18px;
            color: #f66542;
            transition: color 0.3s ease;
        }

        .amenity-card:hover .amenity-icon i {
            color: #fff;
        }

        .amenity-card span {
            font-size: 14px;
            font-weight: 500;
            color: #333;
        }

        /* Map Section */
        .listing-map-section {
            margin-top: 20px;
        }

        .map-container {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .map-container iframe {
            width: 100%;
            height: 300px;
            border: none;
        }

        /* Sidebar Enhanced */
        .listing-sidebar-enhanced {
            position: sticky;
            top: 100px;
        }

        .sidebar-card {
            background: #fff;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }

        .sidebar-card:hover {
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .sidebar-card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
            font-weight: 600;
            color: #1a1a2e;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f66542;
        }

        .sidebar-card-title i {
            color: #f66542;
        }

        /* Contact Card */
        .contact-item {
            margin-bottom: 15px;
        }

        .contact-item:last-of-type {
            margin-bottom: 20px;
        }

        .contact-item a,
        .contact-item p {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            color: #555;
            font-size: 14px;
            transition: all 0.3s ease;
            margin: 0;
        }

        .contact-item a:hover {
            color: #f66542;
        }

        .contact-item i {
            color: #f66542;
            font-size: 16px;
            margin-top: 3px;
        }

        /* Social Links */
        .social-links {
            display: flex;
            gap: 10px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .social-link {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            transform: translateY(-3px);
            color: #fff;
        }

        .social-link.facebook { background: #3b5998; }
        .social-link.facebook:hover { box-shadow: 0 5px 15px rgba(59, 89, 152, 0.4); }

        .social-link.twitter { background: #1da1f2; }
        .social-link.twitter:hover { box-shadow: 0 5px 15px rgba(29, 161, 242, 0.4); }

        .social-link.linkedin { background: #0077b5; }
        .social-link.linkedin:hover { box-shadow: 0 5px 15px rgba(0, 119, 181, 0.4); }

        .social-link.whatsapp { background: #25d366; }
        .social-link.whatsapp:hover { box-shadow: 0 5px 15px rgba(37, 211, 102, 0.4); }

        /* Schedule Card */
        .schedule-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .schedule-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            background: #f8f9fa;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .schedule-item:hover {
            background: #fff5f2;
            transform: translateX(5px);
        }

        .schedule-item .day {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .schedule-item .time {
            color: #f66542;
            font-weight: 500;
            font-size: 13px;
        }

        /* Similar Listings */
        .similar-listings {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .similar-listing-item {
            display: flex;
            gap: 15px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .similar-listing-item:hover {
            background: #fff;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transform: translateX(5px);
        }

        .similar-image {
            width: 70px;
            height: 70px;
            border-radius: 10px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .similar-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .similar-listing-item:hover .similar-image img {
            transform: scale(1.1);
        }

        .similar-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .similar-content h6 {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
            transition: color 0.3s ease;
        }

        .similar-listing-item:hover .similar-content h6 {
            color: #f66542;
        }

        .similar-date {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            color: #888;
        }

        .similar-date i {
            color: #f66542;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .listing-content-card {
                padding: 25px;
                margin-bottom: 30px;
            }

            .listing-header-enhanced h6 {
                font-size: 24px;
            }

            .listing-sidebar-enhanced {
                position: relative;
                top: 0;
            }
        }

        @media (max-width: 575px) {
            .listing-details-enhanced {
                padding: 40px 0;
            }

            .listing-content-card {
                padding: 20px;
            }

            .listing-header-enhanced h6 {
                font-size: 20px;
            }

            .amenity-card {
                padding: 15px;
            }

            .amenity-icon {
                width: 40px;
                height: 40px;
            }

            .sidebar-card {
                padding: 20px;
            }
        }
    </style>
@endsection
