<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Laporan okupansi kamar harian
     * Berisi: nomor kamar, tipe kamar, status keterisian, dan persentase okupansi
     */
    public function okupansi(Request $request)
    {
        $tanggal = $request->input('tanggal', today()->toDateString());

        $kamars = Kamar::orderBy('nomor_kamar')->get();
        $totalKamar = $kamars->count();

        // Cari pemesanan yang aktif pada malam tanggal terpilih (hari check-out dianggap kosong)
        $pemesananAktif = Pemesanan::with('tamu')
            ->whereDate('tanggal_check_in', '<=', $tanggal)
            ->whereDate('tanggal_check_out', '>', $tanggal)
            ->whereIn('status', ['Check-in', 'Dipesan'])
            ->get()
            ->groupBy('kamar_id')
            ->map(fn ($group) => $group->firstWhere('status', 'Check-in') ?? $group->first());

        $kamarData = $kamars->map(function ($kamar) use ($pemesananAktif, $tanggal) {
            $pemesanan = $pemesananAktif->get($kamar->id);
            $isTerisi = $pemesanan !== null && $pemesanan->status === 'Check-in';

            return [
                'nomor_kamar' => $kamar->nomor_kamar,
                'tipe' => $kamar->tipe,
                'harga_per_malam' => $kamar->harga_per_malam,
                'status_kamar' => $kamar->status,
                'status_kebersihan' => $kamar->status_kebersihan,
                'status_keterisian' => $isTerisi ? 'Terisi' : ($pemesanan ? 'Dipesan' : 'Kosong'),
                'nama_tamu' => $pemesanan?->tamu?->nama ?? '-',
            ];
        });

        $kamarTerisiCount = $kamarData->where('status_keterisian', 'Terisi')->count();
        $persentaseOkupansi = $totalKamar > 0 ? round(($kamarTerisiCount / $totalKamar) * 100, 2) : 0;

        $isPrint = $request->boolean('print');

        return view('laporan.okupansi', compact(
            'tanggal',
            'kamarData',
            'totalKamar',
            'kamarTerisiCount',
            'persentaseOkupansi',
            'isPrint'
        ));
    }

    /**
     * Laporan pendapatan hotel bulanan
     * Berisi: rincian kode booking, nama tamu, metode pembayaran, dan total penerimaan
     */
    public function pendapatan(Request $request)
    {
        $bulan = (int) $request->input('bulan', date('m'));
        $tahun = (int) $request->input('tahun', date('Y'));

        $pembayarans = Pembayaran::with(['pemesanan.tamu', 'pemesanan.kamar', 'penerima'])
            ->whereMonth('tanggal_bayar', $bulan)
            ->whereYear('tanggal_bayar', $tahun)
            ->orderBy('tanggal_bayar', 'desc')
            ->get();

        $totalPenerimaan = $pembayarans->sum('jumlah_bayar') - $pembayarans->sum('kembalian');
        $totalTunai = $pembayarans->where('metode_pembayaran', 'Tunai')->sum(fn ($p) => $p->jumlah_bayar - $p->kembalian);
        $totalTransfer = $pembayarans->where('metode_pembayaran', 'Transfer')->sum('jumlah_bayar');
        $totalQRIS = $pembayarans->where('metode_pembayaran', 'QRIS')->sum('jumlah_bayar');

        $isPrint = $request->boolean('print');

        return view('laporan.pendapatan', compact(
            'bulan',
            'tahun',
            'pembayarans',
            'totalPenerimaan',
            'totalTunai',
            'totalTransfer',
            'totalQRIS',
            'isPrint'
        ));
    }

    /**
     * Laporan riwayat tamu bulanan
     * Berisi: daftar tamu yang pernah menginap, kamar yang disewa, dan durasi menginap
     */
    public function riwayatTamu(Request $request)
    {
        $bulan = (int) $request->input('bulan', date('m'));
        $tahun = (int) $request->input('tahun', date('Y'));

        $pemesanans = Pemesanan::with(['tamu', 'kamar'])
            ->whereIn('status', ['Check-in', 'Check-out'])
            ->where(function ($q) use ($bulan, $tahun) {
                $q->whereMonth('tanggal_check_in', $bulan)
                  ->whereYear('tanggal_check_in', $tahun)
                  ->orWhere(function ($q2) use ($bulan, $tahun) {
                      $q2->whereMonth('tanggal_check_out', $bulan)
                         ->whereYear('tanggal_check_out', $tahun);
                  });
            })
            ->orderBy('tanggal_check_in', 'desc')
            ->get();

        $riwayatData = $pemesanans->map(function ($p) {
            $checkIn = Carbon::parse($p->tanggal_check_in);
            $checkOut = Carbon::parse($p->tanggal_check_out);
            $durasiMalam = max(1, $checkIn->diffInDays($checkOut));

            return [
                'nama_tamu' => $p->tamu->nama,
                'no_identitas' => $p->tamu->no_identitas,
                'no_hp' => $p->tamu->no_hp,
                'nomor_kamar' => $p->kamar->nomor_kamar,
                'tipe_kamar' => $p->kamar->tipe,
                'tanggal_check_in' => $checkIn->format('d/m/Y'),
                'tanggal_check_out' => $checkOut->format('d/m/Y'),
                'durasi_malam' => $durasiMalam,
                'status' => $p->status,
                'total_harga' => $p->total_harga,
            ];
        });

        $isPrint = $request->boolean('print');

        return view('laporan.riwayat_tamu', compact(
            'bulan',
            'tahun',
            'riwayatData',
            'isPrint'
        ));
    }
}
