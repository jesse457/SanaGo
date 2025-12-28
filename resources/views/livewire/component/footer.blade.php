<!-- ========================================== -->
<!-- LIVESTYLE FOOTER COMPONENT -->
<!-- ========================================== -->
<footer class="bg-slate-50 dark:bg-[#050a15] border-t border-slate-200 dark:border-slate-800 pt-20 pb-10 z-10 relative">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-10 mb-16">
            <!-- Company Info -->
            <div class="col-span-2 lg:col-span-2">
                <a href="#" class="flex items-center gap-2 mb-6">
                    <!-- This image will sit on top of the gradient -->
                    <img class="h-6 w-8" src="{{ asset('images/logo.webp') }}" alt="Sana Go Health System Logo">
                    <span class="text-2xl font-bold text-slate-900 dark:text-white">SanaGo</span>
                </a>
                <p class="text-slate-500 dark:text-slate-400 max-w-sm mb-8 leading-relaxed">{{ __('footer.description') }}</p>
                <!-- Socials -->
                <div class="flex gap-4">
                    <a href="#" class="h-10 w-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 hover:bg-blue-600 hover:text-white transition-all duration-300">
                      <x-hugeicons-facebook-01/>
                        </a>

                         <a href="#" class="h-10 w-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 hover:bg-blue-600 hover:text-white transition-all duration-300">
                      <x-hugeicons-linkedin-01/>
                        </a>
                    <a href="#" class="h-10 w-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 hover:bg-slate-900 hover:text-white dark:hover:bg-white dark:hover:text-black transition-all duration-300">
                         <x-hugeicons-github-01/> </a>
                </div>
            </div>

            <!-- Product Links -->
            <div>
                <h4 class="font-bold text-slate-900 dark:text-white mb-6">{{ __('footer.product') }}</h4>
                <ul class="space-y-4 text-sm text-slate-600 dark:text-slate-400">
                    <li><a wire:navigate href="{{ route('features') }}" class="hover:text-blue-600 transition-colors">{{ __('footer.features') }}</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition-colors">{{ __('footer.solutions') }}</a></li>
                    <li><a wire:navigate href="{{ route('pricing') }}" class="hover:text-blue-600 transition-colors">{{ __('footer.pricing') }}</a></li>
                    <li><a wire:navigate href="{{ route('book-demo') }}" class="hover:text-blue-600 transition-colors">{{ __('footer.book_demo') }}</a></li>
                </ul>
            </div>

            <!-- Company Links -->
            <div>
                <h4 class="font-bold text-slate-900 dark:text-white mb-6">{{ __('footer.company') }}</h4>
                <ul class="space-y-4 text-sm text-slate-600 dark:text-slate-400">
                    <li><a wire:navigate href="{{ route('about') }}" class="hover:text-blue-600 transition-colors">{{ __('footer.about') }}</a></li>
                    <li><a wire:navigate href="{{ route('career') }}" class="hover:text-blue-600 transition-colors">{{ __('footer.careers') }}</a></li>
                    <li><a wire:navigate href="{{ route('blog') }}" class="hover:text-blue-600 transition-colors">{{ __('footer.blog') }}</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition-colors">{{ __('footer.contact') }}</a></li>
                </ul>
            </div>

            <!-- Legal Links -->
            <div>
                <h4 class="font-bold text-slate-900 dark:text-white mb-6">{{ __('footer.legal') }}</h4>
                <ul class="space-y-4 text-sm text-slate-600 dark:text-slate-400">
                    <li><a href="#" class="hover:text-blue-600 transition-colors">{{ __('footer.privacy') }}</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition-colors">{{ __('footer.terms') }}</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition-colors">{{ __('footer.dpa') }}</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="pt-8 border-t border-slate-200 dark:border-slate-800 flex flex-col md:flex-row justify-between items-center gap-4 text-sm">
            <p class="text-slate-500 dark:text-slate-400">&copy; {{ date('Y') }} SanaGo. {{ __('footer.rights_reserved') }}</p>
            <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 px-3 py-1 rounded-full font-medium">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                {{ __('footer.systems_normal') }}
            </div>
        </div>
    </div>
</footer>
