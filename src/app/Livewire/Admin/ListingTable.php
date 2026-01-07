<?php

namespace App\Livewire\Admin;

use App\Models\Listing;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ListingTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Listing::query()->with(['category', 'location', 'user']))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                ImageColumn::make('image')
                    ->label('Imagen')
                    ->disk('public')
                    ->height(60),

                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(30),

                TextColumn::make('category.name')
                    ->label('Categoría')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('location.name')
                    ->label('Ubicación')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('user.name')
                    ->label('Creado por')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable()
                    ->copyable(),

                IconColumn::make('status')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                IconColumn::make('is_verified')
                    ->label('Verificado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),

                TextColumn::make('views')
                    ->label('Vistas')
                    ->sortable()
                    ->toggleable()
                    ->numeric()
                    ->badge()
                    ->color('success'),

                TextColumn::make('created_at')
                    ->label('Fecha creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('expire_date')
                    ->label('Fecha expiración')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        '1' => 'Activo',
                        '0' => 'Inactivo',
                    ]),
                SelectFilter::make('is_featured')
                    ->label('Destacado')
                    ->options([
                        '1' => 'Sí',
                        '0' => 'No',
                    ]),
                SelectFilter::make('is_verified')
                    ->label('Verificado')
                    ->options([
                        '1' => 'Sí',
                        '0' => 'No',
                    ]),
                SelectFilter::make('category_id')
                    ->label('Categoría')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('location_id')
                    ->label('Ubicación')
                    ->relationship('location', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Action::make('edit')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->url(fn (Listing $record): string => route('admin.listing.edit', $record))
                    ->color('primary'),

                Action::make('schedules')
                    ->label('Horarios')
                    ->icon('heroicon-o-clock')
                    ->url(fn (Listing $record): string => route('admin.listing-schedule.index', $record))
                    ->color('info'),

                DeleteAction::make()
                    ->label('Eliminar')
                    ->url(fn (Listing $record): string => route('admin.listing.destroy', $record))
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                // You can add bulk actions here if needed
            ])
            ->defaultSort('id', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public function render()
    {
        return view('livewire.admin.listing-table');
    }
}
