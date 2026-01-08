{{-- Social Links Manager Component --}}
<div class="form-group" id="social-links-manager">
    <label for="">Redes Sociales <span class="text-muted">(Opcional)</span></label>
    <small class="d-block text-muted mb-2">Agrega enlaces a las redes sociales del proveedor</small>

    <div id="social-links-container">
        @if(isset($existingLinks) && count($existingLinks) > 0)
            @foreach($existingLinks as $index => $link)
                <div class="social-link-row mb-3" data-index="{{ $index }}">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label>Red Social</label>
                            <select name="social_links[{{ $index }}][social_network_id]" class="form-control">
                                <option value="">Seleccionar red social</option>
                                @foreach($socialNetworks as $network)
                                    <option value="{{ $network->id }}"
                                        {{ (isset($link['social_network_id']) && $link['social_network_id'] == $network->id) ? 'selected' : '' }}>
                                        {{ $network->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>URL</label>
                            <input type="url"
                                   name="social_links[{{ $index }}][url]"
                                   class="form-control"
                                   placeholder="https://facebook.com/tu-empresa"
                                   value="{{ $link['url'] ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-block remove-social-link">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="social-link-row mb-3" data-index="0">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label>Red Social</label>
                        <select name="social_links[0][social_network_id]" class="form-control">
                            <option value="">Seleccionar red social</option>
                            @foreach($socialNetworks as $network)
                                <option value="{{ $network->id }}">{{ $network->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>URL</label>
                        <input type="url"
                               name="social_links[0][url]"
                               class="form-control"
                               placeholder="https://facebook.com/tu-empresa">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-block remove-social-link">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <button type="button" id="add-social-link" class="btn btn-primary mt-2">
        <i class="fas fa-plus"></i> Agregar Red Social
    </button>
</div>

{{-- Template for new social link rows --}}
<template id="social-link-template">
    <div class="social-link-row mb-3" data-index="__INDEX__">
        <div class="row align-items-end">
            <div class="col-md-4">
                <label>Red Social</label>
                <select name="social_links[__INDEX__][social_network_id]" class="form-control">
                    <option value="">Seleccionar red social</option>
                    @foreach($socialNetworks as $network)
                        <option value="{{ $network->id }}">{{ $network->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label>URL</label>
                <input type="url"
                       name="social_links[__INDEX__][url]"
                       class="form-control"
                       placeholder="https://facebook.com/tu-empresa">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-block remove-social-link">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</template>
