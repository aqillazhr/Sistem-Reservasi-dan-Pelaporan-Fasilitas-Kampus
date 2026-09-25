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
            font-size: 24px;
            margin-left: auto;
            margin-right: 8px;
            line-height: 1;
            transform: translateY(-6px);
            /*untuk mengatur jarak vertikal panah di "Tanggal"*/
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
        }

        .time-header,
        .availability-row {
            display: grid;
            grid-template-columns: 120px repeat(5, 1fr);
            gap: 10px;
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
            background: #f1e8ff;

            padding: 13px 8px;

            text-align: center;

            border-radius: 6px;

            font-weight: bold;

            color: #54269a;
        }

        .day-label {
            background: #f1e8ff;

            padding: 20px 10px;

            border-radius: 6px;

            text-align: center;

            color: #54269a;

            font-weight: bold;
        }

        .slot {
            height: 55px;

            border-radius: 5px;

            background: #f1e8ff;
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

            <a href="{{ route('pengguna.reservations.create') }}" class="reserve-button">
                Ajukan Reservasi
            </a>
        </div>



        <!-- =========================
             DETAIL FASILITAS
        ========================= -->

        <div class="detail-card">


            <div class="facility-image">

                @if ($facility->photos->first())
                    <img src="{{ asset('storage/' . $facility->photos->first()->file_path) }}"
                        alt="{{ $facility->name }}">
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

                <span class="arrow-down">⌄</span>
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
            </div>

            <div class="availability-row">
                <div class="time">07.00 - 07.30</div>
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
            </div>

            <div class="availability-row">
                <div class="time">08.00 - 08.30</div>
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
            </div>

            <div class="availability-row">
                <div class="time">09.00 - 09.30</div>
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
            </div>

            <div class="availability-row">
                <div class="time">10.00 - 10.30</div>
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
            </div>

            <div class="availability-row">
                <div class="time">11.00 - 11.30</div>
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
            </div>

            <div class="availability-row">
                <div class="time">12.00 - 12.30</div>
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
            </div>

            <div class="availability-row">
                <div class="time">13.00 - 13.30</div>
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
            </div>

            <div class="availability-row">
                <div class="time">14.00 - 14.30</div>
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
            </div>

            <div class="availability-row">
                <div class="time">15.00 - 15.30</div>
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
            </div>

            <div class="availability-row">
                <div class="time">16.00 - 16.30</div>
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
            </div>

            <div class="availability-row">
                <div class="time">17.00 - 17.30</div>
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
            </div>

            <div class="availability-row">
                <div class="time">18.00 - 18.30</div>
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
            </div>

            <div class="availability-row">
                <div class="time">19.00 - 19.30</div>
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
            </div>

        </div>


    </main>



    <!-- =========================
         JAVASCRIPT CALENDAR
    ========================= -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            let currentDate = new Date(2026, 8, 22);
            let selectedDate = new Date(2026, 8, 22);

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


            /* =========================
               KLIK TANGGAL
            ========================= */

            dateButton.addEventListener('click', function() {

                calendar.classList.toggle('show');

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

                        }
                    );


                    calendarGrid.appendChild(button);

                }

            }


            /* =========================
               RENDER AWAL
            ========================= */

            renderCalendar();

        });
    </script>

@endsection