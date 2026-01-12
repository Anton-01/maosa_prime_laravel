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
                        <nav style="--bs-breadcrumb-divider: '';" aria-label="breadcrumb">
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
                    <div class="listing_details_text">
                        <!-- Header Section -->
                        <div class="listing_det_header" style="background: #fff; border-radius: 10px; padding: 30px; box-shadow: 0 2px 15px rgba(0,0,0,0.08); margin-bottom: 30px;">
                            <div class="listing_det_header_text">
                                <h6 style="font-size: 28px; font-weight: 700; color: #1a1a1a; margin-bottom: 15px;">{{ $listing->title }}</h6>
                                <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap; margin-bottom: 15px;">
                                    <p class="host_name" style="margin: 0; color: #666; font-size: 15px;">
                                        <i class="fal fa-user-circle" style="margin-right: 5px;"></i>
                                        Organizado por: <a href="#" style="color: #2d6a4f; font-weight: 600;">{{ $listing->user->name }}</a>
                                    </p>
                                    @if ($listing->is_verified)
                                        <span style="background: #2d6a4f; color: white; padding: 6px 15px; border-radius: 20px; font-size: 13px; display: inline-flex; align-items: center; gap: 5px;">
                                            <i class="far fa-check-circle"></i> Verificado
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Description Section -->
                        <div class="listing_det_text" style="background: #fff; border-radius: 10px; padding: 30px; box-shadow: 0 2px 15px rgba(0,0,0,0.08); margin-bottom: 30px;">
                            <h5 style="font-size: 20px; font-weight: 700; color: #1a1a1a; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">
                                <i class="fal fa-info-circle" style="color: #2d6a4f; margin-right: 10px;"></i>Acerca de la empresa
                            </h5>
                            <div style="color: #555; line-height: 1.8; font-size: 15px;">
                                {!! $listing->description !!}
                            </div>
                        </div>

                        <!-- Amenities Section -->
                        @if ($listing->amenities && count($listing->amenities) > 0)
                            <div class="listing_det_feature" style="background: #fff; border-radius: 10px; padding: 30px; box-shadow: 0 2px 15px rgba(0,0,0,0.08); margin-bottom: 30px;">
                                <h5 style="font-size: 20px; font-weight: 700; color: #1a1a1a; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">
                                    <i class="fal fa-list-ul" style="color: #2d6a4f; margin-right: 10px;"></i>Servicios y Características
                                </h5>
                                <div class="row g-3">
                                    @foreach ($listing->amenities as $amenity)
                                        <div class="col-xl-6 col-md-6">
                                            <div class="listing_det_feature_single" style="background: #f8f9fa; border-radius: 8px; padding: 15px 20px; display: flex; align-items: center; gap: 15px; transition: all 0.3s ease; border: 1px solid #e9ecef;">
                                                <i class="{{ $amenity->amenity->icon }}" style="font-size: 24px; color: #2d6a4f;"></i>
                                                <span style="color: #333; font-weight: 500; font-size: 15px;">{{ $amenity->amenity->name }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Map Section -->

                        @if ($listing->google_map_embed_code)
                            <div class="listing_det_location" style="background: #fff; border-radius: 10px; padding: 30px; box-shadow: 0 2px 15px rgba(0,0,0,0.08); margin-bottom: 30px;">
                                <h5 style="font-size: 20px; font-weight: 700; color: #1a1a1a; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">
                                    <i class="fal fa-map-marker-alt" style="color: #2d6a4f; margin-right: 10px;"></i>Ubicación
                                </h5>
                                <div style="border-radius: 8px; overflow: hidden;">
                                    {!! $listing->google_map_embed_code !!}
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-xl-4 col-lg-5">
                    <div class="listing_details_sidebar">
                        <div class="row">

                            <!-- Contact Information -->
                            <div class="col-12">
                                <div class="listing_det_side_address" style="background: #fff; border-radius: 10px; padding: 30px; box-shadow: 0 2px 15px rgba(0,0,0,0.08); margin-bottom: 30px;">
                                    <h5 style="font-size: 18px; font-weight: 700; color: #1a1a1a; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">
                                        <i class="fal fa-address-card" style="color: #2d6a4f; margin-right: 10px;"></i>Información de Contacto
                                    </h5>
                                    <div style="display: flex; flex-direction: column; gap: 15px;">
                                        <a href="callto:{{ $listing->phone }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #f8f9fa; border-radius: 8px; text-decoration: none; color: #333; transition: all 0.3s ease; border: 1px solid #e9ecef;">
                                            <i class="fal fa-phone-alt" style="font-size: 18px; color: #2d6a4f; min-width: 20px;"></i>
                                            <span style="font-size: 14px; font-weight: 500;">{{ $listing->phone }}</span>
                                        </a>
                                        <a href="mailto:{{ $listing->email }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #f8f9fa; border-radius: 8px; text-decoration: none; color: #333; transition: all 0.3s ease; border: 1px solid #e9ecef; word-break: break-word;">
                                            <i class="fal fa-envelope" style="font-size: 18px; color: #2d6a4f; min-width: 20px;"></i>
                                            <span style="font-size: 14px; font-weight: 500;">{{ $listing->email }}</span>
                                        </a>
                                        <div style="display: flex; align-items: flex-start; gap: 12px; padding: 12px; background: #f8f9fa; border-radius: 8px; border: 1px solid #e9ecef;">
                                            <i class="fal fa-map-marker-alt" style="font-size: 18px; color: #2d6a4f; min-width: 20px; margin-top: 2px;"></i>
                                            <span style="font-size: 14px; color: #555; line-height: 1.6;">{{ $listing->address }}, {{ $listing->location->name }}</span>
                                        </div>
                                        @if ($listing->website)
                                            <a href="{{ $listing->website }}" target="_blank" style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #f8f9fa; border-radius: 8px; text-decoration: none; color: #333; transition: all 0.3s ease; border: 1px solid #e9ecef; word-break: break-all;">
                                                <i class="fal fa-globe" style="font-size: 18px; color: #2d6a4f; min-width: 20px;"></i>
                                                <span style="font-size: 14px; font-weight: 500;">{{ $listing->website }}</span>
                                            </a>
                                        @endif
                                    </div>

                                    @if ($listing->socialNetworks && $listing->socialNetworks->count() > 0)
                                        <div style="margin-top: 25px; padding-top: 20px; border-top: 2px solid #f0f0f0;">
                                            <h6 style="font-size: 14px; font-weight: 600; color: #666; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 0.5px;">Redes Sociales</h6>
                                            <ul style="list-style: none; padding: 0; margin: 0; display: flex; gap: 10px; flex-wrap: wrap;">
                                                @foreach ($listing->socialNetworks as $socialNetwork)
                                                    <li>
                                                        <a href="{{ $socialNetwork->pivot->url }}" target="_blank"
                                                           title="{{ $socialNetwork->name }}"
                                                           style="display: flex; align-items: center; justify-content: center; width: 45px; height: 45px; border-radius: 8px; background: {{ $socialNetwork->color ?? '#6c757d' }}; color: white; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                                            <i class="{{ $socialNetwork->icon }}" style="font-size: 20px;"></i>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Schedule Section -->
                            @if (count($listing->schedules) > 0)
                                <div class="col-12">
                                    <div class="listing_det_side_open_hour" style="background: #fff; border-radius: 10px; padding: 30px; box-shadow: 0 2px 15px rgba(0,0,0,0.08); margin-bottom: 30px;">
                                        <h5 style="font-size: 18px; font-weight: 700; color: #1a1a1a; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">
                                            <i class="fal fa-clock" style="color: #2d6a4f; margin-right: 10px;"></i>Horario de Atención
                                        </h5>
                                        <div style="display: flex; flex-direction: column; gap: 12px;">
                                            @foreach ($listing->schedules as $schedule)
                                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 15px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid #2d6a4f;">
                                                    <span style="font-weight: 600; color: #333; font-size: 14px;">{{ $schedule->day }}</span>
                                                    <span style="color: #666; font-size: 14px;">{{ $schedule->start_time }} - {{ $schedule->end_time }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Similar Providers -->
                            @if (count($smellerListings) > 0)
                                <div class="col-12">
                                    <div class="listing_det_side_list" style="background: #fff; border-radius: 10px; padding: 30px; box-shadow: 0 2px 15px rgba(0,0,0,0.08);">
                                        <h5 style="font-size: 18px; font-weight: 700; color: #1a1a1a; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">
                                            <i class="fal fa-building" style="color: #2d6a4f; margin-right: 10px;"></i>Proveedores Similares
                                        </h5>
                                        <div style="display: flex; flex-direction: column; gap: 15px;">
                                            @foreach ($smellerListings as $smellerListing)
                                                <a href="{{ route('listing.show', $smellerListing->slug) }}" class="sidebar_blog_single" style="display: flex; gap: 15px; padding: 12px; background: #f8f9fa; border-radius: 8px; text-decoration: none; transition: all 0.3s ease; border: 1px solid #e9ecef;">
                                                    <div class="sidebar_blog_img" style="flex-shrink: 0; width: 80px; height: 80px; border-radius: 6px; overflow: hidden;">
                                                        <img src="{{ asset($smellerListing->image) }}" alt="{{ $smellerListing->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                    </div>
                                                    <div class="sidebar_blog_text" style="flex: 1; display: flex; flex-direction: column; justify-content: center;">
                                                        <h6 style="font-size: 14px; font-weight: 600; color: #333; margin: 0 0 8px 0; line-height: 1.4;">{{ truncate($smellerListing->title, 60) }}</h6>
                                                        <p style="margin: 0; font-size: 12px; color: #999;">
                                                            <i class="fal fa-calendar-alt" style="margin-right: 5px;"></i>
                                                            <span>{{ date('d M Y', strtotime($smellerListing->created_at)) }}</span>
                                                        </p>
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
