@extends('frontend.layouts.master')

@section('contents')
    <!--==========================
            BREADCRUMB PART START
        ===========================-->
    <div id="breadcrumb_part">
        <div class="bread_overlay">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 text-center text-white">
                        <h4>Proveedores</h4>
                        <nav style="--bs-breadcrumb-divider: '';" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}"> Inicio </a></li>
                                <li class="breadcrumb-item active" aria-current="page"> Proveedores </li>
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
            LISTING PAGE START
        ===========================-->
    <section id="listing_grid" class="grid_view">
        <div class="container">
            <form action="{{ route('listings') }}" method="GET">
                <div class="row" style="color: #0a568c">
                    <div class="col-xl-3 col-sm-6">
                        <div class="sidebar_line">
                            <input type="text" placeholder="Buscar" name="search" value="{{ request()->search }}">
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="sidebar_line_select">
                            <select class="select_2" name="category">
                                <option>Categorías</option>
                                @foreach ($categories as $category)
                                    <option @selected($category->slug == request()->category) value="{{ $category->slug }}">{{ $category->name }}</option>
                                @endforeach

                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <select class="select_2" name="location">
                            <option value="">Ubicación</option>
                            @foreach ($locations as $location)
                                <option @selected($location->slug == request()->location) value="{{ $location->slug }}">{{ $location->name }}</option>
                            @endforeach

                        </select>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="map_popup_text">
                            <button class="read_btn" type="submit">Buscar</button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row">

                @foreach ($listings as $listing)
                    <div class="col-xl-3 col-sm-6">
                        <div class="wsus__featured_single" style="box-shadow: rgba(149, 157, 165, 0.2) 0px 6px 14px">
                            <div class="wsus__featured_single">
                                <div class="wsus__featured_single_img">
                                    <img src="{{ asset($listing->image) }}" alt="{{ $listing->title }}" class="img-fluid w-100" onclick="showListingModal('{{ $listing->id }}')" style="cursor: pointer; ">
                                    @if ($listing->is_featured)
                                        <span class="small_text">
                                            <i class="fas fa-check-circle"></i> Destacado
                                        </span>
                                    @endif
                                </div>
                                @if ($listing->is_previliged)
                                    <a class="map">
                                        <i class="fas fa-check-circle"></i>
                                    </a>
                                @endif
                                <div class="wsus__featured_single_text">
                                    <a href="{{ route('listing.show', $listing->slug) }}">{{ ucwords(strtolower(truncate($listing->title))) }}</a>
                                    <p class="address">Categoría: {{ $listing->category->name }}</p>
                                    <p class="address">Ubicación: {{ $listing->location->name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="col-12">
                    <div id="pagination">
                        @if ($listings->hasPages())
                            {{ $listings->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--========================== LISTING PAGE START ===========================-->
@endsection
