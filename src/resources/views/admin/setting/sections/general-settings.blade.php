<div class="tab-pane fade show active" id="home4" role="tabpanel" aria-labelledby="home-tab4">
    @if(session()->has('statusStnGen'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            ¡Actualizado correctamente!
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="card border">
      <div class="card-body">
          <form action="{{ route("admin.general-settings.update") }}" method="POST">
              @csrf
              <div class="row">
                  <div class="col-md-12">
                      <div class="form-group">
                          <label for="">Nombre del sitio</label>
                          <input type="text" class="form-control" name="site_name" value="{{ config('settings.site_name') }}">
                      </div>
                  </div>
                  <div class="col-md-6">
                      <div class="form-group">
                          <label for="">Correo electrónico del sitio</label>
                          <input type="text" class="form-control" name="site_email" value="{{ config('settings.site_email') }}">
                      </div>
                  </div>
                  <div class="col-md-6">
                      <div class="form-group">
                          <label for="">Teléfono del sitio</label>
                          <input type="text" class="form-control" name="site_phone" value="{{ config('settings.site_phone') }}">
                      </div>
                  </div>

              </div>
              <button type="submit" class="btn btn-primary">Guardar</button>
          </form>
      </div>
    </div>
  </div>
