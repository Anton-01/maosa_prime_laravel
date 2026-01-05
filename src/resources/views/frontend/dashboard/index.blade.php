@extends('frontend.layouts.master')

@section('contents')
<section id="dashboard">
    <div class="container">
      <div class="row">
        <div class="col-lg-3">
            @include('frontend.dashboard.sidebar')
        </div>
      </div>
    </div>
  </section>
@endsection
