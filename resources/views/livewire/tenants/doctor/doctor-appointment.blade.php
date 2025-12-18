<main class="flex-1 flex flex-col h-full relative overflow-hidden bg-gray-50 dark:bg-gray-900"
    x-data="calendarApp()"
    x-init="initCalendar()"
    @keydown.window.escape="showModal = false">

    {{-- 🟢 TOP HEADER (Sticky, Glassmorphism) --}}
    <header class="flex-shrink-0 z-30 bg-white/90 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm transition-all duration-300">

        {{-- Top Bar: Title & Navigation --}}
        <div class="px-6 py-3 flex flex-col md:flex-row md:items-center justify-between gap-4">
            {{-- Left: Breadcrumbs & Title --}}
            <div>
                <nav class="flex items-center text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 mt-4">
                    <a href="{{ route('doctor.dashboard') }}" class="hover:text-blue-600 transition flex items-center">
                        <x-heroicon-s-home class="w-3 h-3 mr-1" /> Home
                    </a>
                    <x-heroicon-s-chevron-right class="w-3 h-3 mx-1 text-gray-300" />
                    <span class="text-gray-900 dark:text-white">Appointments</span>
                </nav>
                <div class="flex items-center gap-3">
                    <div>
<h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
           Appointments
                    </h1>
 <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('admin.manage_users_description') }}
                        </p>
                    </div>

                    {{-- Quick Action: Jump to Today --}}
                    @if($selectedDate !== now()->format('Y-m-d'))
                        <button @click="setDate('{{ now()->format('Y-m-d') }}')"
                                class="text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-300 px-2 py-1 rounded-md transition-colors">
                            Return to Today
                        </button>
                    @endif
                </div>
            </div>

            {{-- Right: Legend --}}
            <div class="hidden md:flex items-center gap-4 text-xs font-medium text-gray-500 dark:text-gray-400">
                <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Scheduled</div>
                <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-yellow-500"></span> Active</div>
                <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-green-500"></span> Completed</div>
            </div>
        </div>

        {{-- Date Carousel (Compact) --}}
        <div class="relative w-full border-t border-gray-100 dark:border-gray-700/50 py-2">
            <button @click="scrollDates('left')" class="absolute left-0 top-0 bottom-0 z-20 w-12 bg-gradient-to-r from-white via-white/90 to-transparent dark:from-gray-800 dark:via-gray-800/90 flex items-center justify-center text-gray-400 hover:text-blue-600">
                <x-heroicon-m-chevron-left class="w-5 h-5" />
            </button>

            <div x-ref="dateContainer" class="flex overflow-x-auto no-scrollbar gap-2 px-4 scroll-smooth snap-x snap-mandatory">
                @php
                    $startStrip = now()->subDays(7);
                    $endStrip = 28;
                @endphp

                @for ($i = 0; $i < $endStrip; $i++)
                    @php
                        $d = $startStrip->copy()->addDays($i);
                        $dString = $d->format('Y-m-d');
                        $isToday = $d->isToday();
                        $isSelected = $selectedDate === $dString;
                        $hasAppt = in_array($dString, $this->datesWithAppointments);
                    @endphp

                    <button type="button"
                        @click="setDate('{{ $dString }}')"
                        id="date-btn-{{ $dString }}"
                        class="snap-start flex-shrink-0 group relative w-[54px] h-[64px] rounded-xl flex flex-col items-center justify-center transition-all duration-200 border select-none
                        {{ $isSelected
                            ? 'bg-blue-600 border-blue-600 text-white shadow-md shadow-blue-500/20 scale-105 z-10'
                            : 'bg-white dark:bg-gray-800 border-gray-100 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-blue-300 dark:hover:border-gray-500 hover:bg-blue-50/50 dark:hover:bg-gray-700'
                        }}">

                        <span class="text-[10px] font-bold uppercase tracking-wider mb-0.5 {{ $isSelected ? 'text-blue-100' : 'text-gray-400' }}">
                            {{ $d->format('D') }}
                        </span>
                        <span class="text-lg font-bold leading-none">{{ $d->format('d') }}</span>

                        {{-- Indicators --}}
                        <div class="flex gap-1 mt-1.5 h-1.5">
                            @if($isToday)
                                <span class="w-1.5 h-1.5 rounded-full {{ $isSelected ? 'bg-white' : 'bg-blue-600' }}"></span>
                            @elseif($hasAppt)
                                <span class="w-1.5 h-1.5 rounded-full {{ $isSelected ? 'bg-blue-300' : 'bg-blue-400' }}"></span>
                            @endif
                        </div>
                    </button>
                @endfor
            </div>

            <button @click="scrollDates('right')" class="absolute right-0 top-0 bottom-0 z-20 w-12 bg-gradient-to-l from-white via-white/90 to-transparent dark:from-gray-800 dark:via-gray-800/90 flex items-center justify-center text-gray-400 hover:text-blue-600">
                <x-heroicon-m-chevron-right class="w-5 h-5" />
            </button>
        </div>
    </header>

    {{-- 📅 TIMELINE SCROLL AREA --}}
    <div class="flex-1 overflow-y-auto overflow-x-hidden relative bg-gray-50 dark:bg-gray-900 custom-scrollbar scroll-smooth" x-ref="timelineContainer">

        {{-- Grid Container --}}
        <div class="relative min-h-[1440px] w-full pb-32 pt-4" style="height: 1940px;">

            {{-- Background Grid & Time Labels --}}
            @for ($h = 0; $h < 24; $h++)
                <div class="flex w-full absolute pointer-events-none" style="top: {{ $h * 80 + 20 }}px; height: 80px;">
                    {{-- Time Label --}}
                    <div class="w-16 sm:w-20 flex-shrink-0 text-right pr-3 -mt-2.5">
                        <span class="text-xs font-medium text-gray-400 dark:text-gray-500 font-mono">
                            {{ sprintf('%02d:00', $h) }}
                        </span>
                    </div>
                    {{-- Line --}}
                    <div class="flex-1 border-t border-gray-200 dark:border-gray-700/60 border-dashed"></div>
                </div>
            @endfor

            {{-- 📍 Current Time Indicator --}}
            <div x-show="isToday(selectedDate)"
                 x-cloak
                 class="absolute w-full z-20 pointer-events-none flex items-center group"
                 :style="`top: ${currentTimeTop}px;`">
                <div class="w-16 sm:w-20 flex justify-end pr-2">
                    <span class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-md shadow-sm tabular-nums"
                          x-text="currentTimeString"></span>
                </div>
                <div class="flex-1 h-[2px] bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.5)] relative">
                    <div class="absolute -left-1 -top-[3px] w-2 h-2 bg-red-500 rounded-full"></div>
                </div>
            </div>

            {{-- 🗓️ Appointment Cards --}}
            @foreach ($appointmentGroups as $timeSlot => $group)
                @php
                    $topPosition = $group['hourInt'] * 80 + 20;
                    $isPast = $group['hourInt'] < now()->hour && \Carbon\Carbon::parse($selectedDate)->isToday();

                    // Dynamic Styling
                    if ($group['hasActive']) {
                        $cardClass = "bg-amber-50 dark:bg-amber-900/20 border-l-amber-500 hover:shadow-amber-500/20";
                        $textClass = "text-amber-900 dark:text-amber-100";
                        $borderClass = "border-amber-200 dark:border-amber-800";
                    } else {
                        $cardClass = "bg-white dark:bg-gray-800 border-l-blue-500 hover:shadow-blue-500/20";
                        $textClass = "text-gray-900 dark:text-white";
                        $borderClass = "border-gray-200 dark:border-gray-700";
                    }

                    if($isPast && !$group['hasActive']) {
                        $opacityClass = "opacity-60 grayscale-[0.5] hover:opacity-100 hover:grayscale-0";
                    } else {
                        $opacityClass = "opacity-100";
                    }
                @endphp

                <div class="absolute left-16 sm:left-20 right-2 sm:right-6 z-10 transition-all duration-300 {{ $opacityClass }}"
                    style="top: {{ $topPosition }}px; height: 70px;"
                    wire:click="openGroupModal('{{ $timeSlot }}')">

                    <div class="h-full w-full rounded-lg border border-l-[4px] shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 p-2 sm:p-3 cursor-pointer overflow-hidden relative flex items-center justify-between {{ $cardClass }} {{ $borderClass }}">

                        {{-- Left Info --}}
                        <div class="flex items-center gap-3 sm:gap-6">
                            <div class="flex flex-col pl-1 min-w-[70px]">
                                <span class="font-bold text-lg leading-tight {{ $textClass }}">{{ $group['hourRange'] }}</span>
                                @if ($group['hasActive'])
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase text-amber-600 dark:text-amber-400">
                                        <span class="relative flex h-2 w-2">
                                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                          <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                        </span>
                                        Now
                                    </span>
                                @endif
                            </div>

                            <div class="w-px h-8 bg-gray-200 dark:bg-gray-700 hidden sm:block"></div>

                            <div class="flex flex-col justify-center">
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                    {{ $group['totalPatients'] }} {{ Str::plural('Patient', $group['totalPatients']) }}
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Scheduled Visit</span>
                            </div>
                        </div>

                        {{-- Right Avatars --}}
                        <div class="flex -space-x-2 sm:-space-x-3 overflow-hidden p-1">
                            @foreach (array_slice($group['patients'], 0, 5) as $p)
                                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-gray-100 dark:bg-gray-700 border-2 border-white dark:border-gray-800 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300 ring-1 ring-gray-100 dark:ring-gray-600"
                                    title="{{ $p['patientName'] }}">
                                    {{ substr($p['patientName'], 0, 1) }}
                                </div>
                            @endforeach
                            @if (count($group['patients']) > 5)
                                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-gray-800 text-white border-2 border-white dark:border-gray-900 flex items-center justify-center text-[10px] font-bold z-10">
                                    +{{ count($group['patients']) - 5 }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- 💤 Empty State --}}
            @if (empty($appointmentGroups))
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <div class="text-center opacity-40 dark:opacity-30">
                        <div class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                            <x-heroicon-o-calendar class="w-12 h-12 text-gray-400" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">No Appointments</h3>
                        <p class="text-gray-500 max-w-xs mx-auto mt-2">Enjoy your free time! No consultations scheduled for this date.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- 🛑 MODAL (Enhanced Design) --}}
    <div x-show="showModal" style="display: none;" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="showModal"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity" @click="showModal = false"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="showModal"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-900 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-gray-100 dark:border-gray-700">

                    {{-- Modal Header --}}
                    <div class="bg-white dark:bg-gray-800 px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-start">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300 py-1 px-2 rounded-md text-xs font-bold uppercase tracking-wide">
                                    Slot Details
                                </span>
                                <span class="text-sm text-gray-500 dark:text-gray-400" x-text="$wire.modalGroupData.hourRange"></span>
                            </div>
                            <h3 class="text-xl font-bold leading-6 text-gray-900 dark:text-white mt-2">
                                Patient List
                            </h3>
                        </div>
                        <button @click="showModal = false" class="rounded-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gray-500 transition-colors">
                            <x-heroicon-m-x-mark class="h-5 w-5" />
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="px-6 py-6 bg-gray-50 dark:bg-gray-900/50 max-h-[60vh] overflow-y-auto custom-scrollbar">
                        <div class="space-y-4">
                            <template x-for="patient in $wire.modalGroupData.patients" :key="patient.id">
                                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">

                                    {{-- Patient Info --}}
                                    <div class="flex items-start gap-4">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-md flex-shrink-0">
                                            <span x-text="patient.patientName.substring(0,1)"></span>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 dark:text-white" x-text="patient.patientName"></h4>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-xs font-mono text-gray-500 dark:text-gray-400" x-text="patient.time"></span>
                                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium border"
                                                    :class="{
                                                        'bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-800': patient.status_color === 'yellow',
                                                        'bg-green-50 text-green-700 border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-800': patient.status_color === 'green',
                                                        'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800': patient.status_color === 'blue',
                                                        'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-800': patient.status_color === 'red'
                                                    }" x-text="patient.status_label">
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Actions --}}
                                    <div class="sm:text-right">
                                        <template x-if="patient.raw_status !== 'In Consultation' && patient.raw_status !== 'Completed'">
                                            <button wire:click="startConsultation(patient.id)" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-lg bg-blue-600 hover:bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-all focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                                <x-heroicon-m-play class="w-4 h-4"/> Start
                                            </button>
                                        </template>
                                        <template x-if="patient.raw_status === 'In Consultation'">
                                            <button wire:click="endConsultation(patient.id)" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-lg bg-green-600 hover:bg-green-700 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-all">
                                                <x-heroicon-m-check class="w-4 h-4"/> Finish
                                            </button>
                                        </template>
                                        <template x-if="patient.raw_status === 'Completed'">
                                            <span class="inline-flex items-center gap-1 text-sm font-medium text-gray-400 select-none">
                                                <x-heroicon-s-check-circle class="w-5 h-5 text-gray-300 dark:text-gray-600"/>
                                                Completed
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alpine Logic --}}
    <script>
        function calendarApp() {
            return {
                selectedDate: @entangle('selectedDate').live,
                showModal: @entangle('showModal').live,
                currentTimeTop: 0,
                currentTimeString: '',
                timer: null,

                initCalendar() {
                    this.updateTime();
                    this.timer = setInterval(() => this.updateTime(), 60000);
                    this.$nextTick(() => {
                        this.scrollToSelected();
                    });
                    window.addEventListener('resize', () => this.updateTime());
                },

                updateTime() {
                    const now = new Date();
                    const hours = now.getHours();
                    const minutes = now.getMinutes();
                    this.currentTimeString = hours.toString().padStart(2, '0') + ':' + minutes.toString().padStart(2, '0');
                    // 80px per hour + 20px padding
                    this.currentTimeTop = (hours + minutes / 60) * 80 + 20;

                    // Auto-scroll timeline to current time if on today's date
                    if(this.isToday(this.selectedDate) && !this.scrolledToTime) {
                        const container = this.$refs.timelineContainer;
                        if(container) {
                            container.scrollTop = this.currentTimeTop - 200; // Center it a bit
                            this.scrolledToTime = true; // prevent constant jumping
                        }
                    }
                },

                isToday(dateString) {
                    const today = new Date();
                    const offset = today.getTimezoneOffset();
                    const localToday = new Date(today.getTime() - (offset * 60 * 1000)).toISOString().split('T')[0];
                    return dateString === localToday;
                },

                setDate(dateString) {
                    this.selectedDate = dateString;
                    this.scrolledToTime = false; // Reset auto-scroll trigger
                    setTimeout(() => this.scrollToSelected(), 100);
                },

                scrollDates(direction) {
                    const container = this.$refs.dateContainer;
                    const scrollAmount = 300;
                    container.scrollBy({
                        left: direction === 'left' ? -scrollAmount : scrollAmount,
                        behavior: 'smooth'
                    });
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
