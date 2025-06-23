<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\City;
use App\Models\Department;
use App\Models\Employee;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon    = 'heroicon-o-user-group';
    protected static ?string $navigationGroup   = 'Employee Managment';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make('Employee Name')
                ->description('put the employee name details here ')
                ->schema([
                    Forms\Components\TextInput::make('first_name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('middle_name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('last_name')
                        ->required()
                        ->maxLength(255),
                ])->columns(3),
                Forms\Components\Section::make('Employee location')
                ->schema([

                    Forms\Components\Select::make('country_id')
                        ->relationship('country','name')
                        ->searchable()
                        // ->multiple()
                        ->preload()
                        ->id('country_id')
                        ->live() //which tells the form to reload the schema each time it gets changed.
                        ->afterStateUpdated(function (Set $set) {
                            $set('state_id',null);
                            $set('city_id',null);
                        } 
                        )
                        ->required(),

                    Forms\Components\Select::make('state_id')
                        // ->relationship('country','name')
                        ->options(//to display only states belongs to selected country
                            fn (Get $get): Collection => State::query()
                            ->where('country_id',$get('country_id'))
                            ->pluck('name','id')
                        )
                        ->searchable()
                        // ->multiple()
                        ->preload()
                        ->id('state_id')
                        ->live()
                        ->afterStateUpdated(fn (Set $set) => $set('city_id',null))
                        ->required(),

                    Forms\Components\Select::make('city_id')
                        // ->relationship('city','name')
                        ->options(//to display only cities belongs to selected state
                            fn (Get $get): Collection => City::query()
                            ->where('state_id',$get('state_id'))
                            ->pluck('name','id')
                        )
                        ->searchable()
                        // ->multiple()
                        ->preload()
                        ->id('city_id')
                        ->required(),

                    Forms\Components\Select::make('department_id')
                        ->relationship('department','name')
                        ->searchable()
                        // ->multiple()
                        ->preload()
                        ->id('department_id')
                        ->required(),

                ])->columns(2),
                Forms\Components\Section::make('Employee Address')
                ->schema([
                    Forms\Components\TextInput::make('address')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('zip_code')
                        ->required()
                        ->maxLength(255),
                ])->columns(2),
                 Forms\Components\Section::make('Employee related dates')
                ->schema([
                    Forms\Components\DatePicker::make('date_of_birth')
                        ->required()
                        ->native(false)
                        ->format('Y-m-d'),
                    Forms\Components\DatePicker::make('date_of_hire')
                        ->required()
                        ->native(false)
                        ->format('Y-m-d'),//2025-06-20
                       // ->columnSpanFull(), //to display input in full width 
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('country.name')
                    ->numeric()
                    ->sortable()
                    ->hidden(),
                Tables\Columns\TextColumn::make('state.name')
                    ->numeric()
                    ->hidden()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->numeric()
                    ->hidden()
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->numeric()
                    ->hidden()
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_name')->label('employee first name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('middle_name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('zip_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->date()
                    ->hidden()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_of_hire')
                    ->date()
                    ->sortable(),
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
                //
            ])
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
