<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NoteResource\Pages;
use App\Models\Note;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Resources\Resource;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;

use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;

use Illuminate\Database\Eloquent\Builder;

class NoteResource extends Resource
{
    protected static ?string $model = Note::class;

    protected static ?string $navigationIcon  = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $navigationLabel = 'Notes';
    protected static ?int    $navigationSort  = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Details')
                ->description('Title, context, and taxonomies used for discovery.')
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('e.g., Discrete Mathematics – Set Theory')
                        ->helperText('Keep it clear and searchable.')
                        ->columnSpanFull(),

                    Textarea::make('description')
                        ->rows(5)
                        ->placeholder('What’s covered? Chapters, tips, exam pointers…')
                        ->helperText('Optional but recommended for better search.'),

                    Grid::make(3)->schema([
                        Select::make('faculty_id')
                            ->relationship('faculty', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Select faculty')
                            ->label('Faculty'),

                        Select::make('semester_id')
                            ->relationship('semester', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Select semester')
                            ->label('Semester'),

                        Select::make('subject_id')
                            ->relationship('subject', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Select subject')
                            ->label('Subject'),
                    ]),

                    Grid::make(2)->schema([
                        TextInput::make('file_path')
                            ->label('File Path')
                            ->disabled()
                            ->dehydrated(false)
                            ->hiddenOn('create')
                            ->helperText('Set by the uploader workflow.'),

                        TextInput::make('cover_path')
                            ->label('Cover Path')
                            ->disabled()
                            ->dehydrated(false)
                            ->hiddenOn('create')
                            ->helperText('Set by the uploader workflow.'),
                    ]),
                ])
                ->columns(1),

            Section::make('Publication')
                ->description('Control moderation status and visibility.')
                ->schema([
                    Select::make('status')
                        ->options([
                            'pending'  => 'Pending',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                        ])
                        ->required()
                        ->native(false)
                        ->helperText('“Approved” + Public = appears in listings.'),

                    Toggle::make('is_public')
                        ->label('Visible to public')
                        ->helperText('When enabled and status is “Approved”, the note is listed publicly.'),

                    DateTimePicker::make('published_at')
                        ->label('Published At')
                        ->seconds(false)
                        ->helperText('Usually set on approval; adjust if needed.'),

                    Textarea::make('reject_reason')
                        ->label('Reject Reason')
                        ->rows(3)
                        ->visible(fn($get) => $get('status') === 'rejected')
                        ->helperText('Shown to the submitter.'),
                ])
                ->columns(2)
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // Prefer most recently published, then created
            ->defaultSort('created_at', 'desc')
            
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(60)
                    ->description(function (Note $record) {
                        $bits = [
                            optional($record->faculty)->name,
                            optional($record->semester)->name,
                            optional($record->subject)->name,
                        ];
                        return collect($bits)->filter()->implode(' · ');
                    })
                    ->tooltip(fn(Note $record) => $record->title),

                TextColumn::make('user.name')
                    ->label('Author')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('views')
                    ->label('Views')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('downloads')
                    ->label('Downloads')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ])
                    ->icons([
                        'heroicon-o-clock'  => 'pending',
                        'heroicon-o-check'  => 'approved',
                        'heroicon-o-x-mark' => 'rejected',
                    ])
                    ->formatStateUsing(fn(string $state) => ucfirst($state)),

                BadgeColumn::make('is_public')
                    ->label('Public')
                    ->colors([
                        'success'   => fn($state) => (bool) $state === true,
                        'secondary' => fn($state) => (bool) $state === false,
                    ])
                    ->formatStateUsing(fn($state) => $state ? 'Yes' : 'No')
                    ->toggleable(),

                TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'  => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->label('Status'),

                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Public')
                    ->boolean(),

                Tables\Filters\SelectFilter::make('faculty_id')
                    ->relationship('faculty', 'name')
                    ->label('Faculty')
                    ->searchable(),

                Tables\Filters\SelectFilter::make('semester_id')
                    ->relationship('semester', 'name')
                    ->label('Semester')
                    ->searchable(),

                Tables\Filters\SelectFilter::make('subject_id')
                    ->relationship('subject', 'name')
                    ->label('Subject')
                    ->searchable(),

                Tables\Filters\Filter::make('recent')
                    ->label('Added in last 7 days')
                    ->query(fn(Builder $q) => $q->where('created_at', '>=', now()->subDays(7))),
            ])
            ->actions([
                ActionGroup::make([
                    // Preview in a clean modal before taking action
                    Tables\Actions\ViewAction::make('preview')
                        ->label('Preview')
                        ->icon('heroicon-o-eye')
                        ->modalHeading(fn(Note $r) => 'Preview: '.$r->title)
                        ->modalWidth('4xl')
                        ->infolist(function (Note $record): array {
                            return [
                                InfoSection::make('Overview')->schema([
                                    InfoGrid::make(2)->schema([
                                        TextEntry::make('title')->label('Title')->weight('bold'),
                                        TextEntry::make('user.name')->label('Author'),
                                        TextEntry::make('faculty.name')->label('Faculty'),
                                        TextEntry::make('semester.name')->label('Semester'),
                                        TextEntry::make('subject.name')->label('Subject'),
                                        TextEntry::make('status')->label('Status')->formatStateUsing(fn($s)=>ucfirst($s)),
                                        TextEntry::make('is_public')->label('Public')->formatStateUsing(fn($v)=>$v?'Yes':'No'),
                                        TextEntry::make('published_at')->label('Published')->dateTime(),
                                    ]),
                                    TextEntry::make('description')
                                        ->label('Description')
                                        ->columnSpanFull()
                                        ->placeholder('—'),
                                ])->columns(2),

                                InfoSection::make('Media & Links')->schema([
                                    ImageEntry::make('cover_url')
                                        ->label('Cover')
                                        ->visible(fn(Note $r) => filled($r->cover_url))
                                        ->height(160)
                                        ->extraImgAttributes(['style'=>'object-fit:cover;border-radius:12px;border:1px solid rgba(0,0,0,.08)']),
                                    TextEntry::make('file_url')
                                        ->label('File')
                                        ->formatStateUsing(fn(Note $r) => $r->file_url ? 'Open file' : '—')
                                        ->url(fn(Note $r) => $r->file_url, shouldOpenInNewTab: true),
                                    TextEntry::make('views')->label('Views'),
                                    TextEntry::make('downloads')->label('Downloads'),
                                ])->columns(3),
                            ];
                        }),

                    Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn(Note $record) => $record->status !== 'approved')
                        ->action(function (Note $record) {
                            $record->update([
                                'status'       => 'approved',
                                'published_at' => now(),
                            ]);
                        }),

                    Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn(Note $record) => $record->status !== 'rejected')
                        ->form([
                            Forms\Components\Textarea::make('reject_reason')
                                ->required()
                                ->label('Reason')
                                ->rows(3),
                        ])
                        ->action(function (Note $record, array $data) {
                            $record->update([
                                'status'        => 'rejected',
                                'reject_reason' => $data['reject_reason'],
                                'published_at'  => null,
                            ]);
                        }),

                    Tables\Actions\EditAction::make(),

                    Tables\Actions\DeleteAction::make(),

                    // Optional: open the public page (only when actually visible)
                    Action::make('viewPublic')
                        ->label('Open public page')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->url(fn(Note $record) => route('public.notes.show', $record))
                        ->openUrlInNewTab()
                        ->visible(fn(Note $record) => $record->status === 'approved' && $record->is_public),
                ])->icon('heroicon-o-ellipsis-vertical'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('bulkApprove')
                    ->label('Approve selected')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(function ($records) {
                        $records->each->update([
                            'status'       => 'approved',
                            'published_at' => now(),
                        ]);
                    }),

                Tables\Actions\BulkAction::make('bulkReject')
                    ->label('Reject selected')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('reject_reason')
                            ->required()
                            ->label('Reason'),
                    ])
                    ->action(function ($records, array $data) {
                        foreach ($records as $r) {
                            $r->update([
                                'status'        => 'rejected',
                                'reject_reason' => $data['reject_reason'],
                                'published_at'  => null,
                            ]);
                        }
                    }),

                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateHeading('No notes yet')
            ->emptyStateDescription('When notes are created or uploaded, they’ll appear here.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Note::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListNotes::route('/'),
            'create' => Pages\CreateNote::route('/create'),
            'edit'   => Pages\EditNote::route('/{record}/edit'),
            // If you ever want a dedicated "view" page, you can add it here and swap the preview action to link to it.
        ];
    }
}
