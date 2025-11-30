<div class="bg-white text-slate-600 antialiased">
  <style>
        [x-cloak] { display: none !important; }
        html { scroll-behavior: smooth; }

        /* Documentation Specific Styles */
        .screenshot-placeholder {
            background-color: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 0.75rem;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 2rem 0;
            color: #64748b;
            text-align: center;
            transition: all 0.3s;
        }
        .screenshot-placeholder:hover { border-color: #94a3b8; background-color: #f1f5f9; }

        .step-list { counter-reset: step; margin-left: 1rem; border-left: 2px solid #e2e8f0; padding-left: 2rem; }
        .step-item { position: relative; margin-bottom: 2rem; }
        .step-item::before {
            counter-increment: step;
            content: counter(step);
            position: absolute;
            left: -2.9rem;
            top: 0;
            background: #fff;
            border: 2px solid #3b82f6;
            color: #3b82f6;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            text-align: center;
            line-height: 1.8rem;
            font-weight: bold;
            font-size: 0.875rem;
        }

        .doc-table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; font-size: 0.9rem; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
        .doc-table th { background-color: #f1f5f9; text-align: left; padding: 1rem; font-weight: 600; color: #475569; border-bottom: 1px solid #e2e8f0; }
        .doc-table td { padding: 1rem; border-bottom: 1px solid #e2e8f0; color: #475569; background: white; }
        .doc-table tr:last-child td { border-bottom: none; }

        /* Typography */
        h1 { font-size: 2.25rem; font-weight: 800; color: #0f172a; margin-bottom: 1rem; line-height: 1.1; }
        h2 { font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-top: 2.5rem; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid #e2e8f0; }
        h3 { font-size: 1.125rem; font-weight: 600; color: #334155; margin-top: 1.5rem; margin-bottom: 0.75rem; }
        p, li { color: #475569; line-height: 1.7; margin-bottom: 1rem; }

        .role-badge { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
        .role-admin { background: #f3e8ff; color: #7e22ce; }
        .role-doctor { background: #dbeafe; color: #1d4ed8; }
        .role-nurse { background: #fce7f3; color: #be185d; }
        .role-reception { background: #dcfce7; color: #15803d; }
        .role-lab { background: #ffedd5; color: #c2410c; }
        .role-pharmacy { background: #e0f2fe; color: #0284c7; }

        /* Search Styles */
        .search-highlight {
            background-color: #fef3c7;
            padding: 0 2px;
            border-radius: 2px;
        }

        .search-result {
            border-bottom: 1px solid #e5e7eb;
        }

        .search-result:last-child {
            border-bottom: none;
        }
    </style>
    <div x-data="{
        sidebarOpen: false,
        currentSection: 'introduction',
        searchQuery: '',
        searchResults: [],
        searchOpen: false,

        // Documentation content for search
        documentationContent: [
            // System Features
            { section: 'system-features', title: 'Multi-language Support', content: 'A translation button on each dashboard allows users to toggle between English and French.' },
            { section: 'system-features', title: 'User Profiles', content: 'All users have a profile page to update their personal information and password.' },
            { section: 'system-features', title: 'Security & Authentication', content: 'User credentials are based on their email addresses. Patient names and lab test names are encrypted.' },
            { section: 'system-features', title: 'Notifications', content: 'Users receive a text message (SMS) when their lab results are completed.' },
            { section: 'system-features', title: 'Feedback System', content: 'View the history of past feedback submissions and create new feedback.' },

            // Administrator
            { section: 'admin-dashboard', title: 'Administrator Dashboard', content: 'Key metrics cards, data visualizations, and staff statistics.' },
            { section: 'admin-revenue', title: 'Revenue Dashboard', content: 'Total revenue card, time-period toggle, revenue breakdown cards, and patient revenue table.' },
            { section: 'admin-shifts', title: 'Shift Management', content: 'Create new shifts and view, edit, and delete past and future shifts.' },
            { section: 'admin-users', title: 'User Management', content: 'Search for users, filter by role or status, and perform user actions.' },
            { section: 'admin-settings', title: 'Settings Page', content: 'Hospital general info, department, ward, bed type, bed, and supply management.' },
            { section: 'admin-activities', title: 'User Activities Page', content: 'Search activity logs, filter by date or activity type, and view activity history.' },

            // Doctor
            { section: 'doctor-dashboard', title: 'Doctor Dashboard', content: 'Key metrics cards and data tables for appointments and lab results.' },
            { section: 'doctor-patients', title: 'Patient Management', content: 'Search for patients, view patient info, and access consultation details.' },
            { section: 'doctor-consultation', title: 'Consultation Page', content: 'Input consultation details, save drafts, and finalize consultations.' },
            { section: 'doctor-appointments', title: 'Appointments Page', content: 'View appointments by day and start/end active appointments.' },
            { section: 'doctor-tests', title: 'Test Request Page', content: 'Search lab test requests, sort by status, and view completed results.' },

            // Receptionist
            { section: 'reception-dashboard', title: 'Receptionist Dashboard', content: 'Key metrics cards and overview table of appointments.' },
            { section: 'reception-appointments', title: 'Appointments Management', content: 'Filter, search, and manage appointments, and book new appointments.' },
            { section: 'reception-patients', title: 'Patient Management', content: 'Search patients, view patient details, and register new patients.' },
            { section: 'reception-admission', title: 'Patient Admission Workflow', content: 'Manage patient admissions, view admission details, and complete admission forms.' },

            // Lab Technician
            { section: 'lab-dashboard', title: 'Lab Technician Dashboard', content: 'Key metrics cards and quick table of lab requests.' },
            { section: 'lab-requests', title: 'Test Request Management', content: 'Search and sort test requests, and manage test processing.' },
            { section: 'lab-results', title: 'Result Management', content: 'Enter and edit lab results, and view completed results.' },
            { section: 'lab-catalog', title: 'Lab Test Catalog Management', content: 'Manage lab tests and create new test entries.' },

            // Pharmacist
            { section: 'pharmacy-dashboard', title: 'Pharmacist Dashboard', content: 'Key metrics cards and top sellers table.' },
            { section: 'pharmacy-dispense', title: 'Dispense Medication Page', content: 'Select patients, view prescriptions, and dispense medications.' },
            { section: 'pharmacy-inventory', title: 'Drug Inventory Management', content: 'Manage drugs and create new drug entries.' },

            // Nurse
            { section: 'nurse-dashboard', title: 'Nurse Dashboard', content: 'Key metrics cards and recent admissions table.' },
            { section: 'nurse-vitals', title: 'Record Vitals Page', content: 'Select patients, input vital signs, and add nurse notes.' },
            { section: 'nurse-supplies', title: 'Supply Usage Page', content: 'View supplies and record usage quantities.' }
        ],

        navItems: [
            { id: 'introduction', label: 'Introduction', icon: 'home' },
            { id: 'system-features', label: 'System Features', icon: 'cog' },
            { id: 'admin', label: 'Administrator', icon: 'shield-check' },
            { id: 'doctor', label: 'Doctor', icon: 'heart' },
            { id: 'receptionist', label: 'Receptionist', icon: 'calendar' },
            { id: 'lab', label: 'Lab Technician', icon: 'beaker' },
            { id: 'pharmacist', label: 'Pharmacist', icon: 'cube' },
            { id: 'nurse', label: 'Nurse', icon: 'plus-circle' },
        ],

        init() {
            // Check for hash in URL on load
            const hash = window.location.hash.substring(1);
            if (hash && this.navItems.some(item => item.id === hash)) {
                this.currentSection = hash;
            }

            // Update hash when currentSection changes
            this.$watch('currentSection', (value) => {
                window.location.hash = value;
            });

            // Close search when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.search-container')) {
                    this.searchOpen = false;
                }
            });
        },

        navigateTo(id) {
            this.currentSection = id;
            this.sidebarOpen = false;
            this.searchOpen = false;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        performSearch() {
            if (!this.searchQuery.trim()) {
                this.searchResults = [];
                return;
            }

            const query = this.searchQuery.toLowerCase();
            this.searchResults = this.documentationContent.filter(item => {
                return item.title.toLowerCase().includes(query) ||
                       item.content.toLowerCase().includes(query);
            });

            this.searchOpen = true;
        },

        highlightText(text, query) {
            if (!query) return text;

            const regex = new RegExp(`(${query})`, 'gi');
            return text.replace(regex, `<span class=\`search-highlight\`>$1</span>`);
        },

        clearSearch() {
            this.searchQuery = '';
            this.searchResults = [];
            this.searchOpen = false;
        }
    }" class="flex h-screen overflow-hidden">

        <!-- MOBILE OVERLAY -->
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden" @click="sidebarOpen = false"></div>

        <!-- SIDEBAR -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed lg:static inset-y-0 left-0 z-50 w-72 bg-slate-50 border-r border-slate-200 flex flex-col transition-transform duration-300 lg:translate-x-0">
            <!-- Logo -->
            <div class="h-16 flex items-center px-6 border-b border-slate-200 bg-white">
                <div class="flex items-center gap-3 font-bold text-xl text-slate-800">
                   <img class="h-6 w-8" src="{{ asset('images/logo.png') }}" alt="Sana Go Health System Logo">
<a href="{{ route('home') }}"><span>SanaGo</span></a>

                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto p-4 space-y-1">
                <!-- General -->
                <div class="mb-6">
                    <p class="px-3 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">General</p>
                    <a href="#" @click.prevent="navigateTo('introduction')" :class="currentSection === 'introduction' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100'" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <x-heroicon-o-home class="w-5 h-5 opacity-75" />
                        Introduction
                    </a>
                    <a href="#" @click.prevent="navigateTo('system-features')" :class="currentSection === 'system-features' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100'" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <x-heroicon-o-cog-6-tooth class="w-5 h-5 opacity-75" />
                        System Features
                    </a>
                </div>

                <!-- Administrator -->
                <div class="mb-6">
                    <p class="px-3 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Administrator</p>
                    <a href="#" @click.prevent="navigateTo('admin-dashboard')" :class="currentSection === 'admin-dashboard' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100'" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <x-heroicon-o-squares-2x2 class="w-5 h-5 opacity-75" />
                        Dashboard
                    </a>
                    <a href="#" @click.prevent="navigateTo('admin-revenue')" :class="currentSection === 'admin-revenue' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100'" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <x-heroicon-o-currency-dollar class="w-5 h-5 opacity-75" />
                        Revenue Dashboard
                    </a>
                    <a href="#" @click.prevent="navigateTo('admin-shifts')" :class="currentSection === 'admin-shifts' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100'" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <x-heroicon-o-clock class="w-5 h-5 opacity-75" />
                        Shift Management
                    </a>
                    <a href="#" @click.prevent="navigateTo('admin-users')" :class="currentSection === 'admin-users' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100'" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <x-heroicon-o-users class="w-5 h-5 opacity-75" />
                        User Management
                    </a>
                    <a href="#" @click.prevent="navigateTo('admin-settings')" :class="currentSection === 'admin-settings' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100'" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <x-heroicon-o-cog-6-tooth class="w-5 h-5 opacity-75" />
                        Settings
                    </a>
                    <a href="#" @click.prevent="navigateTo('admin-activities')" :class="currentSection === 'admin-activities' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100'" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <x-heroicon-o-clock class="w-5 h-5 opacity-75" />
                        User Activities
                    </a>
                </div>

                <!-- Doctor -->
                <div class="mb-6">
                    <p class="px-3 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Doctor</p>
                    <a href="#" @click.prevent="navigateTo('doctor-dashboard')" :class="currentSection === 'doctor-dashboard' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100'" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <x-heroicon-o-squares-2x2 class="w-5 h-5 opacity-75" />
                        Dashboard
                    </a>
                    <a href="#" @click.prevent="navigateTo('doctor-patients')" :class="currentSection === 'doctor-patients' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100'" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <x-heroicon-o-user-group class="w-5 h-5 opacity-75" />
                        Patient Management
                    </a>
                    <a href="#" @click.prevent="navigateTo('doctor-consultation')" :class="currentSection === 'doctor-consultation' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100'" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <x-heroicon-o-clipboard-document-list class="w-5 h-5 opacity-75" />
                        Consultation
                    </a>
                    <a href="#" @click.prevent="navigateTo('doctor-appointments')" :class="currentSection === 'doctor-appointments' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100'" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <x-heroicon-o-calendar-days class="w-5 h-5 opacity-75" />
                        Appointments
                    </a>
                    <a href="#" @click.prevent="navigateTo('doctor-tests')" :class="currentSection === 'doctor-tests' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100'" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <x-heroicon-o-beaker class="w-5 h-5 opacity-75" />
                        Test Requests
                    </a>
                </div>

                <!-- Receptionist -->
                <div class="mb-6">
                    <p class="px-3 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Receptionist</p>
                    <a href="#" @click.prevent="navigateTo('reception-dashboard')" :class="currentSection === 'reception-dashboard' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100'" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <x-heroicon-o-squares-2x2 class="w-5 h-5 opacity-75" />
                        Dashboard
                    </a>
                    <a href="#" @click.prevent="navigateTo('reception-appointments')" :class="currentSection === 'reception-appointments' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100'" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <x-heroicon-o-calendar class="w-5 h-5 opacity-75" />
                        Appointments
                    </a>
                    <a href="#" @click.prevent="navigateTo('reception-patients')" :class="currentSection === 'reception-patients' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100'" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <x-heroicon-o-user-plus class="w-5 h-5 opacity-75" />
                        Patient Management
                    </a>
                    <a href="#" @click.prevent="navigateTo('reception-admission')" :class="currentSection === 'reception-admission' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100'" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <x-heroicon-o-building-office-2 class="w-5 h-5 opacity-75" />
                        Patient Admission
                    </a>
                </div>

                <!-- Lab Technician -->
                <div class="mb-6">
                    <p class="px-3 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Lab Technician</p>
                    <a href="#" @click.prevent="navigateTo('lab-dashboard')" :class="currentSection === 'lab-dashboard' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100'" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <x-heroicon-o-squares-2x2 class="w-5 h-5 opacity-75" />
                        Dashboard
                    </a>
                    <a href="#" @click.prevent="navigateTo('lab-requests')" :class="currentSection === 'lab-requests' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100'" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <x-heroicon-o-clipboard-document-list class="w-5 h-5 opacity-75" />
                        Test Requests
                    </a>
                    <a href="#" @click.prevent="navigateTo('lab-results')" :class="currentSection === 'lab-results' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100'" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <x-heroicon-o-document-text class="w-5 h-5 opacity-75" />
                        Result Management
                    </a>
                    <a href="#" @click.prevent="navigateTo('lab-catalog')" :class="currentSection === 'lab-catalog' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100'" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <x-heroicon-o-beaker class="w-5 h-5 opacity-75" />
                        Lab Test Catalog
                    </a>
                </div>

                <!-- Pharmacist -->
                <div class="mb-6">
                    <p class="px-3 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Pharmacist</p>
                    <a href="#" @click.prevent="navigateTo('pharmacy-dashboard')" :class="currentSection === 'pharmacy-dashboard' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100'" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <x-heroicon-o-squares-2x2 class="w-5 h-5 opacity-75" />
                        Dashboard
                    </a>
                    <a href="#" @click.prevent="navigateTo('pharmacy-dispense')" :class="currentSection === 'pharmacy-dispense' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100'" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <x-heroicon-o-cube class="w-5 h-5 opacity-75" />
                        Dispense Medication
                    </a>
                    <a href="#" @click.prevent="navigateTo('pharmacy-inventory')" :class="currentSection === 'pharmacy-inventory' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100'" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <x-heroicon-o-archive-box-arrow-down class="w-5 h-5 opacity-75" />
                        Drug Inventory
                    </a>
                </div>

                <!-- Nurse -->
                <div class="mb-6">
                    <p class="px-3 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nurse</p>
                    <a href="#" @click.prevent="navigateTo('nurse-dashboard')" :class="currentSection === 'nurse-dashboard' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100'" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <x-heroicon-o-squares-2x2 class="w-5 h-5 opacity-75" />
                        Dashboard
                    </a>
                    <a href="#" @click.prevent="navigateTo('nurse-vitals')" :class="currentSection === 'nurse-vitals' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100'" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <x-heroicon-o-heart class="w-5 h-5 opacity-75" />
                        Record Vitals
                    </a>
                    <a href="#" @click.prevent="navigateTo('nurse-supplies')" :class="currentSection === 'nurse-supplies' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100'" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <x-heroicon-o-archive-box class="w-5 h-5 opacity-75" />
                        Supply Usage
                    </a>
                </div>
            </nav>

            <!-- Footer -->
            <div class="p-4 border-t border-slate-200">
                <div class="flex items-center gap-3 text-sm text-slate-500">
                    <x-heroicon-o-question-mark-circle class="w-5 h-5" />
                    <span>SanaGo v1.0</span>
                </div>
            </div>
        </aside>

        <!-- CONTENT AREA -->
        <main class="flex-1 overflow-y-auto bg-white">
            <!-- Top Navigation Bar with Search -->
            <div class="sticky top-0 z-30 bg-white border-b border-slate-200">
                <div class="flex items-center justify-between px-4 py-3 lg:px-6">
                    <!-- Mobile Menu Toggle -->
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-md hover:bg-slate-100">
                        <x-heroicon-o-bars-3 class="w-6 h-6" />
                    </button>

                    <!-- Search Bar -->
                    <div class="search-container relative flex-1 max-w-xl mx-4 lg:mx-auto">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400" />
                            </div>
                            <input
                                type="text"
                                x-model="searchQuery"
                                @input="performSearch()"
                                @focus="searchQuery && performSearch()"
                                placeholder="Search documentation..."
                                class="block w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                            >
                            <div x-show="searchQuery" @click="clearSearch()" class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer">
                                <x-heroicon-o-x-mark class="h-5 w-5 text-gray-400 hover:text-gray-500" />
                            </div>
                        </div>

                        <!-- Search Results Dropdown -->
                        <div x-show="searchOpen && searchResults.length > 0"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute mt-2 w-full rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 overflow-hidden z-50">
                            <div class="max-h-80 overflow-y-auto">
                                <template x-for="result in searchResults" :key="result.section + result.title">
                                    <div class="search-result px-4 py-3 hover:bg-gray-50 cursor-pointer"
                                         @click="navigateTo(result.section)">
                                        <div class="flex items-start">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900" x-html="highlightText(result.title, searchQuery)"></p>
                                                <p class="text-sm text-gray-500 mt-1" x-html="highlightText(result.content, searchQuery)"></p>
                                            </div>
                                            <x-heroicon-o-arrow-right class="ml-2 mt-0.5 h-5 w-5 text-gray-400" />
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- No Results Message -->
                        <div x-show="searchOpen && searchResults.length === 0"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute mt-2 w-full rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 overflow-hidden z-50">
                            <div class="px-4 py-3">
                                <p class="text-sm text-gray-500">No results found for "<span x-text="searchQuery"></span>"</p>
                            </div>
                        </div>
                    </div>

                    <!-- User Profile -->
                    <div class="flex items-center">
                        <div class="relative">
                            <button class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <span class="sr-only">Open user menu</span>
                                <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center">
                                    <x-heroicon-o-user class="h-5 w-5 text-white" />
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-4xl mx-auto p-6 lg:p-12 pb-24">

                <!-- SECTION: INTRODUCTION -->
                <article x-show="currentSection === 'introduction'" x-transition.opacity>
                    <h1>Hospital Management System Documentation</h1>
                    <p class="text-lg text-slate-500 mb-8">A comprehensive guide to the role-based SanaGo for administrators, medical staff, and support personnel.</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                        <div class="p-6 bg-blue-50 rounded-xl border border-blue-100">
                            <x-heroicon-o-user-group class="w-8 h-8 text-blue-600 mb-3" />
                            <h3 class="mt-0">Role-Based Access</h3>
                            <p class="text-sm mb-0">Custom interfaces and permissions for different user roles to ensure data security and efficient workflow.</p>
                        </div>
                        <div class="p-6 bg-green-50 rounded-xl border border-green-100">
                            <x-heroicon-o-lock-closed class="w-8 h-8 text-green-600 mb-3" />
                            <h3 class="mt-0">Secure Data</h3>
                            <p class="text-sm mb-0">Encrypted patient information and lab results with secure authentication and access controls.</p>
                        </div>
                        <div class="p-6 bg-purple-50 rounded-xl border border-purple-100">
                            <x-heroicon-o-bell class="w-8 h-8 text-purple-600 mb-3" />
                            <h3 class="mt-0">Real-time Notifications</h3>
                            <p class="text-sm mb-0">Instant SMS notifications for completed lab results and other important updates.</p>
                        </div>
                    </div>

                    <h2>How to Use This Guide</h2>
                    <p>This documentation is organized by user roles. Use the sidebar on the left to navigate to sections relevant to your role in the hospital system. Each section contains detailed information about features and workflows specific to that role.</p>

                    <h2>Using the Search Function</h2>
                    <p>Use the search bar at the top of the page to quickly find information about any feature or process. The search will return results from all sections of the documentation, with highlighted matching terms.</p>
                </article>

                <!-- SECTION: SYSTEM FEATURES -->
                <article x-show="currentSection === 'system-features'" x-cloak x-transition.opacity>
                    <h1>System-Level Features</h1>
                    <p>These features apply across the entire system, regardless of user role.</p>

                    <h2>Multi-language Support</h2>
                    <p>A translation button on each dashboard allows users to toggle between English and French. This feature ensures accessibility for a diverse user base.</p>

                    <h2>User Profiles</h2>
                    <p>All users have a profile page to update their personal information and password. For nurses, the profile page also includes their assigned shifts.</p>

                    <h2>Security & Authentication</h2>
                    <p>User credentials are based on their email addresses. Patient names and lab test names are encrypted for privacy and security. Therefore, all searches must use the full or partial name.</p>

                    <h2>Notifications</h2>
                    <p>Users receive a text message (SMS) when their lab results are completed, ensuring timely communication of important medical information.</p>

                    <h2>Feedback System</h2>
                    <p>The feedback system allows users to submit and view feedback about the system:</p>
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Feedbacks Page:</strong> View the history of past feedback submissions. Each feedback item has a "View" button to see the detailed response. A "Create Feedback" button navigates to the feedback submission form.</li>
                        <li><strong>Create Feedback Page:</strong> A form to input a feedback title, select the problematic system area, and provide a detailed description.</li>
                    </ul>
                </article>

                <!-- SECTION: ADMIN DASHBOARD -->
                <article x-show="currentSection === 'admin-dashboard'" x-cloak x-transition.opacity>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="role-badge role-admin"><x-heroicon-o-shield-check class="w-4 h-4" /> Administrator</span>
                    </div>
                    <h1>Administrator Dashboard</h1>
                    <p>The Administrator dashboard provides a comprehensive overview of hospital operations and key metrics.</p>

                    <h2>Key Metrics Cards</h2>
                    <p>The dashboard displays important metrics at a glance:</p>
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Daily Revenue:</strong> Total income generated for the current day</li>
                        <li><strong>Users Admitted Today:</strong> Number of patients admitted to the hospital today</li>
                        <li><strong>Appointments Today:</strong> Total number of appointments scheduled for today</li>
                        <li><strong>Bed Occupancy Rate:</strong> Percentage of hospital beds currently occupied</li>
                    </ul>

                    <h2>Data Visualizations & Lists</h2>
                    <p>The dashboard includes visual representations of hospital data:</p>
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Graph of Patient Flow Rate:</strong> Visual representation of patient admissions and discharges over time</li>
                        <li><strong>Weekly User Encounters:</strong> Chart showing patient interactions with the hospital over the week</li>
                        <li><strong>Recent User Activity Log:</strong> List of recent system activities and user actions</li>
                    </ul>

                    <h2>Staff Statistics</h2>
                    <p>Information about hospital staff is displayed in dedicated sections:</p>
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Staff Roles & Numbers:</strong> Breakdown of staff by role and quantity</li>
                        <li><strong>New Admissions:</strong> Recent staff additions to the hospital</li>
                        <li><strong>Total Counts:</strong> Numbers of doctors, users, and departments in the hospital</li>
                    </ul>

                    <div class="screenshot-placeholder">
                        <x-heroicon-o-chart-bar class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                        <span class="font-semibold text-slate-700">Administrator Dashboard</span>
                        <span class="text-sm">Key metrics, charts, and staff statistics</span>
                    </div>
                </article>

                <!-- SECTION: ADMIN REVENUE -->
                <article x-show="currentSection === 'admin-revenue'" x-cloak x-transition.opacity>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="role-badge role-admin"><x-heroicon-o-shield-check class="w-4 h-4" /> Administrator</span>
                    </div>
                    <h1>Revenue Dashboard</h1>
                    <p>The Revenue Dashboard provides detailed financial information about hospital income sources and patient contributions.</p>

                    <h2>Total Revenue Card</h2>
                    <p>Displays the hospital's total revenue, providing a quick overview of financial performance.</p>

                    <h2>Time-Period Toggle</h2>
                    <p>Filter revenue statistics by different time periods:</p>
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Today</li>
                        <li>This Week</li>
                        <li>This Month</li>
                        <li>This Year</li>
                    </ul>

                    <h2>Revenue Breakdown Cards</h2>
                    <p>Shows revenue and percentage contribution from different sources:</p>
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Medications</li>
                        <li>Appointments</li>
                        <li>Lab Tests</li>
                        <li>Admissions</li>
                        <li>Bed Fees</li>
                        <li>Patient Payments</li>
                    </ul>

                    <h2>Patient Revenue Table</h2>
                    <p>A detailed table listing each patient's contribution to revenue from various sources:</p>
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Admissions</li>
                        <li>Appointments</li>
                        <li>Medication</li>
                        <li>Bed Fees</li>
                        <li>Lab Tests</li>
                        <li>Total contribution per patient</li>
                    </ul>

                    <div class="screenshot-placeholder">
                        <x-heroicon-o-currency-dollar class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                        <span class="font-semibold text-slate-700">Revenue Dashboard</span>
                        <span class="text-sm">Financial metrics and patient revenue breakdown</span>
                    </div>
                </article>

                <!-- SECTION: ADMIN SHIFTS -->
                <article x-show="currentSection === 'admin-shifts'" x-cloak x-transition.opacity>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="role-badge role-admin"><x-heroicon-o-shield-check class="w-4 h-4" /> Administrator</span>
                    </div>
                    <h1>Shift Management</h1>
                    <p>Administrators can create and manage work shifts for hospital staff.</p>

                    <h2>Create New Shifts</h2>
                    <p>Administrators can create new shifts (e.g., Morning, Evening) by specifying the time period for each shift.</p>

                    <h2>Shift History</h2>
                    <p>View, edit, and delete past and future shifts in a history list. This allows for efficient management of staff schedules and ensures adequate coverage at all times.</p>

                    <div class="screenshot-placeholder">
                        <x-heroicon-o-clock class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                        <span class="font-semibold text-slate-700">Shift Management</span>
                        <span class="text-sm">Create and manage staff work shifts</span>
                    </div>
                </article>

                <!-- SECTION: ADMIN USERS -->
                <article x-show="currentSection === 'admin-users'" x-cloak x-transition.opacity>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="role-badge role-admin"><x-heroicon-o-shield-check class="w-4 h-4" /> Administrator</span>
                    </div>
                    <h1>User Management</h1>
                    <p>Administrators can manage all user accounts in the system, including creating, editing, and deactivating accounts.</p>

                    <h2>Search and Filter Users</h2>
                    <p>The user management interface provides options to:</p>
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Search for users by name:</strong> Find specific users quickly</li>
                        <li><strong>Filter by Role:</strong> View users by specific roles (Doctor, Nurse, etc.)</li>
                        <li><strong>Filter by Status:</strong> View Active or Deactivated accounts</li>
                    </ul>

                    <h2>User Actions</h2>
                    <p>Administrators can perform various actions on user accounts through modals:</p>
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Edit user information:</strong> Update personal details</li>
                        <li><strong>Deactivate/Activate accounts:</strong> Control access to the system</li>
                        <li><strong>Assign shifts:</strong> Schedule work periods for staff</li>
                        <li><strong>Change user roles:</strong> Modify permissions and access levels</li>
                        <li><strong>Permanently delete accounts:</strong> Remove users from the system</li>
                    </ul>

                    <h2>Create New User Page</h2>
                    <p>A form to input new user details:</p>
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Name</li>
                        <li>Phone Number</li>
                        <li>Email</li>
                        <li>Address</li>
                        <li>Gender</li>
                        <li>Profile Picture</li>
                        <li>Department</li>
                        <li>Role</li>
                    </ul>

                    <h2>Creating a New User</h2>
                    <div class="step-list">
                        <div class="step-item">
                            Go to <strong>User Management</strong> > <strong>Add New User</strong>.
                        </div>
                        <div class="step-item">
                            Fill in the <strong>Personal Details</strong> (Name, Phone, Address).
                        </div>
                        <div class="step-item">
                            <strong>Assign Role & Department:</strong> This step is critical as it defines what the user can see.
                        </div>
                        <div class="step-item">
                            Click <strong>Create User</strong>. The system will email login credentials to the user.
                        </div>
                    </div>

                    <div class="screenshot-placeholder">
                        <x-heroicon-o-users class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                        <span class="font-semibold text-slate-700">User Management</span>
                        <span class="text-sm">Create, edit, and manage user accounts</span>
                    </div>
                </article>

                <!-- SECTION: ADMIN SETTINGS -->
                <article x-show="currentSection === 'admin-settings'" x-cloak x-transition.opacity>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="role-badge role-admin"><x-heroicon-o-shield-check class="w-4 h-4" /> Administrator</span>
                    </div>
                    <h1>Settings Page</h1>
                    <p>The Settings page contains six sub-sections for configuring various aspects of the hospital system.</p>

                    <h2>Hospital General Info</h2>
                    <p>Edit hospital name, address, email, and logo. This information is used throughout the system and on printed documents.</p>

                    <h2>Department Management</h2>
                    <p>Create, edit details, and delete hospital departments. Departments help organize staff and resources by specialty.</p>

                    <h2>Ward Management</h2>
                    <p>Create, edit information, and delete wards. Each ward requires:
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Name</li>
                        <li>Number</li>
                        <li>Department</li>
                        <li>Description</li>
                    </ul></p>

                    <h2>Bed Type Management</h2>
                    <p>Create, edit information, and delete bed types. Each bed type requires:
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Name</li>
                        <li>Description</li>
                        <li>Price per day</li>
                    </ul></p>

                    <h2>Bed Management</h2>
                    <p>Create, edit, and delete individual beds. Each bed requires:
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Bed number</li>
                        <li>Ward</li>
                        <li>Bed type</li>
                    </ul></p>

                    <h2>Supply Management</h2>
                    <p>Create, edit, and delete medical supplies. Each supply requires:
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Name</li>
                        <li>Unit of measure</li>
                        <li>Current stock</li>
                        <li>Optional minimum stock level</li>
                    </ul></p>

                    <div class="screenshot-placeholder">
                        <x-heroicon-o-cog-6-tooth class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                        <span class="font-semibold text-slate-700">System Settings</span>
                        <span class="text-sm">Configure hospital information and resources</span>
                    </div>
                </article>

                <!-- SECTION: ADMIN ACTIVITIES -->
                <article x-show="currentSection === 'admin-activities'" x-cloak x-transition.opacity>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="role-badge role-admin"><x-heroicon-o-shield-check class="w-4 h-4" /> Administrator</span>
                    </div>
                    <h1>User Activities Page</h1>
                    <p>The User Activities page provides a comprehensive log of all system activities, allowing administrators to monitor usage and track changes.</p>

                    <h2>Search and Filter Activities</h2>
                    <p>Administrators can search and filter activity logs by:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>User name or email:</strong> Find activities performed by specific users</li>
                        <li><strong>Date:</strong> View activities from specific time periods</li>
                        <li><strong>Activity type:</strong> Filter by specific types of actions</li>
                    </ul></p>

                    <h2>Activity Log</h2>
                    <p>View a paginated history of all system activities, including:
                    <ul class="list-disc pl-5 space-y-2">
                        <li>User who performed the action</li>
                        <li>Date and time of the activity</li>
                        <li>Type of activity</li>
                        <li>Brief description of the action</li>
                    </ul></p>

                    <h2>Activity Details</h2>
                    <p>Click on any activity to view the full details, including additional information about what was changed or created.</p>

                    <div class="screenshot-placeholder">
                        <x-heroicon-o-clock class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                        <span class="font-semibold text-slate-700">User Activities</span>
                        <span class="text-sm">System activity log with search and filtering options</span>
                    </div>
                </article>

                <!-- SECTION: DOCTOR DASHBOARD -->
                <article x-show="currentSection === 'doctor-dashboard'" x-cloak x-transition.opacity>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="role-badge role-doctor"><x-heroicon-o-heart class="w-4 h-4" /> Doctor</span>
                    </div>
                    <h1>Doctor Dashboard</h1>
                    <p>The Doctor dashboard provides quick access to important patient information and upcoming tasks.</p>

                    <h2>Key Metrics Cards</h2>
                    <p>The dashboard displays important metrics at a glance:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Patients Under Care:</strong> Number of patients currently assigned to the doctor</li>
                        <li><strong>Upcoming Appointments:</strong> Number of scheduled appointments</li>
                        <li><strong>New Lab Results:</strong> Count of newly available lab results for review</li>
                    </ul></p>

                    <h2>Data Tables</h2>
                    <p>Two separate tables provide quick access to important information:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Upcoming Appointments Table:</strong> Lists scheduled appointments with patient names and times</li>
                        <li><strong>Newly Arrived Lab Results Table:</strong> Shows recent lab results ready for review</li>
                    </ul></p>

                    <div class="screenshot-placeholder">
                        <x-heroicon-o-squares-2x2 class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                        <span class="font-semibold text-slate-700">Doctor Dashboard</span>
                        <span class="text-sm">Patient metrics, appointments, and lab results</span>
                    </div>
                </article>

                <!-- SECTION: DOCTOR PATIENTS -->
                <article x-show="currentSection === 'doctor-patients'" x-cloak x-transition.opacity>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="role-badge role-doctor"><x-heroicon-o-heart class="w-4 h-4" /> Doctor</span>
                    </div>
                    <h1>Patient Management</h1>
                    <p>Doctors can search for and view detailed information about their assigned patients.</p>

                    <h2>Patient Page</h2>
                    <p>The patient page allows doctors to:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Search for patients:</strong> Find patients by ID, name, or email</li>
                        <li><strong>Sort patient list:</strong> Arrange patients alphabetically (A-Z)</li>
                        <li><strong>Access patient details:</strong> Click on a patient to navigate to their detailed Patient Info Page</li>
                    </ul></p>

                    <h2>Patient Info Page</h2>
                    <p>The Patient Info Page displays comprehensive patient information:
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Personal details (name, email, phone, age, etc.)</li>
                        <li><strong>Admission Request Button:</strong> Formally request the patient's admission to the hospital</li>
                        <li><strong>Two Tabs:</strong>
                            <ul class="list-disc pl-5 space-y-2">
                                <li><strong>Consultation History:</strong> Chronologically divided list of past consultations. Each entry shows diagnosis, complaints, notes, and attachments. From here, a doctor can order new lab tests, prescribe medication, or view a detailed summary of the consultation.</li>
                                <li><strong>Vitals History:</strong> Historical log of the patient's vital signs recorded by nurses.</li>
                            </ul>
                        </li>
                    </ul></p>

                    <h2>Consultation Details Page</h2>
                    <p>View full details of a specific consultation:
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Date and doctor information</li>
                        <li>Diagnosis, complaints, and notes</li>
                        <li><strong>Two Tabs:</strong>
                            <ul class="list-disc pl-5 space-y-2">
                                <li><strong>Prescription:</strong> Shows all prescriptions issued during that specific consultation</li>
                                <li><strong>Lab Results:</strong> Shows all lab results linked to that specific consultation</li>
                            </ul>
                        </li>
                    </ul></p>

                    <div class="screenshot-placeholder">
                        <x-heroicon-o-user-group class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                        <span class="font-semibold text-slate-700">Patient Information</span>
                        <span class="text-sm">Comprehensive patient data and medical history</span>
                    </div>
                </article>

                <!-- SECTION: DOCTOR CONSULTATION -->
                <article x-show="currentSection === 'doctor-consultation'" x-cloak x-transition.opacity>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="role-badge role-doctor"><x-heroicon-o-heart class="w-4 h-4" /> Doctor</span>
                    </div>
                    <h1>Consultation Page</h1>
                    <p>The Consultation Page allows doctors to record patient encounters and create medical records.</p>

                    <h2>Patient Selection</h2>
                    <p>Search for patients assigned to the doctor (admitted or with appointments) to begin a new consultation.</p>

                    <h2>Consultation Details</h2>
                    <p>Input consultation information:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Complaints:</strong> Patient-reported symptoms and issues</li>
                        <li><strong>Diagnosis:</strong> Medical assessment of the patient's condition</li>
                        <li><strong>Notes:</strong> Additional observations and comments</li>
                        <li><strong>File Attachments:</strong> Upload relevant documents, images, or test results</li>
                    </ul></p>

                    <h2>Save Options</h2>
                    <p>Two options for saving the consultation record:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Save Draft:</strong> Saves the record but marks it as incomplete for later editing</li>
                        <li><strong>Save and Sign Final:</strong> Saves the record and marks it as finalized and signed by the doctor</li>
                    </ul></p>

                    <h2>Conducting a Consultation</h2>
                    <div class="step-list">
                        <div class="step-item">
                            Click <strong>New Consultation</strong> on the patient's profile.
                        </div>
                        <div class="step-item">
                            <strong>Chief Complaints:</strong> Record the patient's symptoms.
                        </div>
                        <div class="step-item">
                            <strong>Diagnosis:</strong> Enter your medical assessment.
                        </div>
                        <div class="step-item">
                            <strong>Prescription:</strong> Search the pharmacy database for drugs. Define dosage and duration.
                        </div>
                        <div class="step-item">
                            <strong>Lab Request (Optional):</strong> Check boxes for required tests (e.g., Malaria, CBC).
                        </div>
                        <div class="step-item">
                            <strong>Save:</strong> Choose "Draft" to edit later, or "Finalize" to submit to Pharmacy/Lab.
                        </div>
                    </div>

                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded my-4">
                        <p class="text-yellow-800 font-bold text-sm m-0">Note on Finalizing</p>
                        <p class="text-yellow-700 text-sm m-0">Once a consultation is finalized, prescriptions are instantly sent to the Pharmacist's dashboard and lab requests are sent to the Lab Technician's queue. These cannot be modified after finalization.</p>
                    </div>

                    <div class="screenshot-placeholder">
                        <x-heroicon-o-clipboard-document-list class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                        <span class="font-semibold text-slate-700">Consultation Form</span>
                        <span class="text-sm">Recording patient encounters and medical decisions</span>
                    </div>
                </article>

                <!-- SECTION: DOCTOR APPOINTMENTS -->
                <article x-show="currentSection === 'doctor-appointments'" x-cloak x-transition.opacity>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="role-badge role-doctor"><x-heroicon-o-heart class="w-4 h-4" /> Doctor</span>
                    </div>
                    <h1>Appointments Page</h1>
                    <p>The Appointments Page allows doctors to view and manage their scheduled patient appointments.</p>

                    <h2>Tabbed Interface</h2>
                    <p>View appointments by day of the week (e.g., Monday, Tuesday, Wednesday) for easy organization and planning.</p>

                    <h2>Appointment Management</h2>
                    <p>For each appointment, doctors can:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Start Appointment:</strong> Begin the consultation session</li>
                        <li><strong>End Appointment:</strong> Conclude the consultation and mark it as completed</li>
                    </ul></p>

                    <div class="screenshot-placeholder">
                        <x-heroicon-o-calendar-days class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                        <span class="font-semibold text-slate-700">Appointments Schedule</span>
                        <span class="text-sm">Daily view of patient appointments</span>
                    </div>
                </article>

                <!-- SECTION: DOCTOR TESTS -->
                <article x-show="currentSection === 'doctor-tests'" x-cloak x-transition.opacity>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="role-badge role-doctor"><x-heroicon-o-heart class="w-4 h-4" /> Doctor</span>
                    </div>
                    <h1>Test Request Page</h1>
                    <p>The Test Request Page allows doctors to track the status of lab tests they have ordered for patients.</p>

                    <h2>Search and Sort</h2>
                    <p>Find specific test requests by:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Patient name:</strong> Locate tests for specific patients</li>
                        <li><strong>Test name:</strong> Find specific types of tests</li>
                        <li><strong>Status:</strong> Sort requests by their current status (e.g., Pending, In Progress, Completed)</li>
                    </ul></p>

                    <h2>Action Button</h2>
                    <p>The button's state changes based on the request status:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>If completed:</strong> The button navigates to the View Lab Result page</li>
                        <li><strong>If pending:</strong> The button is disabled, indicating the test is still being processed</li>
                    </ul></p>

                    <div class="screenshot-placeholder">
                        <x-heroicon-o-beaker class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                        <span class="font-semibold text-slate-700">Lab Test Requests</span>
                        <span class="text-sm">Tracking ordered lab tests and results</span>
                    </div>
                </article>

                <!-- SECTION: RECEPTIONIST DASHBOARD -->


<article x-show="currentSection === 'reception-dashboard'" x-cloak x-transition.opacity>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="role-badge role-reception"><x-heroicon-o-calendar class="w-4 h-4" /> Receptionist</span>
                    </div>
                    <h1>Receptionist Dashboard</h1>
                    <p>The Receptionist dashboard provides an overview of patient registration and appointment metrics.</p>

                    <h2>Key Metrics Cards</h2>
                    <p>The dashboard displays important metrics at a glance:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Total Patients Registered:</strong> Number of patients in the hospital database</li>
                        <li><strong>Appointments Pending Today:</strong> Number of appointments scheduled for today</li>
                        <li><strong>Appointments Done Today:</strong> Number of appointments completed today</li>
                    </ul></p>

                    <h2>Overview Table</h2>
                    <p>A summary table of all appointments scheduled for today, including:
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Patient names</li>
                        <li>Assigned doctors</li>
                        <li>Appointment times</li>
                        <li>Current status</li>
                    </ul></p>

                    <div class="screenshot-placeholder">
                        <x-heroicon-o-squares-2x2 class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                        <span class="font-semibold text-slate-700">Receptionist Dashboard</span>
                        <span class="text-sm">Patient registration and appointment metrics</span>
                    </div>
                </article>

                <!-- SECTION: RECEPTIONIST APPOINTMENTS -->
                <article x-show="currentSection === 'reception-appointments'" x-cloak x-transition.opacity>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="role-badge role-reception"><x-heroicon-o-calendar class="w-4 h-4" /> Receptionist</span>
                    </div>
                    <h1>Appointments Management</h1>
                    <p>Receptionists can manage all aspects of patient appointments.</p>

                    <h2>Filtering & Searching</h2>
                    <p>Find specific appointments using:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Date filter:</strong> View appointments for specific dates</li>
                        <li><strong>Doctor filter:</strong> Find appointments with specific doctors</li>
                        <li><strong>Status filter:</strong> View appointments by status (scheduled, completed, cancelled)</li>
                        <li><strong>Patient name search:</strong> Locate appointments for specific patients</li>
                        <li><strong>Clear Filters button:</strong> Reset all filters to view all appointments</li>
                    </ul></p>

                    <h2>Appointment List</h2>
                    <p>Display of appointments includes:
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Patient name</li>
                        <li>Doctor</li>
                        <li>Date & Time</li>
                        <li>Status</li>
                        <li>Queue Number</li>
                    </ul></p>

                    <h2>Appointment Actions</h2>
                    <p>For each appointment, receptionists can:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Cancel appointment:</strong> Mark an appointment as cancelled</li>
                        <li><strong>Reinstate appointment:</strong> Restore a previously cancelled appointment</li>
                        <li><strong>Book Appointment button:</strong> Navigate to the appointment booking page</li>
                    </ul></p>

                    <h2>Book Appointment Page</h2>
                    <p>Create new appointments by:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Search for existing patient:</strong> Select a patient from the database</li>
                        <li><strong>Input appointment details:</strong>
                            <ul class="list-disc pl-5 space-y-2">
                                <li>Time & Date</li>
                                <li>Doctor</li>
                                <li>Reason for Visit (optional)</li>
                                <li>Price</li>
                            </ul>
                        </li>
                    </ul></p>

                    <h2>Booking an Appointment</h2>
                    <div class="step-list">
                        <div class="step-item">
                            Select a <strong>Department</strong> (e.g., Cardiology).
                        </div>
                        <div class="step-item">
                            Choose a <strong>Doctor</strong> from the available list.
                        </div>
                        <div class="step-item">
                            Select a <strong>Time Slot</strong> from the calendar.
                        </div>
                        <div class="step-item">
                            Confirm booking. An SMS notification is sent to the patient.
                        </div>
                    </div>

                    <div class="screenshot-placeholder">
                        <x-heroicon-o-calendar class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                        <span class="font-semibold text-slate-700">Appointment Management</span>
                        <span class="text-sm">Booking, scheduling, and managing patient appointments</span>
                    </div>
                </article>

                <!-- SECTION: RECEPTIONIST PATIENTS -->
                <article x-show="currentSection === 'reception-patients'" x-cloak x-transition.opacity>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="role-badge role-reception"><x-heroicon-o-calendar class="w-4 h-4" /> Receptionist</span>
                    </div>
                    <h1>Patient Management</h1>
                    <p>Receptionists can register new patients and manage existing patient information.</p>

                    <h2>Patient Page</h2>
                    <p>Manage existing patients by:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Search patients:</strong> Find patients by name, phone, or email</li>
                        <li><strong>View patient details:</strong> Display patient information in a table format</li>
                        <li><strong>Edit patient information:</strong> Update patient details as needed</li>
                    </ul></p>

                    <h2>Register New Patient Page</h2>
                    <p>Add new patients to the system with:
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Name</li>
                        <li>Age</li>
                        <li>Address</li>
                        <li>Gender</li>
                        <li>Phone number</li>
                        <li>Optional email</li>
                    </ul></p>

                    <h2>Patient Registration</h2>
                    <p>Before any action can be taken, a patient must be registered. Search by Phone Number first to avoid duplicates.</p>

                    <div class="screenshot-placeholder">
                        <x-heroicon-o-user-plus class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                        <span class="font-semibold text-slate-700">Patient Registration</span>
                        <span class="text-sm">Adding and managing patient information</span>
                    </div>
                </article>

                <!-- SECTION: RECEPTIONIST ADMISSION -->
                <article x-show="currentSection === 'reception-admission'" x-cloak x-transition.opacity>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="role-badge role-reception"><x-heroicon-o-calendar class="w-4 h-4" /> Receptionist</span>
                    </div>
                    <h1>Patient Admission Workflow</h1>
                    <p>Receptionists manage the patient admission process from request to discharge.</p>

                    <h2>Patient Admission Page</h2>
                    <p>Search for patients by name or bed to manage admissions:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Patient table:</strong> Shows patients with their doctor, admission status, date, and action buttons</li>
                        <li><strong>Admit Patient button:</strong>
                            <ul class="list-disc pl-5 space-y-2">
                                <li>Displays pending admission requests</li>
                                <li>Allows selection of a request to proceed to the admission form</li>
                                <li>If no request exists, an info message appears</li>
                            </ul>
                        </li>
                        <li><strong>View Admission Details button:</strong> Redirects to the Patient Admission Details page</li>
                    </ul></p>

                    <h2>Patient Admission Details Page</h2>
                    <p>View a patient's admission history:
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Past admissions</li>
                        <li>Current admission status</li>
                        <li><strong>Discharge Patient button:</strong> Available for currently admitted patients (displayed in red)</li>
                    </ul></p>

                    <h2>Patient Admission Form</h2>
                    <p>Complete the admission process by:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Selecting a bed:</strong> Choose an available bed for the patient</li>
                        <li><strong>Inputting observation fee:</strong> Set the cost for observation</li>
                        <li><strong>Setting admission date:</strong> Record when the patient is admitted</li>
                        <li><strong>Providing reason for admission:</strong> Document why the patient requires hospitalization</li>
                    </ul></p>

                    <h2>Admitting a Patient</h2>
                    <p>If a doctor recommends admission:</p>
                    <div class="step-list">
                        <div class="step-item">
                            Go to <strong>Admissions</strong> > <strong>Pending Requests</strong>.
                        </div>
                        <div class="step-item">
                            Click <strong>Assign Bed</strong> next to the patient's name.
                        </div>
                        <div class="step-item">
                            Select a Ward and an available Bed (Green icons indicate availability).
                        </div>
                        <div class="step-item">
                            Click <strong>Confirm Admission</strong>. The billing clock starts immediately.
                        </div>
                    </div>

                    <div class="screenshot-placeholder">
                        <x-heroicon-o-building-office-2 class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                        <span class="font-semibold text-slate-700">Patient Admission</span>
                        <span class="text-sm">Managing patient admissions and bed assignments</span>
                    </div>
                </article>

                <!-- SECTION: LAB DASHBOARD -->
                <article x-show="currentSection === 'lab-dashboard'" x-cloak x-transition.opacity>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="role-badge role-lab"><x-heroicon-o-beaker class="w-4 h-4" /> Lab Technician</span>
                    </div>
                    <h1>Lab Technician Dashboard</h1>
                    <p>The Lab Technician dashboard provides an overview of lab test processing status.</p>

                    <h2>Key Metrics Cards</h2>
                    <p>The dashboard displays important metrics at a glance:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Tests Completed:</strong> Number of lab tests processed and completed</li>
                        <li><strong>Requests In Progress:</strong> Number of tests currently being processed</li>
                        <li><strong>Pending Requests:</strong> Number of test requests waiting to be started</li>
                    </ul></p>

                    <h2>Quick Table</h2>
                    <p>A small table showing a few of today's lab requests for quick reference, including:
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Patient names</li>
                        <li>Test types</li>
                        <li>Request status</li>
                    </ul></p>

                    <div class="screenshot-placeholder">
                        <x-heroicon-o-squares-2x2 class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                        <span class="font-semibold text-slate-700">Lab Technician Dashboard</span>
                        <span class="text-sm">Test processing metrics and overview</span>
                    </div>
                </article>

                <!-- SECTION: LAB REQUESTS -->
                <article x-show="currentSection === 'lab-requests'" x-cloak x-transition.opacity>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="role-badge role-lab"><x-heroicon-o-beaker class="w-4 h-4" /> Lab Technician</span>
                    </div>
                    <h1>Test Request Management</h1>
                    <p>Lab Technicians can manage and process lab test requests from doctors.</p>

                    <h2>Search and Sort</h2>
                    <p>Find specific test requests by:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Patient name:</strong> Locate tests for specific patients</li>
                        <li><strong>Test name:</strong> Find specific types of tests</li>
                        <li><strong>Status:</strong> Sort requests by their current status (e.g., Pending, In Progress, Completed)</li>
                    </ul></p>

                    <h2>Request Table</h2>
                    <p>Display of test requests includes:
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Date</li>
                        <li>Test Name</li>
                        <li>Patient Name</li>
                        <li>Status</li>
                        <li><strong>Start Test button:</strong> Begins processing the request (becomes disabled after being clicked)</li>
                        <li><strong>Manage Results button:</strong> Dual-purpose button:
                            <ul class="list-disc pl-5 space-y-2">
                                <li>If no result exists, navigates to the "Enter Results" page</li>
                                <li>If a result already exists, navigates to the "Edit Result" page</li>
                            </ul>
                        </li>
                    </ul></p>

                    <h2>Processing Lab Tests</h2>
                    <div class="step-list">
                        <div class="step-item">
                            Go to <strong>Lab Requests</strong> and select a pending test.
                        </div>
                        <div class="step-item">
                            Click <strong>Start Test</strong> to begin processing.
                        </div>
                        <div class="step-item">
                            Once complete, click <strong>Manage Results</strong> to enter findings.
                        </div>
                        <div class="step-item">
                            Upload any relevant files and add analysis comments.
                        </div>
                        <div class="step-item">
                            Click <strong>Submit</strong>. The doctor is notified immediately.
                        </div>
                    </div>

                    <div class="screenshot-placeholder">
                        <x-heroicon-o-clipboard-document-list class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                        <span class="font-semibold text-slate-700">Lab Test Requests</span>
                        <span class="text-sm">Managing and processing lab test requests</span>
                    </div>
                </article>

                <!-- SECTION: LAB RESULTS -->
                <article x-show="currentSection === 'lab-results'" x-cloak x-transition.opacity>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="role-badge role-lab"><x-heroicon-o-beaker class="w-4 h-4" /> Lab Technician</span>
                    </div>
                    <h1>Result Management</h1>
                    <p>Lab Technicians can enter and edit lab test results.</p>

                    <h2>Enter Results Page</h2>
                    <p>Create new lab results by:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Inputting results text:</strong> Enter the numerical or qualitative results of the test</li>
                        <li><strong>Adding analysis comments:</strong> Provide professional interpretation of the results</li>
                        <li><strong>Uploading files:</strong> Attach relevant documents, images, or reports</li>
                    </ul></p>

                    <h2>Edit Result Page</h2>
                    <p>Modify existing lab results by:
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Updating results text</li>
                        <li>Modifying analysis comments</li>
                        <li>Adding or removing file attachments</li>
                    </ul></p>

                    <h2>View Lab Result Page</h2>
                    <p>Search for and view completed results:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Search by patient name and test name:</strong> Find specific results</li>
                        <li><strong>Filter by date:</strong> View results from specific time periods</li>
                        <li><strong>View detailed results:</strong> Each result has a button that leads to a detailed Lab Result page</li>
                    </ul></p>

                    <div class="screenshot-placeholder">
                        <x-heroicon-o-document-text class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                        <span class="font-semibold text-slate-700">Lab Result Management</span>
                        <span class="text-sm">Entering, editing, and viewing lab test results</span>
                    </div>
                </article>

                <!-- SECTION: LAB CATALOG -->
                <article x-show="currentSection === 'lab-catalog'" x-cloak x-transition.opacity>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="role-badge role-lab"><x-heroicon-o-beaker class="w-4 h-4" /> Lab Technician</span>
                    </div>
                    <h1>Lab Test Catalog Management</h1>
                    <p>Lab Technicians can manage the catalog of available lab tests.</p>

                    <h2>Manage LabTest Page</h2>
                    <p>View and manage existing lab tests:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Search for lab tests by name:</strong> Find specific tests in the catalog</li>
                        <li><strong>View test info in a table:</strong> Display test details in an organized format</li>
                        <li><strong>Edit existing tests:</strong> Modify test information as needed</li>
                        <li><strong>Delete tests:</strong> Remove tests from the catalog</li>
                    </ul></p>

                    <h2>Create New Lab Test Page</h2>
                    <p>Add new tests to the catalog by defining:
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Test name</li>
                        <li>Price</li>
                        <li>Units</li>
                        <li>Description</li>
                    </ul></p>

                    <div class="screenshot-placeholder">
                        <x-heroicon-o-beaker class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                        <span class="font-semibold text-slate-700">Lab Test Catalog</span>
                        <span class="text-sm">Managing available lab tests</span>
                    </div>
                </article>

                <!-- SECTION: PHARMACY DASHBOARD -->
                <article x-show="currentSection === 'pharmacy-dashboard'" x-cloak x-transition.opacity>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="role-badge role-pharmacy"><x-heroicon-o-cube class="w-4 h-4" /> Pharmacist</span>
                    </div>
                    <h1>Pharmacist Dashboard</h1>
                    <p>The Pharmacist dashboard provides an overview of medication dispensing and inventory status.</p>

                    <h2>Key Metrics Cards</h2>
                    <p>The dashboard displays important metrics at a glance:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Prescriptions Dispensed Today:</strong> Number of prescriptions processed today</li>
                        <li><strong>Prescriptions Pending:</strong> Number of prescriptions waiting to be dispensed</li>
                        <li><strong>Drugs Left in Inventory:</strong> Current stock levels of medications</li>
                    </ul></p>

                    <h2>Top Sellers Table</h2>
                    <p>A small table showing the top 10 best-selling drugs, including:
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Drug names</li>
                        <li>Quantity sold</li>
                        <li>Revenue generated</li>
                    </ul></p>

                    <div class="screenshot-placeholder">
                        <x-heroicon-o-squares-2x2 class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                        <span class="font-semibold text-slate-700">Pharmacist Dashboard</span>
                        <span class="text-sm">Medication dispensing metrics and inventory status</span>
                    </div>
                </article>

                <!-- SECTION: PHARMACY DISPENSE -->
                <article x-show="currentSection === 'pharmacy-dispense'" x-cloak x-transition.opacity>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="role-badge role-pharmacy"><x-heroicon-o-cube class="w-4 h-4" /> Pharmacist</span>
                    </div>
                    <h1>Dispense Medication Page</h1>
                    <p>Pharmacists can dispense medications based on doctor prescriptions.</p>

                    <h2>Patient Selection</h2>
                    <p>A panel on the left allows pharmacists to:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Search for patients:</strong> Find patients by name or ID</li>
                        <li><strong>Select a patient:</strong> View their prescription information</li>
                    </ul></p>

                    <h2>Prescription View</h2>
                    <p>Once a patient is selected, their prescriptions are displayed:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Past prescriptions:</strong> History of previously dispensed medications</li>
                        <li><strong>Current prescriptions:</strong> New prescriptions ready for dispensing</li>
                    </ul></p>

                    <h2>Dispensing Action</h2>
                    <p>For each prescription, the pharmacist can:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Record dispensed drugs:</strong> Select medications from the prescription</li>
                        <li><strong>Record quantities:</strong> Specify the amount of each medication dispensed</li>
                    </ul></p>

                    <h2>Contextual Information</h2>
                    <p>The pharmacist can view additional information:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Patient admission status:</strong> Whether the patient is currently admitted</li>
                        <li><strong>Prescribing doctor:</strong> The doctor who issued the prescription</li>
                        <li><strong>Date/time of prescription:</strong> When the prescription was created</li>
                    </ul></p>

                    <h2>Dispensing Medication</h2>
                    <div class="step-list">
                        <div class="step-item">
                            Locate the patient using their ID or Name.
                        </div>
                        <div class="step-item">
                            View the digital prescription from the doctor.
                        </div>
                        <div class="step-item">
                            Verify stock availability.
                        </div>
                        <div class="step-item">
                            Click <strong>Dispense</strong>. This updates inventory and adds the cost to the patient's final bill.
                        </div>
                    </div>

                    <div class="screenshot-placeholder">
                        <x-heroicon-o-cube class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                        <span class="font-semibold text-slate-700">Medication Dispensing</span>
                        <span class="text-sm">Processing and dispensing prescriptions</span>
                    </div>
                </article>

                <!-- SECTION: PHARMACY INVENTORY -->
                <article x-show="currentSection === 'pharmacy-inventory'" x-cloak x-transition.opacity>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="role-badge role-pharmacy"><x-heroicon-o-cube class="w-4 h-4" /> Pharmacist</span>
                    </div>
                    <h1>Drug Inventory Management</h1>
                    <p>Pharmacists can manage the hospital's drug inventory.</p>

                    <h2>Manage Drugs Page</h2>
                    <p>View and manage existing drugs:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Search for drugs:</strong> Find specific medications in the inventory</li>
                        <li><strong>Filter by stock status:</strong> View drugs by availability (In Stock, Low Stock)</li>
                        <li><strong>Edit existing drug entries:</strong> Update drug information</li>
                        <li><strong>Delete drug entries:</strong> Remove drugs from the inventory</li>
                    </ul></p>

                    <h2>Create New Drugs Page</h2>
                    <p>Add new drugs to the inventory by providing:
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Drug name</li>
                        <li>Unit price</li>
                        <li>Stock quantity</li>
                        <li>Minimum stock level</li>
                        <li>Dosage unit</li>
                        <li>Description</li>
                    </ul></p>

                    <div class="screenshot-placeholder">
                        <x-heroicon-o-archive-box-arrow-down class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                        <span class="font-semibold text-slate-700">Drug Inventory Management</span>
                        <span class="text-sm">Managing medication stock levels</span>
                    </div>
                </article>

                <!-- SECTION: NURSE DASHBOARD -->
                <article x-show="currentSection === 'nurse-dashboard'" x-cloak x-transition.opacity>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="role-badge role-nurse"><x-heroicon-o-plus-circle class="w-4 h-4" /> Nurse</span>
                    </div>
                    <h1>Nurse Dashboard</h1>
                    <p>The Nurse dashboard provides an overview of patient care and supply status.</p>

                    <h2>Key Metrics Cards</h2>
                    <p>The dashboard displays important metrics at a glance:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Admitted Patients:</strong> Number of patients currently under care</li>
                        <li><strong>Total Supplies:</strong> Quantity of medical supplies available</li>
                        <li><strong>Low Stock Supplies:</strong> Number of supplies that need restocking</li>
                    </ul></p>

                    <h2>Recent Admissions Table</h2>
                    <p>An overview table of the most recently admitted patients, including:
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Patient names</li>
                        <li>Admission dates</li>
                        <li>Assigned doctors</li>
                        <li>Current status</li>
                    </ul></p>

                    <div class="screenshot-placeholder">
                        <x-heroicon-o-squares-2x2 class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                        <span class="font-semibold text-slate-700">Nurse Dashboard</span>
                        <span class="text-sm">Patient care metrics and supply status</span>
                    </div>
                </article>

                <!-- SECTION: NURSE VITALS -->
                <article x-show="currentSection === 'nurse-vitals'" x-cloak x-transition.opacity>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="role-badge role-nurse"><x-heroicon-o-plus-circle class="w-4 h-4" /> Nurse</span>
                    </div>
                    <h1>Record Vitals Page</h1>
                    <p>Nurses can record and track patient vital signs.</p>

                    <h2>Patient Selection</h2>
                    <p>A dropdown menu allows nurses to select a patient for vital sign recording.</p>

                    <h2>Vitals Input Form</h2>
                    <p>Fields to enter patient vital signs:
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Blood Pressure</li>
                        <li>Temperature</li>
                        <li>Heart Rate</li>
                        <li>Oxygen Saturation</li>
                        <li>Respiratory Rate</li>
                        <li>Weight</li>
                        <li>Height</li>
                    </ul></p>

                    <h2>Additional Fields</h2>
                    <p>Additional information can be recorded:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Flag as Normal checkbox:</strong> Mark vital signs as within normal ranges</li>
                        <li><strong>Nurse Notes text area:</strong> Add observations or comments about the patient's condition</li>
                    </ul></p>

                    <h2>Recording Vitals</h2>
                    <div class="step-list">
                        <div class="step-item">
                            Select a patient from the dropdown menu.
                        </div>
                        <div class="step-item">
                            Enter all required vital signs in the form.
                        </div>
                        <div class="step-item">
                            If values are within the normal range, check the <strong>Flag as Normal</strong> box.
                        </div>
                        <div class="step-item">
                            Add any relevant observations in the <strong>Nurse Notes</strong> section.
                        </div>
                        <div class="step-item">
                            Click <strong>Save</strong> to record the vitals to the patient's file.
                        </div>
                    </div>

                    <div class="screenshot-placeholder">
                        <x-heroicon-o-heart class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                        <span class="font-semibold text-slate-700">Vitals Recording</span>
                        <span class="text-sm">Recording patient vital signs</span>
                    </div>
                </article>

                <!-- SECTION: NURSE SUPPLIES -->
                <article x-show="currentSection === 'nurse-supplies'" x-cloak x-transition.opacity>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="role-badge role-nurse"><x-heroicon-o-plus-circle class="w-4 h-4" /> Nurse</span>
                    </div>
                    <h1>Supply Usage Page</h1>
                    <p>Nurses can track and record the usage of medical supplies.</p>

                    <h2>Supply List</h2>
                    <p>A list of all medical supplies registered in the system, including:
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Supply names</li>
                        <li>Current stock levels</li>
                        <li>Unit of measure</li>
                    </ul></p>

                    <h2>Usage Recording</h2>
                    <p>For each supply, nurses can:
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Input quantity used:</strong> Record the amount of each supply consumed</li>
                        <li><strong>Automatic stock deduction:</strong> The system automatically deducts the used amount from the hospital's total stock count</li>
                    </ul></p>

                    <h2>Managing Supplies</h2>
                    <div class="step-list">
                        <div class="step-item">
                            Go to the <strong>Supply Usage</strong> page.
                        </div>
                        <div class="step-item">
                            Locate the supply item that was used from the list.
                        </div>
                        <div class="step-item">
                            Enter the quantity used in the input field next to the item.
                        </div>
                        <div class="step-item">
                            Click <strong>Update Usage</strong>. The system will automatically deduct this amount from the inventory.
                        </div>
                    </div>

                    <div class="screenshot-placeholder">
                        <x-heroicon-o-archive-box class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                        <span class="font-semibold text-slate-700">Supply Usage Tracking</span>
                        <span class="text-sm">Recording medical supply consumption</span>
                    </div>
                </article>

            </div>
        </main>
    </div>

</div>
