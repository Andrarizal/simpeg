<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required(),
                TextInput::make('email')
                    ->label('Alamat Email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn ($state) => filled($state)) // hanya menyimpan jika diisi
                    ->nullable()
                    ->visibleOn('create', 'edit')
                    ->required(fn (string $context): bool => $context == 'create'),
                Select::make('role_id')
                    ->label('Role')
                    ->relationship('role', 'name')
                    ->required()
                    ->native(false),
                Select::make('staff_id')
                    ->label('Nama Pegawai')
                    ->relationship(
                        name: 'staff', 
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query, ?Model $record) {
                            $query->whereDoesntHave('user');

                            if ($record) {
                                $query->orWhere('id', $record->staff_id);
                            }
                            
                            return $query;
                        }
                    )
                    ->searchable()
                    ->preload()
                    ->unique(ignoreRecord: true)
                    ->native(false),
            ]);
    }
}
