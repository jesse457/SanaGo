<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HMS Modern UI</title>
    <!-- Tailwind CSS CDN -->
   @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: hsl(0, 0%, 95%); /* Light background */
            color: hsl(0, 0%, 20%); /* Darker text */
        }
        /* Custom colors based on HSL for better control and consistency */
        .primary-blue { background-color: hsl(210, 70%, 50%); }
        .primary-blue-text { color: hsl(210, 70%, 50%); }
        .primary-blue-hover:hover { background-color: hsl(210, 70%, 40%); }
        .secondary-orange { background-color: hsl(30, 80%, 60%); }
        .secondary-orange-text { color: hsl(30, 80%, 60%); }
        .secondary-orange-hover:hover { background-color: hsl(30, 80%, 50%); }
        .neutral-light-bg { background-color: hsl(0, 0%, 95%); }
        .neutral-white-bg { background-color: hsl(0, 0%, 100%); }
        .neutral-border { border-color: hsl(0, 0%, 85%); }
        .neutral-text { color: hsl(0, 0%, 40%); }
        .success-green { background-color: hsl(140, 60%, 40%); }
        .success-green-text { color: hsl(140, 60%, 40%); }
        .error-red { background-color: hsl(0, 70%, 50%); }
        .error-red-text { color: hsl(0, 70%, 50%); }
        .warning-yellow { background-color: hsl(40, 90%, 60%); }
        .warning-yellow-text { color: hsl(40, 90%, 60%); }
        .info-blue { background-color: hsl(200, 70%, 60%); }
        .info-blue-text { color: hsl(200, 70%, 60%); }

        /* Custom scrollbar for better aesthetics */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: hsl(0, 0%, 90%);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: hsl(0, 0%, 70%);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: hsl(0, 0%, 60%);
        }

        /* Custom classes for consistent button styles */
        .btn-primary {
            @apply primary-blue text-white px-4 py-2 rounded-lg shadow-md primary-blue-hover transition-all duration-200 ease-in-out flex items-center justify-center gap-2;
        }
        .btn-outlined {
            @apply border border-primary-blue-text text-primary-blue-text px-4 py-2 rounded-lg bg-transparent hover:bg-primary-blue-text hover:text-white transition-all duration-200 ease-in-out flex items-center justify-center gap-2;
        }
        .btn-text {
            @apply text-primary-blue-text px-4 py-2 rounded-lg bg-transparent hover:bg-blue-100 transition-all duration-200 ease-in-out flex items-center justify-center gap-2;
        }
        .icon-btn {
            @apply p-2 rounded-full hover:bg-gray-200 transition-all duration-200 ease-in-out flex items-center justify-center;
        }

        /* Form element styling */
        .form-input {
            @apply w-full p-3 border neutral-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-blue-text transition-all duration-200 ease-in-out bg-white;
        }
        .form-label {
            @apply block text-sm font-medium text-gray-700 mb-1;
        }

        /* Card styling */
        .card {
            @apply neutral-white-bg rounded-lg shadow-md p-6;
        }

        /* Table styling */
        .table-hms {
            @apply w-full text-left border-collapse;
        }
        .table-hms th {
            @apply p-4 bg-gray-100 text-gray-600 font-semibold uppercase text-sm border-b border-gray-200;
        }
        .table-hms td {
            @apply p-4 border-b border-gray-200 text-gray-800;
        }
        .table-hms tbody tr:nth-child(odd) {
            @apply bg-white;
        }
        .table-hms tbody tr:nth-child(even) {
            @apply bg-gray-50;
        }

        /* Modal styling */
        .modal-overlay {
            @apply fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden;
        }
        .modal-content {
            @apply bg-white rounded-lg shadow-xl p-8 w-11/12 md:w-1/2 lg:w-1/3 max-h-[90vh] overflow-y-auto;
        }

        /* Snackbar styling */
        .snackbar {
            @apply fixed bottom-8 right-8 bg-gray-800 text-white px-6 py-3 rounded-lg shadow-lg hidden;
        }

        /* Active navigation link */
        .nav-link.active {
            @apply bg-blue-100 text-primary-blue-text font-semibold;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen">
    <!-- Top App Bar -->
    <header class="bg-white shadow-sm p-4 flex items-center justify-between sticky top-0 z-40">
        <div class="flex items-center gap-4">
            <button id="menu-toggle" class="lg:hidden icon-btn">
                <i data-lucide="menu" class="w-6 h-6 text-gray-700"></i>
            </button>
            <h1 class="text-2xl font-bold primary-blue-text">HMS</h1>
            <span class="hidden md:block text-gray-500 text-sm ml-2">Hospital Management System</span>
        </div>
        <div class="flex items-center gap-4">
            <button class="icon-btn">
                <i data-lucide="bell" class="w-6 h-6 text-gray-700"></i>
            </button>
            <div class="flex items-center gap-2 cursor-pointer rounded-full p-2 hover:bg-gray-100">
                <img src="https://placehold.co/40x40/cbd5e1/4a5568?text=JD" alt="User Avatar" class="w-10 h-10 rounded-full border border-gray-300">
                <span class="font-medium text-gray-800 hidden md:block">John Doe</span>
                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-500 hidden md:block"></i>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <div class="flex flex-1">
        <!-- Side Navigation (Drawer) -->
        <aside id="side-nav" class="fixed inset-y-0 left-0 bg-white shadow-lg w-64 p-4 z-30 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col">
            <div class="flex items-center justify-between mb-8 lg:hidden">
                <h2 class="text-xl font-bold primary-blue-text">Navigation</h2>
                <button id="close-menu" class="icon-btn">
                    <i data-lucide="x" class="w-6 h-6 text-gray-700"></i>
                </button>
            </div>
            <nav class="flex-1">
                <ul>
                    <li class="mb-2">
                        <a href="#admin-dashboard" class="nav-link flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 active" data-role="admin">
                            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                            <span>Admin Dashboard</span>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#doctor-dashboard" class="nav-link flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200" data-role="doctor">
                            <i data-lucide="stethoscope" class="w-5 h-5"></i>
                            <span>Doctor Dashboard</span>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#nurse-dashboard" class="nav-link flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200" data-role="nurse">
                            <i data-lucide="nurse" class="w-5 h-5"></i>
                            <span>Nurse Dashboard</span>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#receptionist-dashboard" class="nav-link flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200" data-role="receptionist">
                            <i data-lucide="calendar-check" class="w-5 h-5"></i>
                            <span>Receptionist Dashboard</span>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#lab-dashboard" class="nav-link flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200" data-role="lab">
                            <i data-lucide="flask-conical" class="w-5 h-5"></i>
                            <span>Lab Technician Dashboard</span>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#pharmacist-dashboard" class="nav-link flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200" data-role="pharmacist">
                            <i data-lucide="pills" class="w-5 h-5"></i>
                            <span>Pharmacist Dashboard</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="mt-auto pt-4 border-t border-gray-200">
                <a href="#" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                    <i data-lucide="settings" class="w-5 h-5"></i>
                    <span>Settings</span>
                </a>
                <a href="#" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <!-- Content Area -->
        <main class="flex-1 p-6 lg:ml-64 overflow-x-hidden">
            <!-- Admin Dashboard -->
            <section id="admin-dashboard" class="dashboard-section active-dashboard">
                <h2 class="text-3xl font-bold text-gray-800 mb-6">Admin Dashboard</h2>

                <!-- Dashboard Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="card flex flex-col items-start">
                        <i data-lucide="users" class="w-10 h-10 primary-blue-text mb-3"></i>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Manage Users</h3>
                        <p class="text-gray-600 mb-4">View, add, and edit system users.</p>
                        <button class="btn-outlined mt-auto">
                            <i data-lucide="user-plus" class="w-4 h-4"></i>
                            Add New User
                        </button>
                    </div>
                    <div class="card flex flex-col items-start">
                        <i data-lucide="settings-2" class="w-10 h-10 primary-blue-text mb-3"></i>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Hospital Settings</h3>
                        <p class="text-gray-600 mb-4">Configure departments, billing, and system preferences.</p>
                        <button class="btn-outlined mt-auto">
                            <i data-lucide="cog" class="w-4 h-4"></i>
                            Configure Settings
                        </button>
                    </div>
                    <div class="card flex flex-col items-start">
                        <i data-lucide="bar-chart-2" class="w-10 h-10 primary-blue-text mb-3"></i>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Overall Analytics</h3>
                        <p class="text-gray-600 mb-4">Monitor key hospital performance indicators.</p>
                        <button class="btn-outlined mt-auto">
                            <i data-lucide="line-chart" class="w-4 h-4"></i>
                            View Reports
                        </button>
                    </div>
                    <div class="card flex flex-col items-start">
                        <i data-lucide="bot" class="w-10 h-10 primary-blue-text mb-3"></i>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">AI Assistant</h3>
                        <p class="text-gray-600 mb-4">Get quick insights and support from the AI.</p>
                        <button class="btn-outlined mt-auto">
                            <i data-lucide="message-square" class="w-4 h-4"></i>
                            Open Assistant
                        </button>
                    </div>
                </div>

                <!-- Manage Users Section -->
                <div class="card mb-8">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">Manage Users</h3>
                    <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
                        <div class="relative w-full md:w-1/3">
                            <input type="text" placeholder="Search users..." class="form-input pl-10">
                            <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        </div>
                        <div class="w-full md:w-1/3">
                            <select class="form-input">
                                <option>Filter by Role</option>
                                <option>Admin</option>
                                <option>Doctor</option>
                                <option>Nurse</option>
                                <option>Receptionist</option>
                                <option>Lab Technician</option>
                                <option>Pharmacist</option>
                            </select>
                        </div>
                        <button class="btn-primary w-full md:w-auto">
                            <i data-lucide="plus" class="w-5 h-5"></i>
                            Add New User
                        </button>
                    </div>
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="table-hms">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Email</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Alice Smith</td>
                                    <td>Doctor</td>
                                    <td>alice.s@hms.com</td>
                                    <td>
                                        <button class="icon-btn text-primary-blue-text"><i data-lucide="edit" class="w-5 h-5"></i></button>
                                        <button class="icon-btn text-error-red"><i data-lucide="trash-2" class="w-5 h-5"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Bob Johnson</td>
                                    <td>Nurse</td>
                                    <td>bob.j@hms.com</td>
                                    <td>
                                        <button class="icon-btn text-primary-blue-text"><i data-lucide="edit" class="w-5 h-5"></i></button>
                                        <button class="icon-btn text-error-red"><i data-lucide="trash-2" class="w-5 h-5"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Charlie Brown</td>
                                    <td>Receptionist</td>
                                    <td>charlie.b@hms.com</td>
                                    <td>
                                        <button class="icon-btn text-primary-blue-text"><i data-lucide="edit" class="w-5 h-5"></i></button>
                                        <button class="icon-btn text-error-red"><i data-lucide="trash-2" class="w-5 h-5"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Hospital Settings Section -->
                <div class="card mb-8">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">Hospital Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="department" class="form-label">Department Name</label>
                            <input type="text" id="department" class="form-input mb-4" placeholder="e.g., Cardiology">
                            <button class="btn-primary">Add Department</button>
                        </div>
                        <div>
                            <label for="billing-code" class="form-label">Billing Code</label>
                            <input type="text" id="billing-code" class="form-input mb-4" placeholder="e.g., CPT-90210">
                            <label for="billing-desc" class="form-label">Description</label>
                            <input type="text" id="billing-desc" class="form-input mb-4" placeholder="e.g., Standard Consultation">
                            <button class="btn-primary">Save Billing Config</button>
                        </div>
                    </div>
                </div>

                <!-- Overall Hospital Analytics Section -->
                <div class="card mb-8">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">Overall Hospital Analytics</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <p class="text-gray-600">Placeholder for Patient Numbers Chart (e.g., Bar Chart)</p>
                            <img src="https://placehold.co/600x300/e0e7ff/3f51b5?text=Patient+Numbers+Chart" alt="Patient Numbers Chart" class="w-full h-auto mt-4 rounded-md">
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <p class="text-gray-600">Placeholder for Billing Trends Chart (e.g., Line Chart)</p>
                            <img src="https://placehold.co/600x300/ffe0b2/ff9800?text=Billing+Trends+Chart" alt="Billing Trends Chart" class="w-full h-auto mt-4 rounded-md">
                        </div>
                    </div>
                    <div class="mt-6 flex flex-wrap gap-4">
                        <div class="p-4 rounded-lg bg-blue-50 text-blue-800 font-medium flex-1 min-w-[150px]">
                            <p class="text-sm">Total Patients</p>
                            <p class="text-2xl font-bold">1,234</p>
                        </div>
                        <div class="p-4 rounded-lg bg-green-50 text-green-800 font-medium flex-1 min-w-[150px]">
                            <p class="text-sm">Appointments Today</p>
                            <p class="text-2xl font-bold">87</p>
                        </div>
                        <div class="p-4 rounded-lg bg-purple-50 text-purple-800 font-medium flex-1 min-w-[150px]">
                            <p class="text-sm">Revenue (Month)</p>
                            <p class="text-2xl font-bold">$1.2M</p>
                        </div>
                    </div>
                </div>

                <!-- AI Assistant Section -->
                <div class="card mb-8">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">AI Assistant</h3>
                    <div class="flex flex-col gap-4 h-64 overflow-y-auto border border-gray-200 rounded-lg p-4 bg-gray-50 mb-4">
                        <div class="flex justify-end">
                            <div class="bg-primary-blue text-white p-3 rounded-lg max-w-[70%]">
                                How many patients were admitted last month?
                            </div>
                        </div>
                        <div class="flex justify-start">
                            <div class="bg-gray-200 text-gray-800 p-3 rounded-lg max-w-[70%]">
                                Last month, 250 new patients were admitted.
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <div class="bg-primary-blue text-white p-3 rounded-lg max-w-[70%]">
                                Show me the busiest department.
                            </div>
                        </div>
                        <div class="flex justify-start">
                            <div class="bg-gray-200 text-gray-800 p-3 rounded-lg max-w-[70%]">
                                Cardiology had the highest patient visits.
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <input type="text" placeholder="Ask the AI assistant..." class="form-input flex-1">
                        <button class="btn-primary">
                            <i data-lucide="send" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
            </section>

            <!-- Doctor Dashboard -->
            <section id="doctor-dashboard" class="dashboard-section hidden">
                <h2 class="text-3xl font-bold text-gray-800 mb-6">Doctor Dashboard</h2>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Patient List -->
                    <div class="card lg:col-span-1 max-h-[calc(100vh-180px)] overflow-y-auto">
                        <h3 class="text-2xl font-semibold text-gray-800 mb-4">My Patients</h3>
                        <div class="relative mb-4">
                            <input type="text" placeholder="Search patients..." class="form-input pl-10">
                            <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        </div>
                        <div class="flex flex-col gap-4">
                            <div class="p-4 border border-gray-200 rounded-lg flex items-center gap-4 hover:bg-gray-50 cursor-pointer transition-colors duration-200">
                                <img src="https://placehold.co/50x50/e0e0e0/555?text=JS" alt="Patient Avatar" class="w-12 h-12 rounded-full">
                                <div>
                                    <p class="font-semibold text-gray-800">Jane Smith</p>
                                    <p class="text-sm text-gray-500">DOB: 1990-05-15</p>
                                </div>
                                <button class="ml-auto icon-btn text-primary-blue-text"><i data-lucide="folder-open" class="w-5 h-5"></i></button>
                            </div>
                            <div class="p-4 border border-gray-200 rounded-lg flex items-center gap-4 hover:bg-gray-50 cursor-pointer transition-colors duration-200">
                                <img src="https://placehold.co/50x50/e0e0e0/555?text=MA" alt="Patient Avatar" class="w-12 h-12 rounded-full">
                                <div>
                                    <p class="font-semibold text-gray-800">Michael Adams</p>
                                    <p class="text-sm text-gray-500">DOB: 1982-11-22</p>
                                </div>
                                <button class="ml-auto icon-btn text-primary-blue-text"><i data-lucide="folder-open" class="w-5 h-5"></i></button>
                            </div>
                            <div class="p-4 border border-gray-200 rounded-lg flex items-center gap-4 hover:bg-gray-50 cursor-pointer transition-colors duration-200">
                                <img src="https://placehold.co/50x50/e0e0e0/555?text=EM" alt="Patient Avatar" class="w-12 h-12 rounded-full">
                                <div>
                                    <p class="font-semibold text-gray-800">Emily White</p>
                                    <p class="text-sm text-gray-500">DOB: 1975-03-01</p>
                                </div>
                                <button class="ml-auto icon-btn text-primary-blue-text"><i data-lucide="folder-open" class="w-5 h-5"></i></button>
                            </div>
                        </div>
                    </div>

                    <!-- Patient Details / Medical Records -->
                    <div class="card lg:col-span-2">
                        <h3 class="text-2xl font-semibold text-gray-800 mb-4">Medical Record: Jane Smith</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="form-label">Diagnosis</label>
                                <input type="text" class="form-input" value="Seasonal Allergies">
                            </div>
                            <div>
                                <label class="form-label">Treatment Plan</label>
                                <textarea class="form-input h-24 resize-y">Antihistamines, nasal spray, avoid allergens.</textarea>
                            </div>
                            <div>
                                <label class="form-label">Progress Notes</label>
                                <textarea class="form-input h-24 resize-y">Patient reported significant improvement after starting medication. Advised to continue treatment for 2 weeks.</textarea>
                            </div>
                            <div>
                                <label class="form-label">Last Updated</label>
                                <input type="text" class="form-input bg-gray-50" value="2025-07-07 10:30 AM" readonly>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <button class="btn-primary">
                                <i data-lucide="save" class="w-5 h-5"></i>
                                Save Record
                            </button>
                            <button class="btn-outlined">
                                <i data-lucide="printer" class="w-5 h-5"></i>
                                Print Record
                            </button>
                        </div>

                        <h4 class="text-xl font-semibold text-gray-800 mt-8 mb-4">Upcoming Appointments</h4>
                        <div class="flex flex-col gap-3">
                            <div class="p-3 border border-gray-200 rounded-lg bg-blue-50 flex justify-between items-center">
                                <div>
                                    <p class="font-medium">Follow-up: Jane Smith</p>
                                    <p class="text-sm text-gray-600">2025-07-14, 10:00 AM</p>
                                </div>
                                <button class="btn-text text-blue-700">Reschedule</button>
                            </div>
                        </div>

                        <h4 class="text-xl font-semibold text-gray-800 mt-8 mb-4">Lab Tests & Prescriptions</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="card p-4">
                                <h5 class="font-semibold mb-2">Request Lab Test</h5>
                                <select class="form-input mb-3">
                                    <option>Select Test Type</option>
                                    <option>Complete Blood Count (CBC)</option>
                                    <option>Lipid Panel</option>
                                    <option>Urinalysis</option>
                                </select>
                                <button class="btn-primary w-full">Request Test</button>
                            </div>
                            <div class="card p-4">
                                <h5 class="font-semibold mb-2">Issue Prescription</h5>
                                <input type="text" class="form-input mb-3" placeholder="Medication Name (e.g., Amoxicillin)">
                                <input type="text" class="form-input mb-3" placeholder="Dosage (e.g., 250mg)">
                                <button class="btn-primary w-full">Issue Prescription</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Nurse Dashboard -->
            <section id="nurse-dashboard" class="dashboard-section hidden">
                <h2 class="text-3xl font-bold text-gray-800 mb-6">Nurse Dashboard</h2>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Patient Check-ins -->
                    <div class="card lg:col-span-1 max-h-[calc(100vh-180px)] overflow-y-auto">
                        <h3 class="text-2xl font-semibold text-gray-800 mb-4">Upcoming Check-ins</h3>
                        <div class="flex flex-col gap-4">
                            <div class="p-4 border border-gray-200 rounded-lg flex items-center gap-4 bg-yellow-50">
                                <img src="https://placehold.co/50x50/e0e0e0/555?text=RD" alt="Patient Avatar" class="w-12 h-12 rounded-full">
                                <div>
                                    <p class="font-semibold text-gray-800">Robert Davis</p>
                                    <p class="text-sm text-gray-500">Appointment: 11:00 AM</p>
                                </div>
                                <button class="btn-primary ml-auto">Check-in</button>
                            </div>
                            <div class="p-4 border border-gray-200 rounded-lg flex items-center gap-4">
                                <img src="https://placehold.co/50x50/e0e0e0/555?text=LS" alt="Patient Avatar" class="w-12 h-12 rounded-full">
                                <div>
                                    <p class="font-semibold text-gray-800">Laura Stone</p>
                                    <p class="text-sm text-gray-500">Appointment: 11:30 AM</p>
                                </div>
                                <button class="btn-primary ml-auto">Check-in</button>
                            </div>
                        </div>
                    </div>

                    <!-- Record Vitals & Notes -->
                    <div class="card lg:col-span-2">
                        <h3 class="text-2xl font-semibold text-gray-800 mb-4">Record Vitals: Robert Davis</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="form-label">Blood Pressure (mmHg)</label>
                                <input type="text" class="form-input" placeholder="e.g., 120/80">
                            </div>
                            <div>
                                <label class="form-label">Temperature (°C)</label>
                                <input type="number" step="0.1" class="form-input" placeholder="e.g., 37.0">
                            </div>
                            <div>
                                <label class="form-label">Heart Rate (bpm)</label>
                                <input type="number" class="form-input" placeholder="e.g., 72">
                            </div>
                            <div>
                                <label class="form-label">Oxygen Saturation (%)</label>
                                <input type="number" class="form-input" placeholder="e.g., 98">
                            </div>
                            <div class="md:col-span-2">
                                <label class="form-label">Nurse Notes</label>
                                <textarea class="form-input h-24 resize-y" placeholder="Enter observations..."></textarea>
                            </div>
                        </div>
                        <button class="btn-primary">
                            <i data-lucide="save" class="w-5 h-5"></i>
                            Save Vitals
                        </button>

                        <h4 class="text-xl font-semibold text-gray-800 mt-8 mb-4">Medical Supplies Usage</h4>
                        <div class="flex flex-col gap-4">
                            <div class="flex items-center gap-4">
                                <label class="form-label w-32">Gloves (Pairs)</label>
                                <input type="number" class="form-input flex-1" value="2">
                                <button class="btn-outlined">Update</button>
                            </div>
                            <div class="flex items-center gap-4">
                                <label class="form-label w-32">Syringes (Units)</label>
                                <input type="number" class="form-input flex-1" value="1">
                                <button class="btn-outlined">Update</button>
                            </div>
                            <div class="flex items-center gap-4">
                                <label class="form-label w-32">Bandages (Rolls)</label>
                                <input type="number" class="form-input flex-1" value="0">
                                <button class="btn-outlined">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Pharmacist Dashboard -->
            <section id="pharmacist-dashboard" class="dashboard-section hidden">
                <h2 class="text-3xl font-bold text-gray-800 mb-6">Pharmacist Dashboard</h2>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Dispense Medications -->
                    <div class="card">
                        <h3 class="text-2xl font-semibold text-gray-800 mb-4">Dispense Medications</h3>
                        <div class="mb-4">
                            <label class="form-label">Search Prescription (Patient Name/ID)</label>
                            <input type="text" class="form-input" placeholder="e.g., Jane Smith or RX12345">
                        </div>
                        <div class="p-4 border border-gray-200 rounded-lg bg-blue-50 mb-4">
                            <p class="font-semibold text-gray-800">Prescription ID: RX12345</p>
                            <p class="text-sm text-gray-600">Patient: Jane Smith</p>
                            <p class="text-sm text-gray-600 mb-2">Medication: Amoxicillin 250mg (Qty: 20)</p>
                            <p class="text-sm text-gray-600">Stock Available: <span class="font-bold text-green-700">150 units</span></p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Quantity to Dispense</label>
                            <input type="number" class="form-input" value="20">
                        </div>
                        <div class="flex justify-end">
                            <button class="btn-primary">
                                <i data-lucide="check-circle" class="w-5 h-5"></i>
                                Dispense Medication
                            </button>
                        </div>
                    </div>

                    <!-- Stock Management / Medicine Availability -->
                    <div class="card">
                        <h3 class="text-2xl font-semibold text-gray-800 mb-4">Stock Management</h3>
                        <div class="relative mb-4">
                            <input type="text" placeholder="Search medication..." class="form-input pl-10">
                            <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        </div>
                        <div class="overflow-x-auto rounded-lg border border-gray-200 mb-6">
                            <table class="table-hms">
                                <thead>
                                    <tr>
                                        <th>Medication</th>
                                        <th>Stock</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Amoxicillin 250mg</td>
                                        <td><span class="text-green-600 font-semibold">150</span></td>
                                        <td>
                                            <button class="icon-btn text-primary-blue-text"><i data-lucide="plus" class="w-5 h-5"></i></button>
                                            <button class="icon-btn text-error-red"><i data-lucide="minus" class="w-5 h-5"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Paracetamol 500mg</td>
                                        <td><span class="text-green-600 font-semibold">300</span></td>
                                        <td>
                                            <button class="icon-btn text-primary-blue-text"><i data-lucide="plus" class="w-5 h-5"></i></button>
                                            <button class="icon-btn text-error-red"><i data-lucide="minus" class="w-5 h-5"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Insulin (Vial)</td>
                                        <td><span class="text-warning-yellow font-semibold">15</span></td>
                                        <td>
                                            <button class="icon-btn text-primary-blue-text"><i data-lucide="plus" class="w-5 h-5"></i></button>
                                            <button class="icon-btn text-error-red"><i data-lucide="minus" class="w-5 h-5"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <h3 class="text-2xl font-semibold text-gray-800 mb-4">Sales Reports</h3>
                        <div class="flex gap-4 mb-4">
                            <div>
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-input">
                            </div>
                            <div>
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-input">
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button class="btn-primary">
                                <i data-lucide="file-text" class="w-5 h-5"></i>
                                Generate Report
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Modal Structure (Hidden by default) -->
    <div id="hms-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800" id="modal-title">Modal Title</h3>
                <button id="close-modal" class="icon-btn">
                    <i data-lucide="x" class="w-6 h-6 text-gray-700"></i>
                </button>
            </div>
            <div id="modal-body" class="text-gray-700 mb-6">
                <!-- Modal content goes here -->
                <p>This is a generic modal for displaying additional information or forms.</p>
                <div class="mt-4">
                    <label class="form-label">Example Input</label>
                    <input type="text" class="form-input" placeholder="Enter something...">
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <button class="btn-outlined" id="modal-cancel">Cancel</button>
                <button class="btn-primary" id="modal-confirm">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Snackbar Structure (Hidden by default) -->
    <div id="hms-snackbar" class="snackbar">
        <p id="snackbar-message">Action completed successfully!</p>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        const menuToggle = document.getElementById('menu-toggle');
        const closeMenu = document.getElementById('close-menu');
        const sideNav = document.getElementById('side-nav');
        const navLinks = document.querySelectorAll('.nav-link');
        const dashboardSections = document.querySelectorAll('.dashboard-section');

        // Toggle side navigation for mobile
        menuToggle.addEventListener('click', () => {
            sideNav.classList.remove('-translate-x-full');
        });

        closeMenu.addEventListener('click', () => {
            sideNav.classList.add('-translate-x-full');
        });

        // Handle navigation clicks to show/hide dashboards
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault(); // Prevent default anchor behavior

                // Close side nav on mobile after clicking a link
                if (window.innerWidth < 1024) { // Check if it's a mobile view
                    sideNav.classList.add('-translate-x-full');
                }

                // Remove active class from all links
                navLinks.forEach(nav => nav.classList.remove('active'));
                // Add active class to the clicked link
                link.classList.add('active');

                // Hide all dashboard sections
                dashboardSections.forEach(section => section.classList.add('hidden'));

                // Show the corresponding dashboard section
                const targetId = link.getAttribute('href').substring(1); // Remove '#'
                const targetSection = document.getElementById(targetId);
                if (targetSection) {
                    targetSection.classList.remove('hidden');
                }
            });
        });

        // Modal functionality (basic show/hide)
        const hmsModal = document.getElementById('hms-modal');
        const closeModalBtn = document.getElementById('close-modal');
        const modalCancelBtn = document.getElementById('modal-cancel');
        const modalConfirmBtn = document.getElementById('modal-confirm');
        const modalTitle = document.getElementById('modal-title');
        const modalBody = document.getElementById('modal-body');

        function showModal(title, contentHtml) {
            modalTitle.textContent = title;
            modalBody.innerHTML = contentHtml;
            hmsModal.classList.remove('hidden');
        }

        function hideModal() {
            hmsModal.classList.add('hidden');
        }

        closeModalBtn.addEventListener('click', hideModal);
        modalCancelBtn.addEventListener('click', hideModal);
        // Example of how to trigger the modal (you'd hook this up to buttons)
        // document.querySelector('.btn-primary').addEventListener('click', () => {
        //     showModal('Add New User', '<p>Enter user details:</p><input type="text" class="form-input mt-4" placeholder="Username">');
        // });

        // Snackbar functionality (basic show)
        const hmsSnackbar = document.getElementById('hms-snackbar');
        const snackbarMessage = document.getElementById('snackbar-message');

        function showSnackbar(message, duration = 3000) {
            snackbarMessage.textContent = message;
            hmsSnackbar.classList.remove('hidden');
            setTimeout(() => {
                hmsSnackbar.classList.add('hidden');
            }, duration);
        }

        // Example of how to trigger the snackbar
        // document.getElementById('modal-confirm').addEventListener('click', () => {
        //     hideModal();
        //     showSnackbar('User added successfully!');
        // });

        // Initial load: Ensure the Admin dashboard is visible and its link is active
        document.addEventListener('DOMContentLoaded', () => {
            const initialLink = document.querySelector('.nav-link.active');
            if (initialLink) {
                const targetId = initialLink.getAttribute('href').substring(1);
                const targetSection = document.getElementById(targetId);
                if (targetSection) {
                    targetSection.classList.remove('hidden');
                }
            } else {
                // Fallback if no active class is set initially (e.g., first load)
                const adminLink = document.querySelector('[data-role="admin"]');
                if (adminLink) {
                    adminLink.classList.add('active');
                    document.getElementById('admin-dashboard').classList.remove('hidden');
                }
            }
        });
    </script>
</body>
</html>
