<section id="wsus__counter" style="background: url({{ asset('/uploads/media_655ddb4b122f8.jpg') }})">
    <div class="wsus__counter_overlay">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-6 col-md-3">
                    <div class="wsus__counter_single">
                        <span class="counter">{{ $userCount }}</span>
                        <p>Usuarios</p>
                    </div>
                </div>
                <div class="col-xl-3 col-6 col-md-3">
                    <div class="wsus__counter_single">
                        <span class="counter">{{ $suppliersCount }}</span>
                        <p>Proveedores</p>
                    </div>
                </div>
                <div class="col-xl-3 col-6 col-md-3">
                    <div class="wsus__counter_single">
                        <span class="counter">{{ $categories->count() }}</span>
                        <p>Categorías</p>
                    </div>
                </div>
                <div class="col-xl-3 col-6 col-md-3">
                    <div class="wsus__counter_single">
                        <span class="counter">{{ $locations->count() }}</span>
                        <p>Ubicaciones</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
