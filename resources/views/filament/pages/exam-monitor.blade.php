<x-filament-panels::page>
    <div class="space-y-6" x-data="liveMonitor()">
        <!-- Panel Selector Sesi Ujian -->
        <div class="p-6 bg-white border border-slate-200 rounded-2xl shadow-sm dark:bg-slate-900 dark:border-slate-800">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="w-full md:w-1/2">
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Pilih Jadwal Ujian Yang Sedang Dipantau:</label>
                    <select wire:model.live="selectedExamId" wire:change="loadParticipants" 
                        class="w-full rounded-xl border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-950 focus:border-primary-500 focus:ring-primary-500 text-slate-800 dark:text-white font-medium py-2.5 shadow-sm">
                        <option value="">-- Pilih Ujian Aktif --</option>
                        @foreach($exams as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-center gap-3">
                    <span class="h-3 w-3 bg-emerald-500 rounded-full animate-ping"></span>
                    <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Sistem Live Terkoneksi</p>
                </div>
            </div>
        </div>

        <!-- Tabel Monitoring Real-time -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm dark:bg-slate-900 dark:border-slate-800 overflow-hidden" 
             wire:poll.10s="loadParticipants"> {{-- Fallback auto-poll setiap 10 detik --}}
            
            <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Status Kehadiran & Progres Siswa</h3>
                <span class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-3 py-1 rounded-full text-xs font-bold">
                    Total Peserta: {{ count($participants) }}
                </span>
            </div>

            @if(empty($participants))
                <div class="text-center py-20 text-slate-400">
                    <svg class="mx-auto h-16 w-16 mb-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <p class="font-bold text-slate-600 dark:text-slate-300">Belum ada siswa yang masuk ke dalam ruang ujian ini.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-xs font-bold uppercase tracking-wider text-slate-500">
                                <th class="p-4">Nama Lengkap</th>
                                <th class="p-4">Kelas</th>
                                <th class="p-4">Status</th>
                                <th class="p-4">Progres Soal</th>
                                <th class="p-4 text-center">Pelanggaran</th>
                                <th class="p-4">Aksi Penyelamatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-sm">
                            @foreach($participants as $p)
                                @php
                                    $statusColor = 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
                                    $statusLabel = 'Mengerjakan';
                                    if ($p['status'] === 'submitted') {
                                        $statusColor = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400';
                                        $statusLabel = 'Selesai';
                                    } elseif ($p['status'] === 'suspended') {
                                        $statusColor = 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400';
                                        $statusLabel = 'DITANGGUHKAN';
                                    }
                                @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                                    <td class="p-4 font-semibold text-slate-900 dark:text-white">
                                        {{ $p['name'] }}
                                        <span class="block text-xs font-mono text-slate-400 mt-0.5">IP: {{ $p['ip_address'] ?? '0.0.0.0' }}</span>
                                    </td>
                                    <td class="p-4 text-slate-600 dark:text-slate-300 font-medium">{{ $p['classroom'] }}</td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $statusColor }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="p-4 w-1/4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex-1 bg-slate-200 dark:bg-slate-800 rounded-full h-2">
                                                <div class="bg-primary-600 h-2 rounded-full transition-all duration-500" style="width: {{ $p['progress'] }}%"></div>
                                            </div>
                                            <span class="text-xs font-bold text-slate-600 dark:text-slate-400">{{ $p['progress'] }}% ({{ $p['answered_count'] }}/{{ $p['total_questions'] }})</span>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($p['cheat_count'] > 0)
                                            <span class="bg-red-500 text-white font-extrabold px-3 py-1 rounded-full text-xs animate-pulse">
                                                ⚠️ {{ $p['cheat_count'] }} Pelanggaran
                                            </span>
                                        @else
                                            <span class="text-emerald-500 font-bold text-xs">Aman</span>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        <button wire:click="resetAttempt({{ $p['attempt_id'] }})" 
                                            wire:confirm="PERINGATAN! Melakukan reset akan menghapus seluruh data sesi pengerjaan siswa ini agar ia bisa memulai ulang dari awal. Lanjutkan?"
                                            class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-sm transition">
                                            Reset Sesi
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Alpine Websocket Echo Controller -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('liveMonitor', () => ({
                init() {
                    // Jika Reverb / Echo terpasang di client-side, dengerin channel live update
                    if (typeof window.Echo !== 'undefined') {
                        window.Echo.channel(`exam-monitoring.${@this.get('selectedExamId')}`)
                            .listen('.StateChanged', (e) => {
                                // Refresh data Livewire secara real-time saat ada pesan masuk
                                @this.call('loadParticipants');
                            });
                    }
                }
            }));
        });
    </script>
</x-filament-panels::page>