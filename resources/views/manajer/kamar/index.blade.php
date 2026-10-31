@extends('layouts.app')

@section('title', 'Master Data Kamar (Manajer)')

@section('content')
<div x-data="{ 
    modalTambah: false, 
    modalEdit: false, 
    kamarEdit: null 
}" class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-[#0A1526] tracking-tight">Master Data Kamar Hotel</h1>
            <p class="text-xs sm:text-sm text-[#5A6578] mt-0.5">Kelola tarif sewa per malam, tipe kamar, serta status pemeliharaan fasilitas.</p>
        </div>
        <button @click="modalTambah = true" class="bg-[#0A1526] hover:bg-black text-white text-xs font-bold py-2.5 px-5 rounded-full shadow flex items-center space-x-1.5 transition">
            <span>+ Tambah Kamar Baru</span>
        </button>
    </div>

    <!-- Alert Aturan Bisnis Manajer (Pastel Blue) -->
    <div class="bg-[#d4e5f9] border border-[#b6d4f6] rounded-3xl p-4 text-xs text-[#1e40af] flex items-center space-x-3 shadow-2xs">
        <span class="w-6 h-6 rounded-full bg-white flex items-center justify-center font-bold text-xs flex-shrink-0">
            <svg class="w-3.5 h-3.5 text-[#1e40af]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </span>
        <div class="leading-relaxed">
            <strong>Aturan Bisnis:</strong> Nomor kamar wajib unik. Tarif harga sewa per malam harus lebih dari 0. Hanya peran <strong>Manajer</strong> yang berhak mengubah dan menghapus data kamar.
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-3xl border border-[#EFEAE0] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-[#1c1917]">
                <thead class="bg-[#FCFAF7] uppercase text-[10px] tracking-wider text-[#5A6578] font-extrabold border-b border-[#EFEAE0]">
                    <tr>
                        <th class="px-6 py-4">Nomor Kamar</th>
                        <th class="px-6 py-4">Tipe Kamar</th>
                        <th class="px-6 py-4">Tarif Sewa / Malam</th>
                        <th class="px-6 py-4">Status Operasional</th>
                        <th class="px-6 py-4">Status Kebersihan</th>
                        <th class="px-6 py-4 text-right">Aksi Manajerial</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EFEAE0]">
                    @forelse($kamars as $k)
                        <tr class="hover:bg-[#FCFAF7]/60 transition">
                            <td class="px-6 py-4 font-bold text-base text-[#0A1526]">
                                Kamar {{ $k->nomor_kamar }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-[#0A1526]">
                                {{ $k->tipe }}
                            </td>
                            <td class="px-6 py-4 font-extrabold text-[#0A1526]">
                                Rp {{ number_format($k->harga_per_malam, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($k->status === 'Tersedia')
                                    <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-[#d4e5f9] text-[#1e40af]">Tersedia</span>
                                @elseif($k->status === 'Terisi')
                                    <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-[#fdd8d5] text-[#991b1b]">Terisi</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-[#fae8d8] text-[#854d0e]">Perawatan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($k->status_kebersihan === 'Bersih')
                                    <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-[#d6f5e8] text-[#065f46]">Bersih</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-[#fde8cf] text-[#7c2d12]">Kotor</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                <button @click="modalEdit = true; kamarEdit = {{ $k->toJson() }}" class="text-xs font-bold text-[#0A1526] hover:underline px-2 py-1">
                                    Ubah
                                </button>
                                <form action="{{ route('manajer.kamar.destroy', $k->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Hapus kamar {{ $k->nomor_kamar }}?')" class="text-xs font-bold text-[#991b1b] hover:underline px-2 py-1">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-[#5A6578]">
                                Belum ada data kamar hotel.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kamars->hasPages())
            <div class="p-4 border-t border-[#EFEAE0] bg-[#FCFAF7]">
                {{ $kamars->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL TAMBAH KAMAR -->
    <div x-show="modalTambah" class="fixed inset-0 z-50 overflow-y-auto bg-black/40 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-[#FCFAF7] rounded-3xl max-w-md w-full p-7 shadow-2xl border border-[#dec5b5]" @click.away="modalTambah = false">
            <div class="flex items-center justify-between pb-3 border-b border-[#EFEAE0]">
                <h3 class="text-lg font-bold text-[#0A1526]">Tambah Kamar Baru</h3>
                <button @click="modalTambah = false" class="w-7 h-7 rounded-full bg-white border border-[#EFEAE0] text-[#5A6578] hover:text-[#0A1526] font-bold text-sm flex items-center justify-center">&times;</button>
            </div>

            <form action="{{ route('manajer.kamar.store') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Nomor Kamar (Unik) *</label>
                    <input type="text" name="nomor_kamar" required placeholder="Contoh: 104"
                        class="w-full px-3.5 py-2.5 bg-white border border-[#EFEAE0] rounded-2xl text-xs font-medium focus:ring-2 focus:ring-[#0A1526]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Tipe Kamar *</label>
                    <select name="tipe" required class="w-full px-3.5 py-2.5 bg-white border border-[#EFEAE0] rounded-2xl text-xs font-medium focus:ring-2 focus:ring-[#0A1526]">
                        <option value="Standar">Standar</option>
                        <option value="Deluxe">Deluxe</option>
                        <option value="Suite">Suite</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Harga Sewa per Malam (Rp) *</label>
                    <input type="number" name="harga_per_malam" min="1" step="1000" required placeholder="Contoh: 350000"
                        class="w-full px-3.5 py-2.5 bg-white border border-[#EFEAE0] rounded-2xl text-xs font-bold focus:ring-2 focus:ring-[#0A1526]">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Status Operasional *</label>
                        <select name="status" required class="w-full px-3.5 py-2 bg-white border border-[#EFEAE0] rounded-2xl text-xs font-medium focus:ring-2 focus:ring-[#0A1526]">
                            <option value="Tersedia">Tersedia</option>
                            <option value="Perawatan">Perawatan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Status Kebersihan *</label>
                        <select name="status_kebersihan" required class="w-full px-3.5 py-2 bg-white border border-[#EFEAE0] rounded-2xl text-xs font-medium focus:ring-2 focus:ring-[#0A1526]">
                            <option value="Bersih">Bersih</option>
                            <option value="Kotor">Kotor</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 flex justify-end space-x-2 border-t border-[#EFEAE0]">
                    <button type="button" @click="modalTambah = false" class="px-4 py-2 text-xs text-[#5A6578] font-bold hover:bg-white rounded-full">Batal</button>
                    <button type="submit" class="px-5 py-2.5 text-xs bg-[#0A1526] hover:bg-black text-white rounded-full font-bold shadow">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT KAMAR -->
    <div x-show="modalEdit" class="fixed inset-0 z-50 overflow-y-auto bg-black/40 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-[#FCFAF7] rounded-3xl max-w-md w-full p-7 shadow-2xl border border-[#dec5b5]" @click.away="modalEdit = false">
            <div class="flex items-center justify-between pb-3 border-b border-[#EFEAE0]">
                <h3 class="text-lg font-bold text-[#0A1526]">Ubah Data Kamar</h3>
                <button @click="modalEdit = false" class="w-7 h-7 rounded-full bg-white border border-[#EFEAE0] text-[#5A6578] hover:text-[#0A1526] font-bold text-sm flex items-center justify-center">&times;</button>
            </div>

            <template x-if="kamarEdit">
                <form :action="'/manajer/kamar/' + kamarEdit.id" method="POST" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Nomor Kamar *</label>
                        <input type="text" name="nomor_kamar" x-model="kamarEdit.nomor_kamar" required
                            class="w-full px-3.5 py-2.5 bg-white border border-[#EFEAE0] rounded-2xl text-xs font-medium focus:ring-2 focus:ring-[#0A1526]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Tipe Kamar *</label>
                        <select name="tipe" x-model="kamarEdit.tipe" required class="w-full px-3.5 py-2.5 bg-white border border-[#EFEAE0] rounded-2xl text-xs font-medium focus:ring-2 focus:ring-[#0A1526]">
                            <option value="Standar">Standar</option>
                            <option value="Deluxe">Deluxe</option>
                            <option value="Suite">Suite</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Harga Sewa per Malam (Rp) *</label>
                        <input type="number" name="harga_per_malam" x-model="kamarEdit.harga_per_malam" min="1" step="1000" required
                            class="w-full px-3.5 py-2.5 bg-white border border-[#EFEAE0] rounded-2xl text-xs font-bold focus:ring-2 focus:ring-[#0A1526]">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Status Operasional *</label>
                            <select name="status" x-model="kamarEdit.status" required class="w-full px-3.5 py-2 bg-white border border-[#EFEAE0] rounded-2xl text-xs font-medium focus:ring-2 focus:ring-[#0A1526]">
                                <option value="Tersedia">Tersedia</option>
                                <option value="Terisi">Terisi</option>
                                <option value="Perawatan">Perawatan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Status Kebersihan *</label>
                            <select name="status_kebersihan" x-model="kamarEdit.status_kebersihan" required class="w-full px-3.5 py-2 bg-white border border-[#EFEAE0] rounded-2xl text-xs font-medium focus:ring-2 focus:ring-[#0A1526]">
                                <option value="Bersih">Bersih</option>
                                <option value="Kotor">Kotor</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end space-x-2 border-t border-[#EFEAE0]">
                        <button type="button" @click="modalEdit = false" class="px-4 py-2 text-xs text-[#5A6578] font-bold hover:bg-white rounded-full">Batal</button>
                        <button type="submit" class="px-5 py-2.5 text-xs bg-[#0A1526] hover:bg-black text-white rounded-full font-bold shadow">Perbarui</button>
                    </div>
                </form>
            </template>
        </div>
    </div>

</div>
@endsection

