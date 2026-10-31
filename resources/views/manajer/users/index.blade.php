@extends('layouts.app')

@section('title', 'Manajemen Akun Staf (Manajer)')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    <!-- Form Tambah Staf Baru (4 Cols) -->
    <div class="lg:col-span-4 bg-white p-6 rounded-3xl border border-[#EFEAE0] shadow-sm h-fit">
        <div class="flex items-center space-x-2.5 mb-2">
            <div class="w-7 h-7 rounded-full bg-[#0A1526] text-white flex items-center justify-center">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <h2 class="text-lg font-bold text-[#0A1526]">Tambah Akun Staf</h2>
        </div>
        <p class="text-xs text-[#5A6578] mb-5">Daftarkan akun staf baru dan tentukan hak akses peran.</p>

        <form action="{{ route('manajer.users.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Peran Hak Akses *</label>
                <select name="peran" required class="w-full px-3.5 py-2.5 bg-[#FCFAF7] border border-[#EFEAE0] rounded-2xl text-xs font-bold text-[#0A1526] focus:ring-2 focus:ring-[#0A1526]">
                    <option value="Resepsionis">Resepsionis (Meja Depan)</option>
                    <option value="Petugas Kebersihan">Petugas Kebersihan</option>
                    <option value="Manajer">Manajer Hotel (Penuh)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Nomor Identitas (NIK/Paspor) *</label>
                <input type="text" name="no_identitas" value="{{ old('no_identitas') }}" required placeholder="Contoh: 3201010101010010"
                    class="w-full px-3.5 py-2.5 bg-[#FCFAF7] border border-[#EFEAE0] rounded-2xl text-xs font-medium text-[#0A1526] focus:ring-2 focus:ring-[#0A1526]">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Nama Lengkap *</label>
                <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Nama staf hotel"
                    class="w-full px-3.5 py-2.5 bg-[#FCFAF7] border border-[#EFEAE0] rounded-2xl text-xs font-medium text-[#0A1526] focus:ring-2 focus:ring-[#0A1526]">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Alamat Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="staf@hotel.com"
                    class="w-full px-3.5 py-2.5 bg-[#FCFAF7] border border-[#EFEAE0] rounded-2xl text-xs font-medium text-[#0A1526] focus:ring-2 focus:ring-[#0A1526]">
            </div>

            <div x-data="{ showPass: false }">
                <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Kata Sandi Awal *</label>
                <div class="relative">
                    <input :type="showPass ? 'text' : 'password'" name="password" required placeholder="Minimal 6 karakter"
                        class="w-full pl-3.5 pr-10 py-2.5 bg-[#FCFAF7] border border-[#EFEAE0] rounded-2xl text-xs font-medium text-[#0A1526] focus:ring-2 focus:ring-[#0A1526]">
                    <button type="button" @click="showPass = !showPass"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#5A6578] hover:text-[#0A1526] focus:outline-none transition"
                        :title="showPass ? 'Sembunyikan kata sandi' : 'Lihat kata sandi'">
                        <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="showPass" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                        </svg>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Nomor HP</label>
                <input type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="0812xxxxxxxx"
                    class="w-full px-3.5 py-2.5 bg-[#FCFAF7] border border-[#EFEAE0] rounded-2xl text-xs font-medium text-[#0A1526] focus:ring-2 focus:ring-[#0A1526]">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Alamat Rumah</label>
                <textarea name="alamat" rows="2" placeholder="Alamat tinggal staf"
                    class="w-full px-3.5 py-2.5 bg-[#FCFAF7] border border-[#EFEAE0] rounded-2xl text-xs font-medium text-[#0A1526] focus:ring-2 focus:ring-[#0A1526]">{{ old('alamat') }}</textarea>
            </div>

            <button type="submit" class="w-full bg-[#0A1526] hover:bg-black text-white font-extrabold py-3 px-4 rounded-full text-xs shadow transition mt-2">
                Simpan Akun Staf
            </button>
        </form>
    </div>

    <!-- Tabel Daftar Staf (8 Cols) -->
    <div class="lg:col-span-8 bg-white rounded-3xl border border-[#EFEAE0] shadow-sm overflow-hidden flex flex-col">
        <div class="px-6 py-5 border-b border-[#EFEAE0] flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <div class="w-7 h-7 rounded-full bg-[#0A1526] text-white flex items-center justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-[#0A1526]">Daftar Staf & Hak Akses</h2>
                    <p class="text-xs text-[#5A6578]">Kelola akun staf aktif yang memiliki otorisasi masuk ke sistem.</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto flex-1">
            <table class="w-full text-left text-xs text-[#1c1917]">
                <thead class="bg-[#FCFAF7] uppercase text-[10px] tracking-wider text-[#5A6578] font-extrabold border-b border-[#EFEAE0]">
                    <tr>
                        <th class="px-6 py-3.5">Nama & Kontak</th>
                        <th class="px-6 py-3.5">Peran Jabatan</th>
                        <th class="px-6 py-3.5">No. Identitas</th>
                        <th class="px-6 py-3.5">Status Akun</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EFEAE0]">
                    @forelse($users as $user)
                        <tr class="hover:bg-[#FCFAF7]/60 transition">
                            <td class="px-6 py-3.5">
                                <div class="font-extrabold text-[#0A1526]">{{ $user->nama }}</div>
                                <div class="text-[10px] text-[#5A6578]">{{ $user->email }} | {{ $user->no_hp ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-3.5">
                                @if($user->peran === 'Manajer')
                                    <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-[#e3d6f8] text-[#5b21b6]">Manajer</span>
                                @elseif($user->peran === 'Resepsionis')
                                    <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-[#d4e5f9] text-[#1e40af]">Resepsionis</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-[#fde8cf] text-[#7c2d12]">Petugas Kebersihan</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 font-mono font-bold text-[#0A1526]">
                                {{ $user->no_identitas }}
                            </td>
                            <td class="px-6 py-3.5">
                                @if($user->aktif)
                                    <span class="inline-flex items-center text-[11px] font-bold text-[#065f46]">
                                        <span class="w-2 h-2 rounded-full bg-[#059669] mr-1.5"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-[11px] font-bold text-[#991b1b]">
                                        <span class="w-2 h-2 rounded-full bg-[#dc2626] mr-1.5"></span> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 text-right">
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('manajer.users.toggle', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-[11px] font-bold px-3 py-1 rounded-full border transition {{ $user->aktif ? 'text-[#991b1b] border-[#f8b4ae] hover:bg-[#fdd8d5]' : 'text-[#065f46] border-[#a9ebd0] hover:bg-[#d6f5e8]' }}">
                                            {{ $user->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[11px] text-[#5A6578] italic font-semibold">Akun Anda</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-[#5A6578]">
                                Belum ada data staf terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-[#EFEAE0] bg-[#FCFAF7]">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

