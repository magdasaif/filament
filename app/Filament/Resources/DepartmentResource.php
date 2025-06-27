<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepartmentResource\Pages;
use App\Filament\Resources\DepartmentResource\RelationManagers;
use App\Filament\Resources\DepartmentResource\RelationManagers\EmployeesRelationManager;
use App\Models\Department;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static ?string $navigationIcon    = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel   = 'Department';
    
    protected static ?string $modelLabel        = 'Employee Department';
    
    //to put it into group
    protected static ?string $navigationGroup   = 'System Managment';
   
    //to sort menu items
    protected static ?int $navigationSort       = 4;
    //=============================================================================
    protected static ?string $recordTitleAttribute ='name';
    //=============================================================================
    public static function getNavigationBadge(): ?string
    {
        // This method is used to display a badge on the navigation item.
        return Static::getModel()::count();
    }
    //=============================================================================
    public static function getNavigationBadgeColor(): ?string
    {
        // This method is used to specify the color of the badge.
        return 'primary';
    }
    //=============================================================================
   
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }
    //=============================================================================
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('employees_count')->counts('employees'),
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
                Tables\Actions\DeleteAction::make()
                ->successNotification(
                   Notification::make()
                        ->title(__('Department deleted successfully.'))
                        ->success()
                        ->body(__('The department has been deleted successfully.'))
                ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    //=============================================================================
    //for view popup modal
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
               TextEntry::make('name'),
               TextEntry::make('employee counts')
                ->state(function (Department $record): int {
                    return $record->employees()->count();
                })
            ])
            ->columns(2);
    }
    //=============================================================================
    public static function getRelations(): array
    {
        return [
            EmployeesRelationManager::class
        ];
    }
    //=============================================================================
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
           // 'view' => Pages\ViewDepartment::route('/{record}'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }
    //=============================================================================
}
