@extends('layouts.app')

@section('title', 'Buku Tamu Hotel')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Form Registrasi Tamu Baru (4 Cols) -->
    <div class="lg:col-span-4 bg-white p-6 rounded-3xl border border-[#EFEAE0] shadow-sm h-fit">
        <div class="flex items-center space-x-2.5 mb-2">
            <div class="w-7 h-7 rounded-full bg-[#0A1526] text-white flex items-center justify-center">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            </div>
            <h2 class="text-lg font-bold text-[#0A1526]">Registrasi Tamu Baru</h2>
        </div>
        <p class="text-xs text-[#5A6578] mb-5">Catat data identitas tamu sebelum memproses pemesanan kamar.</p>

        <form action="{{ route('resepsionis.tamu.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Nomor Identitas (NIK/Paspor) *</label>
                <input type="text" name="no_identitas" value="{{ old('no_identitas') }}" required placeholder="Contoh: 3201010101010008"
                    class="w-full px-3.5 py-2.5 bg-[#FCFAF7] border border-[#EFEAE0] rounded-2xl text-xs font-medium text-[#0A1526] focus:ring-2 focus:ring-[#0A1526]">
                <p class="text-[10px] text-[#5A6578] mt-1">Wajib unik, tidak boleh ganda.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Nama Lengkap Tamu *</label>
                <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Nama lengkap sesuai tanda pengenal"
                    class="w-full px-3.5 py-2.5 bg-[#FCFAF7] border border-[#EFEAE0] rounded-2xl text-xs font-medium text-[#0A1526] focus:ring-2 focus:ring-[#0A1526]">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Alamat Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="email.tamu@example.com"
                    class="w-full px-3.5 py-2.5 bg-[#FCFAF7] border border-[#EFEAE0] rounded-2xl text-xs font-medium text-[#0A1526] focus:ring-2 focus:ring-[#0A1526]">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Nomor HP / WhatsApp</label>
                <input type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="0812xxxxxxxx"
                    class="w-full px-3.5 py-2.5 bg-[#FCFAF7] border border-[#EFEAE0] rounded-2xl text-xs font-medium text-[#0A1526] focus:ring-2 focus:ring-[#0A1526]">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Alamat Domisili</label>
                <textarea name="alamat" rows="2" placeholder="Kota / Alamat tempat tinggal"
                    class="w-full px-3.5 py-2.5 bg-[#FCFAF7] border border-[#EFEAE0] rounded-2xl text-xs font-medium text-[#0A1526] focus:ring-2 focus:ring-[#0A1526]">{{ old('alamat') }}</textarea>
            </div>

            <button type="submit" class="w-full bg-[#0A1526] hover:bg-black text-white font-extrabold py-3 px-4 rounded-full text-xs shadow transition mt-2">
                Simpan Profil Tamu
            </button>
        </form>
    </div>

    <!-- Tabel Daftar Tamu (8 Cols) -->
    <div class="lg:col-span-8 bg-white rounded-3xl border border-[#EFEAE0] shadow-sm overflow-hidden flex flex-col">
        <div class="px-6 py-5 border-b border-[#EFEAE0] flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <div class="w-7 h-7 rounded-full bg-[#0A1526] text-white flex items-center justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-[#0A1526]">Buku Tamu Terdaftar</h2>
                    <p class="text-xs text-[#5A6578]">Daftar seluruh tamu yang tercatat dalam sistem operasional.</p>
                </div>
            </div>
            <span class="text-xs font-bold text-[#5A6578] bg-[#FCFAF7] px-3 py-1 rounded-full border border-[#EFEAE0]">
                Total: {{ $tamus->total() }} Tamu
            </span>
        </div>

        <div class="overflow-x-auto flex-1">
            <table class="w-full text-left text-xs text-[#1c1917]">
                <thead class="bg-[#FCFAF7] uppercase text-[10px] tracking-wider text-[#5A6578] font-extrabold border-b border-[#EFEAE0]">
                    <tr>
                        <th class="px-6 py-3.5">Nama Tamu</th>
                        <th class="px-6 py-3.5">No. Identitas</th>
                        <th class="px-6 py-3.5">Kontak</th>
                        <th class="px-6 py-3.5">Alamat</th>
                        <th class="px-6 py-3.5">Tanggal Daftar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EFEAE0]">
                    @forelse($tamus as $tamu)
                        <tr class="hover:bg-[#FCFAF7]/60 transition">
                            <td class="px-6 py-3.5">
                                <div class="font-extrabold text-[#0A1526]">{{ $tamu->nama }}</div>
                                <div class="text-[10px] text-[#5A6578]">{{ $tamu->email }}</div>
                            </td>
                            <td class="px-6 py-3.5 font-mono font-bold text-[#0A1526]">
                                {{ $tamu->no_identitas }}
                            </td>
                            <td class="px-6 py-3.5 font-medium">
                                {{ $tamu->no_hp ?? '-' }}
                            </td>
                            <td class="px-6 py-3.5 text-[#5A6578] max-w-xs truncate">
                                {{ $tamu->alamat ?? '-' }}
                            </td>
                            <td class="px-6 py-3.5 text-[#5A6578] font-medium">
                                {{ $tamu->created_at->format('d/m/Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-[#5A6578]">
                                Belum ada data tamu terdaftar. Silakan input melalui formulir di samping.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tamus->hasPages())
            <div class="p-4 border-t border-[#EFEAE0] bg-[#FCFAF7]">
                {{ $tamus->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

