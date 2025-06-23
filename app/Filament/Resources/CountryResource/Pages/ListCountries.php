<?php

namespace App\Filament\Resources\CountryResource\Pages;

use App\Filament\Resources\CountryResource;
use App\Models\Country;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListCountries extends ListRecords
{
    protected static string $resource = CountryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All'           => Tab::make()->badge(Country::count()) ->icon('heroicon-o-flag'),
            'This Week'     => Tab::make()->query(fn ($query) => $query->where('created_at', '>=', now()->subWeek()))->badge(Country::query()->where('created_at', '>=', now()->subWeek())->count()),
            'This Month'    => Tab::make()->query(fn ($query) => $query->where('created_at', '>=', now()->subMonth()))->badge(Country::query()->where('created_at', '>=', now()->subMonth())->count()),
            'This Year'     => Tab::make()->query(fn ($query) => $query->where('created_at', '>=', now()->subYear()))->badge(Country::query()->where('created_at', '>=', now()->subYear())->count()),
        ];
    }
}
