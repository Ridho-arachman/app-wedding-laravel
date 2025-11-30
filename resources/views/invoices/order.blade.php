<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_code }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .details { margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE PEMBAYARAN</h1>
        <p>Wedding Organizer "Sinar Bahagia"</p>
    </div>

    <div class="details">
        <p><strong>Kode:</strong> {{ $order->order_code }}</p>
        <p><strong>Customer:</strong> {{ $order->customer_name }}</p>
        <p><strong>Tanggal Acara:</strong> {{ $order->event_date->format('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Paket</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $order->package->name ?? '-' }}</td>
                <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>DP (50%)</td>
                <td>Rp {{ number_format($order->dp_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Sisa</td>
                <td>Rp {{ number_format($order->remaining_amount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <p style="margin-top: 20px;">Terima kasih atas kepercayaan Anda.</p>
</body>
</html>