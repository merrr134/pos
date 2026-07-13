{{--
    Struk pembayaran thermal 58mm (FR-008/FR-009) — standalone, auto window.print().
    Layout mengikuti referensi struk fisik: monospace, tanpa prefix "Rp".
    Memakai table (tanpa flex/grid/absolute) agar kompatibel driver printer thermal ESC/POS.
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk — {{ $order->order_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page {
            size: 48mm auto;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            width: 48mm;
            font-family: "Consolas", "Courier New", monospace;
            font-size: 11px;
            line-height: 1.35;
            color: #000;
        }

        .ticket {
            width: 44mm;
            margin: 0 auto;
            padding: 1mm;
        }

        .center { text-align: center; }

        .store-name { font-size: 12px; }
        .store-info { font-size: 11px; }

        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .spacer { height: 6px; }

        table.rows {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        table.rows td {
            padding: 0;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        td.l { text-align: left; padding-right: 4px; }
        td.r { text-align: right; width: 40%; }

        /* baris nama menu memanjang penuh */
        .item-name td { padding-top: 2px; }

        /* baris qty x harga diindent seperti struk asli */
        td.indent { padding-left: 8px; }

        .footer { padding-top: 2px; }

        @media print {
            body { margin: 0; padding: 0; width: 48mm; }
            .ticket { width: 44mm; margin: 0; padding: 1mm; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="ticket">
        {{-- ===== HEADER ===== --}}
        <div class="center">
            <p class="store-name">Pitou Cafe</p>
            <p class="store-info">Jl. .............</p>
            <p class="store-info">08xxxxxxxxxx</p>
        </div>

        <div class="divider"></div>

        {{-- ===== INFO TRANSAKSI ===== --}}
        <table class="rows">
            <tr>
                <td class="l">#{{ $order->order_number }}</td>
                <td class="r">{{ $order->payment->paid_at->format('H:i') }}</td>
            </tr>
        </table>

        <div class="divider"></div>

        {{-- ===== DAFTAR MENU ===== --}}
        <table class="rows">
            @foreach ($order->items as $item)
                <tr class="item-name">
                    <td class="l" colspan="2">{{ $item->menu->name }}</td>
                </tr>
                <tr>
                    <td class="l indent">{{ $item->qty }} x {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="r">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </table>

        <div class="divider"></div>

        {{-- ===== RINGKASAN ===== --}}
        @php
            // BR-016: SNAPSHOT pajak dari payment — struk lama tidak berubah saat Admin mengubah pajak.
            $grandTotal = (float) $order->total + (float) $order->payment->tax_amount;
        @endphp
        <table class="rows">
            <tr>
                <td class="l">Subtotal</td>
                <td class="r">{{ number_format($order->total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="l">Pajak</td>
                <td class="r">{{ number_format($order->payment->tax_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="l">Total</td>
                <td class="r">{{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="spacer"></div>

        {{-- ===== PEMBAYARAN ===== --}}
        <table class="rows">
            @if ($order->payment->payment_method === 'qris')
                {{-- BR-017: QRIS selalu dibayar pas --}}
                <tr>
                    <td class="l indent">Bayar (QRIS)</td>
                    <td class="r">LUNAS</td>
                </tr>
            @else
                <tr>
                    <td class="l indent">Bayar ({{ $order->payment->payment_method === 'cash' ? 'TUNAI' : strtoupper($order->payment->payment_method) }})</td>
                    <td class="r">{{ number_format($order->payment->amount_paid, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="l indent">Kembali</td>
                    <td class="r">{{ number_format($order->payment->change, 0, ',', '.') }}</td>
                </tr>
            @endif
        </table>

        <div class="spacer"></div>

        {{-- ===== FOOTER ===== --}}
        <p class="center footer">* Terima Kasih *</p>
    </div>

    <script>
        // Auto-close setelah dialog print ditutup (print maupun cancel).
        // window.close() hanya berlaku pada tab/popup yang dibuka via script atau
        // target="_blank" — bila dibuka manual, browser mengabaikannya (aman).
        window.addEventListener('afterprint', () => setTimeout(() => window.close(), 100));
    </script>
</body>
</html>
