<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DoctorResource\Pages;
use App\Filament\Admin\Resources\DoctorResource\RelationManagers\ContactPhonesRelationManager;
use App\Filament\Support\ImageUpload;
use App\Filament\Support\JalaliFormatter;
use App\Models\Doctor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DoctorResource extends Resource
{
    protected static ?string $model = Doctor::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'پزشکان';

    protected static ?string $modelLabel = 'پزشک';

    protected static ?string $pluralModelLabel = 'پزشکان';

    protected static ?string $navigationGroup = 'مدیریت پزشکان';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('doctor_tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('اطلاعات اصلی')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Forms\Components\Grid::make(['default' => 1, 'md' => 2])
                                    ->schema([
                                        ImageUpload::avatar('photo_path', 'doctors')
                                            ->columnSpan(['default' => 1, 'md' => 2]),
                                        Forms\Components\TextInput::make('name')
                                            ->label('نام')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true),
                                        Forms\Components\TextInput::make('slug')
                                            ->label('نامک')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true),
                                        Forms\Components\Select::make('city_id')
                                            ->label('شهر')
                                            ->relationship('city', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        Forms\Components\Select::make('primary_specialty_id')
                                            ->label('تخصص اصلی')
                                            ->relationship('primarySpecialty', 'name')
                                            ->searchable()
                                            ->preload(),
                                        Forms\Components\Select::make('specialties')
                                            ->label('تخصص‌ها')
                                            ->relationship('specialties', 'name')
                                            ->multiple()
                                            ->preload()
                                            ->searchable()
                                            ->columnSpan(['default' => 1, 'md' => 2]),
                                        Forms\Components\Textarea::make('bio')
                                            ->label('بیوگرافی')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('website')
                                            ->label('وب‌سایت')
                                            ->url()
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('address')
                                            ->label('آدرس')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('تماس')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\TextInput::make('sms_mobile')
                                    ->label('موبایل پیامک')
                                    ->tel()
                                    ->maxLength(20)
                                    ->helperText('برای ارسال اعلان نوبت و پیامک'),
                                Forms\Components\Repeater::make('contactPhones')
                                    ->label('شماره‌های تماس')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\TextInput::make('phone')
                                            ->label('شماره')
                                            ->tel()
                                            ->required()
                                            ->maxLength(20),
                                        Forms\Components\TextInput::make('label')
                                            ->label('برچسب')
                                            ->maxLength(50)
                                            ->placeholder('مثلاً مطب، منشی'),
                                        Forms\Components\TextInput::make('sort_order')
                                            ->label('ترتیب')
                                            ->numeric()
                                            ->default(0),
                                        Forms\Components\Toggle::make('is_visible')
                                            ->label('نمایش عمومی')
                                            ->default(true),
                                    ])
                                    ->columns(['default' => 1, 'md' => 2])
                                    ->defaultItems(0)
                                    ->addActionLabel('افزودن شماره')
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Tabs\Tab::make('وضعیت')
                            ->icon('heroicon-o-adjustments-horizontal')
                            ->schema([
                                Forms\Components\Grid::make(['default' => 1, 'sm' => 2])
                                    ->schema([
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('فعال')
                                            ->default(true),
                                        Forms\Components\Toggle::make('is_published')
                                            ->label('منتشر شده'),
                                        Forms\Components\Toggle::make('is_vip')
                                            ->label('VIP'),
                                        Forms\Components\DateTimePicker::make('published_at')
                                            ->label('تاریخ انتشار')
                                            ->seconds(false),
                                        Forms\Components\DateTimePicker::make('expires_at')
                                            ->label('تاریخ انقضا')
                                            ->seconds(false)
                                            ->helperText('پس از این تاریخ پروفایل غیرفعال می‌شود')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('حساب کاربری')
                            ->icon('heroicon-o-key')
                            ->schema([
                                Forms\Components\Grid::make(['default' => 1, 'md' => 2])
                                    ->schema([
                                        Forms\Components\Select::make('user_id')
                                            ->label('کاربر متصل')
                                            ->relationship('user', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->visible(fn (?Doctor $record) => $record !== null),
                                        Forms\Components\Toggle::make('create_user')
                                            ->label('ایجاد حساب کاربری جدید')
                                            ->default(false)
                                            ->live()
                                            ->visible(fn (?Doctor $record) => $record === null),
                                        Forms\Components\TextInput::make('user_name')
                                            ->label('نام کاربر')
                                            ->maxLength(255)
                                            ->visible(fn (Get $get, ?Doctor $record) => $record === null && $get('create_user')),
                                        Forms\Components\TextInput::make('user_email')
                                            ->label('ایمیل کاربر')
                                            ->email()
                                            ->maxLength(255)
                                            ->visible(fn (Get $get, ?Doctor $record) => $record === null && $get('create_user')),
                                        Forms\Components\TextInput::make('user_phone')
                                            ->label('موبایل کاربر')
                                            ->tel()
                                            ->maxLength(20)
                                            ->visible(fn (Get $get, ?Doctor $record) => $record === null && $get('create_user')),
                                        Forms\Components\TextInput::make('user_password')
                                            ->label('رمز عبور')
                                            ->password()
                                            ->revealable()
                                            ->minLength(8)
                                            ->visible(fn (Get $get, ?Doctor $record) => $record === null && $get('create_user')),
                                    ]),
                            ])
                            ->visible(fn (?Doctor $record) => $record === null || $record->user_id === null),
                        Forms\Components\Tabs\Tab::make('سئو')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->label('عنوان متا')
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('meta_description')
                                    ->label('توضیحات متا')
                                    ->rows(3)
                                    ->maxLength(500),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo_path')
                    ->label('')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(asset('images/doctor-placeholder.svg'))
                    ->size(40),
                Tables\Columns\TextColumn::make('name')
                    ->label('نام')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Doctor $record): ?string => $record->primarySpecialty?->name),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('شهر')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sms_mobile')
                    ->label('موبایل')
                    ->toggleable()
                    ->copyable(),
                Tables\Columns\IconColumn::make('is_vip')
                    ->label('VIP')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('منتشر')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('انقضا')
                    ->formatStateUsing(fn ($state) => JalaliFormatter::dateTime($state))
                    ->sortable()
                    ->color(fn ($state) => $state && $state->isPast() ? 'danger' : null),
            ])
            ->defaultSort('name')
            ->striped()
            ->filters([
                Tables\Filters\TernaryFilter::make('is_vip')
                    ->label('VIP'),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('منتشر شده'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('فعال'),
                Tables\Filters\SelectFilter::make('city_id')
                    ->label('شهر')
                    ->relationship('city', 'name'),
                Tables\Filters\Filter::make('expiring')
                    ->label('در حال انقضا')
                    ->query(fn ($query) => $query
                        ->whereNotNull('expires_at')
                        ->where('expires_at', '<=', now()->addDays(config('visitiranian.expiring_doctors_days', 30)))),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('ویرایش'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('هنوز پزشکی ثبت نشده')
            ->emptyStateDescription('برای شروع، اولین پزشک را ثبت کنید.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('ثبت پزشک جدید'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ContactPhonesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDoctors::route('/'),
            'create' => Pages\CreateDoctor::route('/create'),
            'edit' => Pages\EditDoctor::route('/{record}/edit'),
        ];
    }
}
