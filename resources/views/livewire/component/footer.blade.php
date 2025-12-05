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
                    <img class="h-6 w-8" src="{{ asset('images/logo.png') }}" alt="Sana Go Health System Logo">
                    <span class="text-2xl font-bold text-slate-900 dark:text-white">SanaGo</span>
                </a>
                <p class="text-slate-500 dark:text-slate-400 max-w-sm mb-8 leading-relaxed">{{ __('footer.description') }}</p>
                <!-- Socials -->
                <div class="flex gap-4">
                    <a href="#" class="h-10 w-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 hover:bg-blue-600 hover:text-white transition-all duration-300">
                        <span class="sr-only">{{ __('footer.sr_twitter') }}</span>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"></path></svg>
                    </a>
                    <a href="#" class="h-10 w-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 hover:bg-slate-900 hover:text-white dark:hover:bg-white dark:hover:text-black transition-all duration-300">
                        <span class="sr-only">{{ __('footer.sr_github') }}</span>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"></path></svg>
                    </a>
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
