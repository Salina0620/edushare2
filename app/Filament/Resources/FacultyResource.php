<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FacultyResource\Pages;
use App\Models\Faculty;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;

class FacultyResource extends Resource
{
    protected static ?string $model = Faculty::class;
    protected static ?string $navigationIcon  = 'heroicon-o-building-library';
    protected static ?string $navigationGroup = 'Taxonomy';
    protected static ?int    $navigationSort  = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Faculty details')
                ->description('The public name and a short blurb shown to students.')
                ->icon('heroicon-m-building-library')
                ->schema([
                    TextInput::make('name')
                        ->label('Name')
                        ->placeholder('e.g., Faculty of Engineering')
                        ->required()
                        ->maxLength(120)
                        ->unique(ignoreRecord: true)
                        ->autofocus(),

                    Textarea::make('description')
                        ->label('Description')
                        ->placeholder('Short sentence about this faculty…')
                        ->rows(3)
                        ->maxLength(500),
                ])
                ->columns(1),

            Section::make('Meta')
                ->description('Read-only information about this record.')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Placeholder::make('created_at')
                        ->label('Created')
                        ->content(fn (?Faculty $record) => $record?->created_at?->diffForHumans() ?? '—'),
                    Placeholder::make('updated_at')
                        ->label('Last updated')
                        ->content(fn (?Faculty $record) => $record?->updated_at?->diffForHumans() ?? '—'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->deferLoading()
            ->defaultSort('name')
            ->searchPlaceholder('Search faculties…')
            ->columns([
                TextColumn::make('name')
                    ->label('Faculty')
                    ->icon('heroicon-m-building-library')
                    ->weight('semibold')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(80)
                    ->toggleable()
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From'),
                        Forms\Components\DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Faculty')
                    ->icon('heroicon-m-plus'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->emptyStateIcon('heroicon-o-academic-cap')
            ->emptyStateHeading('No faculties yet')
            ->emptyStateDescription('Get started by creating your first faculty.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Create faculty'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFaculties::route('/'),
            'create' => Pages\CreateFaculty::route('/create'),
            'edit'   => Pages\EditFaculty::route('/{record}/edit'),
        ];
    }
}
