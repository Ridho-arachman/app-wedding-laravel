<?php

namespace App\Filament\Admin\Resources\Items;

use App\Filament\Admin\Resources\Items\Pages\CreateItem;
use App\Filament\Admin\Resources\Items\Pages\EditItem;
use App\Filament\Admin\Resources\Items\Pages\ListItems;
use App\Filament\Admin\Resources\Items\Schemas\ItemForm;
use App\Filament\Admin\Resources\Items\Tables\ItemsTable;
use App\Models\Item;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ItemResource extends Resource
{
    protected static ?string $navigationLabel = "Barang";
    protected static ?string $pluralLabel = "List Barang";
    protected static ?string $model = Item::class;
    protected static string|\UnitEnum|null $navigationGroup = "Manajemen Data";
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    public static function form(Schema $schema): Schema
    {
        return ItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ItemsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
                //
            ];
    }

    public static function getPages(): array
    {
        return [
            "index" => ListItems::route("/"),
            "create" => CreateItem::route("/create"),
            "edit" => EditItem::route("/{record}/edit"),
        ];
    }
}
