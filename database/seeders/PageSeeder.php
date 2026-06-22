<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => 'راهنمای رزرو نوبت',
                'slug' => 'rahnama-rzerv',
                'body' => '<h2>راهنمای رزرو نوبت آنلاین</h2><p>برای رزرو نوبت، ابتدا پزشک مورد نظر خود را جستجو کنید، سپس از صفحه پروفایل پزشک زمان مناسب را انتخاب و اطلاعات خود را وارد نمایید.</p><p>پس از ثبت نوبت، کد پیگیری برای شما ارسال می‌شود.</p>',
                'meta_title' => 'راهنمای رزرو نوبت | ویزیت ایرانیان',
                'meta_description' => 'آموزش گام‌به‌گام رزرو نوبت آنلاین پزشکان در ویزیت ایرانیان',
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'راهنمای پزشکان',
                'slug' => 'rahnama-pezeshk',
                'body' => '<h2>راهنمای ثبت‌نام و مدیریت پروفایل پزشک</h2><p>پزشکان محترم می‌توانند با تماس با پشتیبانی یا از طریق پنل مدیریت، پروفایل خود را ایجاد و اطلاعات تماس، تخصص‌ها و ساعات کاری را به‌روزرسانی کنند.</p>',
                'meta_title' => 'راهنمای پزشکان | ویزیت ایرانیان',
                'meta_description' => 'راهنمای ثبت‌نام، تکمیل پروفایل و مدیریت نوبت‌دهی برای پزشکان',
                'is_published' => true,
                'published_at' => now(),
            ],
        ];

        foreach ($pages as $page) {
            Page::query()->updateOrCreate(
                ['slug' => $page['slug']],
                $page,
            );
        }
    }
}
