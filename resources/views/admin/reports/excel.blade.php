<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th {
            background: #bd93f8;
            color: #260f45;
            border: 1px solid #999;
            padding: 8px;
        }

        td {
            border: 1px solid #999;
            padding: 8px;
        }
    </style>
</head>

<body>

<h2>
    Rekap Fasilitas
</h2>

<p>
    Periode: {{ $selectedMonth }}
</p>

<table>

    <thead>
        <tr>
            <th>Fasilitas</th>
            <th>Lokasi</th>
            <th>Okupansi</th>
            <th>Frekuensi Kerusakan</th>
        </tr>
    </thead>

    <tbody>

        @foreach ($rows as $row)

            <tr>
                <td>{{ $row['facility_name'] }}</td>
                <td>{{ $row['location'] }}</td>
                <td>{{ $row['occupancy'] }}%</td>
                <td>{{ $row['damage_count'] }}</td>
            </tr>

        @endforeach

    </tbody>

</table>

</body>
</html>