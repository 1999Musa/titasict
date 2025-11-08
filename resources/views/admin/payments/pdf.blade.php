<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payment Receipt</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            background-color: #f9fafc;
            color: #333;
            margin: 40px;
        }

        .receipt-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 30px 40px;
            max-width: 800px;
            margin: auto;
        }

        header {
            text-align: center;
            border-bottom: 3px solid #033163;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }

        header h1 {
            color: #032963;
            margin-bottom: 5px;
            font-size: 28px;
        }

        header h2 {
            margin: 0;
            font-size: 22px;
            color: #444;
        }

        p {
            font-size: 16px;
            line-height: 1.6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            font-size: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px 12px;
            text-align: left;
        }

        th {
            background-color: #23578f;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        tr:nth-child(even) {
            background-color: #f8f9fb;
        }

        .amount {
            margin-top: 25px;
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

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
    </style>
</head>

<body>
    <div class="receipt-container">
        <header>
            <h1>Titas ICT</h1>
            <h2>Payment Receipt</h2>
        </header>

        <p><strong>Student Name:</strong> {{ $student->name }}</p>
        <p><strong>Mobile:</strong> {{ $student->mobile_number }}</p>

        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Month</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                    <tr>
                        <td>{{ ucfirst($payment->type) }}</td>
                        <td>{{ $payment->month ?? '-' }}</td>
                        <td>{{ $payment->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @php
            $paidPayment = collect($payments)->firstWhere('status', 'Paid');
        @endphp

        @if($paidPayment)
            <p class="amount">Amount Paid: <strong>Tk {{ number_format($paidPayment->amount) }}/-</strong></p>
        @endif

        <footer>
            <p class="timestamp">
                Generated automatically at 
                {{ now()->setTimezone('Asia/Dhaka')->format('d M Y, h:i A') }} <br>
                by Titas ICT Student Management Software.
            </p>
            <p>
                Software developed by 
                <strong>Musa Md Obayed </strong> <small>(01722402173)</small>
            </p>
        </footer>
    </div>
</body>

</html>
