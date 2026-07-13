{{--
    Checker station (FR-006 / FR-007) — view SHARED Kitchen & Barista.
    Pemanggil cukup mengirim: $order (dengan relasi table), $items (item station terkait,
    dengan relasi menu), $station ('kitchen' | 'barista'). Tanpa duplikasi kode di Modul 7.

    Standalone (tanpa layouts.app / Vite) — auto window.print().
    Layout mengikuti struk pembayaran: @page 48mm, table-based (tanpa flex/grid/absolute)
    agar kompatibel driver printer thermal ESC/POS.
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checker {{ ucfirst($station) }} — {{ $order->order_number }}</title>
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
        td.r { text-align: right; width: 45%; }

        /* qty kolom sempit di kiri, nama menu mengisi sisanya */
        td.qty { width: 22%; text-align: left; }

        .vip {
            font-size: 12px;
            letter-spacing: 2px;
            border: 1px solid #000;
            padding: 2px 0;
            margin: 5px 0;
        }

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
            <p class="store-info">CHECKER {{ strtoupper($station) }}</p>
        </div>

        <div class="divider"></div>

        {{-- ===== VIP ===== --}}
        @if ($order->table->is_vip)
            <div class="center vip">*** VIP ***</div>
            <div class="divider"></div>
        @endif

        {{-- ===== INFO ORDER ===== --}}
        <table class="rows">
            <tr>
                <td class="l">#{{ $order->order_number }}</td>
                <td class="r">{{ $order->created_at->format('H:i') }}</td>
            </tr>
            <tr>
                <td class="l">Meja</td>
                <td class="r">{{ $order->table->name }}</td>
            </tr>
            <tr>
                <td class="l">Customer</td>
                <td class="r">{{ $order->customer_name }}</td>
            </tr>
            <tr>
                <td class="l">Tanggal</td>
                <td class="r">{{ $order->created_at->format('d/m/Y') }}</td>
            </tr>
        </table>

        <div class="divider"></div>

        {{-- ===== DAFTAR ITEM STATION ===== --}}
        <table class="rows">
            @foreach ($items as $item)
                <tr>
                    <td class="qty">{{ $item->qty }}x</td>
                    <td class="l">{{ $item->menu->name }}</td>
                </tr>
            @endforeach
        </table>

        <div class="divider"></div>

        {{-- ===== FOOTER ===== --}}
        <p class="center footer">
            Tiket penyiapan — bukan bukti bayar.<br>
            Selesai? Bunyikan lonceng.
        </p>
    </div>
</body>
</html>
