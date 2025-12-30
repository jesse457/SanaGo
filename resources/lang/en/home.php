<?php

return [
    'name' => 'SanaGo',

    // Navigation
    'features' => 'System Features',
    'solutions' => 'Role-Based Access',
    'pricing' => 'Subscription Plans',
    'faq' => 'Support',
    'login' => 'Portal Login',

    // Hero
    'hero_explore' => 'Multi-Tenant SaaS Platform',
    'hero_title_p1' => 'Comprehensive',
    'hero_title_p2' => 'Healthcare Management',
    'hero_subtitle' => 'A unified, role-based ecosystem connecting Administrators, Doctors, Nurses, Receptionists, Lab Technicians, and Pharmacists for seamless patient care.',
    'hero_cta' => 'Request System Demo',

    // Features (Tabs)
    'features_title' => 'Integrated Healthcare Management',
    'features_subtitle' => 'One platform with six specialized role-based interfaces working in perfect sync.',
    // Role-Based Features (II. Role-Based Features)
    'tabs_data' => [
        [
            'icon' => 'admin',
            'title' => 'Administrator',
            'subtitle' => 'System Management',
            'heading' => 'Complete Hospital Oversight',
            'copy' => 'Empower administrators with real-time revenue dashboards, staff shift management, and granular department settings.',
            'bullets' => [
                ['title' => 'Revenue Dashboard', 'desc' => 'Track revenue from medications, appointments, lab tests, and admissions'],
                ['title' => 'Shift Management', 'desc' => 'Create and manage staff schedules and rotations'],
                ['title' => 'User Management', 'desc' => 'Control access and permissions for all staff roles'],
                ['title' => 'Hospital Settings', 'desc' => 'Configure departments, wards, beds, and medical supplies']
            ]
        ],
        [
            'icon' => 'doctor',
            'title' => 'Doctor',
            'subtitle' => 'Clinical Care',
            'heading' => 'Streamlined Patient Management',
            'copy' => 'Doctors can manage patient consultations, order lab tests, prescribe medications, and track comprehensive patient history.',
            'bullets' => [
                ['title' => 'Clinical Dashboard', 'desc' => 'View real-time patient status and medical history'],
                ['title' => 'Consultation Management', 'desc' => 'Record detailed diagnoses and treatment plans'],
                ['title' => 'Lab Test Ordering', 'desc' => 'Request tests and receive results instantly'],
                ['title' => 'e-Prescriptions', 'desc' => 'Digitally prescribe medications to the pharmacy module']
            ]
        ],
        [
            'icon' => 'receptionist',
            'title' => 'Receptionist',
            'subtitle' => 'Patient Front Desk',
            'heading' => 'Efficient Patient Processing',
            'copy' => 'Receptionists manage appointments, patient registrations, and admissions with streamlined automated workflows.',
            'bullets' => [
                ['title' => 'Appointment Booking', 'desc' => 'Schedule and manage patient doctor visits'],
                ['title' => 'Patient Registration', 'desc' => 'Register new patients and manage digital records'],
                ['title' => 'Admission Management', 'desc' => 'Process inpatient admissions and bed assignments'],
                ['title' => 'Billing & Payments', 'desc' => 'Generate invoices and process patient payments']
            ]
        ],
        [
            'icon' => 'lab',
            'title' => 'Lab Technician',
            'subtitle' => 'Laboratory Management',
            'heading' => 'High-Precision Lab Operations',
            'copy' => 'Lab technicians manage test requests, record results, and maintain the facility lab test catalog.',
            'bullets' => [
                ['title' => 'Request Management', 'desc' => 'Prioritize and process clinical lab requests'],
                ['title' => 'Digital Result Entry', 'desc' => 'Document and upload encrypted test results'],
                ['title' => 'Test Catalog', 'desc' => 'Manage available tests, templates, and pricing'],
                ['title' => 'Automated Alerts', 'desc' => 'Notify doctors and patients when results are ready']
            ]
        ],
        [
            'icon' => 'pharmacist',
            'title' => 'Pharmacist',
            'subtitle' => 'Pharmacy & Inventory',
            'heading' => 'Automated Medication Dispensing',
            'copy' => 'Pharmacists dispense medications accurately while tracking stock levels and inventory movements in real-time.',
            'bullets' => [
                ['title' => 'Dispensing Queue', 'desc' => 'Process digital prescriptions sent by doctors'],
                ['title' => 'Inventory Tracking', 'desc' => 'Manage medicine batches, expiry dates, and stock'],
                ['title' => 'Purchase Orders', 'desc' => 'Automate supply requests for low-stock items'],
                ['title' => 'Sales Reporting', 'desc' => 'Detailed tracking of daily medication revenue']
            ]
        ],
        [
            'icon' => 'nurse',
            'title' => 'Nurse',
            'subtitle' => 'Patient Monitoring',
            'heading' => 'Enhanced Ward Management',
            'copy' => 'Nurses monitor patient vitals, track daily care activities, and manage ward-level medical supplies.',
            'bullets' => [
                ['title' => 'Vitals Monitoring', 'desc' => 'Record and track blood pressure, pulse, and temperature'],
                ['title' => 'Medication Administration', 'desc' => 'Track scheduled doses for admitted patients'],
                ['title' => 'Ward Supply Tracking', 'desc' => 'Manage consumables and ward-specific equipment'],
                ['title' => 'Shift Documentation', 'desc' => 'Digital handovers and patient care logging']
            ]
        ]
            ],
    // Solutions (Cards)
    'solutions_title' => 'Role-Based Access System',
    'solutions_subtitle' => 'Specialized interfaces designed for the specific needs of each healthcare role.',
    'feature_explore' => 'Learn More',
    // Solutions Cards (Landing Page)
    'solutions_data' => [
        [
            'title' => 'Administrator',
            'badge' => 'System Control',
            'badgeClass' => 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300',
            'desc' => 'Oversight of hospital operations, staff shifts, revenue, and system configuration.',
            'points' => ['Revenue Dashboard', 'Shift Management', 'User Control', 'Activity Logs']
        ],
        [
            'title' => 'Doctor',
            'badge' => 'Clinical',
            'badgeClass' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
            'desc' => 'Management of patient consultations, lab orders, and digital prescriptions.',
            'points' => ['Consultation History', 'Vitals View', 'Test Ordering', 'Admission Requests']
        ],
        [
            'title' => 'Receptionist',
            'badge' => 'Front Desk',
            'badgeClass' => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
            'desc' => 'Patient registration, appointment scheduling, and admission workflows.',
            'points' => ['Patient Records', 'Doctor Scheduling', 'Admission/Discharge', 'Bed Search']
        ],
        [
            'title' => 'Lab Technician',
            'badge' => 'Laboratory',
            'badgeClass' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-300',
            'desc' => 'Process test requests, enter results, and manage the lab test catalog.',
            'points' => ['Test Requests', 'Result Management', 'Catalog Pricing', 'SMS Alerts']
        ],
        [
            'title' => 'Pharmacist',
            'badge' => 'Pharmacy',
            'badgeClass' => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
            'desc' => 'Dispense medication and manage the hospital drug inventory.',
            'points' => ['Medication Dispensing', 'Inventory Alerts', 'Top Seller Stats', 'Drug Management']
        ],
        [
            'title' => 'Nurse',
            'badge' => 'Patient Care',
            'badgeClass' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300',
            'desc' => 'Record patient vitals with notes and track ward supply usage.',
            'points' => ['Vitals Recording', 'Nurse Notes', 'Supply Tracking', 'Admission Overview']
        ]
    ],

    // Testimonials
    'testimonials_title' => 'Trusted by Healthcare Professionals',
    'testimonials_subtitle' => 'Real feedback from staff using our role-based hospital management system.',
    'testimonials_data' => [
        [
            'name' => 'Dr. Sarah Johnson',
            'role' => 'Chief of Medicine',
            'quote' => 'The role-based access ensures I only see the information relevant to my work, making patient management more efficient.'
        ],
        [
            'name' => 'Michael Chen',
            'role' => 'Hospital Administrator',
            'quote' => 'The revenue dashboard and user management features have transformed how we oversee hospital operations.'
        ],
        [
            'name' => 'Elena Rodriguez',
            'role' => 'Head Nurse',
            'quote' => 'Recording vitals and managing supplies through the dedicated nurse interface has streamlined our workflow significantly.'
        ],
        [
            'name' => 'James Wilson',
            'role' => 'Lab Technician',
            'quote' => 'The lab module makes it easy to manage test requests and deliver results quickly to physicians.'
        ],
        [
            'name' => 'Patricia Kim',
            'role' => 'Pharmacist',
            'quote' => 'The pharmacy interface helps us track inventory accurately and dispense medications efficiently.'
        ],
        [
            'name' => 'Amanda Davis',
            'role' => 'Receptionist',
            'quote' => 'Managing appointments and patient admissions has never been easier with our dedicated interface.'
        ]
    ],

    // Pricing
    'pricing_title' => 'Flexible Subscription Plans',
    'pricing_subtitle' => 'Scalable solutions for healthcare facilities of all sizes.',
    'limited_time' => 'Early Adopter',
    'exclusive_access' => 'Enterprise Plan',
    'free' => 'Demo',
    'apply_beta' => 'Request Access',
    'beta_program' => 'All Roles Included',

    // FAQ
    'faq_title' => 'Frequently Asked Questions',
    'faq_subtitle' => 'Learn more about our role-based hospital management system.',
    'faqs_data' => [
        [
            'q' => 'Is the system multi-language?',
            'a' => 'Yes, the entire interface toggles instantly between English and French to support diverse staff.'
        ],
        [
            'q' => 'How does the role-based access work?',
            'a' => 'Each user role (Administrator, Doctor, Nurse, Receptionist, Lab Technician, Pharmacist) has a specialized interface with only the features relevant to their job responsibilities.'
        ],
        [
            'q' => 'How is patient data secured?',
            'a' => 'We use industry-standard encryption for patient names and test results. The system includes comprehensive audit logs to track all user activities.'
        ],
        [
            'q' => 'Can we customize the system for our hospital?',
            'a' => 'Yes, administrators can configure departments, wards, bed types, and other settings to match your hospital\'s specific requirements.'
        ],
        [
            'q' => 'How does the multi-tenant architecture work?',
            'a' => 'Each hospital operates in its own secure environment with complete data isolation, while sharing the same application infrastructure for cost efficiency.'
        ]
    ],

    // Footer / CTA
    'cta_title' => 'Ready to Transform Your Hospital Management?',
    'cta_subtitle' => 'Join the network of healthcare facilities using our comprehensive role-based system.',
    'book_demo' => 'Get Started',
    'product' => 'Product',
    'company' => 'Company',
    'legal' => 'Legal',
    'about' => 'About',
    'careers' => 'Careers',
    'contact' => 'Contact',
    'privacy' => 'Privacy',
    'terms' => 'Terms',
    'dpa' => 'DPA',
    'systems_status' => 'Systems Operational'
];
