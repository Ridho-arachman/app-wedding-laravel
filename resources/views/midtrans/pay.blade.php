<!DOCTYPE html>
<html>
<head>
    <title>Pembayaran Order {{ $order->order_number }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background: #f5f5f5; }
        .card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; max-width: 400px; }
        h1 { font-size: 1.5rem; margin-bottom: 1rem; }
        button { background: #4CAF50; color: white; border: none; padding: 12px 24px; font-size: 1rem; border-radius: 4px; cursor: pointer; }
        button:hover { background: #45a049; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Pembayaran Order #{{ $order->order_number }}</h1>
        <p>Total yang harus dibayar: <strong>Rp {{ number_format($amount, 0, ',', '.') }}</strong></p>
        <button id="pay-button">Bayar Sekarang</button>
    </div>

    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function() {
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result) {
                    window.location.href = '{{ route("midtrans.finish") }}?order_id=' + result.order_id;
                },
                onPending: function(result) {
                    window.location.href = '{{ route("midtrans.finish") }}?order_id=' + result.order_id;
                },
                onError: function(result) {
                    alert('Pembayaran gagal: ' + result.status_message);
                }
            });
        };
    </script>
</body>
</html>