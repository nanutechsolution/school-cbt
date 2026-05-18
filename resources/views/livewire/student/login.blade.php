<div class="flex flex-col justify-center min-h-full py-12 sm:px-6 lg:px-8 bg-slate-900">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-3xl font-extrabold text-center text-white tracking-tight">
            PORTAL CBT SEKOLAH
        </h2>
        <p class="mt-2 text-sm text-center text-slate-400">
            Silakan masuk menggunakan NIS yang terdaftar
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="px-4 py-8 bg-white shadow-2xl sm:rounded-xl sm:px-10">
            <form wire:submit.prevent="login" class="space-y-6">
                <div>
                    <label for="username" class="block text-sm font-medium text-slate-700">Username / NIS</label>
                    <div class="mt-1">
                        <input wire:model="username" id="username" type="text" autocomplete="username" required 
                            class="block w-full px-3 py-2 border rounded-md shadow-sm border-slate-300 placeholder-slate-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    @error('username') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                    <div class="mt-1">
                        <input wire:model="password" id="password" type="password" autocomplete="current-password" required 
                            class="block w-full px-3 py-2 border rounded-md shadow-sm border-slate-300 placeholder-slate-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </div>

                <div>
                    <button type="submit" 
                        class="flex justify-center w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        Masuk Sistem
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>