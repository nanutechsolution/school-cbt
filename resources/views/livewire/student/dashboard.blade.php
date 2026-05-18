<div class="min-h-full">
    <nav class="bg-white shadow-sm border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-slate-900 tracking-tight">CBT PORTAL SISWA</span>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold text-slate-700">{{ $student->user->name }}</p>
                        <p class="text-xs text-slate-500">Kelas: {{ $student->classroom->name }}</p>
                    </div>
                    <form action="{{ route('student.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <div class="md:grid md:grid-cols-3 md:gap-6">

            <div class="md:col-span-1 mb-6 md:mb-0">
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-4 border-b pb-2">Informasi Peserta</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="text-slate-500 block">Nama Lengkap</span>
                            <span class="font-semibold text-slate-800">{{ $student->user->name }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">NIS</span>
                            <span class="font-mono bg-slate-100 px-2 py-0.5 rounded text-slate-700">{{ $student->nis }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Ruang / Sesi</span>
                            <span class="font-semibold text-slate-800">{{ $student->room?->name ?? '-' }} / {{ $student->examSession?->name ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-4 border-b pb-2">Daftar Ujian Hari Ini</h3>

                    @error('token')
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md text-sm">
                        {{ $message }}
                    </div>
                    @enderror
                    @if (session()->has('cheat_error'))
                    <div class="mb-4 bg-amber-50 border-2 border-amber-500 text-amber-900 px-4 py-4 rounded-xl text-sm font-semibold shadow-sm animate-pulse">
                        <div class="flex items-center space-x-2">
                            ⚠️ <span>{{ session('cheat_error') }}</span>
                        </div>
                    </div>
                    @endif
                    @if($activeExams->isEmpty())
                    <div class="text-center py-12 text-slate-500">
                        <svg class="mx-auto h-12 w-12 text-slate-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <p class="font-medium text-slate-600">Tidak ada jadwal ujian yang aktif saat ini.</p>
                        <p class="text-xs text-slate-400 mt-1">Jadwal ujian akan muncul otomatis jika sudah memasuki jam pengerjaan.</p>
                    </div>
                    @else
                    <div class="space-y-4">
                        @foreach($activeExams as $exam)
                        @php
                        $attempt = $attempts->get($exam->id);
                        $isSubmitted = $attempt && $attempt->status === 'submitted';
                        @endphp
                        <div class="border border-slate-200 rounded-lg p-4 hover:border-blue-300 transition bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                            <div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mb-1">
                                    {{ $exam->questionBank->subject->name }}
                                </span>
                                <h4 class="text-base font-bold text-slate-800">{{ $exam->name }}</h4>
                                <p class="text-xs text-slate-500 mt-1">
                                    Durasi: <span class="font-semibold text-slate-700">{{ $exam->duration }} Menit</span>
                                    | Batas Akses: <span class="font-semibold text-slate-700">{{ $exam->end_time->format('H:i') }} WITA</span>
                                </p>
                            </div>

                            <div class="flex items-center space-x-2 w-full sm:w-auto">
                                @if($isSubmitted)
                                <span class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-green-700 bg-green-100 w-full text-center justify-center">
                                    Selesai Dikerjakan
                                </span>
                                @else
                                <div class="flex space-x-2 w-full">
                                    <input wire:model.defer="tokenInput" type="text" placeholder="TOKEN" maxlength="6"
                                        class="uppercase text-center block w-24 px-2 py-2 border rounded-md shadow-sm border-slate-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-mono tracking-widest">

                                    <button wire:click="startExam({{ $exam->id }})"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none transition flex-1 sm:flex-initial justify-center">
                                        Mulai
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </main>
</div>