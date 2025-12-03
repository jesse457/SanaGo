<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Dashboard
    |--------------------------------------------------------------------------
    */
    // Dashboard Analytics Section Header & Navigation
    'dashboard_title' => 'Dashboard',
    'home' => 'Home',
    'guest' => 'Guest',
    'profile' => 'Profile',
    'settings' => 'Settings',
    'logout' => 'Logout',
    'dismiss' => 'Dismiss',
    'hospital_analytics' => 'Overall Hospital Analytics',
    'total_doctors' => 'Total Doctors',
    'specialists_gps' => 'Specialists & GPs',
    'total_patients' => 'Total Patients Registered',
    'all_registered_patients' => 'All registered patients',
    'beds_occupied' => 'Beds Occupied',
    'scheduled_today' => 'Scheduled for today',
    'revenue_today' => 'Revenue (Today)',
    'todays_sales' => 'Today\'s sales',
    'latest_activity_logs' => 'Latest Activity Logs',
    'no_recent_activity' => 'No recent activity.',
    'activity_type' => [
        'login' => 'Login',
        'logout' => 'Logout',
        'created' => 'Created',
        'updated' => 'Updated',
        'deleted' => 'Deleted',
    ],
    'user_role_summary' => 'User Role Summary',
    'table_header_role' => 'Role',
    'table_header_total_users' => 'Total Users',
    'table_header_active_users' => 'Active (Last 30 Days)',
    'table_header_last_added' => 'Last User Added',
    'not_available' => 'N/A',
    'no_role_data_available' => 'No user role data available.',
    'ensure_roles_configured' => 'Please ensure roles and users are configured.',

    /*
    |--------------------------------------------------------------------------
    | Admin Sidebar
    |--------------------------------------------------------------------------
    */

    'shifts_bar' => 'Shift Management',
    'revenue_report_bar' => 'Revenue Report',
    'user_management_bar' => 'User Management',
    'settings_bar' => 'Settings',
    'user_activities_bar' => 'User Activities',
    'feedbacks_bar' => 'Feedbacks',

    // Shifts Management
    'shifts_title' => '',
    'shifts_description' => '',
    'shifts_date' => '',
    'shifts_type' => '',
    'shifts_time' => '',
    'shifts_assigned' => '',
    'shifts_button' => '',
    'shifts_empty' => '',
    'shifts_start' => 'Start',
    'shifts_edit_title' => '',

    // Revenue Report
    'revenue_title' => 'Revenue Dashboard',
    'revenue_description' => 'An overview of the hospital\'s financial performance.',
    'financial_summary' => 'Financial Summary',
    'financial_summary_description' => 'Select a period to view revenue details.',

    // Time Period Filters
    'time_filter_today' => 'Today',
    'time_filter_week' => 'This Week',
    'time_filter_month' => 'This Month',
    'time_filter_year' => 'This Year',

    // Stats Cards
    'stat_total_revenue' => 'Total Revenue',
    'stat_total_revenue_desc' => 'Combined from all sources',
    'stat_medication_revenue' => 'Medication',
    'stat_medication_revenue_desc' => 'From pharmacy dispensations',
    'stat_appointments_revenue' => 'Appointments',
    'stat_appointments_revenue_desc' => 'From consultations',
    'stat_lab_revenue' => 'Lab Services',
    'stat_lab_revenue_desc' => 'From laboratory tests',

    // Patient Revenue Table
    'table_revenue_by_patient' => 'Revenue by Patient',
    'table_revenue_by_patient_desc' => 'Detailed breakdown of revenue generated per patient for the selected period.',
    'table_header_appointments' => 'Appointments',
    'table_header_medications' => 'Medications',
    'table_header_lab_tests' => 'Lab Tests',
    'table_header_total_revenue' => 'Total Revenue',
    'no_revenue_data_found' => 'No revenue data found for this period.',
    'try_selecting_different_frame' => 'Try selecting a different time frame.',

    // User Management Page
    'user_management_title' => 'User Management',
    'manage_users_title' => 'Manage Users',
    'manage_users_description' => 'View, search, and manage all user accounts.',
    'add_user_button' => 'Add User',

    // Search & Filters (User Management)
    'search_placeholder_users' => 'Search users by name, email...',
    'filter_all_roles' => 'All Roles',
    'role_admin' => 'Admin',
    'role_doctor' => 'Doctor',
    'role_nurse' => 'Nurse',
    'role_receptionist' => 'Receptionist',
    'role_lab_technician' => 'Lab Technician',
    'role_pharmacist' => 'Pharmacist',
    'filter_all_statuses' => 'All Statuses',
    'status_active' => 'Active',
    'status_inactive' => 'Inactive',

    // Table Headers (User Management)
    'table_header_name' => 'Name',
    'table_header_status' => 'Status',
    'table_header_action' => 'Action',

    // Table Actions (User Management)
    'action_edit' => 'Edit',
    'action_delete' => 'Delete',

    // Empty State (User Management)
    'no_users_found_title' => 'No Users Found',
    'no_users_found_text' => 'Try adjusting your search or filter to find what you\'re looking for.',
    'add_new_user_button' => 'Add New User',

    // Loading State
    'loading_text' => 'Loading...',

    // Edit User Modal
    'edit_user_modal_title' => 'Edit User:',
    'form_label_name' => 'Name',
    'form_label_email' => 'Email',
    'form_label_role' => 'Role',
    'form_label_phone' => 'Phone',
    'account_active' => 'Account Active',
    'account_disabled' => 'Account Disabled',

    // Shift Management in Modal
    'shift_management_section' => 'Shift Management',
    'assign_upcoming_shift_title' => 'Assign Upcoming Shift',
    'search_shifts_placeholder' => 'Search upcoming shifts...',
    'no_upcoming_shift' => 'No upcoming shift',
    'shift_history_title' => 'Shift History',
    'no_past_shifts_recorded' => 'No past shifts recorded for this user.',

    // Modal Actions
    'modal_button_cancel' => 'Cancel',
    'modal_button_save_changes' => 'Save Changes',
    'modal_button_saving' => 'Saving...',
    'toast_user_updated' => 'User updated successfully!',

    // --- User Activities Page ---
    'activities_page_title' => 'User Activities',
    'activities_page_description' => 'Monitor and review all user actions.',

    // Search & Filters (Activities)
    'activities_search_placeholder' => 'Search by user name, or email…',
    'activities_filter_date' => 'Date',
    'activities_filter_type' => 'Type',
    'activities_filter_all_types' => 'All types',
    'activities_reset_filters' => 'Reset',

    // Active Filters Badges
    'active_filters_label' => 'Active filters:',
    'active_filter_search' => 'Search:',
    'active_filter_type' => 'Type:',
    'active_filter_user' => 'User:',
    'active_filter_date' => 'Date:',

    // Loading State
    'activities_loading_text' => 'Loading activities…',

    // Table Headers (Activities)
    'activities_table_user' => 'User',
    'activities_table_type' => 'Type',
    'activities_table_description' => 'Description',
    'activities_table_time' => 'Time',
    'activities_table_actions' => 'Actions',
    'activities_unknown_user' => 'Unknown User',
    'activities_view_details' => 'View details',

    // Empty State (Activities)
    'activities_no_activities_found' => 'No activities found.',
    'activities_reset_filters_button' => 'Reset filters',

    // Pagination
    'activities_pagination_showing' => 'Showing',
    'activities_pagination_to' => 'to',
    'activities_pagination_of' => 'of',
    'activities_pagination_results' => 'results',

    // Activity Details Modal
    'modal_activity_details_title' => 'Activity Details',
    'modal_detail_user' => 'User:',
    'modal_detail_email' => 'Email:',
    'modal_detail_type' => 'Type:',
    'modal_detail_description' => 'Description:',
    'modal_detail_ip_address' => 'IP Address:',
    'modal_detail_user_agent' => 'User Agent:',
    'modal_detail_timestamp' => 'Timestamp:',
    'modal_button_close' => 'Close',

    // --- Add New User Page ---
    'create_user_profile_title' => 'Create a New User Profile',
    'manage_users_breadcrumb' => 'Manage Users',
    'add_new_user_breadcrumb' => 'Add New User',

    // Section 1: Personal Information
    'section_personal_info' => 'Personal Information',
    'label_full_name' => 'Full Name',
    'placeholder_full_name' => 'Enter full name',
    'label_phone_number' => 'Phone Number',
    'placeholder_phone_number' => 'e.g., +237612345678',
    'label_email' => 'Email',
    'placeholder_email' => 'Enter email',
    'label_address' => 'Address',
    'placeholder_address' => 'e.g., 123 Main St, Kumba',
    'label_gender' => 'Gender',
    'select_gender' => 'Select Gender',
    'gender_male' => 'Male',
    'gender_female' => 'Female',
    'gender_other' => 'Other',

    // Profile Picture Upload
    'label_profile_picture' => 'Profile Picture',
    'upload_file_button' => 'Upload a file',
    'upload_drag_drop' => 'or drag and drop',
    'upload_requirements' => 'PNG, JPG, GIF up to 2MB',
    'upload_error_failed' => 'Upload failed. Please try again.',
    'upload_error_invalid_type' => 'Please upload a valid image file (PNG, JPG, GIF)',
    'upload_error_max_size' => 'File size must be less than 2MB',

    // Section 2: Employment Details
    'section_employment_details' => 'Employment Details',
    'label_department' => 'Department',
    'select_department' => 'Select Department',
    'label_hire_date' => 'Hire Date',
    'label_role' => 'Role',
    'select_role' => 'Select Role',
    'label_account_status' => 'Account Status',
    'status_active_toggle' => 'Active',
    'status_inactive_toggle' => 'Inactive',

    // Form Actions
    'button_cancel' => 'Cancel',
    'button_create_user' => 'Create User',
    'button_creating' => 'Creating...',
    'error_prefix' => 'Error!',
    'session_error' => '{{ session(\'error\') }}',

    // --- User Profile Page ---
    'profile_title' => 'Profile',
    'profile_quick_info' => 'Quick Info',
    'profile_role_default' => 'User',

    // Quick Info Card Details
    'profile_info_phone' => 'Phone:',
    'profile_info_address' => 'Address:',
    'profile_info_na' => 'N/A',

    // Personal Information Form
    'profile_personal_info_title' => 'Personal Information',
    'profile_label_name' => 'Name',
    'profile_label_email' => 'Email',
    'profile_label_phone' => 'Phone',
    'profile_label_address' => 'Address',
    'profile_button_update_profile' => 'Update Profile',

    // Change Password Form
    'profile_change_password_title' => 'Change Password',
    'profile_label_current_password' => 'Current Password',
    'profile_label_new_password' => 'New Password',
    'profile_label_confirm_password' => 'Confirm Password',
    'profile_button_update_password' => 'Update Password',

    // --- Feedback History Page ---
    'feedback_history_title' => 'Feedback History',
    'feedback_history_description' => 'View, search, and manage user feedback submissions.',
    'feedback_button_submit' => 'Submit Feedback',

    // Search & Filters
    'feedback_search_placeholder' => 'Search subject, message or response...',
    'feedback_filter_all_categories' => 'All categories',
    'feedback_category_general' => 'General',
    'feedback_category_billing' => 'Billing',
    'feedback_category_technical' => 'Technical',
    'feedback_per_page' => ' / page',

    // Empty State
    'feedback_no_found_title' => 'No feedback found',
    'feedback_no_found_text' => 'Submit your first feedback using the button above.',
    'feedback_button_submit_first' => 'Submit Feedback',

    // Table Headers
    'feedback_table_subject' => 'Subject',
    'feedback_table_category' => 'Category',
    'feedback_table_priority' => 'Priority',
    'feedback_table_status' => 'Status',
    'feedback_table_submitted' => 'Submitted',
    'feedback_table_actions' => 'Actions',
    'feedback_table_no_subject' => 'No subject',

    // Table Actions
    'feedback_action_view' => 'View',

    // Priority & Status Labels
    'priority_low' => 'Low',
    'priority_normal' => 'Normal',
    'priority_high' => 'High',
    'priority_urgent' => 'Urgent',
    'priority_unknown' => 'Unknown',

    'status_pending' => 'Pending',
    'status_resolved' => 'Resolved',
    'status_closed' => 'Closed',
    'status_open' => 'Open',

    // Pagination
    'feedback_pagination_showing' => 'Showing',
    'feedback_pagination_to' => 'to',
    'feedback_pagination_of' => 'of',

    // Modal
    'modal_feedback_details_title' => 'Feedback Details',
    'modal_label_category' => 'Category',
    'modal_label_submitted' => 'Submitted',
    'modal_label_priority' => 'Priority',
    'modal_label_status' => 'Status',
    'modal_section_response' => 'Response',
    'modal_no_response_yet' => 'No response yet',

    'modal_responded_by' => 'Responded by',
    'modal_responded_staff_fallback' => 'Staff',
    'modal_button_close' => 'Close',
    'modal_loading' => 'Loading…',

    // --- Submit Feedback Page ---
    'submit_feedback_breadcrumb' => 'Submit Feedback',
    'submit_feedback_title' => 'Submit Feedback',
    'submit_feedback_description' => 'Your thoughts are important! Help us make it better.',

    // Form Header
    'form_header_title' => 'Help Us Make It Better',
    'form_header_description' => 'Your thoughts are important! Tell us what you think to help us improve.',
    'form_header_app_name' => 'SanaGo',

    // Form Section 1: Subject and Category
    'label_subject' => 'What is this about?',
    'placeholder_subject' => 'e.g., I have a problem with appointments',
    'label_category' => 'Which part of the system',
    'option_dashboard' => 'Dashboard',
    'option_shift_management' => 'Shift Management',
    'option_create_shifts' => 'Create Shifts',
    'option_revenue_report' => 'Revenue Report',
    'option_settings' => 'Settings',
    'option_user_activities' => 'User Activities',
    'option_user_management' => 'User Management',
    'option_create_new_user' => 'Create New User',

    // Form Section 2: Priority
    'label_priority' => 'How important is this?',
    'priority_button_low' => 'Low',
    'priority_button_normal' => 'Normal',
    'priority_button_high' => 'High',
    'priority_button_urgent' => 'Urgent',

    // Form Section 3: Message
    'label_message' => 'Tell us what happened',
    'placeholder_message' => 'Please describe what you experienced in your own words. The more details you give us, the better we can help.',
    'message_tip' => '**Tip:** Tell us what you were trying to do, what happened, and what you expected to happen instead.',

    // Form Actions & Footer
    'disclaimer_prefix' => 'By sending this, you agree to our',
    'disclaimer_rules' => 'rules',
    'disclaimer_and' => 'and',
    'disclaimer_privacy_policy' => 'privacy policy',
    'button_start_over' => 'Start Over',
    'button_send' => 'Send',
    'button_sending' => 'Sending...',

    // --- Hospital Settings Page ---
    'settings_title' => 'Hospital Settings',
    'settings_description' => 'Edit settings, track supplies, and manage wards and departments.',
    'settings_breadcrumb' => 'Settings',

    // 1. General Information
    'general_info_header' => 'General Information',
    'label_hospital_name' => 'Hospital Name',
    'placeholder_hospital_name' => 'e.g., City General Hospital',
    'label_hospital_address' => 'Address',
    'placeholder_hospital_address' => 'e.g., 123 Main St, Anytown, USA',
    'label_hospital_email' => 'Email Address',
    'placeholder_hospital_email' => 'e.g., info@hospital.com',
    'label_hospital_logo' => 'Hospital Logo',
    'logo_upload_tip' => 'Upload a new logo (Max 2MB, PNG/JPG).',
    'button_save_general_settings' => 'Save General Settings',
    'button_saving' => 'Saving...',

    // 2. Department Management
    'department_management_header' => 'Department Management',
    'label_new_department' => 'Department Name',
    'placeholder_new_department' => 'e.g., Pediatrics',
    'label_department_description' => 'Description (Optional)',
    'placeholder_department_description' => 'Brief description of the department\'s function.',
    'button_add_department' => 'Add Department',
    'button_adding_department' => 'Adding Department...',
    'label_existing_departments' => 'Existing Departments',
    'placeholder_search_departments' => 'Search departments...',
    'department_no_description' => 'No description provided.',
    'no_departments_found_title' => 'No departments found',
    'no_departments_found_tip' => 'Try a different search or add a new department.',

    // Department Edit Modal
    'modal_edit_department_title' => 'Edit Department',
    'label_edit_department_name' => 'Department Name',
    'label_edit_department_description' => 'Description',
    'modal_delete_department_title' => 'Delete Department',
    'modal_delete_department_message' => 'Are you sure you want to delete this department? This action cannot be undone.',

    // 3. Ward Management
    'ward_management_header' => 'Ward Management',
    'label_new_ward' => 'Ward Name',
    'placeholder_new_ward' => 'e.g., General Ward A',
    'label_ward_number' => 'Ward Number (Optional)',
    'placeholder_ward_number' => 'e.g., W-101',
    'label_assign_to_department' => 'Assign to Department',
    'option_select_department' => 'Select Department',
    'label_ward_description' => 'Description (Optional)',
    'placeholder_ward_description' => 'Brief description of the ward\'s purpose or characteristics.',
    'button_add_ward' => 'Add Ward',
    'button_adding_ward' => 'Adding Ward...',
    'label_existing_wards' => 'Existing Wards',
    'placeholder_search_wards' => 'Search wards...',
    'ward_info_no' => 'Ward No',
    'ward_info_dept' => 'Dept',
    'ward_info_na' => 'N/A',
    'ward_no_description' => 'No description provided.',
    'no_wards_found_title' => 'No wards found',
    'no_wards_found_tip' => 'Try a different search or add a new ward.',

    // Ward Edit Modal
    'modal_edit_ward_title' => 'Edit Ward',
    'label_edit_ward_name' => 'Ward Name',
    'label_edit_ward_number' => 'Ward Number',
    'label_edit_ward_department' => 'Department',
    'label_edit_ward_description' => 'Description',
    'modal_delete_ward_title' => 'Delete Ward',
    'modal_delete_ward_message' => 'Are you sure you want to delete this ward? This action cannot be undone.',

    // 4. Bed Type Management
    'bed_type_management_header' => 'Bed Type Management',
    'label_new_bed_type_name' => 'Bed Type Name',
    'placeholder_new_bed_type_name' => 'e.g., ICU Bed, Private Room',
    'label_bed_type_description' => 'Description (Optional)',
    'placeholder_bed_type_description' => 'Brief description of this bed type.',
    'label_bed_type_price' => 'Price Per Day ($)',
    'placeholder_bed_type_price' => 'e.g., 150.00',
    'button_add_bed_type' => 'Add Bed Type',
    'button_adding_bed_type' => 'Adding Bed Type...',
    'label_existing_bed_types' => 'Existing Bed Types',
    'placeholder_search_bed_types' => 'Search bed types...',
    'bed_type_price' => 'Price',
    'bed_type_price_per_day' => '/day',
    'bed_type_no_description' => 'No description provided.',
    'no_bed_types_found_title' => 'No bed type found',
    'no_bed_types_found_tip' => 'Try a different search or add a new bed type.',

    // Bed Type Edit Modal
    'modal_edit_bed_type_title' => 'Edit Bed Type',
    'label_edit_bed_type_name' => 'Bed Type Name',
    'label_edit_bed_type_description' => 'Description',
    'label_edit_bed_type_price' => 'Price Per Day ($)',
    'modal_delete_bed_type_title' => 'Delete Bed Type',
    'modal_delete_bed_type_message' => 'Are you sure you want to delete this bed type? This action cannot be undone.',

    // 5. Bed Management
    'bed_management_header' => 'Bed Management',
    'label_new_bed_number' => 'Bed Number',
    'placeholder_new_bed_number' => 'e.g., A-101, ICU-05',
    'label_bed_assign_ward' => 'Assign to Ward',
    'option_select_ward' => 'Select Ward',
    'label_bed_type' => 'Bed Type',
    'option_select_bed_type' => 'Select Bed Type',
    'button_add_bed' => 'Add Bed',
    'button_adding_bed' => 'Adding Bed...',
    'label_existing_beds' => 'Existing Beds',
    'placeholder_search_beds' => 'Search beds...',
    'bed_list_ward' => 'Ward',
    'bed_list_type' => 'Type',
    'no_beds_found_title' => 'No beds found',
    'no_beds_found_tip' => 'Try a different search or add a new bed.',

    // Bed Edit Modal
    'modal_edit_bed_title' => 'Edit Bed',
    'label_edit_bed_number' => 'Bed Number',
    'label_edit_bed_ward' => 'Ward',
    'label_edit_bed_type' => 'Bed Type',
    'modal_delete_bed_title' => 'Delete Bed',
    'modal_delete_bed_message' => 'Are you sure you want to delete this bed? This action cannot be undone.',

    // 6. Supply Management
    'supply_management_header' => 'Supply Management',
    'label_new_supply_name' => 'Supply Name',
    'placeholder_new_supply_name' => 'e.g., Bandages',
    'label_supply_unit_of_measure' => 'Unit of Measure (e.g., pcs, boxes)',
    'placeholder_supply_unit_of_measure' => 'e.g., boxes',
    'label_supply_current_stock' => 'Current Stock',
    'placeholder_supply_current_stock' => 'e.g., 100',
    'label_supply_min_stock_level' => 'Minimum Stock Level (Optional)',
    'placeholder_supply_min_stock_level' => 'e.g., 20',
    'button_add_supply' => 'Add Supply',
    'button_adding_supply' => 'Adding Supply...',
    'label_existing_supplies' => 'Existing Supplies',
    'placeholder_search_supplies' => 'Search supplies...',
    'supply_list_name' => 'Item',
    'supply_list_stock' => 'Stock',
    'supply_list_min_stock' => 'Min Stock',
    'supply_no_description' => 'No description provided.',
    'no_supplies_found_title' => 'No supplies found',
    'no_supplies_found_tip' => 'Try a different search or add a new supply.',

    // Supply Edit Modal
    'modal_edit_supply_title' => 'Edit Supply',
    'label_edit_supply_name' => 'Supply Name',
    'label_edit_supply_unit' => 'Unit of Measure',
    'label_edit_supply_stock' => 'Current Stock',
    'label_edit_supply_min_stock' => 'Minimum Stock Level',
    'modal_delete_supply_title' => 'Delete Supply',
    'modal_delete_supply_message' => 'Are you sure you want to delete this supply? This action cannot be undone.',

    // 7. Subscription Management
    'subscription_management_header' => 'Subscription Management',
    'subscription_current_plan' => 'Current Plan',
    'subscription_no_plan' => 'No Active Plan',
    'subscription_no_active_plan' => 'No Active Plan',
    'subscription_status_active' => 'Active',
    'subscription_status_trial' => 'On Trial',
    'subscription_status_none' => 'None',
    'subscription_price' => 'Price',
    'subscription_next_billing' => 'Next Billing Date',
    'subscription_trial_ends' => 'Trial Ends At',
    'subscription_upgrade_plan' => 'Upgrade Plan',
    'subscription_select_plan' => 'Select Plan',
    'subscription_cancel_plan' => 'Cancel Plan',
    'subscription_usage_statistics' => 'Usage Statistics',
    'subscription_users' => 'Users',
    'subscription_storage' => 'Storage',
    'subscription_plan_features' => 'Plan Features',
    'subscription_billing_history' => 'Billing History',
    'subscription_unknown' => 'Unknown Plan',
    'subscription_empty_state_desc' => ':default',
    'subscription_browse_plans' => 'Browse Plans',

    // Feature List (Used in loops)
    'subscription_feature_max_users' => 'User Limit',
    'subscription_feature_max_storage' => 'Storage Limit',
    'subscription_feature_api_access' => 'API Access',
    'subscription_feature_priority_support' => 'Priority Support',
    'subscription_feature_custom_domain' => 'Custom Domain',
    'subscription_feature_advanced_analytics' => 'Advanced Analytics',
    'subscription_feature_dedicated_support' => 'Dedicated Support',
    'subscription_feature_custom_integrations' => 'Custom Integrations',
    'subscription_no_features' => 'No features listed for this plan.',

    // Billing Table
    'billing_date' => 'Date',
    'billing_description' => 'Description',
    'billing_amount' => 'Amount',
    'billing_status' => 'Status',
    'billing_invoice' => 'Invoice',
    'billing_status_paid' => 'Paid',
    'billing_status_pending' => 'Pending',
    'billing_status_failed' => 'Failed',
    'billing_view_invoice' => 'View Invoice',
    'billing_no_history' => 'No billing history available.',

    // Subscription Modal
    'subscription_cancel_message' => 'Are you sure you want to cancel your subscription? You will lose access to premium features at the end of your current billing period.',
    'subscription_cancel_reason' => 'Reason for Cancellation',
    'subscription_select_reason' => 'Select a reason',
    'subscription_reason_too_expensive' => 'Too expensive',
    'subscription_reason_missing_features' => 'Missing features',
    'subscription_reason_switching_service' => 'Switching to another service',
    'subscription_reason_no_longer_needed' => 'No longer needed',
    'subscription_reason_other' => 'Other',
    'subscription_cancel_feedback' => 'Feedback (Optional)',
    'subscription_cancel_feedback_placeholder' => 'Please let us know how we can improve...',
    'subscription_confirm_cancel' => 'Confirm Cancellation',

    // General Modal Buttons
    'modal_button_cancel' => 'Cancel',
    'modal_button_save_changes' => 'Save Changes',
    'modal_button_delete' => 'Delete',
    'modal_button_edit' => 'Edit',
];
