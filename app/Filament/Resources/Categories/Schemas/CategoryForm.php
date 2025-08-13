<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Forms\Components\Group ;

class CategoryForm
{
        public static function configure(Schema $schema): Schema
        {
        return $schema
                ->components([
                TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        // ->live()
                        ->live(onBlur:true)

                        // its function called after field updated => if we in create case we set slug to the same state of name
                        ->afterStateUpdated(function (string $operation, $state, $set) {
                                return $operation === 'create'
                                ? $set('slug', Str::slug($state))
                                : null;
                        }),
                TextInput::make('slug')
                        ->required()
                        ->disabled()
                        // send value to database evenif the field is disabled
                        ->dehydrated()
                        // slug is a unique value in category table in database && ignore the changes when the field is still write or in actions
                        ->unique(Category::class, 'slug', ignoreRecord: true), // unique avoid to send repeat data to db

                        FileUpload::make('image')
                        ->image()
                        ->directory('category')
                        ->imageEditor(),

                        Toggle::make('is_active')
                        ->required(),
                ]);
        }
}
