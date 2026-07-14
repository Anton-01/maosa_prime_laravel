{{--
    Modal generador de contraseñas.
    Se abre con el botón #open-password-generator y al confirmar setea el
    valor en los inputs #password y #password_confirmation del formulario.
--}}
<div class="modal fade" id="password-generator-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-key"></i> Generar contraseña</h5>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="d-block">Contraseña generada</label>
                    <div class="input-group">
                        <input type="text" id="generated-password" class="form-control font-weight-bold" readonly>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" id="pg-regenerate" title="Volver a generar">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <small class="form-text text-danger" id="pg-error" style="display:none">
                        Seleccione al menos un tipo de carácter.
                    </small>
                </div>

                <div class="form-group">
                    <label for="pg-length">Longitud: <strong id="pg-length-value">12</strong> caracteres</label>
                    <input type="range" id="pg-length" class="custom-range" min="8" max="32" step="1" value="12">
                </div>

                <label class="d-block">Incluir:</label>
                <div class="row">
                    <div class="col-6">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input pg-charset" id="pg-lower" data-set="lower" checked>
                            <label class="custom-control-label" for="pg-lower">Minúsculas (a-z)</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input pg-charset" id="pg-upper" data-set="upper" checked>
                            <label class="custom-control-label" for="pg-upper">Mayúsculas (A-Z)</label>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input pg-charset" id="pg-numbers" data-set="numbers" checked>
                            <label class="custom-control-label" for="pg-numbers">Números (0-9)</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input pg-charset" id="pg-symbols" data-set="symbols" checked>
                            <label class="custom-control-label" for="pg-symbols">Símbolos (!@#$…)</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="pg-cancel">Cancelar</button>
                <button type="button" class="btn btn-primary" id="pg-use">
                    <i class="fas fa-check"></i> Usar contraseña
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(function () {
            const modal = $('#password-generator-modal');

            // La plantilla Stisla define `.section { position: relative; z-index: 1 }`,
            // lo que crea un stacking context que deja la modal por debajo del
            // .modal-backdrop (agregado al <body>). Reparentamos la modal al body
            // para que el velo oscuro no bloquee la interacción.
            modal.appendTo('body');

            const output = $('#generated-password');
            const lengthInput = $('#pg-length');
            const lengthValue = $('#pg-length-value');
            const errorMsg = $('#pg-error');
            const useBtn = $('#pg-use');

            // Mismos conjuntos sin caracteres ambiguos que usa el backend
            const CHARSETS = {
                lower: 'abcdefghjkmnpqrstuvwxyz',
                upper: 'ABCDEFGHJKLMNPQRSTUVWXYZ',
                numbers: '23456789',
                symbols: '!@#$%^&*()_+-=[]{}|;:,.<>?'
            };

            function randomInt(max) {
                const buf = new Uint32Array(1);
                window.crypto.getRandomValues(buf);
                return buf[0] % max;
            }

            function selectedSets() {
                return $('.pg-charset:checked').map(function () {
                    return CHARSETS[$(this).data('set')];
                }).get();
            }

            function generate() {
                const sets = selectedSets();
                const length = parseInt(lengthInput.val(), 10);
                lengthValue.text(length);

                if (!sets.length) {
                    output.val('');
                    errorMsg.show();
                    useBtn.prop('disabled', true);
                    return;
                }
                errorMsg.hide();
                useBtn.prop('disabled', false);

                // Garantiza al menos un carácter de cada conjunto seleccionado
                let chars = sets.map(set => set[randomInt(set.length)]);
                const all = sets.join('');
                while (chars.length < length) {
                    chars.push(all[randomInt(all.length)]);
                }
                // Mezcla Fisher-Yates para no dejar los garantizados al inicio
                for (let i = chars.length - 1; i > 0; i--) {
                    const j = randomInt(i + 1);
                    [chars[i], chars[j]] = [chars[j], chars[i]];
                }
                output.val(chars.join(''));
            }

            // Generación en tiempo real al cambiar cualquier opción
            $('#open-password-generator').on('click', function () {
                generate();
                modal.modal('show');
            });
            lengthInput.on('input change', generate);
            $('.pg-charset').on('change', generate);
            $('#pg-regenerate').on('click', generate);
            $('#pg-cancel').on('click', () => modal.modal('hide'));

            useBtn.on('click', function () {
                const password = output.val();
                if (!password) return;
                $('#password').val(password).trigger('input');
                $('#password_confirmation').val(password).trigger('input');
                modal.modal('hide');
            });
        });
    </script>
@endpush
