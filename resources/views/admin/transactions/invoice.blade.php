{{--
    Invoice PDF A4 (FR-009) — dirender DomPDF (tanpa Tailwind/Vite/JS, CSS inline saja).
    SEMUA nilai finansial dari SNAPSHOT payment (tax_percent/tax_amount/amount_paid/change)
    — reprint kapan saja tetap identik walau Setting pajak berubah.
--}}
@php
    $order      = $payment->order;
    $taxLabel   = rtrim(rtrim(number_format($payment->tax_percent, 2, ',', '.'), '0'), ',');
    $grandTotal = (float) $payment->amount_paid - (float) $payment->change;
    $rp         = fn ($n) => 'Rp' . number_format($n, 0, ',', '.');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $invoiceNumber }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; }
        .header { width: 100%; border-bottom: 3px solid #7C4A2D; padding-bottom: 14px; }
        .brand-name { font-size: 22px; font-weight: bold; color: #7C4A2D; }
        .muted { color: #64748b; font-size: 11px; }
        .invoice-title { font-size: 26px; font-weight: bold; color: #7C4A2D; text-align: right; }
        .meta { margin-top: 16px; width: 100%; }
        .meta td { vertical-align: top; padding: 2px 0; font-size: 12px; }
        .meta .label { color: #64748b; width: 110px; }
        .items { width: 100%; border-collapse: collapse; margin-top: 18px; }
        .items th {
            background: #7C4A2D; color: #fff; text-align: left;
            padding: 8px 10px; font-size: 11px; text-transform: uppercase;
        }
        .items th.right, .items td.right { text-align: right; }
        .items th.center, .items td.center { text-align: center; }
        .items td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; }
        .summary { width: 46%; margin-left: 54%; margin-top: 14px; border-collapse: collapse; }
        .summary td { padding: 5px 10px; font-size: 12px; }
        .summary .label { color: #64748b; }
        .summary .right { text-align: right; }
        .summary .grand td {
            border-top: 2px solid #7C4A2D; border-bottom: 2px solid #7C4A2D;
            font-weight: bold; font-size: 14px; color: #7C4A2D;
        }
        .footer { margin-top: 40px; text-align: center; color: #64748b; font-size: 11px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: bold; }
        .badge-paid { background: #d1fae5; color: #047857; }
    </style>
</head>
<body>
    {{-- Header: identitas cafe + judul invoice --}}
    <table class="header">
        <tr>
            <td>
                <p class="brand-name">Pitou Cafe</p>
                <p class="muted">{{ $storeAddress }}</p>
            </td>
            <td style="text-align: right;">
                <p class="invoice-title">INVOICE</p>
                <p class="muted">{{ $invoiceNumber }}</p>
            </td>
        </tr>
    </table>

    {{-- Meta transaksi --}}
    <table class="meta">
        <tr>
            <td style="width: 50%;">
                <table>
                    <tr><td class="label">Nomor Order</td><td>: {{ $order->order_number }}</td></tr>
                    <tr><td class="label">Tanggal</td><td>: {{ $payment->paid_at->format('d/m/Y H:i') }}</td></tr>
                    <tr><td class="label">Customer</td><td>: {{ $order->customer_name }}</td></tr>
                </table>
            </td>
            <td style="width: 50%;">
                <table>
                    <tr><td class="label">Meja</td><td>: {{ $order->table->name }}</td></tr>
                    <tr><td class="label">Kasir</td><td>: {{ $payment->receiver->name }}</td></tr>
                    <tr><td class="label">Status</td><td>: <span class="badge badge-paid">LUNAS</span></td></tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Daftar item (snapshot harga di order_items) --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width: 44%;">Menu</th>
                <th class="center" style="width: 12%;">Qty</th>
                <th class="right" style="width: 22%;">Harga</th>
                <th class="right" style="width: 22%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->menu->name }}</td>
                    <td class="center">{{ $item->qty }}</td>
                    <td class="right">{{ $rp($item->price) }}</td>
                    <td class="right">{{ $rp($item->subtotal) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Ringkasan (snapshot payment) --}}
    <table class="summary">
        <tr>
            <td class="label">Subtotal</td>
            <td class="right">{{ $rp($order->total) }}</td>
        </tr>
        <tr>
            <td class="label">Pajak ({{ $taxLabel }}%)</td>
            <td class="right">{{ $rp($payment->tax_amount) }}</td>
        </tr>
        <tr class="grand">
            <td>GRAND TOTAL</td>
            <td class="right">{{ $rp($grandTotal) }}</td>
        </tr>
        <tr>
            <td class="label">Nominal Bayar</td>
            <td class="right">{{ $rp($payment->amount_paid) }}</td>
        </tr>
        <tr>
            <td class="label">Kembalian</td>
            <td class="right">{{ $rp($payment->change) }}</td>
        </tr>
        <tr>
            <td class="label">Metode Pembayaran</td>
            <td class="right">{{ strtoupper($payment->payment_method) }}</td>
        </tr>
    </table>

    <div class="footer">
        <p>Terima kasih atas kunjungan Anda di Pitou Cafe!</p>
        <p>Invoice ini dibuat otomatis oleh sistem dan sah tanpa tanda tangan.</p>
    </div>
</body>
</html>
