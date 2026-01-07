<div>
    <div class="form-group">
        <label>Redes Sociales</label>
        <small class="text-muted d-block mb-2">Selecciona la red social y agrega la URL correspondiente</small>

        @foreach($socialLinks as $index => $link)
            <div class="row mb-3 social-link-row" wire:key="social-link-{{ $index }}">
                <div class="col-md-5">
                    <select
                        class="form-control"
                        name="social_links[{{ $index }}][social_network_id]"
                        wire:model="socialLinks.{{ $index }}.social_network_id">
                        <option value="">Selecciona una red social</option>
                        @foreach($availableNetworks as $network)
                            <option value="{{ $network->id }}">
                                {{ $network->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <input
                        type="url"
                        class="form-control"
                        name="social_links[{{ $index }}][url]"
                        placeholder="https://..."
                        wire:model="socialLinks.{{ $index }}.url">
                </div>
                <div class="col-md-2">
                    @if($index > 0)
                        <button
                            type="button"
                            class="btn btn-danger btn-sm"
                            wire:click="removeSocialLink({{ $index }})">
                            <i class="fas fa-trash"></i>
                        </button>
                    @endif
                    @if($index === array_key_last($socialLinks))
                        <button
                            type="button"
                            class="btn btn-success btn-sm ml-1"
                            wire:click="addSocialLink">
                            <i class="fas fa-plus"></i>
                        </button>
                    @endif
                </div>
            </div>
        @endforeach

        <div class="mt-2">
            <small class="text-info">
                <i class="fas fa-info-circle"></i> Puedes agregar múltiples redes sociales usando el botón <strong>+</strong>
            </small>
        </div>
    </div>

    <style>
        .social-link-row {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            border-left: 3px solid #007bff;
        }
        .social-link-row:hover {
            background-color: #e9ecef;
        }
    </style>
</div>
