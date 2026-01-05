@extends('frontend.layouts.master')

@section('contents')
    @guest
        @include('frontend.home.sections.banner-guest-section')
    @endguest
    @auth
        @include('frontend.home.sections.banner-section')

        @include('frontend.home.sections.category-slider-section')

        @include('frontend.home.sections.features-section')

        @include('frontend.home.sections.featured-category-section')

        @include('frontend.home.sections.featured-location-section')

        @include('frontend.home.sections.counter-section')

        @include('frontend.home.sections.featured-listing-section')
    @endauth
@endsection
