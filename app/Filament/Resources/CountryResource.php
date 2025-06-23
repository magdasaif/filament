<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CountryResource\Pages;
use App\Filament\Resources\CountryResource\RelationManagers;
use App\Filament\Resources\CountryResource\RelationManagers\EmployeesRelationManager;
use App\Filament\Resources\CountryResource\RelationManagers\StatesRelationManager;
use App\Models\Country;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Actions\Action;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;

class CountryResource extends Resource
{
    protected static ?string $model             = Country::class;

    protected static ?string $navigationIcon    = 'heroicon-o-flag';
    
    protected static ?string $navigationLabel   = 'Country';
    
    protected static ?string $modelLabel        = 'Employee Country';
    
    //to put it into group
    protected static ?string $navigationGroup   = 'System Managment';

    //to customize url //make sure this route found in system
    protected static ?string $slug              = 'countries';
   
    //to sort menu items
    protected static ?int $navigationSort       = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phonecode')
                    ->required()
                    ->maxLength(5)
                    ->numeric(),
                TextInput::make('code')
                    ->required()
                    ->maxLength(3)
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->sortable()
                    ->numeric()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phonecode')
                    ->sortable()
                    ->numeric()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            
            ])
            ->filters([
                //add select filter for country states
                SelectFilter::make('state')
                ->relationship('states','name')
                ->searchable()
                ->preload()
                ->label('choose state')
                ->indicator('state'),//this is the label of filterthat will appear above table after apply any filter

                //add custom filter for created dates=
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    //to add indicator for this custom filte use indicateUsing
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators[] = Indicator::make('Created from ' . Carbon::parse($data['created_from'])->toFormattedDateString())
                                ->removeField('created_from');
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators[] = Indicator::make('Created until ' . Carbon::parse($data['created_until'])->toFormattedDateString())
                                ->removeField('created_until');
                        }

                        return $indicators;
                    })
                    ->columnSpan(2)->columns(2)
                    /*
                    case AboveContent;			    //هيظهر الفلتر فوق الجدول ع طول
                    case AboveContentCollapsible;	//هيظهر الفلتر فوق الجدول بس هيكون مقفول ... افتحه واقفله ب ايقون الفلتر
                    case BelowContent;			    //هيظهر الفلتر تحت الجدول
                    case Dropdown;			        // دى القيمه الافتراضيه ... هيزهر ايقون للفلتر جنب البحث
                    case Modal;				        // هنا هيظهره فى بوب اب موديول
                    case Hidden;			        // هيخفى الفلتر
                    */
                ],layout:FiltersLayout::Modal  //show popup modal
                ) ->filtersFormColumns(3)

            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('name')->label('country name'),
                TextEntry::make('phonecode'),
                TextEntry::make('code'),
                
            ])
            ->columns(2);
    }

    public static function getRelations(): array
    {
        return [
            StatesRelationManager::class,
            EmployeesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCountries::route('/'),
            'create' => Pages\CreateCountry::route('/create'),
            'edit' => Pages\EditCountry::route('/{record}/edit'),
        ];
    }
}
