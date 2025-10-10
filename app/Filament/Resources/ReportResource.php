<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Models\Report;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;
    protected static ?string $navigationIcon  = 'heroicon-o-flag';
    protected static ?string $navigationGroup = 'Moderation';
    protected static ?int    $navigationSort  = 20;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Select::make('note_id')->relationship('note','title')->searchable()->required(),
                Select::make('user_id')->relationship('user','name')->searchable(),
                Textarea::make('reason')->rows(4)->required(),
                Select::make('status')
                    ->options([
                        'pending'   => 'Pending',
                        'resolved'  => 'Resolved',
                        'dismissed' => 'Dismissed',
                    ])->required(),
            ])->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('note.title')->label('Note')->limit(40)->searchable(),
                TextColumn::make('user.name')->label('Reporter')->searchable(),
                TextColumn::make('reason')->limit(60)->toggleable(),
                BadgeColumn::make('status')
                    ->icons([
                        'pending'   => 'heroicon-o-clock',
                        'resolved'  => 'heroicon-o-check',
                        'dismissed' => 'heroicon-o-minus-circle',
                    ])
                    ->colors([
                        'pending'   => 'warning',
                        'resolved'  => 'success',
                        'dismissed' => 'gray',
                    ])
                    ->sortable(),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'pending'   => 'Pending',
                    'resolved'  => 'Resolved',
                    'dismissed' => 'Dismissed',
                ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            'edit'   => Pages\EditReport::route('/{record}/edit'),
        ];
    }
}
