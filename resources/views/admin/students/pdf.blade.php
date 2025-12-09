@php
    use Carbon\Carbon;

    // Build monthly payment view
    $joinMonth = $student->joining_month ?? null;
    $monthsFromJoin = [];
    if ($joinMonth) {
        $start = Carbon::createFromFormat('Y-m', $joinMonth)->startOfMonth();
        $now = Carbon::now()->startOfMonth();

        while ($start <= $now) {
            $monthsFromJoin[$start->format('F Y')] = $start->format('M');
            $start->addMonth();
        }
    }

    $paidMonths = $student->payments
        ->where('type', 'monthly')
        ->where('status', 'Paid')
        ->pluck('month')
        ->toArray();

    $admissionPaid = $student->payments
        ->where('type', 'admission')
        ->where('status', 'Paid')
        ->first();

@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $student->name }} - Payment Report</title>
    <style>
        footer {
            border-top: 2px solid #e0e0e0;
            text-align: center;
            margin-top: 40px;
            padding-top: 15px;
            font-size: 13px;
            color: #777;
        }

        footer p {
            margin: 5px 0;
        }

        .timestamp {
            color: #555;
            font-style: italic;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #111;
        }

        .section {
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        td,
        th {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f9f9f9;
        }

        .badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }

        .bg-green {
            background-color: #c6f6d5;
            color: #22543d;
        }

        .bg-red {
            background-color: #fed7d7;
            color: #742a2a;
        }

        .month-badge {
            display: inline-block;
            margin: 2px;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 10px;
        }

        .paid {
            background: #c6f6d5;
            color: #22543d;
        }

        .pending {
            background: #fed7d7;
            color: #742a2a;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .info-table td {
            padding: 4px 8px;
            border: none;
        }
    </style>
</head>

<body>
    <h2>Student Profile Report</h2>

    <div class="section">
        <table class="info-table">
            <tr>
                <td><strong>Name:</strong></td>
                <td>{{ $student->name }}</td>
            </tr>
            <tr>
                <td><strong>Mobile:</strong></td>
                <td>{{ $student->mobile_number }}</td>
            </tr>
            <tr>
                <td><strong>Guardian Mobile:</strong></td>
                <td>{{ $student->guardian_mobile_number }}</td>
            </tr>
            <tr>
                <td><strong>Gender:</strong></td>
                <td>{{ $student->gender }}</td>
            </tr>
            <tr>
                <td><strong>Exam Year:</strong></td>
                <td>{{ $student->exam_year ?? '—' }}</td>
            </tr>
            <tr>
                <td><strong>Joining Month:</strong></td>
                <td>{{ \Carbon\Carbon::parse($student->joining_month)->format('F Y') }}</td>
            </tr>

            <tr>
                <td><strong>Batch Day:</strong></td>
                <td>{{ $student->batchDay->days ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Batch Time:</strong></td>
                <td>{{ $student->batchTime->time ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>Payment Summary</h3>
        <small style="color: rgb(184, 26, 26);">Red = Unpaid</small>,
        <small style="color: green;">Green = Paid</small>
        <table>
            <tr>
                <th>Admission Fee</th>
                <th>Admission Fee Status</th>
                <th>Total Monthly Paid</th>
                <th>Total Pending</th>
            </tr>
            <tr>
                <td>Required</td>
                <td>
                    @if ($admissionPaid)
                        <span class="badge bg-green">Paid</span>
                    @else
                        <span class="badge bg-red">Pending</span>
                    @endif
                </td>
                <td>{{ count($paidMonths) }}</td>
                <td>{{ count($monthsFromJoin) - count($paidMonths) }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>
            Monthly Payments (From Joined Month:
            <strong>
                {{ \Carbon\Carbon::parse($student->join_date)->format('F Y') }}
            </strong>)

            <br>

        </h3>
        <div>
            @foreach ($monthsFromJoin as $full => $short)
                @php
                    // Detect join month (first item in array)
                    $isJoinMonth = $loop->first;
                    $isPaid = in_array($full, $paidMonths);
                @endphp

                <span class="month-badge {{ $isPaid ? 'paid' : 'pending' }}">
                    {{ $short }}
                    @if($isJoinMonth)
                        <small style="font-size: 11px; color: #555;">(Joined)</small>
                    @endif
                </span>
            @endforeach
        </div>
    </div>


    <footer>
        <p class="timestamp">
            Generated automatically at
            {{ now()->setTimezone('Asia/Dhaka')->format('d M Y, h:i A') }} <br>
            by Titas ICT Student Management Software.
        </p>
        <br>
        <p>
            Software developed by
            <strong>Musa Md Obayed </strong> <small>(01722402173)</small>
        </p>
    </footer>
</body>

</html>