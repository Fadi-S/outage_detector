<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OutageResource\Pages;
use App\Models\Outage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OutageResource extends Resource
{
    protected static ?string $model = Outage::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where("user_id", "=", auth()->id());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->orderBy("start", "desc"))
            ->columns([
                TextColumn::make("start")
                ->dateTime("d/m/Y h:i a"),

                TextColumn::make("end")
                    ->dateTime("d/m/Y h:i a"),

                TextColumn::make("time"),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Outages'),
            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('end')),
            'finished' => Tab::make('Inactive')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('end')),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOutages::route('/'),
            'edit' => Pages\EditOutage::route('/{record}/edit'),
        ];
    }
}
