<?php

return [
    // pharmacist.php
    'dashboard' => [
        // Dashboard Header
        'dashboard_title' => 'Dashboard',
        'welcome_message' => 'Welcome back, :name!',
        'breadcrumb_home' => 'Home',

        // Dropdown Menu
        'profile' => 'Profile',
        'logout' => 'Logout',

        // Flash Messages
        'view_all' => 'View All →',

        // Dashboard Cards
        'prescriptions_dispensed_today' => 'Prescriptions Dispensed Today',
        'prescriptions_pending' => 'Prescriptions Pending',
        'drugs_left_in_inventory' => 'Drugs Left in Inventory',

        // Top Sold Drugs Table
        'top_sold_drugs' => 'Top Sold Drugs',
        'medication' => 'Medication',
        'current_stock' => 'Current Stock',
        'min_level' => 'Min Level',
        'code' => 'Code',

        // Empty State
        'no_medications_found' => 'No Medications Found',
        'try_adjusting_filters' => 'Try adjusting your search or filters.',

        // Pagination
        'showing_to_of' => 'Showing :first to :last of :total',
    ],

    'create_drugs_page' => [
        // Create Drug Page
        'breadcrumb_home' => 'Home',
        'breadcrumb_manage_drugs' => 'Manage Drugs',
        'breadcrumb_create_drugs' => 'Create Drugs',
        'create_new_drug_title' => 'Create New Drug',
        'drug_name' => 'Drug Name',
        'drug_name_placeholder' => 'e.g., Paracetamol',
        'unit_price' => 'Unit Price',
        'unit_price_placeholder' => 'e.g., 2.50',
        'stock_quantity' => 'Stock Quantity',
        'stock_quantity_placeholder' => 'e.g., 500',
        'min_stock_level' => 'Min Stock Level',
        'min_stock_level_placeholder' => 'e.g., 50',
        'dosage_unit' => 'Dosage Unit',
        'dosage_unit_placeholder' => 'e.g., mg, tablets, ml',
        'description' => 'Description',
        'description_placeholder' => 'Provide a detailed description of the drug, its uses, and side effects.',
        'create_drug_button' => 'Create Drug',
        'creating_text' => 'Creating...',
    ],
    'create_drugs_component' => [
        // Validation Messages
        'validation_name_required' => 'The drug name field is required.',
        'validation_name_string' => 'The drug name must be a string.',
        'validation_name_max' => 'The drug name may not be greater than 255 characters.',
        'validation_unit_price_required' => 'The unit price field is required.',
        'validation_unit_price_numeric' => 'The unit price must be a number.',
        'validation_unit_price_min' => 'The unit price must be at least 0.',
        'validation_stock_quantity_required' => 'The stock quantity field is required.',
        'validation_stock_quantity_numeric' => 'The stock quantity must be a number.',
        'validation_stock_quantity_min' => 'The stock quantity must be at least 0.',
        'validation_min_stock_level_required' => 'The minimum stock level field is required.',
        'validation_min_stock_level_numeric' => 'The minimum stock level must be a number.',
        'validation_min_stock_level_min' => 'The minimum stock level must be at least 0.',
        'validation_description_string' => 'The description must be a string.',

        // Alert Messages
        'alert_success' => 'Success',
        'alert_drug_created_successfully' => 'Drug created successfully!',
        'alert_error' => 'Error',
        'alert_failed_to_create_drug' => 'Failed to create drug: :error',
    ],
    // pharmacist.php
    'manage_drugs_page' => [
        'manage_drugs_title' => 'Manage Drugs',
        'manage_drugs_description' => 'View, search, and manage all pharmaceutical products.',
        'add_new_drug_button' => 'Add New Drug',
        'search_by_drug_name' => 'Search by Drug Name...',
        'filter_all_drugs' => 'All Drugs',
        'filter_low_stock' => 'Low Stock',
        'filter_in_stock' => 'In Stock',
        'drug_name' => 'Drug Name',
        'stock_quantity' => 'Stock Quantity',  // Added this
        'price' => 'Price',
        'min_stock_level' => 'Min Stock Level',  // Added this
        'actions' => 'Actions',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'no_drugs_found' => 'No Drugs Found',
        'try_adjusting_search_filter' => 'Try adjusting your search or filter criteria.',
        'get_started_adding_drug' => 'Get started by adding a new drug.',
        'cancel' => 'Cancel',

        // Breadcrumbs
        'breadcrumb_home' => 'Dashboard',  // Added this
        'breadcrumb_manage_drugs' => 'Manage Drugs',  // Added this

        // Edit Drug Modal
        'edit_drug_title' => 'Edit Drug',
        'dosage_units' => 'Dosage Units',
        'price_usd' => 'Price (USD)',
        'minimum_stock_level' => 'Minimum Stock Level',
        'save_changes' => 'Save Changes',
    ],

    'manage_drugs_component' => [
        // Livewire Component: ManageDrugsInventory.php

        'alert_success' => 'Success',
        'alert_drug_updated_successfully' => 'Drug updated successfully',
        'alert_failed_to_update_drug' => 'Failed to update drug.',
        'alert_are_you_sure' => 'Are you sure?',
        'alert_confirm_delete_text' => 'You are about to delete :drug_name. This action cannot be undone.',
        'alert_drug_deleted_successfully' => 'Drug deleted successfully',
        'alert_failed_to_delete_drug' => 'Failed to delete drug.',
        'validation_drug_name_required' => 'The drug name field is required.',
        'validation_drug_name_max' => 'The drug name may not be greater than 255 characters.',
        'validation_dosage_units_max' => 'The dosage units may not be greater than 100 characters.',
        'validation_price_numeric' => 'The price must be a number.',
        'validation_price_min' => 'The price must be at least 0.',
        'validation_stock_quantity_integer' => 'The stock quantity must be an integer.',
        'validation_stock_quantity_min' => 'The stock quantity must be at least 0.',
        'validation_min_stock_level_integer' => 'The min stock level must be an integer.',
        'validation_min_stock_level_min' => 'The min stock level must be at least 0.',
        'validation_description_max' => 'The description may not be greater than 1000 characters.',
    ],
    'dispense_medications_page' => [
        // Dispense Medications Page
        'breadcrumb_dispense_medications' => 'Dispense Medications',
        'dispense_medications_title' => 'Dispense Medications',
        'dispense_medications_description' => 'Select a patient, review prescriptions, and dispense medications safely.',
        'search_patients' => 'Search Patients',
        'search_patients_placeholder' => 'Search patients by name or ID...',
        'patients' => 'Patients',
        'no_patients_found' => 'No Patients Found',
        'try_different_search' => 'Try a different search.',
        'prescriptions_for' => 'Prescriptions for',
        'select_prescription_view_items' => 'Select a prescription to view items and dispense.',
        'id' => 'ID',
        'date' => 'Date',
        'prescribed_by' => 'Prescribed By',
        'status' => 'Status',
        'action' => 'Action',
        'view_items' => 'View Items',
        'no_prescriptions_found' => 'No Prescriptions Found',
        'no_prescriptions_recorded' => 'No prescriptions have been recorded for this patient yet.',
        'select_a_patient' => 'Select a Patient',
        'choose_patient_to_view' => 'Choose a patient from the list to see their prescriptions.',
        'dispense_items_prescription' => 'Dispense Items — Prescription #:id',
        'patient' => 'Patient',
        'medication' => 'Medication',
        'prescribed' => 'Prescribed',
        'dispensed' => 'Dispensed',
        'remaining' => 'Remaining',
        'dispense_now' => 'Dispense Now',
        'stock' => 'Stock',
        'notes' => 'Notes',
        'no_items_available' => 'No Items Available',
        'no_items_available_description' => 'There are no items available for this prescription.',
        'dispensation_tip' => 'Enter the quantity to dispense now for each medication.',
        'save_dispensation' => 'Save Dispensation',
    ],

    'medications_component' => [
        // Livewire Component: Medications.php
        'alert_selected_patient_not_found' => 'Selected patient not found.',
        'alert_prescription_not_found' => 'Prescription not found.',
        'alert_no_prescription_selected' => 'No prescription selected.',
        'alert_no_new_items_to_dispense' => 'No new items to dispense. Please enter a quantity or a note.',
        'alert_dispensed_successfully' => 'Dispensed successfully.',
        'validation_quantity_must_be_whole' => 'Quantity must be a whole number.',
        'validation_quantity_cannot_be_negative' => 'Quantity cannot be negative.',
        'validation_notes_cannot_exceed' => 'Notes cannot exceed 500 characters.',
        'validation_quantity_min_for' => 'Quantity for :medication must be at least 1.',
        'validation_quantity_max_for' => 'Quantity for :medication cannot exceed the remaining amount (:remaining).',
        'activity_log_dispensed' => 'Pharmacist :pharmacist dispensed :quantity of :medication for prescription #:prescription_id',
        // Generic Alert Titles
        'alert_title_success' => 'Success',
        'alert_title_warning' => 'Warning',
        'alert_title_error' => 'Error',
        'alert_title_info' => 'Info',

        // Fallback User Name
        'fallback_pharmacist_name' => 'Unknown Pharmacist',

        // Exception Messages (for logs or user display)
        'exception_medication_not_found' => 'Medication not found for prescription item ID :itemId.',
        'exception_insufficient_stock' => 'Insufficient stock for :medication. Available: :available, required: :required.',
    ],

    'submit_feedback_page' => [  // Submit Feedback Page
        'breadcrumb_feedbacks' => 'Feedbacks',
        'breadcrumb_submit_feedback' => 'Submit Feedback',
        'submit_feedback_title' => 'Submit Feedback',
        'submit_feedback_description' => 'Your thoughts are important! Help us make it better.',
        'help_us_make_it_better' => 'Help Us Make It Better',
        'help_us_description' => 'Your thoughts are important! Tell us what you think to help us improve.',
        'subject_label' => 'What is this about?',
        'subject_placeholder' => 'e.g., I have a problem with appointments',
        'category_label' => 'Which part of the system',
        'category_dashboard' => 'Dashboard',
        'category_dispense_medication' => 'Dispense Medication',
        'category_manage_drugs' => 'Manage Drugs',
        'category_create_new_drugs' => 'Create New Drugs',
        'category_profile' => 'Profile',
        'message_label' => 'Tell us what happened',
        'message_placeholder' => 'Please describe what you experienced in your own words. The more details you give us, the better we can help.',
        'message_tip' => 'Tell us what you were trying to do, what happened, and what you expected to happen instead.',
        'agreement_text' => 'By sending this, you agree to our :rules and :privacy_policy.',
        'rules' => 'rules',
        'privacy_policy' => 'privacy policy',
        'start_over_button' => 'Start Over',
        'send_button' => 'Send',
        'sending_text' => 'Sending...',
    ],

    'profile' => [
        // Profile Page
        'quick_info' => 'Quick Info',
        'phone' => 'Phone',
        'address' => 'Address',
        'personal_information' => 'Personal Information',
        'name' => 'Name',
        'email' => 'Email',
        'update_profile' => 'Update Profile',
        'change_password' => 'Change Password',
        'current_password' => 'Current Password',
        'new_password' => 'New Password',
        'confirm_password' => 'Confirm Password',
        'update_password' => 'Update Password',
    ],
];
