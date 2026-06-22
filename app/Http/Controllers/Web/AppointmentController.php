<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookAppointmentRequest;
use App\Http\Requests\PeygiriAppointmentRequest;
use App\Http\Requests\TrackAppointmentRequest;
use App\Models\Doctor;
use App\Services\Appointments\AppointmentService;
use App\Services\Seo\SeoBuilder;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class AppointmentController extends Controller
{
    public function __construct(
        private readonly SeoBuilder $seo,
        private readonly AppointmentService $appointments,
    ) {}

    public function trackForm(Request $request): View
    {
        $code = $request->query('tracking_code');

        return view('appointments.track', $this->trackViewData($code ? (string) $code : null));
    }

    public function trackByCode(string $trackingCode): View
    {
        return view('appointments.track', $this->trackViewData(strtoupper($trackingCode)));
    }

    /**
     * @return array<string, mixed>
     */
    private function trackViewData(?string $code): array
    {
        $appointment = $code
            ? $this->appointments->findByTrackingCode($code)
            : null;

        return [
            'seo' => $this->seo->forAppointmentTrack(),
            'appointment' => $appointment,
            'trackingCode' => $code,
        ];
    }

    public function track(TrackAppointmentRequest $request): View
    {
        return view('appointments.track', $this->trackViewData($request->validated('tracking_code')));
    }

    public function cancel(Request $request): RedirectResponse
    {
        $request->validate([
            'tracking_code' => ['required', 'string', 'size:8'],
            'cancellation_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $appointment = $this->appointments->findByTrackingCode($request->string('tracking_code')->toString());

        if (! $appointment || $appointment->status !== 'confirmed' || $appointment->starts_at->isPast()) {
            return back()->withErrors(['tracking_code' => 'امکان لغو این نوبت وجود ندارد.']);
        }

        $result = $this->appointments->cancel($appointment, $request->string('cancellation_reason')->toString() ?: null);

        if (! $result['success']) {
            return back()->withErrors(['tracking_code' => $result['message']]);
        }

        return back()->with('success', 'نوبت شما با موفقیت لغو شد.');
    }

    public function peygiriForm(): View
    {
        return view('appointments.peygiri', [
            'seo' => $this->seo->forAppointmentPeygiri(),
            'appointments' => collect(),
        ]);
    }

    public function peygiri(PeygiriAppointmentRequest $request): View
    {
        $validated = $request->validated();

        $appointments = $this->appointments->findByPatient(
            $validated['patient_phone'],
            $validated['patient_national_code'],
        );

        return view('appointments.peygiri', [
            'seo' => $this->seo->forAppointmentPeygiri(),
            'appointments' => $appointments,
            'patientPhone' => $validated['patient_phone'],
            'patientNationalCode' => $validated['patient_national_code'],
        ]);
    }

    public function book(BookAppointmentRequest $request, Doctor $doctor): RedirectResponse
    {
        abort_unless($doctor->is_published && $doctor->is_active, 404);

        try {
            $appointment = $this->appointments->book(
                doctor: $doctor,
                startsAt: Carbon::parse($request->validated('starts_at')),
                patientName: $request->validated('patient_name'),
                patientPhone: $request->validated('patient_phone'),
                patientNationalCode: $request->validated('patient_national_code'),
            );
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['starts_at' => $e->getMessage()]);
        }

        return redirect()
            ->route('appointments.track', ['tracking_code' => $appointment->tracking_code])
            ->with('success', 'نوبت شما با موفقیت ثبت شد.');
    }

    public function slots(Request $request, Doctor $doctor): \Illuminate\Http\JsonResponse
    {
        abort_unless($doctor->is_published && $doctor->is_active, 404);

        $date = Carbon::parse($request->input('date', now()->toDateString()));
        $slots = $this->appointments->availableSlots($doctor, $date);

        return response()->json([
            'slots' => $slots->map(fn (Carbon $slot) => [
                'value' => $slot->toIso8601String(),
                'label' => $slot->format('H:i'),
            ]),
        ]);
    }
}
