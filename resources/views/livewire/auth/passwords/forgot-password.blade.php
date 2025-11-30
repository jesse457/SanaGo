<div class="min-h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">
  @if (session('error') || $error)
    <div role="alert" class="max-w-md mx-auto mt-6 px-4">
      <div class="rounded-md bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 p-3 text-red-700 dark:text-red-200 text-sm shadow-sm">
        {{ session('error') ?? $error }}
      </div>
    </div>
  @endif

  @if ($status)
    <div role="alert" class="max-w-md mx-auto mt-6 px-4">
      <div class="rounded-md bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 p-3 text-green-700 dark:text-green-200 text-sm shadow-sm">
        {{ $status }}
      </div>
    </div>
  @endif

  <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <main class="w-full max-w-md">
      <section class="bg-white/90 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-2xl p-8 sm:p-10 border border-gray-200 dark:border-gray-700">
        <header class="flex flex-col items-center mb-6">
          <a href="#" class="flex items-center space-x-3 mb-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-sm">
            <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 text-white font-bold shadow-sm">SG</div>
            <div class="text-left">
              <span class="block text-lg font-bold leading-none">sanaGo</span>
              <span class="block text-xs text-gray-500 dark:text-gray-300 -mt-0.5">AIHMS</span>
            </div>
          </a>

          <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white text-center">Forgot Password</h1>
          <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 text-center">Enter your email to receive a password reset link.</p>
        </header>

        <form wire:submit.prevent="sendResetLink" class="space-y-5" novalidate>
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email address</label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-400">
                <x-heroicon-o-envelope class="w-5 h-5" />
              </span>
              <input
                id="email"
                name="email"
                type="email"
                wire:model="email"
                required
                autocomplete="email"
                placeholder="you@example.com"
                class="block w-full pl-12 pr-4 py-3 border rounded-lg shadow-sm placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-500 transition-colors duration-200" />
            </div>
            @error('email')
              <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <button
              type="submit"
              wire:loading.attr="disabled"
              aria-live="polite"
              class="w-full inline-flex justify-center items-center gap-3 px-6 py-3 border border-transparent text-base font-semibold rounded-xl shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-transform duration-200 transform hover:-translate-y-0.5 disabled:opacity-60 disabled:cursor-not-allowed dark:bg-blue-700 dark:hover:bg-blue-800 dark:focus:ring-offset-gray-800">
              <span wire:loading.remove wire:target="sendResetLink">Email Password Reset Link</span>
              <span wire:loading wire:target="sendResetLink" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                Sending...
              </span>
            </button>
          </div>
        </form>

        <footer class="mt-6 text-center">
          <p class="text-sm text-gray-600 dark:text-gray-400">
            Remembered your password?
            <a href="{{ route('login') }}"
               class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-semibold transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded">
              Sign in
            </a>
          </p>
        </footer>
      </section>
    </main>
  </div>
</div>
