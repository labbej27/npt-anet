<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationGroup = 'Contenus';
    protected static ?string $navigationIcon  = 'heroicon-o-newspaper';
    protected static ?string $navigationLabel = 'Articles';
    protected static ?int    $navigationSort  = 10;

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Contenu')
                ->schema([
                    TextInput::make('title')->label('Titre')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                    TextInput::make('slug')->required()->unique(ignoreRecord: true),
                    TextInput::make('excerpt')->label('Chapo'),
                    RichEditor::make('content')->label('Contenu')->columnSpanFull(),
                ])->columns(2),

            Section::make('Médias')
                ->schema([
                    SpatieMediaLibraryFileUpload::make('cover')
                        ->collection('cover')
                        ->label('Image de couverture')
                        ->image()
                        ->imageEditor() // crop/rotate/flip/resize
                        ->imageEditorAspectRatios(['16:9','4:3','1:1'])
                        ->imageResizeMode('cover')
                        ->imageResizeTargetWidth(1600)
                        ->imageResizeTargetHeight(900)
                        ->downloadable()
                        ->openable(),

                    SpatieMediaLibraryFileUpload::make('images')
                        ->collection('images')
                        ->label('Galerie')
                        ->multiple()
                        ->reorderable()
                        ->image()
                        ->imageEditor()
                        ->panelLayout('grid')
                        ->downloadable()
                        ->openable(),
                ])->collapsible(),

            Section::make('Publication')
                ->schema([
                    Toggle::make('is_published')->label('Publié'),
                    DateTimePicker::make('published_at')->label('Date de publication'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            SpatieMediaLibraryImageColumn::make('cover')
                ->collection('cover')
                ->conversion('thumb_webp')
                ->label('Cover'),
            TextColumn::make('title')->label('Titre')->searchable()->sortable(),
            TextColumn::make('is_published')->label('Publié')->badge()
                ->state(fn (Article $r) => $r->is_published ? 'Oui' : 'Non')
                ->color(fn (Article $r) => $r->is_published ? 'success' : 'gray'),
            TextColumn::make('published_at')->dateTime('d/m/Y H:i')->label('Publi.')->sortable(),
            TextColumn::make('updated_at')->since()->label('MAJ'),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit'   => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
