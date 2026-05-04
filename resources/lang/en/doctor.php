<?php

return [
    /*
    |--------------------------------------------------------------------------
    | General, Global & Navigation Translations
    |--------------------------------------------------------------------------
    */

    'home' => 'Home',
    'patients' => 'Patients',
    'appointments' => 'Appointments',
    'lab_requests' => 'Lab Requests',
    'clinical_assistant' => 'Clinical Assistant',
    'not_available' => 'N/A',
    'view_all' => 'View All',
    'view_details' => 'View Details',
    'actions' => 'Actions',
    'status' => 'Status',
    'date' => 'Date',
    'time' => 'Time',
    'id' => 'ID',
    'age' => 'Age',
    'phone' => 'Phone',
    'email' => 'Email',
    'name' => 'Name',
    'search' => 'Search',
    'all_statuses' => 'All Statuses',
    'clear_all_filters' => 'Clear all filters',
    'close' => 'Close',
    'open_navigation' => 'Open navigation',
    'saving' => 'Saving',

    /*
    |--------------------------------------------------------------------------
    | Time Buckets & Global Statuses
    |--------------------------------------------------------------------------
    | Used across multiple components (e.g., patient timeline, lab requests)
    */

    // Time Buckets (used by PHP helper)
    'today' => 'Today',
    'tomorrow' => 'Tomorrow',
    'yesterday' => 'Yesterday',
    'this_week' => 'This Week',
    'this_month' => 'This Month',
    'this_year' => 'This Year',
    'older' => 'Older',

    // Global Statuses (must match keys used in the application)
    'status_Pending' => 'Pending',
    'status_Completed' => 'Completed',
    'status_In_Consultation' => 'In Consultation',
    'status_In_Progress' => 'In Progress', // Used in Lab Requests
    'status_Cancelled' => 'Cancelled',
    'status_active' => 'Active',
    'status_inactive' => 'Inactive',
    'status_new' => 'New',

    /*
    |--------------------------------------------------------------------------
    | Doctor Dashboard Translations
    |--------------------------------------------------------------------------
    */

    // Header & Navigation
    'dashboard' => 'Dashboard',
    'search_placeholder' => 'Search...', // NEW: Global Search placeholder
    'welcome_message' => 'Welcome back, Dr. :name!', // :name is a placeholder
    'notifications' => 'Notifications',
    'user_avatar_alt' => 'User avatar',
    'my_profile' => 'My Profile',
    'settings' => 'Settings',
    'sign_out' => 'Sign Out',
    'greeting_time' => ':time', // NEW: Placeholder for 'Good morning', etc.
    'dashboard_overview_text' => 'Quickly view your key performance indicators, upcoming schedule, and the latest lab results requiring your attention.', // NEW: Welcome subtitle

    // KPI Cards
    'kpi_patients' => 'Patients Under Care', // NEW: KPI card label
    'kpi_appointments' => 'Upcoming Appointments', // NEW: KPI card label
    'kpi_lab_results' => 'New Lab Results', // NEW: KPI card label

    // Appointments Table
    'upcoming_schedule' => 'Upcoming Schedule', // NEW: Appointments Card Header
    'view_calendar' => 'View Calendar', // NEW: Appointments Card Link
    'patient' => 'Patient', // NEW: Table Header
    'action' => 'Action', // NEW: Table Header
    'reason' => 'Reason',
    'reason_for_visit' => 'Reason:', // Used in mobile card
    'no_upcoming_appointments' => 'No upcoming appointments found.',
    'add_new_appointment' => 'Add New Appointment',

    // Lab Results Table
    'latest_lab_results' => 'Latest Lab Results', // NEW: Lab Results Card Header
    'test_name' => 'Test Name', // NEW: Table Header
    'result_date' => 'Result Date',
    'no_new_lab_results' => 'No new lab results found.',

    /*
    |--------------------------------------------------------------------------
    | Patient List (My Patients) Translations
    |--------------------------------------------------------------------------
    */

    'my_patients' => 'My Patients',
    'my_patients_subtitle' => 'Manage and view detailed information for all patients under your care.',
    'search_patients' => 'Search Patients',
    'search_patient_placeholder_long' => 'Search patient UID, name or bed...',
    'sort_by' => 'Sort By',
    'filter_by_status' => 'Filter by Status',

    // Patient Card Details
    'uid' => 'UID',
    'last_visit' => 'Last Visit',
    'next_appt' => 'Next Appt',
    'none' => 'None',
    'starting_chat_with' => 'Starting chat with',
    'chat_with' => 'Chat with',
    'next' => 'Next Appt',
    'last' => 'Last Visit',
    'registered' => 'Registered',
    'quick_message' => 'Quick Message',

    // Empty State (Patient List)
    'no_patients_found_header' => 'No patients found.',
    'no_patients_found_subtext' => 'Try adjusting your search or filters.',
    'try_adjusting_search' => 'Try adjusting your search or filters.',
    'clear_search' => 'Clear Search',
    'no_patients_found' => 'No patients found',

    /*
    |--------------------------------------------------------------------------
    | Patient Consultation (New/Edit Record) Translations
    |--------------------------------------------------------------------------
    */

    'patient_consultation' => 'Patient Consultation',
    'clinical_consultation' => 'Clinical Consultation',
    'consultation_header_subtitle' => 'Record and manage patient consultation details for accurate medical history and follow-up.',
    'select_patient' => 'Select Patient',
    'search_by_name_or_id' => 'Search by name or ID…',
    'search_patient_placeholder' => 'Search by name, ID, or phone',
    'search_results' => 'Search Results',
    'select' => 'Select',
    'yrs' => 'yrs',
    'create_new' => 'Create new',

    // Patient Summary Card
    'unsaved_changes' => 'Unsaved Changes',

    // Clinical Assessment Section
    'clinical_assessment' => 'Clinical Assessment',
    'assessment' => 'Assessment',
    'assessment_subtitle' => 'Symptoms, findings, and working diagnosis.',
    'chief_complaint' => 'Chief Complaint',
    'chief_complaint_placeholder' => 'e.g., Acute cough, High blood pressure',
    'working_diagnosis' => 'Working Diagnosis',
    'working_diagnosis_placeholder' => 'e.g., Migraine with aura',
    'clinical_notes_and_plan' => 'Clinical Notes & Plan',
    'clinical_notes_placeholder' => 'Document patient history, exam findings, and treatment plan.',
    'add_template' => 'Add Template',

    // Attachments Section
    'attachments' => 'Attachments',
    'files' => 'Files',
    'click_to_upload' => 'Click to upload',
    'attachments_subtitle' => 'Upload optional labs, images, or consent forms.',
    'drag_drop_or_click_to_upload' => 'Drag & drop or click to upload',
    'file_upload_limits' => 'PNG, JPG, PDF up to 10MB.',
    'uploading' => 'Uploading',
    'attached_files' => 'Attached Files',
    'new_attachment_preview' => 'New attachment preview',
    'remove_attachment' => 'Remove attachment',
    'remove' => 'Remove',

    // Form Actions & Footer
    'save_draft' => 'Save Draft',
    'save_sign_final' => 'Save & Sign Final',
    'save_complete_record' => 'Save Complete Record',
    'consultation_id' => 'Consultation ID',
    'finalize_record' => 'Finalize Record',
    'processing' => 'Processing',

    // No Patient Selected Placeholder
    'no_patient_selected' => 'No Patient Selected',
    'select_patient_to_begin' => 'Please search for and select a patient to begin a consultation.',
    'ready_for_consultation' => 'Ready for Consultation',
    'use_search_above_instruction' => 'Please use the search bar above to find or select a patient to begin a new record.',

    /*
    |--------------------------------------------------------------------------
    | Patient Consultation Details (View Only) Translations
    |--------------------------------------------------------------------------
    */

    'consultation_details' => 'Consultation Details',
    'consultation_on' => 'Consultation on :date', // Laravel will replace :date
    'dr_name' => 'Dr. :name', // Laravel will replace :name
    'diagnosis' => 'Diagnosis',
    'no_diagnosis_provided' => 'No diagnosis provided.',
    'notes' => 'Notes',
    'no_notes_available' => 'No notes available.',

    // Prescriptions Tab
    'prescriptions' => 'Prescriptions',
    'prescription_num' => 'Prescription #:id', // Laravel will replace :id
    'medication' => 'Medication',
    'dosage_mg' => 'Dosage/ mg',
    'frequency_per_day' => 'Frequency/per Day',
    'duration_days' => 'Duration/ days',
    'quantity' => 'Qty',
    'unknown' => 'Unknown',
    'no_prescriptions_found' => 'No prescriptions found.',

    // Lab Results Tab
    'lab_results' => 'Lab Results',
    'lab_result_num' => 'Lab Result #:id', // Laravel will replace :id
    'results' => 'Results',
    'analysis_comments' => 'Analysis & Comments',
    'no_result_text_provided' => 'No result text provided.',
    'no_comments_provided' => 'No comments provided.',
    'no_lab_results_found' => 'No lab results found.',

    /*
    |--------------------------------------------------------------------------
    | Patient Information (Patient Info & Vitals Tabs) Translations
    |--------------------------------------------------------------------------
    */

    // Overview Card
    'patient_information' => 'Patient Information',
    'avatar' => 'Avatar',
    'patient_id' => 'Patient ID',
    'dob' => 'DOB',
    'gender' => 'Gender',
    'admission_pending' => 'Admission Pending',
    'admitted' => 'Admitted',
    'request_admission' => 'Request Admission',

    // Tabs
    'consultation_history' => 'Consultation History',
    'vitals' => 'Vitals',

    // Consultation History Timeline
    'dr' => 'Dr',
    'treatment_plan' => 'Treatment Plan',
    'complaint' => 'Complaint',
    'notes_header' => 'Notes',
    'lab_test' => 'Lab Test',
    'prescription' => 'Prescription',
    'no_consultations_found_header' => 'No Consultations Found',
    'no_consultations_found_subtext' => 'This patient does not have any medical records yet.',

    // Vitals Tab
    'recorded_by' => 'Recorded by',
    'abnormal' => 'ABNORMAL',
    'temp' => 'Temp',
    'blood_pressure' => 'Blood Pressure',
    'heart_rate' => 'Heart Rate',
    'spo2' => 'SpO₂',
    'weight' => 'Weight',
    'bmi' => 'BMI',
    'nurses_note' => "Nurse's Note",
    'no_vitals_found_header' => 'No Vitals Found',
    'no_vitals_found_subtext' => 'No vital signs have been recorded for this patient yet.',

    /*
    |--------------------------------------------------------------------------
    | Appointments View Translations
    |--------------------------------------------------------------------------
    */

    'appointments_overview' => 'Appointments Overview',
    'view_appointments_subtitle' => 'View your past and current appointments.',
    'patient_singular' => 'Patient', // Used as a single table header
    'scheduled' => 'Scheduled',
    'no_appointments_header' => 'No appointments scheduled.',
    'no_appointments_subtext' => 'No appointments found for the selected day.',
    'time_slot' => 'Time Slot',
    'close_modal' => 'Close modal', // Used for aria-label
    'end' => 'End',
    'view' => 'View',
    'start' => 'Start',

    /*
    |--------------------------------------------------------------------------
    | AI Clinical Assistant Translations
    |--------------------------------------------------------------------------
    */

    'ai_clinical_assistant' => 'AI Clinical Assistant',
    'ai_assistant_description' => 'Utilize the AI to get immediate explanations on medical concepts, explore possible diagnoses, or generate relevant questions for your patient consultations. This tool is designed to support your clinical decision-making.',

    // Patient Context Section
    'patient_context' => 'Patient Context',
    'find_or_select_patient' => 'Find or Select Patient',
    'ai_prompt_placeholder' => "e.g., Explain 'Myocardial Infarction' and its common treatment protocols, or 'Given a patient with persistent cough, fever, and fatigue, what are possible diagnoses and key questions to ask?'",
    'no_specific_patient_selected' => 'No specific patient selected',

    // Ask the AI Section
    'ask_the_ai' => 'Ask the AI',
    'generate' => 'Generate',
    'generating_insights' => 'Generating insights',

    // AI Responses Section
    'ai_responses' => 'AI Responses',
    'ai_initial_greeting' => "Hello, Doctor! I'm ready to assist you. Provide details about a patient, a medical concept, or symptoms, and I can help with explanations, potential diagnoses, or relevant questions for your patient interaction.",
    'you' => 'You',
    'example_user_prompt' => "Explain 'Myocardial Infarction' and its common treatment protocols.",
    'example_ai_response' => 'Myocardial Infarction (MI), commonly known as a heart attack, occurs when blood flow to a part of the heart is blocked for a prolonged period, usually by a blood clot. This blockage can damage or destroy heart muscle. Common treatment protocols include immediate revascularization (e.g., angioplasty with stent placement or bypass surgery) to restore blood flow, medications like aspirin, nitroglycerin, beta-blockers, and ACE inhibitors to manage symptoms and prevent further damage, and lifestyle changes (diet, exercise, smoking cessation) for long-term management.',

    /*
    |--------------------------------------------------------------------------
    | Lab Requests Translations
    |--------------------------------------------------------------------------
    */

    'lab_requests_subtitle' => 'View, search, and filter all lab requests.',
    'showing_requests' => 'Showing :first-:last of :total requests', // Laravel pluralization
    'search_lab_placeholder' => 'Search by Patient UID, Name, or Test Name...',
    'request_date' => 'Request Date',
    'view_results' => 'View Results',
    'results_pending' => 'Results Pending',
    'no_lab_requests_found' => 'No Lab Requests Found',
    'lab_requests_empty_subtext' => 'Try adjusting your search or filter criteria.',

    /*
    |--------------------------------------------------------------------------
    | Profile Translations
    |--------------------------------------------------------------------------
    */

    'profile' => 'Profile',
    'user' => 'User', // Fallback for Auth::user()->role
    'quick_info' => 'Quick Info',
    'address' => 'Address',

    // Personal Information Form
    'personal_information' => 'Personal Information',
    'update_profile' => 'Update Profile',

    // Change Password Form
    'change_password' => 'Change Password',
    'current_password' => 'Current Password',
    'new_password' => 'New Password',
    'confirm_password' => 'Confirm Password',
    'update_password' => 'Update Password',

    /*
    |--------------------------------------------------------------------------
    | Attachment Modal Translations (Global)
    |--------------------------------------------------------------------------
    */

    'attachment_preview' => 'Attachment Preview',
    'file_type_not_previewable' => 'This file type cannot be previewed.',
    'download_file' => 'Download File',
    'no_preview_available' => 'No preview available.',
    'fullscreen' => 'Fullscreen',
    'unsupported_file_type' => 'This file type cannot be previewed directly.',

    /*
    |--------------------------------------------------------------------------
    | MISSING KEYS FROM BLADE/PHP FILES (Navigation & Auth)
    |--------------------------------------------------------------------------
    */
    'menu_main' => 'Main Menu',
    'consultations' => 'Consultations',
    'feedback' => 'Feedback',
    'logout' => 'Sign Out',
];
