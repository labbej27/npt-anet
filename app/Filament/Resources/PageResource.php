<?php
// app/Filament/Resources/PageResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationGroup = 'Contenus';
    protected static ?string $navigationIcon  = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Pages';
    protected static ?int    $navigationSort  = 20;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->label('Titre')
                ->required()
                ->default(fn () => request('title')), // permet de préremplir via ?title=

            Forms\Components\TextInput::make('slug')
                ->label('Slug')
                ->helperText('Ex. association-intro, contact, mentions-legales')
                ->required()
                ->unique(ignoreRecord: true)
                ->default(fn () => request('slug')),  // permet de préremplir via ?slug=
            
            Forms\Components\RichEditor::make('content')
                ->label('Contenu')
                ->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('title')
                ->label('Titre')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('slug')
                ->badge()
                ->copyable()
                ->label('Slug'),

            Tables\Columns\TextColumn::make('updated_at')
                ->since()
                ->label('MAJ')
                ->sortable(),
        ])->actions([
            Tables\Actions\EditAction::make()->label('Modifier'),
            Tables\Actions\DeleteAction::make()->label('Supprimer'),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit'   => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
