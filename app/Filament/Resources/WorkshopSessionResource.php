<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkshopSessionResource\Pages;
use App\Filament\Resources\WorkshopSessionResource\RelationManagers\ReservationsRelationManager;
use App\Models\WorkshopSession;
use Filament\Forms;
use Filament\Forms\Form;      // v3
use Filament\Tables;
use Filament\Tables\Table;    // v3
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;

class WorkshopSessionResource extends Resource
{
    protected static ?string $model = WorkshopSession::class;

    protected static ?string $navigationIcon   = 'heroicon-o-calendar';
    protected static ?string $navigationGroup  = 'Ateliers';
    protected static ?string $navigationLabel  = 'Créneaux';
    protected static ?string $modelLabel       = 'Créneau';
    protected static ?string $pluralModelLabel = 'Créneaux';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\DatePicker::make('date')
                ->label('Date')
                ->required()
                ->native(false),

            Forms\Components\TimePicker::make('start_time')
                ->label('Début')
                ->required()
                ->seconds(false),

            Forms\Components\TimePicker::make('end_time')
                ->label('Fin')
                ->required()
                ->seconds(false),

            Forms\Components\TextInput::make('capacity')
                ->label('Capacité')
                ->numeric()
                ->minValue(1)
                ->default(5)
                ->required(),

            Forms\Components\TextInput::make('location')
                ->label('Lieu')
                ->default("Mairie d'Anet")
                ->required(),

            Forms\Components\TextInput::make('topic')
                ->label('Sujet')
                ->default('Inclusion numérique – logiciels libres')
                ->required(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Début')
                    ->time('H:i'),

                Tables\Columns\TextColumn::make('end_time')
                    ->label('Fin')
                    ->time('H:i'),

                Tables\Columns\TextColumn::make('capacity')
                    ->label('Capacité'),

                // Compteurs par statut (basés sur les relations du modèle)
                Tables\Columns\TextColumn::make('confirmed_reservations_count')
                    ->counts('confirmedReservations')
                    ->label('Confirmés')
                    ->sortable(),

                Tables\Columns\TextColumn::make('pending_reservations_count')
                    ->counts('pendingReservations')
                    ->label('En attente')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('cancelled_reservations_count')
                    ->counts('cancelledReservations')
                    ->label('Annulés')
                    ->toggleable(isToggledHiddenByDefault: true),

                // Places restantes calculées à l’affichage
                Tables\Columns\BadgeColumn::make('places_restantes')
                    ->label('Restantes')
                    ->state(fn (WorkshopSession $record) => $record->spotsLeft())
                    ->colors([
                        'danger'  => fn ($state) => (int)$state === 0,
                        'warning' => fn ($state, $record) => (int)$state > 0 && (int)$state <= max(1, (int)floor(($record->capacity ?? 5) / 3)),
                        'success' => fn ($state) => (int)$state > 0,
                    ]),
            ])
            ->filters([
                Tables\Filters\Filter::make('upcoming')
                    ->label('À venir')
                    ->query(fn (Builder $q) => $q->whereDate('date', '>=', now()->toDateString())),

                Tables\Filters\Filter::make('full')
                    ->label('Complet')
                    ->query(fn (Builder $q) =>
                        // nécessite withCount('confirmedReservations') (cf. getEloquentQuery)
                        $q->whereColumn('capacity', '<=', 'confirmed_reservations_count')
                    ),
            ])
            ->defaultSort('date', 'asc')
            ->paginated([25, 50, 100])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    /** Onglet "Inscrits" dans la page d’édition */
    public static function getRelations(): array
    {
        return [
            ReservationsRelationManager::class,
        ];
    }

    /** Précharge les compteurs pour les filtres/colonnes */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount([
            'confirmedReservations',
            'pendingReservations',
            'cancelledReservations',
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListWorkshopSessions::route('/'),
            'create' => Pages\CreateWorkshopSession::route('/create'),
            'edit'   => Pages\EditWorkshopSession::route('/{record}/edit'),
        ];
    }
}
