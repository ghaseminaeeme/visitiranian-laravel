<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\City;
use App\Models\Doctor;
use App\Models\DoctorContactPhone;
use App\Models\DoctorSchedule;
use App\Models\Review;
use App\Models\Specialty;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DoctorSeeder extends Seeder
{
    private const ASSETS_DIR = 'database/seeders/assets/doctors';

    /** @var list<array<string, mixed>> */
    private array $doctors = [
        [
            'name' => 'دکتر علی رضایی',
            'slug' => 'dr-ali-rezaei',
            'city' => 'tehran',
            'specialty' => 'cardiologist',
            'is_vip' => true,
            'photo' => 'https://randomuser.me/api/portraits/men/32.jpg',
            'bio' => "فوق تخصص قلب و عروق با بیش از ۱۸ سال سابقه.\nتخصص در اکوکاردیوگرافی، آنژیوگرافی و درمان بیماری‌های مزمن قلبی.\nعضو انجمن قلب ایران.",
            'address' => 'تهران، سعادت‌آباد، بلوار دریا، پلاک ۱۲۴، طبقه ۳',
            'phone' => '02188765432',
        ],
        [
            'name' => 'دکتر مریم کریمی',
            'slug' => 'dr-maryam-karimi',
            'city' => 'tehran',
            'specialty' => 'dermatologist',
            'is_vip' => true,
            'photo' => 'https://randomuser.me/api/portraits/women/44.jpg',
            'bio' => "متخصص پوست، مو و زیبایی.\nدرمان آکنه، لک، پیری پوست و لیزر درمانی.\nدارای بورد تخصصی از دانشگاه علوم پزشکی تهران.",
            'address' => 'تهران، ونک، خیابان ملاصدرا، برج پزشکان، واحد ۸۰۵',
            'phone' => '02122334455',
        ],
        [
            'name' => 'دکتر حسین محمدی',
            'slug' => 'dr-hossein-mohammadi',
            'city' => 'mashhad',
            'specialty' => 'pediatrician',
            'is_vip' => false,
            'photo' => 'https://randomuser.me/api/portraits/men/52.jpg',
            'bio' => "متخصص اطفال و نوزادان.\nویزیت و واکسیناسیون، رشد و تکامل کودکان.\nپذیرش شبانه‌روزی در بخش اورژانس اطفال.",
            'address' => 'مشهد، احمدآباد، خیابان امام رضا، ساختمان سلامت',
            'phone' => '05138456789',
        ],
        [
            'name' => 'دکتر فاطمه احمدی',
            'slug' => 'dr-fatemeh-ahmadi',
            'city' => 'isfahan',
            'specialty' => 'gynecologist',
            'is_vip' => true,
            'photo' => 'https://randomuser.me/api/portraits/women/68.jpg',
            'bio' => "متخصص زنان، زایمان و نازایی.\nجراحی‌های لاپاراسکوپی و مراقبت‌های بارداری.\nبیش از ۱۵۰۰ زایمان موفق.",
            'address' => 'اصفهان، خیابان چهارباغ، مجتمع درمانی نور',
            'phone' => '03136221100',
        ],
        [
            'name' => 'دکتر رضا نوری',
            'slug' => 'dr-reza-nouri',
            'city' => 'shiraz',
            'specialty' => 'internist',
            'is_vip' => false,
            'photo' => 'https://randomuser.me/api/portraits/men/75.jpg',
            'bio' => "متخصص داخلی و فوق تخصص گوارش.\nاندوسکopy، کولونوسکopy و درمان بیماری‌های التهابی روده.",
            'address' => 'شیراز، بلوار زند، ساختمان پزشکان پارس',
            'phone' => '07132345678',
        ],
        [
            'name' => 'دکتر سارا موسوی',
            'slug' => 'dr-sara-mousavi',
            'city' => 'tabriz',
            'specialty' => 'ophthalmologist',
            'is_vip' => false,
            'photo' => 'https://randomuser.me/api/portraits/women/26.jpg',
            'bio' => "متخصص چشم‌پزشکی و جراحی لیزیک.\nدرمان آب مروارید، گلوکوم و اصلاح بینایی.",
            'address' => 'تبریز، خیابان امام، کلینیک بینایی سپهر',
            'phone' => '04133445566',
        ],
        [
            'name' => 'دکتر امیر حسینی',
            'slug' => 'dr-amir-hosseini',
            'city' => 'karaj',
            'specialty' => 'orthopedist',
            'is_vip' => true,
            'photo' => 'https://randomuser.me/api/portraits/men/46.jpg',
            'bio' => "متخصص ارتوپدی و جراحی مفاصل.\nتعویض مفصل زانو و لگن، درمان شکستگی و آسیب‌های ورزشی.",
            'address' => 'کرج، گوهردشت، بلوار موذن، ساختمان سلامت استخوان',
            'phone' => '02634567890',
        ],
        [
            'name' => 'دکتر نرگس صادقی',
            'slug' => 'dr-narges-sadeghi',
            'city' => 'rasht',
            'specialty' => 'psychiatrist',
            'is_vip' => false,
            'photo' => 'https://randomuser.me/api/portraits/women/65.jpg',
            'bio' => "متخصص روانپزشک و روان‌درمانگر.\nدرمان افسردگی، اضطراب، OCD و اختلالات خواب.\nجلسات فردی و خانواده‌درمانی.",
            'address' => 'رشت، گلسار، خیابان ۱۵ خرداد، ساختمان آرامش',
            'phone' => '01333556677',
        ],
        [
            'name' => 'دکتر مهدی جعفری',
            'slug' => 'dr-mehdi-jafari',
            'city' => 'ahvaz',
            'specialty' => 'urologist',
            'is_vip' => false,
            'photo' => 'https://randomuser.me/api/portraits/men/22.jpg',
            'bio' => "متخصص اورولوژی و جراحی کلیه.\nدرمان سنگ کلیه، پروستات و ناتوانی‌های جنسی مردان.",
            'address' => 'اهواز، کیانپارس، بلوار ساحلی، کلینیک اروند',
            'phone' => '06133221100',
        ],
        [
            'name' => 'دکتر لیلا باقری',
            'slug' => 'dr-leila-bagheri',
            'city' => 'sari',
            'specialty' => 'ent-specialist',
            'is_vip' => false,
            'photo' => 'https://randomuser.me/api/portraits/women/33.jpg',
            'bio' => "متخصص گوش، حلق و بینی.\nجراحی سینوس، tonsillectomy و اصلاح انحراف بینی.",
            'address' => 'ساری، میدان امام، ساختمان پزشکان مازندران',
            'phone' => '01133224455',
        ],
        [
            'name' => 'دکتر پرویز اکبری',
            'slug' => 'dr-parviz-akbari',
            'city' => 'tehran',
            'specialty' => 'neurologist',
            'is_vip' => true,
            'photo' => 'https://randomuser.me/api/portraits/men/67.jpg',
            'bio' => "متخصص مغز و اعصاب.\nدرمان سردرد، میگرن، ام‌اس و بیماری‌های عصبی.\nدارای فلوشیپ از آلمان.",
            'address' => 'تهران، جردن، خیابان ناهید، پلاک ۴۵',
            'phone' => '02188771234',
        ],
        [
            'name' => 'دکتر نازنین رحیمی',
            'slug' => 'dr-nazanin-rahimi',
            'city' => 'kashan',
            'specialty' => 'dentist',
            'is_vip' => false,
            'photo' => 'https://randomuser.me/api/portraits/women/47.jpg',
            'bio' => "متخصص دندانپزشکی زیبایی و ایمپلنت.\nطراحی لبخند، بلیچینگ و جراحی فک.",
            'address' => 'کاشان، میدان کمال الملک، مجتمع دندانپزشکی الماس',
            'phone' => '03155443322',
        ],
        [
            'name' => 'دکتر کامران شریفی',
            'slug' => 'dr-kamran-sharifi',
            'city' => 'mashhad',
            'specialty' => 'cardiologist',
            'is_vip' => false,
            'photo' => null,
            'bio' => "متخصص قلب و عروق.\nنوار قلب، فشار خون و کنترل کلسترول.",
            'address' => 'مشهد، بلوار وکیل‌آباد، ساختمان قلب توس',
            'phone' => '05137654321',
        ],
        [
            'name' => 'دکتر زهرا ملکی',
            'slug' => 'dr-zahra-maleki',
            'city' => 'isfahan',
            'specialty' => 'pediatrician',
            'is_vip' => false,
            'photo' => 'https://randomuser.me/api/portraits/women/89.jpg',
            'bio' => "متخصص اطفال.\nویزیت نوزادان، تغذیه و بیماری‌های شایع کودکان.",
            'address' => 'اصفهان، مرداویج، خیابان گلستان',
            'phone' => '03136889900',
        ],
        [
            'name' => 'دکتر بهرام کاظمی',
            'slug' => 'dr-bahram-kazemi',
            'city' => 'tehran',
            'specialty' => 'internist',
            'is_vip' => false,
            'photo' => 'https://randomuser.me/api/portraits/men/11.jpg',
            'bio' => "متخصص داخلی و دیابت.\nمدیریت قند خون، فشار خون و چربی خون.",
            'address' => 'تهران، پاسداران، بوستان یکم، پلاک ۸',
            'phone' => '02122887766',
        ],
        [
            'name' => 'دکتر شیدا فرهادی',
            'slug' => 'dr-shida-farhad',
            'city' => 'shiraz',
            'specialty' => 'gynecologist',
            'is_vip' => true,
            'photo' => 'https://randomuser.me/api/portraits/women/72.jpg',
            'bio' => "متخصص زنان و زایمان.\nسونوگرافی، مراقبت‌های بارداری و مشاوره قبل از بارداری.",
            'address' => 'شیراز، قصردشت، بلوار مدرس',
            'phone' => '07136554433',
        ],
    ];

    /** @var list<array{author: string, body: string, rating: int}> */
    private array $sampleReviews = [
        ['author' => 'مریم', 'body' => 'پزشک بسیار دلسوز و با حوصله. توضیحات کامل دادند.', 'rating' => 5],
        ['author' => 'علی', 'body' => 'نوبت‌دهی منظم و مطب تمیز. پیشنهاد می‌کنم.', 'rating' => 5],
        ['author' => 'سمیه', 'body' => 'تشخیص دقیق و درمان مؤثر. ممنونم.', 'rating' => 4],
        ['author' => 'رضا', 'body' => 'کمی معطل شدم ولی کیفیت ویزیت عالی بود.', 'rating' => 4],
        ['author' => 'نرگس', 'body' => 'بهترین تجربه پزشکی که داشتم.', 'rating' => 5],
    ];

    public function run(): void
    {
        Storage::disk('public')->makeDirectory('doctor-photos');

        foreach ($this->doctors as $data) {
            $city = City::query()->where('slug', $data['city'])->first();
            $specialty = Specialty::query()->where('slug', $data['specialty'])->first();

            if ($city === null || $specialty === null) {
                continue;
            }

            $photoPath = $this->resolvePhotoPath($data['slug'], $data['photo'] ?? null);

            $doctor = Doctor::query()->updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'bio' => $data['bio'],
                    'photo_path' => $photoPath,
                    'address' => $data['address'],
                    'city_id' => $city->id,
                    'primary_specialty_id' => $specialty->id,
                    'is_published' => true,
                    'is_active' => true,
                    'is_vip' => $data['is_vip'],
                    'published_at' => now()->subDays(random_int(1, 90)),
                    'meta_title' => $data['name'].' — '.$specialty->name,
                    'meta_description' => Str::limit(str_replace("\n", ' ', $data['bio']), 155),
                ],
            );

            $doctor->specialties()->syncWithoutDetaching([
                $specialty->id => ['is_primary' => true],
            ]);

            DoctorContactPhone::query()->updateOrCreate(
                ['doctor_id' => $doctor->id, 'phone' => $data['phone']],
                ['label' => 'مطب', 'sort_order' => 1, 'is_visible' => true],
            );

            $this->seedSchedules($doctor);
            $this->seedReviews($doctor);
        }
    }

    private function resolvePhotoPath(string $slug, ?string $fallbackUrl): ?string
    {
        $localPath = $this->findLocalPhoto($slug);

        if ($localPath !== null) {
            $destination = 'doctor-photos/seed/'.$slug.'.'.pathinfo($localPath, PATHINFO_EXTENSION);
            Storage::disk('public')->put($destination, File::get($localPath));

            return $destination;
        }

        if ($fallbackUrl !== null && str_starts_with($fallbackUrl, 'http')) {
            return $this->downloadRemotePhoto($slug, $fallbackUrl);
        }

        return $fallbackUrl;
    }

    private function downloadRemotePhoto(string $slug, string $url): ?string
    {
        try {
            $contents = file_get_contents($url);

            if ($contents === false) {
                return $url;
            }

            $destination = 'doctor-photos/seed/'.$slug.'.jpg';
            Storage::disk('public')->put($destination, $contents);

            return $destination;
        } catch (\Throwable) {
            return $url;
        }
    }

    private function findLocalPhoto(string $slug): ?string
    {
        $base = base_path(self::ASSETS_DIR.'/'.$slug);

        foreach (['jpg', 'jpeg', 'webp', 'png'] as $ext) {
            $path = $base.'.'.$ext;

            if (File::exists($path)) {
                return $path;
            }
        }

        return null;
    }

    private function seedSchedules(Doctor $doctor): void
    {
        if ($doctor->schedules()->exists()) {
            return;
        }

        foreach ([0, 1, 2, 3, 4] as $day) {
            DoctorSchedule::query()->create([
                'doctor_id' => $doctor->id,
                'day_of_week' => $day,
                'start_time' => '09:00:00',
                'end_time' => '13:00:00',
                'slot_duration_minutes' => 30,
                'is_active' => true,
            ]);

            DoctorSchedule::query()->create([
                'doctor_id' => $doctor->id,
                'day_of_week' => $day,
                'start_time' => '16:00:00',
                'end_time' => '20:00:00',
                'slot_duration_minutes' => 30,
                'is_active' => true,
            ]);
        }
    }

    private function seedReviews(Doctor $doctor): void
    {
        if ($doctor->reviews()->exists()) {
            return;
        }

        $count = random_int(2, 4);

        for ($i = 0; $i < $count; $i++) {
            $sample = $this->sampleReviews[array_rand($this->sampleReviews)];

            Review::query()->create([
                'doctor_id' => $doctor->id,
                'rating' => $sample['rating'],
                'body' => $sample['body'],
                'author_name' => $sample['author'],
                'is_approved' => true,
            ]);
        }
    }
}
