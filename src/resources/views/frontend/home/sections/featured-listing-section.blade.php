<section id="wsus__featured_listing">
    <div class="container">
        <div class="row">
            <div class="col-xl-5 m-auto">
                <div class="wsus__heading_area">
                    <h2>{{ $sectionTitle?->our_featured_listing_title }}</h2>
                    <p>{{ $sectionTitle?->our_featured_listing_sub_title }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row listing_slider">
            @foreach ($featuredListings as $listing)
                <div class="col-xl-4 col-sm-6">
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
                            <a href="{{ route('listing.show', $listing->slug) }}">{{ truncate($listing->title) }}</a>
                            <p class="address">Categoría: {{ $listing->category->name }}</p>
                            <p class="address">Ubicación: {{ $listing->location->name }}</p>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</section>
