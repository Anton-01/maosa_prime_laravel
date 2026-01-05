<section style="background-color: #f7f1e3;" >
    <div style="height: 70vh;"> <!-- class="wsus__banner_overlay" -->
        <div class="container-fluid" style="height: 100%;">
            <div class="row" style="height: 100%;">
                <div class="col-xl-6 col-lg-6" style="background: url({{ asset(@$hero->background) }})">

                </div>
                <div class="col-xl-6 col-lg-6" style="display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 0 180px;">
                    <h3 style="margin-bottom: 40px;">{!! @$hero->title !!}</h3>
                    {!! @$hero->sub_title !!}
                </div>
            </div>
        </div>
    </div>
</section>
