@extends('admin.layouts.master')

@section('contents')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('admin.user-price.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Lista de Precios</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.user-price.index') }}">Listas de Precios</a></div>
                <div class="breadcrumb-item">Crear</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Crear Lista de Precios</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.user-price.store') }}" method="POST" id="priceForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Usuario <span class="text-danger">*</span></label>
                                            <select name="user_id" id="user_id" class="form-control select2 @error('user_id') is-invalid @enderror" required>
                                                <option value="">Seleccionar usuario...</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                        {{ $user->name }} ({{ $user->email }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('user_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Sucursal <small class="text-muted">(Opcional)</small></label>
                                            <select name="user_branch_id" id="user_branch_id" class="form-control @error('user_branch_id') is-invalid @enderror">
                                                <option value="">Sin sucursal</option>
                                            </select>
                                            <small class="text-muted">Seleccione un usuario para ver sus sucursales</small>
                                            @error('user_branch_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Fecha <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('price_date') is-invalid @enderror"
                                                   name="price_date" value="{{ old('price_date', date('Y-m-d')) }}" required>
                                            @error('price_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <h5>Precios por Terminal</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="items-table">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width: 25%;">Terminal <span class="text-danger">*</span></th>
                                                <th style="width: 12%;">Flete</th>
                                                <th style="width: 12%;">Magna</th>
                                                <th style="width: 12%;">Premium</th>
                                                <th style="width: 12%;">Diesel</th>
                                                <th style="width: 20%;">Terminal Sistema</th>
                                                <th style="width: 7%;">Acc.</th>
                                            </tr>
                                        </thead>
                                        <tbody id="items-container">
                                            <tr class="item-row" data-index="0">
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" name="items[0][terminal_name]"
                                                           placeholder="Nombre de Terminal" required>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.0001" class="form-control form-control-sm" name="items[0][shipping_price]"
                                                           placeholder="0.0000" value="0">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.0001" class="form-control form-control-sm" name="items[0][magna_price]"
                                                           placeholder="0.0000">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.0001" class="form-control form-control-sm" name="items[0][premium_price]"
                                                           placeholder="0.0000">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.0001" class="form-control form-control-sm" name="items[0][diesel_price]"
                                                           placeholder="0.0000">
                                                </td>
                                                <td>
                                                    <select name="items[0][fuel_terminal_id]" class="form-control form-control-sm terminal-select">
                                                        <option value="">Opcional</option>
                                                        @foreach($terminals as $terminal)
                                                            <option value="{{ $terminal->id }}" data-name="{{ $terminal->name }}">{{ $terminal->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm remove-item" disabled>
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="form-group">
                                    <button type="button" class="btn btn-success" id="addItem">
                                        <i class="fas fa-plus"></i> Agregar Terminal
                                    </button>
                                </div>

                                <hr>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Crear Lista de Precios</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        let itemIndex = 1;
        const terminalsOptions = `<option value="">Opcional</option>
        @foreach($terminals as $terminal)
            <option value="{{ $terminal->id }}" data-name="{{ $terminal->name }}">{{ $terminal->name }}</option>
        @endforeach`;

        // Load branches when user changes
        $('#user_id').on('change', function() {
            const userId = $(this).val();
            const branchSelect = $('#user_branch_id');

            branchSelect.html('<option value="">Sin sucursal</option>');

            if (userId) {
                $.ajax({
                    url: '{{ route("admin.user-branch.get-branches") }}',
                    data: { user_id: userId },
                    success: function(branches) {
                        if (branches.length > 0) {
                            branches.forEach(function(branch) {
                                branchSelect.append(`<option value="${branch.id}">${branch.name}</option>`);
                            });
                            branchSelect.closest('.form-group').find('small').text('Seleccione una sucursal o deje vacío');
                        } else {
                            branchSelect.closest('.form-group').find('small').text('Este usuario no tiene sucursales');
                        }
                    }
                });
            } else {
                branchSelect.closest('.form-group').find('small').text('Seleccione un usuario para ver sus sucursales');
            }
        });

        // Auto-fill terminal name when selecting from ComboBox
        $(document).on('change', 'select[name$="[fuel_terminal_id]"]', function() {
            const selectedOption = $(this).find('option:selected');
            const terminalName = selectedOption.data('name');
            const row = $(this).closest('.item-row');
            const terminalNameInput = row.find('input[name$="[terminal_name]"]');
            if (terminalName) {
                terminalNameInput.val(terminalName);
            }
        });

        $('#addItem').click(function() {
            const newRow = `
            <tr class="item-row" data-index="${itemIndex}">
                <td>
                    <input type="text" class="form-control form-control-sm" name="items[${itemIndex}][terminal_name]"
                           placeholder="Nombre de Terminal" required>
                </td>
                <td>
                    <input type="number" step="0.0001" class="form-control form-control-sm" name="items[${itemIndex}][shipping_price]"
                           placeholder="0.0000" value="0">
                </td>
                <td>
                    <input type="number" step="0.0001" class="form-control form-control-sm" name="items[${itemIndex}][magna_price]"
                           placeholder="0.0000">
                </td>
                <td>
                    <input type="number" step="0.0001" class="form-control form-control-sm" name="items[${itemIndex}][premium_price]"
                           placeholder="0.0000">
                </td>
                <td>
                    <input type="number" step="0.0001" class="form-control form-control-sm" name="items[${itemIndex}][diesel_price]"
                           placeholder="0.0000">
                </td>
                <td>
                    <select name="items[${itemIndex}][fuel_terminal_id]" class="form-control form-control-sm terminal-select">
                        ${terminalsOptions}
                    </select>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
            $('#items-container').append(newRow);
            itemIndex++;
            updateRemoveButtons();
        });

        $(document).on('click', '.remove-item', function() {
            $(this).closest('.item-row').remove();
            updateRemoveButtons();
        });

        function updateRemoveButtons() {
            const rows = $('.item-row');
            rows.find('.remove-item').prop('disabled', rows.length <= 1);
        }
    </script>
@endpush
