@extends('layouts.dashboard')

@section('title', 'Detail ' . $facility->name)

@push('styles')
    <style>
        * {
            box-sizing: border-box;
        }

        /* =========================
                                                                                       CONTENT
                                                                                    ========================= */

        .container {
            padding: 20px 0 40px;
        }

        .back-button {
            font-size: 34px;
            color: #54269a;
            text-decoration: none;
        }

        h1,
        h2 {
            color: #54269a;
        }

        h1 {
            font-size: 42px;
            margin: 5px 0 25px;
        }

        h2 {
            font-size: 40px;
            margin-top: 35px;
        }


        /* =========================
                                                                                       DETAIL CARD
                                                                                    ========================= */

        .detail-card {
            background: white;

            border-radius: 12px;

            padding: 30px;

            display: flex;
            gap: 35px;

            box-shadow: 0 3px 12px rgba(70, 30, 120, 0.12);
        }

        .facility-image {
            width: 300px;
            height: 240px;

            flex-shrink: 0;
        }

        .facility-image img,
        .no-image {
            width: 100%;
            height: 100%;

            object-fit: cover;

            border-radius: 8px;
            background: #d8bcfa;
        }

        .detail-info {
            padding-top: 5px;
        }

        .detail-info h3 {
            color: #54269a;
            font-size: 34px;
            margin-top: 0;
            margin-bottom: 20px;
        }

        .detail-row {
            display: grid;
            grid-template-columns: 130px 20px 1fr;

            margin: 10px 0;

            font-size: 19px;
        }

        .detail-row .label {
            color: #777;
        }

        .detail-row .colon {
            color: #777;
        }

        .detail-row strong {
            color: #29213b;
        }

        .status {
            display: inline-block;

            padding: 5px 12px;

            border-radius: 15px;

            background: #c8ffc8;
            color: #267326;
        }

        .detail-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
        }

        .detail-header h1 {
            margin: 5px 0 25px;
        }

        .reserve-button {
            display: inline-block;
            background: #54269a;
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
        }

        .reserve-button:hover {
            background: #7133d1;
        }

        /* =========================
                                                                                       JADWAL
                                                                                    ========================= */

        .schedule-header {
            position: relative;

            display: flex;
            align-items: center;
        }

        .date-button {
            width: 230px;
            height: 42px;

            border: none;
            border-radius: 6px;

            background: #c7a1f5;
            color: white;

            font-size: 19px;
            font-weight: bold;

            cursor: pointer;

            display: flex;
            align-items: center;

            padding: 0 18px;
        }

        .date-button span:first-child {
            margin-left: 5px;
        }

        .arrow-down {
            width: 14px;
            height: 14px;
            border-right: 4px solid white;
            border-bottom: 4px solid white;
            transform: rotate(45deg);
            margin-left: auto;
            margin-right: 8px;
            margin-top: -6px;
            transition: transform 0.2s ease;
        }

        .date-button.active .arrow-down {
            transform: rotate(225deg);
        }


        /* =========================
                                                                                       CALENDAR
                                                                                    ========================= */

        .calendar {
            display: none;

            position: absolute;

            left: 0;
            top: 50px;

            z-index: 999;

            width: 500px;

            background: #eee3ff;

            border-radius: 8px;

            box-shadow: 0 8px 25px rgba(50, 20, 90, 0.2);

            overflow: hidden;
        }

        .calendar.show {
            display: block;
        }

        .calendar-year {
            height: 55px;

            background: #b47bea;
            color: white;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 23px;
            font-weight: bold;
        }

        .calendar-month {
            height: 55px;

            display: flex;
            align-items: center;
            justify-content: center;

            gap: 45px;

            font-size: 21px;
            font-weight: bold;

            color: #54269a;
        }

        .month-arrow {
            border: none;
            background: transparent;

            font-size: 35px;
            font-weight: bold;

            color: #54269a;

            cursor: pointer;

            padding: 0 10px;
        }

        .month-arrow:hover {
            color: #7133d1;
        }

        .calendar-grid {
            padding: 12px 18px 20px;

            display: grid;
            grid-template-columns: repeat(7, 1fr);

            gap: 6px;
        }

        .day-name {
            text-align: center;

            font-weight: bold;

            color: #555;

            padding: 8px 0;
        }

        .day {
            height: 38px;
            width: 100%;

            display: flex;
            align-items: center;
            justify-content: center;

            border: none;
            border-radius: 5px;

            background: transparent;

            color: #2d1b4e;

            font-size: 16px;

            cursor: pointer;
        }

        .day:hover {
            background: #d3b5f8;
        }

        .day.active {
            background: #7133d1;
            color: white;
            font-weight: bold;
        }


        /* =========================
                                                                                       AVAILABILITY
                                                                                    ========================= */

        .availability {
            margin-top: 25px;
            background: #b47bea;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
        }

        .time-header,
        .availability-row {
            display: grid;
            grid-template-columns: 120px repeat(7, minmax(120px, 1fr));
            gap: 8px;
            width: 100%;
        }

        .availability-row {
            margin-bottom: 10px;
        }

        .availability-row:last-child {
            margin-bottom: 0;
        }

        .time-header {
            margin-bottom: 10px;
        }

        .time {
            min-width: 120px;
            height: 58px;

            background: #f1e8ff;

            padding: 10px 8px;

            display: flex;
            align-items: center;
            justify-content: center;

            text-align: center;

            border-radius: 6px;

            font-weight: 700;
            font-size: 13px;
            line-height: 1.2;

            color: #54269a;
        }

        .day-label {
            min-height: 62px;

            background: #f1e8ff;
            padding: 10px 8px;

            border-radius: 6px;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            text-align: center;

            color: #54269a;
            font-weight: 700;
            font-size: 14px;
            line-height: 1.35;
        }

        .availability .slot {
            height: 58px;
            min-width: 120px;

            border-radius: 6px;
            background: #f1e8ff;

            display: flex;
            align-items: center;
            justify-content: center;

            text-align: center;

            font-size: 12px;
            font-weight: 700;
            line-height: 1.25;

            color: #54269a;

            padding: 6px 8px;

            overflow-wrap: break-word;
            word-break: normal;

            transition:
                background 0.2s ease,
                transform 0.15s ease;
        }

        .availability .slot.available {
            background: #dff5df;
            color: #267326;
        }

        .availability .slot.unavailable {
            background: #f5dcdc;
            color: #a33333;
        }

        .availability .slot.pending {
            background: #fff3b8;
            color: #8a6800;
        }
    </style>
@endpush



<!-- =========================
         CONTENT
    ========================= -->

@section('content')
    <main class="container">


        <!-- BACK -->

        <a href="{{ request('from', route('facilities.index')) }}" class="back-button">
            ←
        </a>


        <div class="detail-header">
            <h1>Detail Fasilitas</h1>

            @auth
                @if (auth()->user()->role === 'pengguna')
                    {{--
                        TODO (Orang 2, koordinasi dengan Orang 3):
                        Ganti tombol <a> di bawah ini jadi modal langsung,
                        tidak usah pindah halaman ke reservations.create lagi.

                        1. Sertakan partial: reservations.partials.booking-modal
                           (boleh ditaruh di mana saja di halaman ini, dekat
                           akhir file juga boleh — lihat komentar TODO kedua
                           di bagian bawah file ini).

                        2. Ganti tag <a href="..."> di bawah jadi <button
                           type="button">, dengan onclick yang memanggil
                           fungsi JS global: openReservationModal($facility->id)
                           Tidak perlu argumen kedua (tanggal) — defaultnya
                           otomatis hari ini.

                        Kondisi @auth + role==='pengguna' ini TETAP DIPAKAI,
                        cuma isi tombolnya yang berubah dari link jadi trigger
                        modal. Jangan bikin modal/JS baru sendiri di sini —
                        semua logikanya (fetch jadwal, validasi, submit) sudah
                        lengkap di partial itu.
                    --}}
                    <button type="button" class="reserve-button" onclick="openReservationModal({{ $facility->id }})">
                        Ajukan Reservasi
                    </button>
                @endif
            @endauth
        </div>



        <!-- =========================
                                                                                         DETAIL FASILITAS
                                                                                    ========================= -->

        <div class="detail-card">


            <div class="facility-image">

                @if ($facility->photos->first())
                    <img src="{{ asset('storage/' . $facility->photos->first()->file_path) }}" alt="{{ $facility->name }}">
                @else
                    <div class="no-image"></div>
                @endif

            </div>



            <div class="detail-info">


                <h3>
                    {{ $facility->name }}
                </h3>


                <div class="detail-row">

                    <span class="label">
                        Tipe
                    </span>

                    <span class="colon">
                        :
                    </span>

                    <strong>
                        {{ $facility->type->name ?? '-' }}
                    </strong>

                </div>



                <div class="detail-row">

                    <span class="label">
                        Kapasitas
                    </span>

                    <span class="colon">
                        :
                    </span>

                    <strong>
                        {{ $facility->capacity }} orang
                    </strong>

                </div>



                <div class="detail-row">

                    <span class="label">
                        Status
                    </span>

                    <span class="colon">
                        :
                    </span>

                    <strong>

                        <span class="status">
                            {{ ucfirst($facility->status) }}
                        </span>

                    </strong>

                </div>



                <div class="detail-row">

                    <span class="label">
                        Lokasi
                    </span>

                    <span class="colon">
                        :
                    </span>

                    <strong>
                        {{ $facility->location->ruangan ?? '-' }}
                    </strong>

                </div>



                <div class="detail-row">

                    <span class="label">
                        Deskripsi
                    </span>

                    <span class="colon">
                        :
                    </span>

                    <strong>
                        {{ $facility->description ?? '-' }}
                    </strong>

                </div>


            </div>

        </div>



        <!-- =========================
                                                                                         JADWAL
                                                                                    ========================= -->

        <h2>
            Jadwal Fasilitas
        </h2>


        <div class="schedule-header">

            <button type="button" class="date-button" id="dateButton">
                <span id="selectedDateText">Tanggal</span>

                <span class="arrow-down"></span>
            </button>


            <!-- =========================
                                                                                             CALENDAR
                                                                                        ========================= -->

            <div id="calendar" class="calendar">


                <div class="calendar-year" id="calendarYear">
                </div>


                <div class="calendar-month">


                    <button type="button" class="month-arrow" onclick="changeMonth(-1)">
                        ‹
                    </button>


                    <span id="monthYear">
                        September 2026
                    </span>


                    <button type="button" class="month-arrow" onclick="changeMonth(1)">
                        ›
                    </button>


                </div>


                <!-- Tanggal dibuat oleh JavaScript -->

                <div class="calendar-grid" id="calendarGrid">
                </div>


            </div>

        </div>



        <!-- =========================
                                                                                         AVAILABILITY
                                                                                    ========================= -->

        <div class="availability">

            <!-- Header Hari -->
            <div class="time-header">
                <div></div>
                <div class="day-label">Senin</div>
                <div class="day-label">Selasa</div>
                <div class="day-label">Rabu</div>
                <div class="day-label">Kamis</div>
                <div class="day-label">Jumat</div>
                <div class="day-label">Sabtu</div>
                <div class="day-label">Minggu</div>
            </div>

            <div class="availability-row">
                <div class="time">07.00 - 07.30</div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
            </div>

            <div class="availability-row">
                <div class="time">07.30 - 08.00</div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
            </div>

            <div class="availability-row">
                <div class="time">08.00 - 08.30</div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
            </div>

            <div class="availability-row">
                <div class="time">08.30 - 09.00</div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
            </div>

            <div class="availability-row">
                <div class="time">09.00 - 09.30</div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
            </div>

            <div class="availability-row">
                <div class="time">09.30 - 10.00</div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
            </div>

            <div class="availability-row">
                <div class="time">10.00 - 10.30</div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
            </div>

            <div class="availability-row">
                <div class="time">10.30 - 11.00</div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
            </div>

            <div class="availability-row">
                <div class="time">11.00 - 11.30</div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
            </div>

            <div class="availability-row">
                <div class="time">11.30 - 12.00</div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
            </div>

            <div class="availability-row">
                <div class="time">12.00 - 12.30</div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
            </div>

            <div class="availability-row">
                <div class="time">12.30 - 13.00</div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
            </div>

            <div class="availability-row">
                <div class="time">13.00 - 13.30</div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
            </div>

            <div class="availability-row">
                <div class="time">13.30 - 14.00</div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
            </div>

            <div class="availability-row">
                <div class="time">14.00 - 14.30</div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
            </div>

            <div class="availability-row">
                <div class="time">14.30 - 15.00</div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
            </div>

            <div class="availability-row">
                <div class="time">15.00 - 15.30</div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
            </div>

            <div class="availability-row">
                <div class="time">15.30 - 16.00</div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
            </div>

            <div class="availability-row">
                <div class="time">16.00 - 16.30</div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
            </div>

            <div class="availability-row">
                <div class="time">16.30 - 17.00</div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
            </div>

            <div class="availability-row">
                <div class="time">17.00 - 17.30</div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
            </div>

            <div class="availability-row">
                <div class="time">17.30 - 18.00</div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
            </div>

            <div class="availability-row">
                <div class="time">18.00 - 18.30</div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
            </div>

            <div class="availability-row">
                <div class="time">18.30 - 19.00</div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
            </div>

            <div class="availability-row">
                <div class="time">19.00 - 19.30</div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
            </div>

            <div class="availability-row">
                <div class="time">19.30 - 20.00</div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
            </div>

        </div>


    </main>



    <!-- =========================
                                                                                     JAVASCRIPT CALENDAR
                                                                                ========================= -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            let currentDate = new Date();
            let selectedDate = new Date();

            const calendar =
                document.getElementById('calendar');

            const dateButton =
                document.getElementById('dateButton');

            const calendarGrid =
                document.getElementById('calendarGrid');

            const monthYear =
                document.getElementById('monthYear');

            const calendarYear =
                document.getElementById('calendarYear');


            const monthNames = [
                'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember'
            ];

            // =========================
            // AVAILABILITY
            // =========================

            const availability =
                document.querySelector('.availability');

            const dayLabels =
                availability.querySelectorAll('.day-label');

            const availabilityRows =
                availability.querySelectorAll('.availability-row');

            const dayNames = [
                'Senin',
                'Selasa',
                'Rabu',
                'Kamis',
                'Jumat',
                'Sabtu',
                'Minggu'
            ];

            function formatDate(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');

                return `${year}-${month}-${day}`;
            }

            function getMonday(date) {
                const result = new Date(date);
                const day = result.getDay();

                const difference = day === 0 ? -6 : 1 - day;

                result.setDate(result.getDate() + difference);

                return result;
            }

            function getTimeFromIndex(index) {
                const totalMinutes = 7 * 60 + (index * 30);

                const hour = Math.floor(totalMinutes / 60);
                const minute = totalMinutes % 60;

                return `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}`;
            }

            async function renderAvailability(date) {

                const monday = getMonday(date);

                // =========================
                // HEADER HARI + TANGGAL
                // =========================

                dayLabels.forEach((label, index) => {

                    const currentDay = new Date(monday);

                    currentDay.setDate(
                        monday.getDate() + index
                    );

                    const displayDate =
                        currentDay.toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric'
                        });

                    label.innerHTML = `
            ${dayNames[index]}
            <br>
            ${displayDate}
        `;
                });


                // =========================
                // AMBIL DATA RESERVASI
                // =========================

                try {

                    const response = await fetch(
                        `{{ route('facilities.slots', $facility) }}?from=${formatDate(monday)}&days=7`
                    );

                    if (!response.ok) {
                        throw new Error('Gagal mengambil data availability.');
                    }

                    const data = await response.json();
                    console.log('DATA RESERVASI:', data.slots);


                    // =========================
                    // CATAT SLOT YANG TERISI
                    // =========================

                    const occupied = new Map();

                    data.slots.forEach(reservation => {

                        const reservationStart =
                            reservation.start_time;

                        const reservationEnd =
                            reservation.end_time;

                        for (let i = 0; i < 26; i++) {

                            const slotStart =
                                getTimeFromIndex(i);

                            const slotEnd =
                                getTimeFromIndex(i + 1);

                            if (
                                slotStart < reservationEnd &&
                                slotEnd > reservationStart
                            ) {

                                occupied.set(
                                    `${reservation.date}|${slotStart}`,
                                    reservation.state
                                );

                            }
                        }
                    });


                    // =========================
                    // ISI KOTAK YANG SUDAH ADA
                    // =========================

                    availabilityRows.forEach((row, rowIndex) => {

                        const slots =
                            row.querySelectorAll('.slot');

                        const startTime =
                            getTimeFromIndex(rowIndex);

                        slots.forEach((slot, dayIndex) => {

                            const currentDay =
                                new Date(monday);

                            currentDay.setDate(
                                monday.getDate() + dayIndex
                            );

                            const dateString =
                                formatDate(currentDay);


                            // =========================
                            // CEK STATUS FASILITAS
                            // =========================

                            let isAvailable =
                                @json($facility->status) === 'aktif';


                            // =========================
                            // CEK RESERVASI
                            // =========================

                            const reservationStatus = occupied.get(
                                `${dateString}|${startTime}`
                            );

                            if (reservationStatus === 'approved') {
                                isAvailable = false;
                            }


                            // =========================
                            // CEK WAKTU YANG SUDAH LEWAT
                            // =========================

                            const slotDateTime =
                                new Date(
                                    `${dateString}T${startTime}:00`
                                );

                            if (
                                slotDateTime <= new Date()
                            ) {
                                isAvailable = false;
                            }


                            // =========================
                            // TAMPILKAN
                            // =========================

                            slot.classList.remove(
                                'available',
                                'unavailable',
                                'pending'
                            );

                            if (reservationStatus === 'pending' && isAvailable) {
                                slot.classList.add('pending');
                                slot.textContent = 'Menunggu';
                            } else if (isAvailable) {
                                slot.classList.add('available');
                                slot.textContent = 'Tersedia';
                            } else {
                                slot.classList.add('unavailable');
                                slot.textContent = 'Tidak tersedia';
                            }

                        });

                    });

                } catch (error) {

                    console.error(error);

                }
            }
            /* =========================
                           KLIK TANGGAL
                        ========================= */

            dateButton.addEventListener('click', function() {
                calendar.classList.toggle('show');
                dateButton.classList.toggle('active');
                renderCalendar();

            });


            /* =========================
               GANTI BULAN
            ========================= */

            window.changeMonth = function(direction) {

                currentDate.setMonth(
                    currentDate.getMonth() + direction
                );

                renderCalendar();

            };


            /* =========================
               RENDER CALENDAR
            ========================= */

            function renderCalendar() {

                const year =
                    currentDate.getFullYear();

                const month =
                    currentDate.getMonth();


                calendarYear.textContent =
                    year;


                monthYear.textContent =
                    `${monthNames[month]} ${year}`;


                calendarGrid.innerHTML = `

                <div class="day-name">Min</div>
                <div class="day-name">Sen</div>
                <div class="day-name">Sel</div>
                <div class="day-name">Rab</div>
                <div class="day-name">Kam</div>
                <div class="day-name">Jum</div>
                <div class="day-name">Sab</div>

            `;


                const firstDay =
                    new Date(
                        year,
                        month,
                        1
                    ).getDay();


                const daysInMonth =
                    new Date(
                        year,
                        month + 1,
                        0
                    ).getDate();


                // Kosong sebelum tanggal 1

                for (
                    let i = 0; i < firstDay; i++
                ) {

                    calendarGrid.innerHTML += `
                    <div></div>
                `;

                }


                // Tanggal

                for (
                    let day = 1; day <= daysInMonth; day++
                ) {

                    const isSelected =
                        selectedDate.getFullYear() === year &&
                        selectedDate.getMonth() === month &&
                        selectedDate.getDate() === day;


                    const button =
                        document.createElement('button');

                    button.type = 'button';

                    button.className =
                        `day ${isSelected ? 'active' : ''}`;

                    button.textContent = day;


                    button.addEventListener(
                        'click',
                        function() {

                            selectedDate =
                                new Date(
                                    year,
                                    month,
                                    day
                                );

                            // Ubah tulisan tombol Tanggal
                            document.getElementById('selectedDateText').textContent =
                                `${day} ${monthNames[month]}`;

                            console.log(
                                'Tanggal dipilih:',
                                selectedDate
                            );

                            renderCalendar();
                            renderAvailability(selectedDate);

                            calendar.classList.remove('show');
                            dateButton.classList.remove('active');

                        }
                    );


                    calendarGrid.appendChild(button);

                }

            }


            /* =========================
               RENDER AWAL
            ========================= */

            renderCalendar();
            renderAvailability(selectedDate);

        });
    </script>

    {{--
        TODO (Orang 2, koordinasi dengan Orang 3):
        Tambahkan di sini: @include('reservations.partials.booking-modal')
        Partial itu sudah membawa dialog modal + JS-nya sendiri (fetch
        jadwal, validasi, popup konfirmasi, submit) DAN style-nya sendiri
        (reservations._styles) — tidak perlu include tambahan apa pun.
        Setelah baris include ini ada, fungsi window.openReservationModal(id)
        otomatis tersedia di halaman ini dan bisa dipanggil dari tombol
        "Ajukan Reservasi" di atas (lihat komentar TODO di dekat tombol itu).

        Kalau mau ubah tampilan modalnya, edit file partial itu langsung
        (resources/views/reservations/partials/booking-modal.blade.php),
        jangan salin/tulis ulang modal baru di file ini.
    --}}
    @include('reservations.partials.booking-modal')
@endsection
