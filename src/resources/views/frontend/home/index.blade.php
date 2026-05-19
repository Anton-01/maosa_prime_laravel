@extends('frontend.layouts.master')

@section('contents')
    @include('frontend.home.sections.banner-section')
    @include('frontend.home.sections.category-slider-section')
    @include('frontend.home.sections.features-section')
    @include('frontend.home.sections.featured-category-section')
    @include('frontend.home.sections.featured-location-section')
    @include('frontend.home.sections.counter-section')
    @include('frontend.home.sections.featured-listing-section')
@endsection
