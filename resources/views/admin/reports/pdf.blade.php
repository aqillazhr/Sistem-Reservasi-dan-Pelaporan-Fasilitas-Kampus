<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <style>

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }

        p {
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 7px;
            text-align: left;
        }

        th {
            background: #bd93f8;
        }

    </style>

</head>


<body>

    <h1>
        Rekap Fasilitas
    </h1>

    <p>
        Periode: {{ $month }}
    </p>


    <table>

        <thead>

            <tr>
                <th>Fasilitas</th>
                <th>Tipe</th>
                <th>Lokasi</th>
                <th>Okupansi (%)</th>
                <th>Jumlah Kerusakan</th>
                <th>Frekuensi Kerusakan (%)</th>
            </tr>

        </thead>


        <tbody>

            @foreach ($data['rows'] as $row)

                <tr>

                    <td>
                        {{ $row['facility'] }}
                    </td>

                    <td>
                        {{ $row['type'] }}
                    </td>

                    <td>
                        {{ $row['location'] }}
                    </td>

                    <td>
                        {{ $row['occupancy'] }}%
                    </td>

                    <td>
                        {{ $row['damage_count'] }}
                    </td>

                    <td>
                        {{ $row['damage_percentage'] }}%
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

</body>

</html>