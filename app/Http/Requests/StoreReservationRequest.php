<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi SERVER pengajuan reservasi. Client-side (JS di view) hanya
 * kemudahan pemakaian; yang menentukan tetap validasi di sini.
 */
class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->isPengguna();
    }

    protected function prepareForValidation(): void
    {
        // Terima "08:00" maupun "08:00:00" -> dinormalkan ke H:i
        foreach (['start_time', 'end_time'] as $field) {
            $value = $this->input($field);

            if (is_string($value) && preg_match('/^\d{2}:\d{2}:\d{2}$/', $value)) {
                $this->merge([$field => substr($value, 0, 5)]);
            }
        }

        if (is_string($this->input('purpose'))) {
            $this->merge(['purpose' => trim($this->input('purpose'))]);
        }
    }

    public function rules(): array
    {
        $maxDate = now()->addDays((int) config('reservation.max_days_ahead'))->toDateString();

        return [
            'facility_id' => [
                'required',
                'integer',
                Rule::exists('facilities', 'id')->whereNull('deleted_at')->where('status', 'aktif'),
            ],
            'reservation_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today', 'before_or_equal:'.$maxDate],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'purpose' => ['required', 'string', 'min:5', 'max:1000'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            if ($validator->errors()->hasAny(['reservation_date', 'start_time', 'end_time'])) {
                return; // format dasar sudah salah, jangan lanjut
            }

            $start = $this->input('start_time');
            $end = $this->input('end_time');
            $open = config('reservation.open_time');
            $close = config('reservation.close_time');
            $step = (int) config('reservation.slot_minutes');

            // Jam operasional (H:i berpadding nol, jadi perbandingan string aman)
            if ($start < $open || $end > $close) {
                $validator->errors()->add('start_time', 'Reservasi harus dalam jam operasional '.str_replace(':', '.', $open).'–'.str_replace(':', '.', $close).'.');
            }

            // Kelipatan slot 30 menit
            foreach (['start_time' => $start, 'end_time' => $end] as $field => $time) {
                [$h, $m] = array_map('intval', explode(':', $time));

                if (($h * 60 + $m) % $step !== 0) {
                    $validator->errors()->add($field, "Waktu harus kelipatan {$step} menit (mis. 08.00 atau 08.30).");
                }
            }

            // Jam mulai tidak boleh sudah lewat kalau reservasinya hari ini
            $startsAt = Carbon::parse($this->input('reservation_date').' '.$start, config('app.timezone'));

            if ($startsAt->lessThanOrEqualTo(now())) {
                $validator->errors()->add('start_time', 'Jam mulai sudah lewat. Pilih waktu yang akan datang.');
            }
        }];
    }

    public function messages(): array
    {
        return [
            'facility_id.required' => 'Pilih fasilitas dulu.',
            'facility_id.exists' => 'Fasilitas tidak ditemukan atau sedang tidak dapat direservasi.',
            'reservation_date.required' => 'Pilih tanggal reservasi.',
            'reservation_date.date_format' => 'Format tanggal tidak valid.',
            'reservation_date.after_or_equal' => 'Tanggal tidak boleh sebelum hari ini.',
            'reservation_date.before_or_equal' => 'Reservasi hanya bisa diajukan sampai '.config('reservation.max_days_ahead').' hari ke depan.',
            'start_time.required' => 'Pilih jam mulai.',
            'start_time.date_format' => 'Format jam mulai tidak valid.',
            'end_time.required' => 'Pilih jam selesai.',
            'end_time.date_format' => 'Format jam selesai tidak valid.',
            'end_time.after' => 'Jam selesai harus setelah jam mulai.',
            'purpose.required' => 'Tulis tujuan penggunaan fasilitas.',
            'purpose.min' => 'Tujuan penggunaan minimal :min karakter.',
            'purpose.max' => 'Tujuan penggunaan maksimal :max karakter.',
        ];
    }
}
