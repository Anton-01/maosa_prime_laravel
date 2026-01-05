@extends('admin.layouts.master')

@section('contents')
<section class="section">
    <div class="section-header">
      <h1>Dashboard</h1>
    </div>
    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-list"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Total Proveedores</h4>
            </div>
            <div class="card-body">
              {{ $totalListingCount }}
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger">
            <i class="fas fa-list-ol"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Total Categorias</h4>
            </div>
            <div class="card-body">
              {{ $listingCategoryCount }}
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-info">
            <i class="fas fa-location-arrow"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Total ubicaciones</h4>
            </div>
            <div class="card-body">
              {{ $locationCount }}
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-user-shield"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Total Admins</h4>
            </div>
            <div class="card-body">
              {{ $adminCount }}
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger">
            <i class="fas fa-fingerprint"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Total usuarios</h4>
            </div>
            <div class="card-body">
              {{ $usersCount }}
            </div>
          </div>
        </div>
      </div>


    </div>

  </section>
@endsection
