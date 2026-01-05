<section id="wsus__location">
    <div class="wsus__location_overlay">
        <div class="container">
            <div class="row">
                <div class="col-xl-5 m-auto">
                    <div class="wsus__heading_area">
                        <h2>{{ $sectionTitle?->our_location_title }}</h2>
                        <p>{{ $sectionTitle?->our_location_sub_title }}</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <div class="wsus__location_filter">
                        @foreach ($featuredLocations as $location)
                        <button class="{{ $loop->index === 0 ? 'l_first_tab' : '' }}" data-filter=".{{ $location->slug }}">{{ $location->name }}</button>
                        @endforeach

                    </div>
                </div>
            </div>
            <div class="row grid">
                @foreach ($featuredLocations as $location)
                    @foreach ($location->listings as $listing)
                    <div class="col-xl-3 col-sm-6 col-lg-4 {{ $location->slug }} ">
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
                                <a href="{{ route('listing.show', $listing->slug) }}">{{ truncate($listing->title, 18) }}</a>
                                <p class="address">Categoría: {{ $listing->category->name }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
</section>

@push('scripts')
    <script>

        $(document).ready(function(){
            setTimeout(function(){
                $('.l_first_tab').trigger('click');
            }, 1000);
        })

    </script>
@endpush
