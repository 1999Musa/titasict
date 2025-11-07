<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Payment Receipt</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        h2 {
            text-align: center;
        }
    </style>
</head>

<body>
    <h2>Payment Receipt</h2>

    <p><strong>Student:</strong> {{ $student->name }}</p>
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

    {{-- Show amount per payment as total paid --}}
    @php
        $paidPayment = collect($payments)->firstWhere('status', 'Paid');
    @endphp

    @if($paidPayment)
        <p><strong>Total Paid:</strong> {{ number_format($paidPayment->amount, 2) }}</p>
    @endif
</body>

</html>