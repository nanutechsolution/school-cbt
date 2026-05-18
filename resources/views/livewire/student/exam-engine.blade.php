<div class="h-screen flex flex-col bg-slate-100 select-none" x-data="cbtTimer({{ $timeLeftSeconds }})">
    <header class="bg-slate-900 text-white h-14 px-6 flex items-center justify-between shadow-md shrink-0">
        <div class="flex items-center space-x-3">
            <span class="font-bold tracking-wider text-sm bg-blue-600 px-2.5 py-1 rounded">SOAL NO. {{ $currentQuestionIndex + 1 }}</span>
        </div>

        <div class="flex items-center space-x-2 font-mono bg-slate-800 px-4 py-1.5 rounded-md border border-slate-700">
            <svg class="h-5 w-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-lg font-bold text-amber-400" x-text="formatTime()">00:00:00</span>
        </div>

        <div>
            <button x-on:click="if(confirm('Apakah Anda yakin ingin mengakhiri ujian? Jawaban tidak bisa diubah lagi.')) { $wire.submitExam() }"
                class="bg-red-600 hover:bg-red-700 text-white text-xs font-bold px-4 py-2 rounded shadow transition">
                Hentikan Ujian
            </button>
        </div>
    </header>

    <div class="flex-1 flex overflow-hidden">

        <div class="flex-1 overflow-y-auto p-6 sm:p-8 bg-white border-r border-slate-200">
            <div class="max-w-3xl mx-auto">

                <div class="prose max-w-none text-slate-800 text-base leading-relaxed mb-8">
                    {!! $question->content !!}
                </div>

                @if($question->type->value === 'multiple_choice')
                <div class="space-y-3">
                    @foreach($question->options as $index => $option)
                    @php $letter = chr(65 + $index); @endphp
                    <label class="flex items-start p-4 border rounded-xl cursor-pointer transition shadow-sm
                                {{ $selectedOptionId === $option->id ? 'border-blue-600 bg-blue-50 text-blue-900 font-semibold' : 'border-slate-200 hover:bg-slate-50 text-slate-700' }}">
                        <input type="radio" name="answer_option" value="{{ $option->id }}" wire:model.live="selectedOptionId" wire:click="saveCurrentAnswer" class="sr-only">
                        <span class="h-6 w-6 shrink-0 flex items-center justify-center rounded-lg border text-xs mr-3 font-bold
                                    {{ $selectedOptionId === $option->id ? 'bg-blue-600 border-blue-600 text-white' : 'bg-slate-100 border-slate-300 text-slate-600' }}">
                            {{ $letter }}
                        </span>
                        <div class="prose prose-sm max-w-none text-current">
                            {!! $option->content !!}
                        </div>
                    </label>
                    @endforeach
                </div>
                @endif

                @if($question->type->value === 'essay')
                <div class="mt-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tulis Jawaban Uraian Anda di Sini:</label>
                    <textarea wire:model.blur="essayAnswerText" wire:change="saveCurrentAnswer" rows="6"
                        class="block w-full p-4 border rounded-xl shadow-sm border-slate-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-none placeholder-slate-400"
                        placeholder="Ketik jawaban lengkap Anda..."></textarea>
                </div>
                @endif

            </div>
        </div>

        <div class="w-80 overflow-y-auto bg-slate-50 p-6 shrink-0 hidden md:block">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Navigasi Soal</h3>
            <div class="grid grid-cols-5 gap-2">
                @foreach($questions as $index => $q)
                @php
                $hasAnswer = $savedAnswers->has($q->id);
                $isDoubtful = $hasAnswer && $savedAnswers->get($q->id)->is_doubtful;

                // Menentukan warna background box nomor
                $bgColor = 'bg-white text-slate-700 border-slate-200 hover:border-slate-400';
                if ($currentQuestionIndex === $index) {
                $bgColor = 'bg-slate-900 text-white border-slate-900 ring-2 ring-offset-2 ring-slate-800';
                } elseif ($isDoubtful) {
                $bgColor = 'bg-amber-500 text-white border-amber-500';
                } elseif ($hasAnswer) {
                $bgColor = 'bg-emerald-600 text-white border-emerald-600';
                }
                @endphp
                <button wire:click="changeQuestion({{ $index }})"
                    class="h-10 text-sm font-bold rounded-lg border flex items-center justify-center transition shadow-sm {{ $bgColor }}">
                    {{ $index + 1 }}
                </button>
                @endforeach
            </div>

            <div class="mt-8 border-t border-slate-200 pt-4 space-y-2 text-xs text-slate-500">
                <div class="flex items-center"><span class="h-3 w-3 bg-white border border-slate-300 rounded mr-2"></span> Belum Diisi</div>
                <div class="flex items-center"><span class="h-3 w-3 bg-emerald-600 rounded mr-2"></span> Sudah Terjawab</div>
                <div class="flex items-center"><span class="h-3 w-3 bg-amber-500 rounded mr-2"></span> Ragu-Ragu</div>
                <div class="flex items-center"><span class="h-3 w-3 bg-slate-900 rounded mr-2"></span> Posisi Aktif</div>
            </div>
        </div>
    </div>

    <footer class="bg-white border-t border-slate-200 h-16 px-6 flex items-center justify-between shrink-0">
        <button wire:click="changeQuestion({{ $currentQuestionIndex - 1 }})" {{ $currentQuestionIndex === 0 ? 'disabled' : '' }}
            class="inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed shadow-sm transition">
            &larr; Soal Sebelumnya
        </button>

        <button wire:click="toggleDoubtful"
            class="px-4 py-2 text-sm font-semibold rounded-lg border shadow-sm transition
                {{ $isDoubtfulCheck ? 'bg-amber-500 text-white border-amber-500' : 'bg-white text-amber-600 border-amber-300 hover:bg-amber-50' }}">
            {{ $isDoubtfulCheck ? '✓ Ragu-Ragu Aktif' : '🛈 Tandai Ragu-Ragu' }}
        </button>

        @if($currentQuestionIndex === count($questions) - 1)
        <button x-on:click="if(confirm('Apakah Anda yakin ingin mengakhiri ujian?')) { $wire.submitExam() }"
            class="px-5 py-2 text-sm font-bold rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition">
            Selesai Ujian
        </button>
        @else
        <button wire:click="changeQuestion({{ $currentQuestionIndex + 1 }})"
            class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg text-white bg-slate-800 hover:bg-slate-700 shadow-sm transition">
            Soal Berikutnya &rarr;
        </button>
        @endif
    </footer>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cbtTimer', (initialSeconds) => ({
                secondsLeft: initialSeconds,
                init() {
                    let timer = setInterval(() => {
                        if (this.secondsLeft <= 0) {
                            clearInterval(timer);
                            alert('Waktu ujian Anda telah habis! Sistem akan mengumpulkan lembar jawaban Anda otomatis.');
                            @this.call('submitExam');
                        } else {
                            this.secondsLeft--;
                        }
                    }, 1000);
                },
                formatTime() {
                    let h = Math.floor(this.secondsLeft / 3600).toString().padStart(2, '0');
                    let m = Math.floor((this.secondsLeft % 3600) / 60).toString().padStart(2, '0');
                    let s = (this.secondsLeft % 60).toString().padStart(2, '0');
                    return `${h}:${m}:${s}`;
                }
            }));
        });
    </script>

    <div x-data="{ open: false, count: 0, max: 0 }"
        x-on:show-cheat-warning.window="open = true; count = $event.detail.count; max = $event.detail.max"
        x-show="open"
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-950/80 p-4"
        x-cloak>
        <div class="bg-white rounded-2xl max-w-md w-full p-6 text-center shadow-2xl border-2 border-red-500 animate-bounce">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">PERINGATAN PELANGGARAN!</h3>
            <p class="text-sm text-slate-600 mt-2">
                Terdeteksi usaha meninggalkan layar ujian (pindah tab / membuka aplikasi lain).
            </p>
            <div class="my-4 bg-red-50 p-3 rounded-lg border border-red-200">
                <p class="text-xs font-bold text-red-700">
                    Pelanggaran Ke: <span class="text-lg" x-text="count">0</span> dari <span x-text="max">0</span> Batas Maksimal
                </p>
            </div>
            <p class="text-xs text-slate-400 mb-4">
                Jika Anda melewati batas maksimal, sistem akan otomatis mengunci jawaban Anda dan mengeluarkan Anda dari ujian!
            </p>
            <button x-on:click="open = false" class="w-full bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold py-2 rounded-xl transition">
                Saya Mengerti, Kembali ke Ujian
            </button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // 1. Matikan Klik Kanan (Context Menu)
            document.addEventListener('contextmenu', e => e.preventDefault());

            // 2. Matikan Inspect Element Shortcuts & Copy Paste Keys
            document.addEventListener('keydown', (e) => {
                // Blokir F12
                if (e.key === 'F12') e.preventDefault();

                // Blokir Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+Shift+C
                if (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J' || e.key === 'C')) e.preventDefault();

                // Blokir Ctrl+U (View Source)
                if (e.ctrlKey && e.key === 'U') e.preventDefault();

                // Blokir Ctrl+C dan Ctrl+V (Copy-Paste)
                if (e.ctrlKey && (e.key === 'c' || e.key === 'v' || e.key === 'C' || e.key === 'V')) e.preventDefault();
            });

            // 3. PONDASI ANTI TAB SWITCHING (Page Visibility API & Window Blur)
            let isTabActive = true;

            const triggerCheatingLog = () => {
                if (isTabActive) {
                    isTabActive = false;
                    // Panggil fungsi backend Livewire secara asinkron tanpa memuat ulang komponen utama
                    @this.call('logViolation');

                    // Set timeout pengunci state agar tidak memicu double log dalam waktu mili-second bersamaan
                    setTimeout(() => {
                        isTabActive = true;
                    }, 1500);
                }
            };

            // Deteksi jika tab di-switch atau browser di-minimize
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'hidden') {
                    triggerCheatingLog();
                }
            });

            // Deteksi jika kursor keluar layar atau fokus beralih ke aplikasi lain (Notepad, WA, dll)
            window.addEventListener('blur', () => {
                triggerCheatingLog();
            });
        });
    </script>
</div>