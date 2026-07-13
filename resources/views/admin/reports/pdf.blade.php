{{--
    Export Laporan Penjualan PDF A4 (FR-011) — DomPDF, CSS inline (tanpa Tailwind/JS).
    Grafik Chart.js TIDAK bisa dirender DomPDF → diganti TABEL penjualan harian
    yang merepresentasikan grafik (keputusan user), konsisten dengan filter terpilih.
--}}
@php($rp = fn ($n) => 'Rp' . number_format($n, 0, ',', '.'))
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan — {{ $from->format('d/m/Y') }} s.d. {{ $to->format('d/m/Y') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; }
        .header { width: 100%; border-bottom: 3px solid #7C4A2D; padding-bottom: 12px; }
        .brand-name { font-size: 20px; font-weight: bold; color: #7C4A2D; }
        .muted { color: #64748b; font-size: 10px; }
        .title { font-size: 18px; font-weight: bold; color: #7C4A2D; text-align: right; }
        h3 { font-size: 13px; color: #7C4A2D; margin: 18px 0 8px; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th {
            background: #7C4A2D; color: #fff; text-align: left;
            padding: 6px 8px; font-size: 10px; text-transform: uppercase;
        }
        table.data th.right, table.data td.right { text-align: right; }
        table.data th.center, table.data td.center { text-align: center; }
        table.data td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; }
        table.data tr.total td { font-weight: bold; border-top: 2px solid #7C4A2D; border-bottom: none; }
        table.data tr.peak td { background: #fef3c7; font-weight: bold; } /* peak = gold (SRS) */
        .cards { width: 100%; margin-top: 14px; border-collapse: separate; border-spacing: 6px 0; }
        .cards td { width: 25%; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px; vertical-align: top; }
        .cards .label { color: #64748b; font-size: 10px; }
        .cards .value { font-size: 14px; font-weight: bold; margin-top: 3px; }
        .footer { margin-top: 30px; text-align: center; color: #64748b; font-size: 10px; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>
                <p class="brand-name">Pitou Cafe</p>
                <p class="muted">Laporan Penjualan</p>
            </td>
            <td style="text-align: right;">
                <p class="title">LAPORAN PENJUALAN</p>
                <p class="muted">Periode: {{ $from->format('d/m/Y') }} — {{ $to->format('d/m/Y') }} · Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
            </td>
        </tr>
    </table>

    {{-- Ringkasan --}}
    <table class="cards">
        <tr>
            <td>
                <p class="label">Total Pendapatan</p>
                <p class="value">{{ $rp($stats->revenue) }}</p>
            </td>
            <td>
                <p class="label">Total Pesanan</p>
                <p class="value">{{ $stats->orders }}</p>
            </td>
            <td>
                <p class="label">Rata-rata Nilai Pesanan</p>
                <p class="value">{{ $rp($average) }}</p>
            </td>
            <td>
                <p class="label">Total Pajak Terkumpul</p>
                <p class="value">{{ $rp($stats->tax) }}</p>
            </td>
        </tr>
    </table>

    {{-- Penjualan harian (representasi grafik; baris peak disorot gold) --}}
    <h3>Penjualan Harian</h3>
    @php($maxDaily = collect($daily)->max('total'))
    <table class="data">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th class="center">Jumlah Order</th>
                <th class="right">Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($daily as $row)
                <tr @class(['peak' => $maxDaily > 0 && $row['total'] === $maxDaily])>
                    <td>{{ $row['label'] }}</td>
                    <td class="center">{{ $row['orders'] }}</td>
                    <td class="right">{{ $rp($row['total']) }}</td>
                </tr>
            @endforeach
            <tr class="total">
                <td>Total</td>
                <td class="center">{{ $stats->orders }}</td>
                <td class="right">{{ $rp($stats->revenue) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Distribusi metode --}}
    <h3>Metode Pembayaran</h3>
    <table class="data">
        <thead>
            <tr>
                <th>Metode</th>
                <th class="right">Pendapatan</th>
                <th class="right">Persentase</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Cash</td>
                <td class="right">{{ $rp($methods['cash']) }}</td>
                <td class="right">{{ $methods['cash_percent'] }}%</td>
            </tr>
            <tr>
                <td>QRIS</td>
                <td class="right">{{ $rp($methods['qris']) }}</td>
                <td class="right">{{ $methods['qris_percent'] }}%</td>
            </tr>
        </tbody>
    </table>

    {{-- Produk terlaris lengkap --}}
    <h3>Produk Terlaris</h3>
    <table class="data">
        <thead>
            <tr>
                <th class="center" style="width: 8%;">#</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th class="right">Jumlah Terjual</th>
                <th class="right">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($topProducts as $item)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $item->menu->name }}</td>
                    <td>{{ $item->menu->category->name }}</td>
                    <td class="right">{{ $item->total_qty }}</td>
                    <td class="right">{{ $rp($item->total_revenue) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="center" style="color:#64748b;">Tidak ada produk terjual pada rentang ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Laporan dibuat otomatis oleh sistem Pitou Cafe POS. Seluruh nilai termasuk pajak (snapshot saat transaksi).</p>
    </div>
</body>
</html>
