<x-filament-panels::page>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>

    <div class="space-y-6">
        {{ $this->form }}

        <div
            x-data="doctorPhotoCropper()"
            x-on:photo-uploaded.window="reset()"
            class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900"
        >
            <div class="space-y-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-950 dark:text-white">
                        انتخاب عکس
                    </label>
                    <input
                        type="file"
                        accept="image/*"
                        x-ref="fileInput"
                        x-on:change="loadImage($event)"
                        class="block w-full text-sm text-gray-600 file:me-4 file:rounded-lg file:border-0 file:bg-primary-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-primary-500"
                    >
                </div>

                <div x-show="hasImage" x-cloak class="max-w-xl">
                    <img x-ref="image" alt="پیش‌نمایش" class="max-h-96 w-full rounded-lg bg-gray-100">
                </div>

                <p x-show="hasImage" x-cloak class="text-sm text-gray-500 dark:text-gray-400">
                    با ماوس ناحیه دلخواه را انتخاب کنید. نسبت تصویر ۱:۱ پیشنهاد می‌شود.
                </p>

                <div class="flex flex-wrap gap-3">
                    <x-filament::button
                        type="button"
                        x-on:click="crop()"
                        x-bind:disabled="!hasImage"
                    >
                        برش و آماده‌سازی
                    </x-filament::button>

                    <x-filament::button
                        type="button"
                        color="gray"
                        x-on:click="reset()"
                        x-bind:disabled="!hasImage"
                    >
                        پاک کردن
                    </x-filament::button>
                </div>

                <div x-show="previewUrl" x-cloak>
                    <p class="mb-2 text-sm font-medium text-gray-950 dark:text-white">پیش‌نمایش نهایی</p>
                    <img x-bind:src="previewUrl" alt="نتیجه برش" class="h-32 w-32 rounded-full object-cover ring-2 ring-primary-500">
                </div>
            </div>
        </div>

        <form wire:submit="save">
            <x-filament::button type="submit">
                ارسال برای بررسی
            </x-filament::button>
        </form>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('doctorPhotoCropper', () => ({
                    cropper: null,
                    hasImage: false,
                    previewUrl: null,

                    loadImage(event) {
                        const file = event.target.files?.[0];

                        if (!file) {
                            return;
                        }

                        this.reset(false);

                        const reader = new FileReader();
                        reader.onload = () => {
                            this.$refs.image.src = reader.result;
                            this.hasImage = true;

                            this.$nextTick(() => {
                                if (this.cropper) {
                                    this.cropper.destroy();
                                }

                                this.cropper = new window.Cropper(this.$refs.image, {
                                    aspectRatio: 1,
                                    viewMode: 1,
                                    autoCropArea: 1,
                                });
                            });
                        };
                        reader.readAsDataURL(file);
                    },

                    crop() {
                        if (!this.cropper) {
                            return;
                        }

                        const canvas = this.cropper.getCroppedCanvas({
                            width: 800,
                            height: 800,
                        });

                        this.previewUrl = canvas.toDataURL('image/jpeg', 0.9);
                        this.$wire.set('croppedImage', this.previewUrl);
                    },

                    reset(clearInput = true) {
                        if (this.cropper) {
                            this.cropper.destroy();
                            this.cropper = null;
                        }

                        this.hasImage = false;
                        this.previewUrl = null;
                        this.$wire.set('croppedImage', null);

                        if (clearInput && this.$refs.fileInput) {
                            this.$refs.fileInput.value = '';
                        }
                    },
            }));
        });
    </script>
</x-filament-panels::page>
