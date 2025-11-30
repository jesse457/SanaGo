@extends('docs.index')

@section('content')
<h1 class="text-3xl font-bold mb-8">API and Route Reference</h1>
<p>This document provides a reference for the application's routes, organized by user role. These routes are defined in <code>routes/web.php</code> and <code>routes/tenant.php</code> and are handled by Livewire components.</p>

<h2 class="text-2xl font-bold mt-8 mb-4">Landlord Routes</h2>
<p>These routes are for the central application and are not tenant-specific.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Method</th>
            <th class="px-4 py-2">URI</th>
            <th class="px-4 py-2">Route Name</th>
            <th class="px-4 py-2">Livewire Component</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/</td>
            <td class="border px-4 py-2">home</td>
            <td class="border px-4 py-2">Home</td>
            <td class="border px-4 py-2">The landing page for the application.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/login</td>
            <td class="border px-4 py-2">login</td>
            <td class="border px-4 py-2">Auth.Login</td>
            <td class="border px-4 py-2">Displays the login page.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/forgot-password</td>
            <td class="border px-4 py-2">password.request</td>
            <td class="border px-4 py-2">Auth.Passwords.ForgotPassword</td>
            <td class="border px-4 py-2">Displays the forgot password page.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/reset-password/{token}</td>
            <td class="border px-4 py-2">password.reset</td>
            <td class="border px-4 py-2">Auth.Passwords.ResetPassword</td>
            <td class="border px-4 py-2">Displays the reset password page.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/dashboard</td>
            <td class="border px-4 py-2">landlord.dashboard</td>
            <td class="border px-4 py-2">Landlord.Dashboard</td>
            <td class="border px-4 py-2">Displays the landlord dashboard.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/settings</td>
            <td class="border px-4 py-2">settings</td>
            <td class="border px-4 py-2">Landlord.Settings</td>
            <td class="border px-4 py-2">Page for application settings.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/manage-tenants</td>
            <td class="border px-4 py-2">manage-tenants</td>
            <td class="border px-4 py-2">Landlord.ManageTenants</td>
            <td class="border px-4 py-2">Page for managing tenants.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/create-tenants</td>
            <td class="border px-4 py-2">create-tenants</td>
            <td class="border px-4 py-2">Landlord.CreateTenant</td>
            <td class="border px-4 py-2">Page to create a new tenant.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/feedbacks</td>
            <td class="border px-4 py-2">feedbacks</td>
            <td class="border px-4 py-2">Landlord.Feedback</td>
            <td class="border px-4 py-2">Page for viewing feedback.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/respond-feedback/{feedback}</td>
            <td class="border px-4 py-2">respond-feedback</td>
            <td class="border px-4 py-2">Landlord.RespondFeedback</td>
            <td class="border px-4 py-2">Page for responding to feedback.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/send-feedback</td>
            <td class="border px-4 py-2">send-feedback</td>
            <td class="border px-4 py-2">SendFeedback</td>
            <td class="border px-4 py-2">Page for sending feedback.</td>
        </tr>
    </tbody>
</table>

<h2 class="text-2xl font-bold mt-8 mb-4">Tenant Routes</h2>
<p>These routes are for the tenant-specific application.</p>

<h3 class="text-xl font-bold mt-8 mb-4">General Routes</h3>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Method</th>
            <th class="px-4 py-2">URI</th>
            <th class="px-4 py-2">Route Name</th>
            <th class="px-4 py-2">Livewire Component</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/</td>
            <td class="border px-4 py-2">tenant.root</td>
            <td class="border px-4 py-2">N/A</td>
            <td class="border px-4 py-2">Tenant's root page.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/login</td>
            <td class="border px-4 py-2">login</td>
            <td class="border px-4 py-2">Auth.Login</td>
            <td class="border px-4 py-2">Displays the login page.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">POST</td>
            <td class="border px-4 py-2">/logout</td>
            <td class="border px-4 py-2">auth.logout</td>
            <td class="border px-4 py-2">N/A</td>
            <td class="border px-4 py-2">Logs the user out.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/dashboard</td>
            <td class="border px-4 py-2">dashboard</td>
            <td class="border px-4 py-2">N/A</td>
            <td class="border px-4 py-2">Redirects the user to their role-specific dashboard.</td>
        </tr>
    </tbody>
</table>

<h3 class="text-xl font-bold mt-8 mb-4">Admin Routes</h3>
<p><strong>Prefix:</strong> /admin</p>
<p><strong>Middleware:</strong> role:admin</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Method</th>
            <th class="px-4 py-2">URI</th>
            <th class="px-4 py-2">Route Name</th>
            <th class="px-4 py-2">Livewire Component</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/dashboard</td>
            <td class="border px-4 py-2">admin.dashboard</td>
            <td class="border px-4 py-2">Tenants.Admin.Index</td>
            <td class="border px-4 py-2">Displays the admin dashboard.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/user-management</td>
            <td class="border px-4 py-2">admin.user-management</td>
            <td class="border px-4 py-2">Tenants.Admin.UserManagement</td>
            <td class="border px-4 py-2">Page for managing users.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/settings</td>
            <td class="border px-4 py-2">admin.settings</td>
            <td class="border px-4 py-2">Tenants.Admin.Settings</td>
            <td class="border px-4 py-2">Page for application settings.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/ai-assistant</td>
            <td class="border px-4 py-2">admin.ai-assistant</td>
            <td class="border px-4 py-2">Tenants.Admin.AiAssistant</td>
            <td class="border px-4 py-2">Displays the AI assistant interface.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/create-new-user</td>
            <td class="border px-4 py-2">admin.create-new-user</td>
            <td class="border px-4 py-2">Tenants.Admin.CreateNewUser</td>
            <td class="border px-4 py-2">Page to create a new user.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/user-activities</td>
            <td class="border px-4 py-2">admin.user-activities</td>
            <td class="border px-4 py-2">Tenants.Admin.UserActivities</td>
            <td class="border px-4 py-2">View user activity logs.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/send-feedback</td>
            <td class="border px-4 py-2">admin.admin-feedback</td>
            <td class="border px-4 py-2">Tenants.Admin.SendAdminFeedback</td>
            <td class="border px-4 py-2">Page for admins to send feedback.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/profile</td>
            <td class="border px-4 py-2">admin.profile</td>
            <td class="border px-4 py-2">Tenants.Admin.Profile</td>
            <td class="border px-4 py-2">Displays the admin's profile.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/user-shifts</td>
            <td class="border px-4 py-2">admin.user-shifts</td>
            <td class="border px-4 py-2">Tenants.Admin.Shifts</td>
            <td class="border px-4 py-2">Page for managing user shifts.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/revenue-report</td>
            <td class="border px-4 py-2">admin.revenue-report</td>
            <td class="border px-4 py-2">Tenants.Admin.RevenueDashboard</td>
            <td class="border px-4 py-2">Page for viewing revenue reports.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/feedback-history</td>
            <td class="border px-4 py-2">admin.feedback-history</td>
            <td class="border px-4 py-2">Tenants.Admin.AdminFeedbackHistory</td>
            <td class="border px-4 py-2">Page for viewing feedback history.</td>
        </tr>
    </tbody>
</table>

<h3 class="text-xl font-bold mt-8 mb-4">Doctor Routes</h3>
<p><strong>Prefix:</strong> /doctor</p>
<p><strong>Middleware:</strong> role:doctor</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Method</th>
            <th class="px-4 py-2">URI</th>
            <th class="px-4 py-2">Route Name</th>
            <th class="px-4 py-2">Livewire Component</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/dashboard</td>
            <td class="border px-4 py-2">doctor.dashboard</td>
            <td class="border px-4 py-2">Tenants.Doctor.Index</td>
            <td class="border px-4 py-2">Displays the doctor's dashboard.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/patients</td>
            <td class="border px-4 py-2">doctor.patients</td>
            <td class="border px-4 py-2">Tenants.Doctor.Patient</td>
            <td class="border px-4 py-2">Lists patients assigned to the doctor.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/appointments</td>
            <td class="border px-4 py-2">doctor.appointments</td>
            <td class="border px-4 py-2">Tenants.Doctor.DoctorAppointment</td>
            <td class="border px-4 py-2">View and manage appointments.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/medical-records</td>
            <td class="border px-4 py-2">doctor.medical-records</td>
            <td class="border px-4 py-2">Tenants.Doctor.MedicalRecord</td>
            <td class="border px-4 py-2">Access patient medical records.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/medical-explainer</td>
            <td class="border px-4 py-2">doctor.medical-explainer</td>
            <td class="border px-4 py-2">Tenants.Doctor.MedicalExplainer</td>
            <td class="border px-4 py-2">AI-powered medical explainer tool.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/patient-info/{patient}</td>
            <td class="border px-4 py-2">doctor.patient-info</td>
            <td class="border px-4 py-2">Tenants.Doctor.ViewPatientInfo</td>
            <td class="border px-4 py-2">View detailed information for a specific patient.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/consultation/{consultationId}</td>
            <td class="border px-4 py-2">doctor.consultation</td>
            <td class="border px-4 py-2">Tenants.Doctor.LabTestAndPrescription</td>
            <td class="border px-4 py-2">Page for handling a patient consultation.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/feedbacks</td>
            <td class="border px-4 py-2">doctor.feedbacks</td>
            <td class="border px-4 py-2">Tenants.Doctor.Feedbacks</td>
            <td class="border px-4 py-2">View feedback requests.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/send-feedback</td>
            <td class="border px-4 py-2">doctor.send-feedback</td>
            <td class="border px-4 py-2">Tenants.Doctor.SendFeedback</td>
            <td class="border px-4 py-2">Page to submit feedback.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/profile</td>
            <td class="border px-4 py-2">doctor.profile</td>
            <td class="border px-4 py-2">Tenants.Doctor.Profile</td>
            <td class="border px-4 py-2">Displays the doctor's profile.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/lab-request</td>
            <td class="border px-4 py-2">doctor.lab-request</td>
            <td class="border px-4 py-2">Tenants.Doctor.DoctorLabRequest</td>
            <td class="border px-4 py-2">Page for creating a lab request.</td>
        </tr>
    </tbody>
</table>

<h3 class="text-xl font-bold mt-8 mb-4">Receptionist Routes</h3>
<p><strong>Prefix:</strong> /receptionist</p>
<p><strong>Middleware:</strong> role:receptionist</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Method</th>
            <th class="px-4 py-2">URI</th>
            <th class="px-4 py-2">Route Name</th>
            <th class="px-4 py-2">Livewire Component</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/dashboard</td>
            <td class="border px-4 py-2">receptionist.dashboard</td>
            <td class="border px-4 py-2">Tenants.Receptionist.Index</td>
            <td class="border px-4 py-2">Displays the receptionist's dashboard.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/register-patient</td>
            <td class="border px-4 py-2">receptionist.register-patient</td>
            <td class="border px-4 py-2">Tenants.Receptionist.RegisterPatient</td>
            <td class="border px-4 py-2">Page to register a new patient.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/book-appointment</td>
            <td class="border px-4 py-2">receptionist.book-appointment</td>
            <td class="border px-4 py-2">Tenants.Receptionist.BookAppointment</td>
            <td class="border px-4 py-2">Page to book a new appointment.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/appointments</td>
            <td class="border px-4 py-2">receptionist.appointments</td>
            <td class="border px-4 py-2">Tenants.Receptionist.Appointments</td>
            <td class="border px-4 py-2">View and manage all appointments.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/patients</td>
            <td class="border px-4 py-2">receptionist.patients</td>
            <td class="border px-4 py-2">Tenants.Receptionist.Patients</td>
            <td class="border px-4 py-2">View and manage patient information.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/profile</td>
            <td class="border px-4 py-2">receptionist.profile</td>
            <td class="border px-4 py-2">Tenants.Receptionist.Profile</td>
            <td class="border px-4 py-2">Displays the receptionist's profile.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/checkin</td>
            <td class="border px-4 py-2">receptionist.checkin</td>
            <td class="border px-4 py-2">Tenants.Receptionist.Checkin</td>
            <td class="border px-4 py-2">Patient check-in interface.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/admit-patient/{admission}</td>
            <td class="border px-4 py-2">receptionist.admit-patient</td>
            <td class="border px-4 py-2">Tenants.Receptionist.AdmitPatient</td>
            <td class="border px-4 py-2">Page to handle patient admission.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/nurse/admission-details/{patient}</td>
            <td class="border px-4 py-2">receptionist.view-admission-details</td>
            <td class="border px-4 py-2">Tenants.Receptionist.ViewAdmissionDetails</td>
            <td class="border px-4 py-2">View details of a patient's admission.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/send-feedback</td>
            <td class="border px-4 py-2">receptionist.receptionist-feedback</td>
            <td class="border px-4 py-2">Tenants.Receptionist.ReceptionistFeedBack</td>
            <td class="border px-4 py-2">Page for receptionists to send feedback.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/feedback-history</td>
            <td class="border px-4 py-2">receptionist.feedback-history</td>
            <td class="border px-4 py-2">Tenants.Receptionist.Feedbacks</td>
            <td class="border px-4 py-2">Page for viewing feedback history.</td>
        </tr>
    </tbody>
</table>

<h3 class="text-xl font-bold mt-8 mb-4">Lab Technician Routes</h3>
<p><strong>Prefix:</strong> /lab-technician</p>
<p><strong>Middleware:</strong> role:lab-technician</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Method</th>
            <th class="px-4 py-2">URI</th>
            <th class="px-4 py-2">Route Name</th>
            <th class="px-4 py-2">Livewire Component</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/dashboard</td>
            <td class="border px-4 py-2">lab-technician.dashboard</td>
            <td class="border px-4 py-2">Tenants.LabTechnician.Index</td>
            <td class="border px-4 py-2">Displays the lab technician's dashboard.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/lab-results</td>
            <td class="border px-4 py-2">lab-technician.lab-results</td>
            <td class="border px-4 py-2">Tenants.LabTechnician.LabResult</td>
            <td class="border px-4 py-2">View and manage lab results.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/enter-results/{labRequest}</td>
            <td class="border px-4 py-2">lab-technician.enter-results</td>
            <td class="border px-4 py-2">Tenants.LabTechnician.EnterResults</td>
            <td class="border px-4 py-2">Page to enter results for a specific lab request.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/test-requests</td>
            <td class="border px-4 py-2">lab-technician.test-requests</td>
            <td class="border px-4 py-2">Tenants.LabTechnician.TestRequest</td>
            <td class="border px-4 py-2">View incoming lab test requests.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/manage-lab-definitions</td>
            <td class="border px-4 py-2">lab-technician.manage-lab-definitions</td>
            <td class="border px-4 py-2">Tenants.LabTechnician.ManageLabTestDefinitions</td>
            <td class="border px-4 py-2">Page to manage lab test definitions.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/create-lab-definitions</td>
            <td class="border px-4 py-2">lab-technician.create-lab-definitions</td>
            <td class="border px-4 py-2">Tenants.LabTechnician.CreateLabTestDefinition</td>
            <td class="border px-4 py-2">Page to create a new lab test definition.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/feedbacks</td>
            <td class="border px-4 py-2">lab-technician.feedbacks</td>
            <td class="border px-4 py-2">Tenants.LabTechnician.Feedbacks</td>
            <td class="border px-4 py-2">View feedback requests.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/send-feedback</td>
            <td class="border px-4 py-2">lab-technician.send-feedback</td>
            <td class="border px-4 py-2">Tenants.LabTechnician.SendFeedback</td>
            <td class="border px-4 py-2">Page to submit feedback.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/profile</td>
            <td class="border px-4 py-2">lab-technician.profile</td>
            <td class="border px-4 py-2">Tenants.LabTechnician.Profile</td>
            <td class="border px-4 py-2">Displays the lab technician's profile.</td>
        </tr>
    </tbody>
</table>

<h3 class="text-xl font-bold mt-8 mb-4">Nurse Routes</h3>
<p><strong>Prefix:</strong> /nurse</p>
<p><strong>Middleware:</strong> role:nurse</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Method</th>
            <th class="px-4 py-2">URI</th>
            <th class="px-4 py-2">Route Name</th>
            <th class="px-4 py-2">Livewire Component</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/dashboard</td>
            <td class="border px-4 py-2">nurse.dashboard</td>
            <td class="border px-4 py-2">Tenants.Nurse.Dashboard</td>
            <td class="border px-4 py-2">Displays the nurse's dashboard.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/medical-usage</td>
            <td class="border px-4 py-2">nurse.medical-usage</td>
            <td class="border px-4 py-2">Tenants.Nurse.SupplyUsage</td>
            <td class="border px-4 py-2">Log and track medical supply usage.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/record-vitals</td>
            <td class="border px-4 py-2">nurse.record-vitals</td>
            <td class="border px-4 py-2">Tenants.Nurse.RecordVitals</td>
            <td class="border px-4 py-2">Page to record patient vitals.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/profile</td>
            <td class="border px-4 py-2">nurse.profile</td>
            <td class="border px-4 py-2">Tenants.Nurse.Profile</td>
            <td class="border px-4 py-2">Displays the nurse's profile.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/feedbacks</td>
            <td class="border px-4 py-2">nurse.feedbacks</td>
            <td class="border px-4 py-2">Tenants.Nurse.Feedbacks</td>
            <td class="border px-4 py-2">View feedback requests.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/send-feedback</td>
            <td class="border px-4 py-2">nurse.send-feedback</td>
            <td class="border px-4 py-2">Tenants.Nurse.SendFeedback</td>
            <td class="border px-4 py-2">Page to submit feedback.</td>
        </tr>
    </tbody>
</table>

<h3 class="text-xl font-bold mt-8 mb-4">Pharmacist Routes</h3>
<p><strong>Prefix:</strong> /pharmacist</p>
<p><strong>Middleware:</strong> role:pharmacist</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Method</th>
            <th class="px-4 py-2">URI</th>
            <th class="px-4 py-2">Route Name</th>
            <th class="px-4 py-2">Livewire Component</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/dashboard</td>
            <td class="border px-4 py-2">pharmacist.dashboard</td>
            <td class="border px-4 py-2">Tenants.Pharmacist.Dashboard</td>
            <td class="border px-4 py-2">Displays the pharmacist's dashboard.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/feedbacks</td>
            <td class="border px-4 py-2">pharmacist.feedbacks</td>
            <td class="border px-4 py-2">Tenants.Pharmacist.FeedBackRequestList</td>
            <td class="border px-4 py-2">View feedback requests.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/submit-feedback</td>
            <td class="border px-4 py-2">pharmacist.submit-feedback</td>
            <td class="border px-4 py-2">Tenants.Pharmacist.SubmitFeedBack</td>
            <td class="border px-4 py-2">Page to submit feedback.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/manage-drugs</td>
            <td class="border px-4 py-2">pharmacist.manage-drugs</td>
            <td class="border px-4 py-2">Tenants.Pharmacist.ManageDrugsInventory</td>
            <td class="border px-4 py-2">Manage drug inventory.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/create-drugs</td>
            <td class="border px-4 py-2">pharmacist.create-drugs</td>
            <td class="border px-4 py-2">Tenants.Pharmacist.CreateDrugs</td>
            <td class="border px-4 py-2">Page to add new drugs to the inventory.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/sales-report</td>
            <td class="border px-4 py-2">pharmacist.sales-report</td>
            <td class="border px-4 py-2">Tenants.Pharmacist.SalesReport</td>
            <td class="border px-4 py-2">View and generate sales reports.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/medications</td>
            <td class="border px-4 py-2">pharmacist.medications</td>
            <td class="border px-4 py-2">Tenants.Pharmacist.Medications</td>
            <td class="border px-4 py-2">View and manage medication dispensations.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">GET</td>
            <td class="border px-4 py-2">/profile</td>
            <td class="border px-4 py-2">pharmacist.profile</td>
            <td class="border px-4 py-2">Tenants.Pharmacist.Profile</td>
            <td class="border px-4 py-2">Displays the pharmacist's profile.</td>
        </tr>
    </tbody>
</table>
@endsection
