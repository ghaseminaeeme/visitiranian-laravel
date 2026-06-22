<?php

namespace App\Filament\Admin\Pages;

use App\Models\SupportTicket;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Str;

class ContactSupportPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-lifebuoy';

    protected static ?string $navigationLabel = 'تماس با پشتیبانی';

    protected static ?string $title = 'تماس با پشتیبانی';

    protected static ?string $navigationGroup = 'پشتیبانی';

    protected static ?int $navigationSort = 80;

    protected static string $view = 'filament.admin.pages.contact-support-page';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'category' => 'question',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('category')
                    ->label('دسته‌بندی')
                    ->options([
                        'question' => 'سؤال',
                        'bug' => 'گزارش خطا',
                        'feature' => 'درخواست قابلیت',
                        'other' => 'سایر',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('subject')
                    ->label('موضوع')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('body')
                    ->label('متن پیام')
                    ->required()
                    ->rows(6),
                Forms\Components\TextInput::make('page_url')
                    ->label('آدرس صفحه (اختیاری)')
                    ->url()
                    ->maxLength(500),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        SupportTicket::query()->create([
            'ticket_number' => 'TKT-'.strtoupper(Str::random(8)),
            'user_id' => auth()->id(),
            'subject' => $data['subject'],
            'body' => $data['body'],
            'category' => $data['category'],
            'status' => 'sent',
            'page_url' => $data['page_url'] ?? null,
        ]);

        $this->form->fill(['category' => 'question']);

        Notification::make()
            ->title('تیکت پشتیبانی ثبت شد')
            ->body('تیم پشتیبانی به زودی پاسخ خواهد داد.')
            ->success()
            ->send();
    }
}
