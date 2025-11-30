<main class="flex-1 flex flex-col h-screen overflow-hidden px-2 bg-gray-50 dark:bg-gray-900"
    x-data="calendarApp()"
    x-init="initCalendar()"
    @keydown.window.escape="showModal = false">

    {{-- 🧩 Breadcrumbs Section --}}
    <nav class="hidden md:flex text-sm font-medium text-gray-500 dark:text-gray-400 px-6 pt-8 pb-2 pt-3 flex-shrink-0" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
            <li>
                <a href="{{ route('doctor.dashboard') }}"
                    class="hover:text-blue-600 dark:hover:text-blue-400 transition flex items-center">
                    <x-heroicon-s-home class="w-4 h-4 mr-1" />
                    {{ __('doctor.home') }}
                </a>
            </li>
            <li><x-heroicon-s-chevron-right class="w-4 h-4 text-gray-200" /></li>
            <li class="text-gray-900 dark:text-white">{{ __('doctor.appointments') }}</li>
        </ol>
    </nav>

    <header class=" dark:bg-gray-800 shadow-sm z-30 flex-shrink-0 relative">
        {{-- Top Bar (was previously the main header content) --}}
        <div class="px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-gray-100 dark:border-gray-700">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 p-2 rounded-lg">
                        <x-heroicon-s-calendar-days class="w-6 h-6" />
                    </span>
                    {{ __('doctor.appointments_overview') }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 ml-12">
                    {{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->format('l, F jS, Y') : now()->format('l, F jS, Y') }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                 {{-- Month/Date Display --}}
                <div class="hidden md:block text-right mr-2">
                    <span class="block text-xs text-gray-400 uppercase tracking-wider font-bold">Selected Date</span>
                    <span class="block text-sm font-bold text-gray-900 dark:text-white" x-text="formatDateDisplay(selectedDate)"></span>
                </div>
            </div>
        </div>

        {{-- Date Carousel --}}
        <div class="relative w-full bg-white dark:bg-gray-800 py-3">
            {{-- Left Gradient/Button --}}
            <div class="absolute left-0 top-0 bottom-0 w-16 bg-gradient-to-r from-white via-white to-transparent dark:from-gray-800 dark:via-gray-800 z-10 flex items-center pl-2">
                <button @click="scrollDates('left')" class="p-2 rounded-full bg-white dark:bg-gray-700 shadow-md border border-gray-100 dark:border-gray-600 hover:text-blue-600 text-gray-500 transition-colors">
                    <x-heroicon-m-chevron-left class="w-4 h-4" />
                </button>
            </div>

            {{-- Scrollable Container --}}
            <div x-ref="dateContainer" class="flex overflow-x-auto no-scrollbar gap-3 px-6 scroll-smooth snap-x snap-mandatory py-2">
                {{-- Generate -7 days to +21 days for a better carousel feel --}}
                @php
                    $startStrip = now()->subDays(7);
                    $endStrip = 28;
                @endphp

                @for ($i = 0; $i < $endStrip; $i++)
                    @php
                        $d = $startStrip->copy()->addDays($i);
                        $dString = $d->format('Y-m-d');
                        $isToday = $d->isToday();
                    @endphp

                    <button type="button"
                        @click="setDate('{{ $dString }}')"
                        {{-- ID used for auto-scrolling to selected date --}}
                        id="date-btn-{{ $dString }}"
                        class="snap-center flex-shrink-0 w-[72px] h-[84px] rounded-2xl flex flex-col items-center justify-center relative transition-all duration-300 border group"
                        :class="selectedDate === '{{ $dString }}'
                            ? 'bg-blue-600 border-blue-600 text-white shadow-lg shadow-blue-200 dark:shadow-none scale-105 z-10'
                            : 'bg-gray-50 dark:bg-gray-700/50 border-transparent hover:border-blue-200 dark:hover:border-gray-500 text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700'">

                        <span class="text-[10px] uppercase font-bold tracking-wide"
                            :class="selectedDate === '{{ $dString }}' ? 'opacity-100' : 'opacity-60'">
                            {{ $d->format('D') }}
                        </span>
                        <span class="text-2xl font-bold mt-0.5">{{ $d->format('d') }}</span>

                        @if($isToday)
                            <span class="absolute -top-1.5 -right-1.5 flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                            </span>
                        @endif

                        {{-- Dot Indicator for Appointments --}}
                        @if (in_array($dString, $this->datesWithAppointments))
                            <span class="absolute bottom-2 w-1.5 h-1.5 rounded-full transition-colors"
                                :class="selectedDate === '{{ $dString }}' ? 'bg-white' : 'bg-blue-500'"></span>
                        @endif
                    </button>
                @endfor
            </div>

            {{-- Right Gradient/Button --}}
            <div class="absolute right-0 top-0 bottom-0 w-16 bg-gradient-to-l from-white via-white to-transparent dark:from-gray-800 dark:via-gray-800 z-10 flex items-center justify-end pr-2">
                <button @click="scrollDates('right')" class="p-2 rounded-full bg-white dark:bg-gray-700 shadow-md border border-gray-100 dark:border-gray-600 hover:text-blue-600 text-gray-500 transition-colors">
                    <x-heroicon-m-chevron-right class="w-4 h-4" />
                </button>
            </div>
        </div>
    </header>

    {{-- Timeline Section (rest of the code remains the same as previous response) --}}
    <div class="flex-1 overflow-y-auto relative bg-white dark:bg-gray-900 custom-scrollbar scroll-smooth" x-ref="timelineContainer">
        <div class="relative min-h-[1440px] w-full pb-20" style="height: 1920px;">

            {{-- Background Grid Lines --}}
            @for ($h = 0; $h < 24; $h++)
                <div class="group flex w-full absolute box-border" style="top: {{ $h * 80 + 20 }}px; height: 80px;">
                    <div class="w-20 flex-shrink-0 text-right pr-4 -mt-3 select-none">
                        <span class="text-xs font-semibold text-gray-400 dark:text-gray-600 font-mono">
                            {{ sprintf('%02d:00', $h) }}
                        </span>
                    </div>
                    <div class="flex-1 border-t border-dashed border-gray-100 dark:border-gray-700/50 group-hover:border-gray-200 dark:group-hover:border-gray-600 transition-colors"></div>
                </div>
            @endfor

            {{-- Current Time Indicator (Red Line) --}}
            <div x-show="isToday(selectedDate)"
                 x-cloak
                 class="absolute w-full z-20 pointer-events-none flex items-center transition-all duration-1000 ease-linear"
                 :style="`top: ${currentTimeTop}px;`">
                <div class="w-20 flex justify-end pr-2">
                    <span class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded shadow-sm"
                          x-text="currentTimeString"></span>
                </div>
                <div class="flex-1 h-px bg-red-500 shadow-[0_0_4px_rgba(239,68,68,0.6)]"></div>
                <div class="absolute left-20 -ml-1 w-2 h-2 bg-red-500 rounded-full"></div>
            </div>

            {{-- Appointment Cards --}}
            @foreach ($appointmentGroups as $timeSlot => $group)
                @php
                    $topPosition = $group['hourInt'] * 80 + 20;
                    // Dynamic styling based on status
                    $baseClasses = "h-full w-full rounded-xl border-l-[6px] shadow-sm hover:shadow-lg transition-all duration-200 p-3 flex items-center justify-between cursor-pointer overflow-hidden relative";
                    if ($group['hasActive']) {
                        $wrapperClass = "border-yellow-500 bg-yellow-50/80 dark:bg-yellow-900/10";
                        $textClass = "text-yellow-900 dark:text-yellow-100";
                    } else {
                        $wrapperClass = "border-blue-500 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700";
                        $textClass = "text-gray-900 dark:text-white";
                    }
                @endphp

                <div class="absolute left-20 right-4 md:right-10 z-10 group"
                    style="top: {{ $topPosition }}px; height: 70px;"
                    wire:click="openGroupModal('{{ $timeSlot }}')">

                    <div class="{{ $baseClasses }} {{ $wrapperClass }} group-hover:-translate-y-1">
                        <div class="flex items-center gap-4">
                             {{-- Time & Status --}}
                            <div class="flex flex-col justify-center pl-1">
                                <span class="font-bold text-lg {{ $textClass }}">{{ $group['hourRange'] }}</span>
                                @if ($group['hasActive'])
                                    <span class="text-[10px] font-bold uppercase text-yellow-600 flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full animate-pulse"></span>
                                        Active
                                    </span>
                                @endif
                            </div>

                            {{-- Vertical Divider --}}
                            <div class="w-px h-8 bg-gray-200 dark:bg-gray-700"></div>

                            {{-- Patient Count --}}
                            <div class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-1.5">
                                <x-heroicon-m-users class="w-4 h-4 text-gray-400" />
                                <span>{{ $group['totalPatients'] }} {{ Str::plural('Patient', $group['totalPatients']) }}</span>
                            </div>
                        </div>

                        {{-- Avatars --}}
                        <div class="hidden sm:flex -space-x-3 mr-2">
                            @foreach (array_slice($group['patients'], 0, 4) as $p)
                                <div class="w-9 h-9 rounded-full bg-gray-100 dark:bg-gray-700 border-2 border-white dark:border-gray-800 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300 shadow-sm ring-1 ring-gray-100 dark:ring-gray-700"
                                    title="{{ $p['patientName'] }}">
                                    {{ substr($p['patientName'], 0, 1) }}
                                </div>
                            @endforeach
                            @if (count($group['patients']) > 4)
                                <div class="w-9 h-9 rounded-full bg-gray-800 text-white border-2 border-white flex items-center justify-center text-xs font-bold shadow-sm z-10">
                                    +{{ count($group['patients']) - 4 }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Empty State --}}
            @if (empty($appointmentGroups))
                <div class="absolute inset-0 flex flex-col items-center justify-center pt-32 pointer-events-none opacity-60">
                    <div class="bg-gray-100 dark:bg-gray-800 p-6 rounded-full mb-4">
                        <x-heroicon-o-calendar class="w-12 h-12 text-gray-400 dark:text-gray-500" />
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Free Slot</h3>
                    <p class="text-gray-500 text-sm">No appointments scheduled for this day.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal (Kept mostly functional, slightly styled) --}}
    <div x-show="showModal" style="display: none;" class="px-2 fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showModal = false"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-900 text-left shadow-2xl transition-all sm:w-full sm:max-w-2xl border border-gray-100 dark:border-gray-700">

                <div class="bg-white dark:bg-gray-800 px-4 py-4 sm:px-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold leading-6 text-gray-900 dark:text-white">
                            Appointments
                        </h3>
                        <p class="text-sm text-blue-600 dark:text-blue-400 font-medium mt-1" x-text="$wire.modalGroupData.hourRange"></p>
                    </div>
                    <button @click="showModal = false" class="rounded-full p-1 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 transition-colors">
                        <x-heroicon-m-x-mark class="h-6 w-6" />
                    </button>
                </div>

                <div class="px-4 py-5 sm:p-6 max-h-[60vh] overflow-y-auto bg-gray-50 dark:bg-black/20 custom-scrollbar">
                    <div class="space-y-4">
                        <template x-for="patient in $wire.modalGroupData.patients" :key="patient.id">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-xs font-bold font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-gray-600 dark:text-gray-300" x-text="patient.time"></span>
                                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset"
                                            :class="{
                                                'bg-yellow-50 text-yellow-700 ring-yellow-600/20': patient.status_color === 'yellow',
                                                'bg-green-50 text-green-700 ring-green-600/20': patient.status_color === 'green',
                                                'bg-blue-50 text-blue-700 ring-blue-700/10': patient.status_color === 'blue',
                                                'bg-red-50 text-red-700 ring-red-600/10': patient.status_color === 'red'
                                            }" x-text="patient.status_label">
                                        </span>
                                    </div>
                                    <h4 class="font-bold text-gray-900 dark:text-white text-lg" x-text="patient.patientName"></h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5" x-text="patient.type"></p>
                                </div>

                                <div class="mt-4 sm:mt-0 sm:ml-4 flex flex-col gap-2 min-w-[140px]">
                                    <template x-if="patient.raw_status !== 'In Consultation' && patient.raw_status !== 'Completed'">
                                        <button wire:click="startConsultation(patient.id)" class="w-full inline-flex justify-center items-center gap-2 rounded-lg bg-blue-600 px-3 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors">
                                            Start Visit
                                        </button>
                                    </template>
                                    <template x-if="patient.raw_status === 'In Consultation'">
                                        <button wire:click="endConsultation(patient.id)" class="w-full inline-flex justify-center items-center gap-2 rounded-lg bg-green-600 px-3 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-500 transition-colors">
                                            End Visit
                                        </button>
                                    </template>
                                    <template x-if="patient.raw_status === 'Completed'">
                                        <button disabled class="w-full inline-flex justify-center items-center gap-2 rounded-lg bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-400 cursor-not-allowed">
                                            <x-heroicon-s-check-circle class="w-4 h-4"/> Done
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function calendarApp() {
            return {
                selectedDate: @entangle('selectedDate').live,
                showModal: @entangle('showModal').live,
                currentTimeTop: 0,
                currentTimeString: '',
                timer: null,

                // Logic to center the active date on load or change
                initCalendar() {
                    this.updateTime();
                    this.timer = setInterval(() => this.updateTime(), 60000); // Update every minute
                    this.$nextTick(() => {
                        this.scrollToSelected();
                    });

                    // Update timeline position on window resize
                    window.addEventListener('resize', () => this.updateTime());
                },

                // Fix for the "1 hour behind" bug.
                // Uses browser time instead of server time.
                updateTime() {
                    const now = new Date();
                    const hours = now.getHours();
                    const minutes = now.getMinutes();

                    // Format string HH:MM
                    this.currentTimeString = hours.toString().padStart(2, '0') + ':' + minutes.toString().padStart(2, '0');

                    // Calculate pixels (80px per hour + 20px padding top)
                    const totalMinutes = (hours * 60) + minutes;
                    this.currentTimeTop = (totalMinutes / 60) * 80 + 20;
                },

                isToday(dateString) {
                    const today = new Date();
                    // Handle timezone offsets for comparison
                    const offset = today.getTimezoneOffset();
                    const localToday = new Date(today.getTime() - (offset * 60 * 1000)).toISOString().split('T')[0];
                    return dateString === localToday;
                },

                formatDateDisplay(dateString) {
                    const date = new Date(dateString);
                    return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
                },

                setDate(dateString) {
                    this.selectedDate = dateString;
                    // Wait for Livewire to update, then scroll
                    setTimeout(() => this.scrollToSelected(), 100);
                },

                // Carousel Navigation
                scrollDates(direction) {
                    const container = this.$refs.dateContainer;
                    const scrollAmount = 200; // px to scroll
                    if (direction === 'left') {
                        container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
                    } else {
                        container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                    }
                },

                scrollToSelected() {
                    const activeBtn = document.getElementById('date-btn-' + this.selectedDate);
                    if (activeBtn) {
                        activeBtn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                    }
                }
            }
        }
    </script>
</main>
