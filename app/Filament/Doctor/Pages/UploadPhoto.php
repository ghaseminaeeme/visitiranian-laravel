<?php

namespace App\Filament\Doctor\Pages;

use App\Models\DoctorPhoto;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class UploadPhoto extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-camera';

    protected static ?string $navigationLabel = 'آپلود عکس';

    protected static ?string $title = 'آپلود عکس پروفایل';

    protected static ?int $navigationSort = 5;

    protected static string $view = 'filament.doctor.pages.upload-photo';

    public ?array $data = [];

    public ?string $croppedImage = null;

    public function mount(): void
    {
        abort_unless(auth()->user()?->doctor !== null, 403);

        Gate::authorize('create', DoctorPhoto::class);

        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('راهنما')
                    ->schema([
                        Forms\Components\Placeholder::make('cropper_note')
                            ->label('برش تصویر')
                            ->content('پس از انتخاب عکس، با ابزار Cropper.js ناحیه دلخواه را برش دهید. تصویر برش‌خورده برای بررسی ادمین ارسال می‌شود و پس از تأیید در پروفایل عمومی نمایش داده خواهد شد.'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        Gate::authorize('create', DoctorPhoto::class);

        $doctor = auth()->user()->doctor;

        if ($this->croppedImage === null || $this->croppedImage === '') {
            Notification::make()
                ->title('لطفاً عکس را انتخاب و برش دهید')
                ->danger()
                ->send();

            return;
        }

        if (! preg_match('/^data:image\/(\w+);base64,/', $this->croppedImage, $matches)) {
            Notification::make()
                ->title('فرمت تصویر نامعتبر است')
                ->danger()
                ->send();

            return;
        }

        $extension = strtolower($matches[1]) === 'jpeg' ? 'jpg' : strtolower($matches[1]);
        $imageData = base64_decode(substr($this->croppedImage, strpos($this->croppedImage, ',') + 1));

        if ($imageData === false) {
            Notification::make()
                ->title('خطا در پردازش تصویر')
                ->danger()
                ->send();

            return;
        }

        $filename = Str::uuid()->toString().'.'.$extension;
        $directory = 'doctor-photos/'.$doctor->id;
        $filePath = $directory.'/'.$filename;
        $thumbPath = $directory.'/thumb_'.$filename;

        Storage::disk('public')->put($filePath, $imageData);

        $image = Image::read(Storage::disk('public')->path($filePath));
        $width = $image->width();
        $height = $image->height();

        $thumb = Image::read(Storage::disk('public')->path($filePath));
        $thumb->cover(200, 200);
        Storage::disk('public')->put($thumbPath, (string) $thumb->toJpeg(quality: 85));

        DoctorPhoto::query()->create([
            'doctor_id' => $doctor->id,
            'file_path' => $filePath,
            'thumb_path' => $thumbPath,
            'width' => $width,
            'height' => $height,
            'status' => 'pending',
        ]);

        $this->croppedImage = null;
        $this->dispatch('photo-uploaded');

        Notification::make()
            ->title('عکس ارسال شد')
            ->body('عکس شما در صف بررسی قرار گرفت.')
            ->success()
            ->send();
    }
}
