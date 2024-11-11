<?php

namespace Consignr\FilamentPrintNode\Widgets;

use Filament\Tables\Table;
use Illuminate\Support\Number;
use Filament\Tables\Actions\Action;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Consignr\FilamentPrintNode\Models\Client;
use Filament\Widgets\TableWidget as BaseWidget;
use Consignr\FilamentPrintNode\Enums\OperatingSystem;

class PrintNodeClientDownloads extends BaseWidget
{
    protected int | string | array $columnSpan = 2;

    public function table(Table $table): Table
    {
        return $table
            ->heading(null)
            ->query(
                Client::query()
            )
            ->groups([
                Group::make('version')
                    // ->titlePrefixedWithLabel(false)
                    ->label('Version')
                    ->getDescriptionFromRecordUsing(fn (Client $record): string => "Released: {$record->releaseTimestamp->format('d M Y')}")
                    ->orderQueryUsing(fn (Builder $query, string $direction) => $query->orderBy('releaseTimestamp', 'DESC')),
            ])
            ->defaultGroup('version')
            ->columns([
                TextColumn::make('os')
                    ->label('Operating System')
                    ->icon(fn (OperatingSystem $state): ?string => $state->getIcon())
                    ->tooltip(fn (OperatingSystem $state): ?string => $state->getTooltip()),
                TextColumn::make('filename')
                    ->wrap(),
                TextColumn::make('filesize')
                    ->formatStateUsing(fn ($state): string => Number::fileSize($state, precision: 1)),
            ])
            ->actions([
                Action::make('download')
                    ->icon('heroicon-o-arrow-down-on-square')
                    ->action(function (Client $record) {
                        return response()->streamDownload(function () use ($record) {
                            echo file_get_contents($record->url);
                        }, $record->filename);
                    })
            ]);
    }
}
