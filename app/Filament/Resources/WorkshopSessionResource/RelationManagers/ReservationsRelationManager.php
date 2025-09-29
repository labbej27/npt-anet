<?php

namespace App\Filament\Resources\WorkshopSessionResource\RelationManagers;

use App\Mail\ReservationConfirmed;
use App\Models\Reservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ReservationsRelationManager extends RelationManager
{
    protected static string $relationship = 'reservations';
    protected static ?string $title = 'Inscrits';
    protected static ?string $recordTitleAttribute = 'full_name';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('full_name')->label('Nom')
                ->required()->maxLength(255),
            Forms\Components\TextInput::make('email')->email()
                ->required()->maxLength(255),
            Forms\Components\TextInput::make('phone')->tel()->maxLength(50),

            Forms\Components\Select::make('status')->label('Statut')
                ->options([
                    Reservation::STATUS_PENDING   => 'En attente',
                    Reservation::STATUS_CONFIRMED => 'Confirmée',
                    Reservation::STATUS_CANCELLED => 'Annulée',
                ])->default(Reservation::STATUS_CONFIRMED)->required(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')->label('Nom')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('phone')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\BadgeColumn::make('status')->label('Statut')
                    ->colors([
                        'warning' => Reservation::STATUS_PENDING,
                        'success' => Reservation::STATUS_CONFIRMED,
                        'danger'  => Reservation::STATUS_CANCELLED,
                    ])
                    ->formatStateUsing(fn (string $state) => [
                        Reservation::STATUS_PENDING   => 'En attente',
                        Reservation::STATUS_CONFIRMED => 'Confirmée',
                        Reservation::STATUS_CANCELLED => 'Annulée',
                    ][$state] ?? $state),
                Tables\Columns\TextColumn::make('created_at')->label('Créée')
                    ->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Statut')->options([
                    Reservation::STATUS_PENDING   => 'En attente',
                    Reservation::STATUS_CONFIRMED => 'Confirmée',
                    Reservation::STATUS_CANCELLED => 'Annulée',
                ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['cancel_token']  = (string) Str::uuid();
                        $data['confirm_token'] = $data['status'] === Reservation::STATUS_CONFIRMED ? null : (string) Str::uuid();
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('confirm')
                    ->label('Confirmer')
                    ->icon('heroicon-o-check')
                    ->visible(fn (Reservation $record) => $record->status === Reservation::STATUS_PENDING)
                    ->requiresConfirmation()
                    ->action(function (Reservation $record) {
                        $record->update(['status' => Reservation::STATUS_CONFIRMED, 'confirm_token' => null]);
                        // (Optionnel) envoyer l’e-mail + ICS :
                        // \Mail::to($record->email)->send(new ReservationConfirmed($record));
                    }),

                Tables\Actions\Action::make('cancel')
                    ->label('Annuler')
                    ->icon('heroicon-o-x-mark')->color('danger')
                    ->visible(fn (Reservation $record) => $record->status !== Reservation::STATUS_CANCELLED)
                    ->requiresConfirmation()
                    ->action(fn (Reservation $record) => $record->update(['status' => Reservation::STATUS_CANCELLED])),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('confirmSelected')
                        ->label('Confirmer sélection')->icon('heroicon-o-check')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $r) {
                                if ($r->status === Reservation::STATUS_PENDING) {
                                    $r->update(['status' => Reservation::STATUS_CONFIRMED, 'confirm_token' => null]);
                                }
                            }
                        }),
                ]),
            ]);
    }
}
