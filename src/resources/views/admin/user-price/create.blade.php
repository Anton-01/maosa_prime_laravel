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
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Usuario <span class="text-danger">*</span></label>
                                            <select name="user_id" class="form-control select2 @error('user_id') is-invalid @enderror" required>
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
                                    <div class="col-md-6">
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
                                <div id="items-container">
                                    <div class="item-row row mb-3" data-index="0">
                                        <div class="col-md-3">
                                            <input type="text" class="form-control" name="items[0][terminal_name]"
                                                   placeholder="Nombre de Terminal" required>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" step="0.0001" class="form-control" name="items[0][magna_price]"
                                                   placeholder="Magna">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" step="0.0001" class="form-control" name="items[0][premium_price]"
                                                   placeholder="Premium">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" step="0.0001" class="form-control" name="items[0][diesel_price]"
                                                   placeholder="Diesel">
                                        </div>
                                        <div class="col-md-2">
                                            <select name="items[0][fuel_terminal_id]" class="form-control terminal-select">
                                                <option value="">Terminal (opcional)</option>
                                                @foreach($terminals as $terminal)
                                                    <option value="{{ $terminal->id }}" data-name="{{ $terminal->name }}">{{ $terminal->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-danger btn-sm remove-item" disabled>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
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
        const terminalsOptions = `<option value="">Terminal (opcional)</option>
        @foreach($terminals as $terminal)
        <option value="{{ $terminal->id }}" data-name="{{ $terminal->name }}">{{ $terminal->name }}</option>
        @endforeach`;

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
            <div class="item-row row mb-3" data-index="${itemIndex}">
                <div class="col-md-3">
                    <input type="text" class="form-control terminal-name-input" name="items[${itemIndex}][terminal_name]"
                           placeholder="Nombre de Terminal" required>
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.0001" class="form-control" name="items[${itemIndex}][magna_price]"
                           placeholder="Magna">
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.0001" class="form-control" name="items[${itemIndex}][premium_price]"
                           placeholder="Premium">
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.0001" class="form-control" name="items[${itemIndex}][diesel_price]"
                           placeholder="Diesel">
                </div>
                <div class="col-md-2">
                    <select name="items[${itemIndex}][fuel_terminal_id]" class="form-control terminal-select">
                        ${terminalsOptions}
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
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
