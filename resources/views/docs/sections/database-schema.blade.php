@extends('docs.index')

@section('content')
<h1 class="text-3xl font-bold mb-8">Database Schema</h1>
<p>This document provides a detailed overview of the database schema for the AIHMS-vbeta application. The schema is organized into several tables that store information about patients, users, appointments, and other entities within the system.</p>

<h2 class="text-2xl font-bold mt-8 mb-4">Table Reference</h2>
<p>Below is a list of all the tables in the database, along with a brief description of their purpose.</p>
<ul class="list-disc list-inside">
    <li><a href="#admissions" class="text-blue-500 hover:underline">admissions</a></li>
    <li><a href="#appointments" class="text-blue-500 hover:underline">appointments</a></li>
    <li><a href="#beds" class="text-blue-500 hover:underline">beds</a></li>
    <li><a href="#bed_types" class="text-blue-500 hover:underline">bed_types</a></li>
    <li><a href="#departments" class="text-blue-500 hover:underline">departments</a></li>
    <li><a href="#dispensations" class="text-blue-500 hover:underline">dispensations</a></li>
    <li><a href="#feedback" class="text-blue-500 hover:underline">feedback</a></li>
    <li><a href="#invoices" class="text-blue-500 hover:underline">invoices</a></li>
    <li><a href="#lab_requests" class="text-blue-500 hover:underline">lab_requests</a></li>
    <li><a href="#lab_results" class="text-blue-500 hover:underline">lab_results</a></li>
    <li><a href="#lab_result_attachments" class="text-blue-500 hover:underline">lab_result_attachments</a></li>
    <li><a href="#lab_test_definitions" class="text-blue-500 hover:underline">lab_test_definitions</a></li>
    <li><a href="#medical_records" class="text-blue-500 hover:underline">medical_records</a></li>
    <li><a href="#medical_record_attachments" class="text-blue-500 hover:underline">medical_record_attachments</a></li>
    <li><a href="#medications" class="text-blue-500 hover:underline">medications</a></li>
    <li><a href="#notifications" class="text-blue-500 hover:underline">notifications</a></li>
    <li><a href="#patients" class="text-blue-500 hover:underline">patients</a></li>
    <li><a href="#prescriptions" class="text-blue-500 hover:underline">prescriptions</a></li>
    <li><a href="#prescription_items" class="text-blue-500 hover:underline">prescription_items</a></li>
    <li><a href="#procedure_kits" class="text-blue-500 hover:underline">procedure_kits</a></li>
    <li><a href="#revenue_summaries" class="text-blue-500 hover:underline">revenue_summaries</a></li>
    <li><a href="#supplies" class="text-blue-500 hover:underline">supplies</a></li>
    <li><a href="#supply_usages" class="text-blue-500 hover:underline">supply_usages</a></li>
    <li><a href="#tenants" class="text-blue-500 hover:underline">tenants</a></li>
    <li><a href="#users" class="text-blue-500 hover:underline">users</a></li>
    <li><a href="#user_activities" class="text-blue-500 hover:underline">user_activities</a></li>
    <li><a href="#user_shifts" class="text-blue-500 hover:underline">user_shifts</a></li>
    <li><a href="#vitals" class="text-blue-500 hover:underline">vitals</a></li>
    <li><a href="#wards" class="text-blue-500 hover:underline">wards</a></li>
</ul>

<h2 class="text-2xl font-bold mt-8 mb-4">Table Details</h2>

<h3 id="admissions" class="text-xl font-bold mt-8 mb-4">admissions</h3>
<p>Stores information about patient admissions.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">patient_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>patients</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">doctor_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>users</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">tenant_id</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">Foreign key to the <code>tenants</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">bed_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>beds</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">admission_date</td>
            <td class="border px-4 py-2">datetime</td>
            <td class="border px-4 py-2">The date and time of admission.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">observation_fee</td>
            <td class="border px-4 py-2">decimal</td>
            <td class="border px-4 py-2">The fee for observation.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">discharge_date</td>
            <td class="border px-4 py-2">datetime</td>
            <td class="border px-4 py-2">The date and time of discharge.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">reason_for_admission</td>
            <td class="border px-4 py-2">text</td>
            <td class="border px-4 py-2">The reason for admission.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">status</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The status of the admission.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
    </tbody>
</table>

<h3 id="appointments" class="text-xl font-bold mt-8 mb-4">appointments</h3>
<p>Stores information about appointments.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">patient_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>patients</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">doctor_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>users</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">tenant_id</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">Foreign key to the <code>tenants</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">appointment_date</td>
            <td class="border px-4 py-2">date</td>
            <td class="border px-4 py-2">The date of the appointment.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">appointment_time</td>
            <td class="border px-4 py-2">datetime</td>
            <td class="border px-4 py-2">The time of the appointment.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">price</td>
            <td class="border px-4 py-2">decimal</td>
            <td class="border px-4 py-2">The price of the appointment.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">reason_for_visit</td>
            <td class="border px-4 py-2">text</td>
            <td class="border px-4 py-2">The reason for the visit.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">status</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The status of the appointment.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">notes</td>
            <td class="border px-4 py-2">text</td>
            <td class="border px-4 py-2">Additional notes about the appointment.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">queue_position</td>
            <td class="border px-4 py-2">integer</td>
            <td class="border px-4 py-2">The position of the patient in the queue.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">actual_start_time</td>
            <td class="border px-4 py-2">datetime</td>
            <td class="border px-4 py-2">The actual start time of the appointment.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">actual_end_time</td>
            <td class="border px-4 py-2">datetime</td>
            <td class="border px-4 py-2">The actual end time of the appointment.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
    </tbody>
</table>

<h3 id="beds" class="text-xl font-bold mt-8 mb-4">beds</h3>
<p>Stores information about beds.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">ward_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>wards</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">tenant_id</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">Foreign key to the <code>tenants</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">bed_type_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>bed_types</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">bed_number</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The number of the bed.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">is_occupied</td>
            <td class="border px-4 py-2">boolean</td>
            <td class="border px-4 py-2">Whether the bed is occupied.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
    </tbody>
</table>

<h3 id="bed_types" class="text-xl font-bold mt-8 mb-4">bed_types</h3>
<p>Stores information about bed types.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">name</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The name of the bed type.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">tenant_id</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">Foreign key to the <code>tenants</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">description</td>
            <td class="border px-4 py-2">text</td>
            <td class="border px-4 py-2">A description of the bed type.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">price_per_day</td>
            <td class="border px-4 py-2">decimal</td>
            <td class="border px-4 py-2">The price per day for the bed type.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
    </tbody>
</table>

<h3 id="departments" class="text-xl font-bold mt-8 mb-4">departments</h3>
<p>Stores information about departments.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">tenant_id</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">Foreign key to the <code>tenants</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">name</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The name of the department.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">description</td>
            <td class="border px-4 py-2">text</td>
            <td class="border px-4 py-2">A description of the department.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
    </tbody>
</table>

<h3 id="dispensations" class="text-xl font-bold mt-8 mb-4">dispensations</h3>
<p>Stores information about dispensations.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">prescription_item_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>prescription_items</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">tenant_id</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">Foreign key to the <code>tenants</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">pharmacist_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>users</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">quantity_issued</td>
            <td class="border px-4 py-2">integer</td>
            <td class="border px-4 py-2">The quantity of the medication issued.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">batch_number</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The batch number of the medication.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">total_price</td>
            <td class="border px-4 py-2">decimal</td>
            <td class="border px-4 py-2">The total price of the dispensation.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">dispensed_at</td>
            <td class="border px-4 py-2">datetime</td>
            <td class="border px-4 py-2">The date and time of the dispensation.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
    </tbody>
</table>

<h3 id="feedback" class="text-xl font-bold mt-8 mb-4">feedback</h3>
<p>Stores feedback from users.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">subject</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The subject of the feedback.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">category</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The category of the feedback.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">priority</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The priority of the feedback.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">tenant_id</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">Foreign key to the <code>tenants</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">message</td>
            <td class="border px-4 py-2">text</td>
            <td class="border px-4 py-2">The feedback message.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">response</td>
            <td class="border px-4 py-2">text</td>
            <td class="border px-4 py-2">The response to the feedback.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">response_draft</td>
            <td class="border px-4 py-2">text</td>
            <td class="border px-4 py-2">A draft of the response to the feedback.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">user_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>users</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
    </tbody>
</table>

<h3 id="invoices" class="text-xl font-bold mt-8 mb-4">invoices</h3>
<p>Stores information about invoices.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">patient_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>patients</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">amount</td>
            <td class="border px-4 py-2">decimal</td>
            <td class="border px-4 py-2">The amount of the invoice.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">status</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The status of the invoice.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">payment_method</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The payment method used for the invoice.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
    </tbody>
</table>

<h3 id="lab_requests" class="text-xl font-bold mt-8 mb-4">lab_requests</h3>
<p>Stores information about lab requests.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">patient_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>patients</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">requested_by_doctor_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>users</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">lab_test_definition_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>lab_test_definitions</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">lab_tech_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>users</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">consultation_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>medical_records</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">reason_for_test</td>
            <td class="border px-4 py-2">text</td>
            <td class="border px-4 py-2">The reason for the test.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">urgency_level</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The urgency level of the test.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">request_date</td>
            <td class="border px-4 py-2">datetime</td>
            <td class="border px-4 py-2">The date and time of the request.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">status</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The status of the request.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
    </tbody>
</table>

<h3 id="lab_results" class="text-xl font-bold mt-8 mb-4">lab_results</h3>
<p>Stores information about lab results.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">lab_request_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>lab_requests</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">lab_technician_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>users</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">tenant_id</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">Foreign key to the <code>tenants</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">doctor_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>users</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">consultation_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>medical_records</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">result_date</td>
            <td class="border px-4 py-2">datetime</td>
            <td class="border px-4 py-2">The date and time of the result.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">results_text</td>
            <td class="border px-4 py-2">text</td>
            <td class="border px-4 py-2">The text of the results.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">analysis_comments</td>
            <td class="border px-4 py-2">text</td>
            <td class="border px-4 py-2">Comments on the analysis.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">status</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The status of the result.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
    </tbody>
</table>

<h3 id="lab_result_attachments" class="text-xl font-bold mt-8 mb-4">lab_result_attachments</h3>
<p>Stores attachments for lab results.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">lab_result_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>lab_results</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">tenant_id</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">Foreign key to the <code>tenants</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">file_path</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The path to the attachment file.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">file_name</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The name of the attachment file.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">file_type</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The type of the attachment file.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
    </tbody>
</table>

<h3 id="lab_test_definitions" class="text-xl font-bold mt-8 mb-4">lab_test_definitions</h3>
<p>Stores definitions for lab tests.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">test_name</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The name of the test.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">description</td>
            <td class="border px-4 py-2">text</td>
            <td class="border px-4 py-2">A description of the test.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">price</td>
            <td class="border px-4 py-2">decimal</td>
            <td class="border px-4 py-2">The price of the test.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">test_code</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The code for the test.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">normal_range</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The normal range for the test results.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">units</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The units for the test results.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
    </tbody>
</table>

<h3 id="medical_records" class="text-xl font-bold mt-8 mb-4">medical_records</h3>
<p>Stores medical records for patients.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">patient_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>patients</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">doctor_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>users</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">complaint</td>
            <td class="border px-4 py-2">text</td>
            <td class="border px-4 py-2">The patient's complaint.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">general_notes</td>
            <td class="border px-4 py-2">text</td>
            <td class="border px-4 py-2">General notes about the medical record.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">diagnosis_text</td>
            <td class="border px-4 py-2">text</td>
            <td class="border px-4 py-2">The diagnosis text.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">treatment_plan</td>
            <td class="border px-4 py-2">text</td>
            <td class="border px-4 py-2">The treatment plan.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">finalized</td>
            <td class="border px-4 py-2">boolean</td>
            <td class="border px-4 py-2">Whether the medical record is finalized.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">record_type</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The type of the medical record.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
    </tbody>
</table>

<h3 id="medical_record_attachments" class="text-xl font-bold mt-8 mb-4">medical_record_attachments</h3>
<p>Stores attachments for medical records.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">medical_record_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>medical_records</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">file_path</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The path to the attachment file.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">file_name</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The name of the attachment file.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">file_type</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The type of the attachment file.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
    </tbody>
</table>

<h3 id="medications" class="text-xl font-bold mt-8 mb-4">medications</h3>
<p>Stores information about medications.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">name</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The name of the medication.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">description</td>
            <td class="border px-4 py-2">text</td>
            <td class="border px-4 py-2">A description of the medication.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">stock_quantity</td>
            <td class="border px-4 py-2">integer</td>
            <td class="border px-4 py-2">The quantity of the medication in stock.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">min_stock_level</td>
            <td class="border px-4 py-2">integer</td>
            <td class="border px-4 py-2">The minimum stock level for the medication.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">unit_price_purchase</td>
            <td class="border px-4 py-2">decimal</td>
            <td class="border px-4 py-2">The purchase price per unit of the medication.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
    </tbody>
</table>

<h3 id="notifications" class="text-xl font-bold mt-8 mb-4">notifications</h3>
<p>Stores notifications for users.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">user_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>users</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">type</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The type of the notification.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">data</td>
            <td class="border px-4 py-2">json</td>
            <td class="border px-4 py-2">The data for the notification.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">is_read</td>
            <td class="border px-4 py-2">boolean</td>
            <td class="border px-4 py-2">Whether the notification has been read.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">read_at</td>
            <td class="border px-4 py-2">datetime</td>
            <td class="border px-4 py-2">The date and time when the notification was read.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
    </tbody>
</table>

<h3 id="patients" class="text-xl font-bold mt-8 mb-4">patients</h3>
<p>Stores information about patients.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">patient_uid</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The unique ID of the patient.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">first_name</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The first name of the patient.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">last_name</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The last name of the patient.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">phone</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The phone number of the patient.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">email</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The email address of the patient.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">address</td>
            <td class="border px-4 py-2">text</td>
            <td class="border px-4 py-2">The address of the patient.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">referral_note_path</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The path to the referral note file.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">age</td>
            <td class="border px-4 py-2">integer</td>
            <td class="border px-4 py-2">The age of the patient.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">gender</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The gender of the patient.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">is_admitted_approve</td>
            <td class="border px-4 py-2">boolean</td>
            <td class="border px-4 py-2">Whether the patient is approved for admission.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
    </tbody>
</table>

<h3 id="prescriptions" class="text-xl font-bold mt-8 mb-4">prescriptions</h3>
<p>Stores information about prescriptions.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">patient_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>patients</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">doctor_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>users</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">consultation_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>medical_records</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">prescription_date</td>
            <td class="border px-4 py-2">datetime</td>
            <td class="border px-4 py-2">The date and time of the prescription.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">general_notes</td>
            <td class="border px-4 py-2">text</td>
            <td class="border px-4 py-2">General notes about the prescription.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">status</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The status of the prescription.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
    </tbody>
</table>

<h3 id="prescription_items" class="text-xl font-bold mt-8 mb-4">prescription_items</h3>
<p>Stores items for prescriptions.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">prescription_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>prescriptions</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">medication_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>medications</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">dosage</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The dosage of the medication.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">frequency</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The frequency of the medication.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">duration</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The duration of the medication.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">quantity_prescribed</td>
            <td class="border px-4 py-2">integer</td>
            <td class="border px-4 py-2">The quantity of the medication prescribed.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">dispensed_quantity</td>
            <td class="border px-4 py-2">integer</td>
            <td class="border px-4 py-2">The quantity of the medication dispensed.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">notes</td>
            <td class="border px-4 py-2">text</td>
            <td class="border px-4 py-2">Additional notes about the prescription item.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
    </tbody>
</table>

<h3 id="procedure_kits" class="text-xl font-bold mt-8 mb-4">procedure_kits</h3>
<p>Stores information about procedure kits.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">name</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The name of the procedure kit.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">description</td>
            <td class="border px-4 py-2">text</td>
            <td class="border px-4 py-2">A description of the procedure kit.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">price</td>
            <td class="border px-4 py-2">decimal</td>
            <td class="border px-4 py-2">The price of the procedure kit.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">kit_code</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The code for the procedure kit.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">is_active</td>
            <td class="border px-4 py-2">boolean</td>
            <td class="border px-4 py-2">Whether the procedure kit is active.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
    </tbody>
</table>

<h3 id="revenue_summaries" class="text-xl font-bold mt-8 mb-4">revenue_summaries</h3>
<p>Stores revenue summaries.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">patient_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>patients</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">transaction_date</td>
            <td class="border px-4 py-2">date</td>
            <td class="border px-4 py-2">The date of the transaction.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">medication_revenue</td>
            <td class="border px-4 py-2">decimal</td>
            <td class="border px-4 py-2">The revenue from medication.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">appointment_revenue</td>
            <td class="border px-4 py-2">decimal</td>
            <td class="border px-4 py-2">The revenue from appointments.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">lab_revenue</td>
            <td class="border px-4 py-2">decimal</td>
            <td class="border px-4 py-2">The revenue from lab tests.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
    </tbody>
</table>

<h3 id="supplies" class="text-xl font-bold mt-8 mb-4">supplies</h3>
<p>Stores information about supplies.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">name</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The name of the supply.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">tenant_id</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">Foreign key to the <code>tenants</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">unit_of_measure</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The unit of measure for the supply.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">current_stock</td>
            <td class="border px-4 py-2">integer</td>
            <td class="border px-4 py-2">The current stock of the supply.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">min_stock_level</td>
            <td class="border px-4 py-2">integer</td>
            <td class="border px-4 py-2">The minimum stock level for the supply.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
    </tbody>
</table>

<h3 id="supply_usages" class="text-xl font-bold mt-8 mb-4">supply_usages</h3>
<p>Stores information about supply usages.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">supply_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>supplies</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">tenant_id</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">Foreign key to the <code>tenants</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">user_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>users</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">patient_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>patients</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">quantity_used</td>
            <td class="border px-4 py-2">integer</td>
            <td class="border px-4 py-2">The quantity of the supply used.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">usage_date</td>
            <td class="border px-4 py-2">datetime</td>
            <td class="border px-4 py-2">The date and time of the usage.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
    </tbody>
</table>

<h3 id="tenants" class="text-xl font-bold mt-8 mb-4">tenants</h3>
<p>Stores information about tenants.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">data</td>
            <td class="border px-4 py-2">json</td>
            <td class="border px-4 py-2">Additional data for the tenant.</td>
        </tr>
    </tbody>
</table>

<h3 id="users" class="text-xl font-bold mt-8 mb-4">users</h3>
<p>Stores information about users.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">name</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The name of the user.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">phone_number</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The phone number of the user.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">role</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The role of the user.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">email</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The email address of the user.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">password</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The password of the user.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">is_active</td>
            <td class="border px-4 py-2">boolean</td>
            <td class="border px-4 py-2">Whether the user is active.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">department_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>departments</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">tenant_id</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">Foreign key to the <code>tenants</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">can_assign_shift</td>
            <td class="border px-4 py-2">boolean</td>
            <td class="border px-4 py-2">Whether the user can assign shifts.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">profile_picture</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The path to the user's profile picture.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">address</td>
            <td class="border px-4 py-2">text</td>
            <td class="border px-4 py-2">The address of the user.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">date_of_birth</td>
            <td class="border px-4 py-2">date</td>
            <td class="border px-4 py-2">The date of birth of the user.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">gender</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The gender of the user.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">hire_date</td>
            <td class="border px-4 py-2">date</td>
            <td class="border px-4 py-2">The hire date of the user.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
    </tbody>
</table>

<h3 id="user_activities" class="text-xl font-bold mt-8 mb-4">user_activities</h3>
<p>Stores user activities.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">user_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>users</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">activity_type</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The type of the activity.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">description</td>
            <td class="border px-4 py-2">text</td>
            <td class="border px-4 py-2">A description of the activity.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">ip_address</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The IP address of the user.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">tenant_id</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">Foreign key to the <code>tenants</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">user_agent</td>
            <td class="border px-4 py-2">text</td>
            <td class="border px-4 py-2">The user agent of the user.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">properties</td>
            <td class="border px-4 py-2">json</td>
            <td class="border px-4 py-2">Additional properties of the activity.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
    </tbody>
</table>

<h3 id="user_shifts" class="text-xl font-bold mt-8 mb-4">user_shifts</h3>
<p>Stores user shifts.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">shift_type</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The type of the shift.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">start_time</td>
            <td class="border px-4 py-2">datetime</td>
            <td class="border px-4 py-2">The start time of the shift.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">tenant_id</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">Foreign key to the <code>tenants</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">end_time</td>
            <td class="border px-4 py-2">datetime</td>
            <td class="border px-4 py-2">The end time of the shift.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">shift_date</td>
            <td class="border px-4 py-2">date</td>
            <td class="border px-4 py-2">The date of the shift.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
    </tbody>
</table>

<h3 id="vitals" class="text-xl font-bold mt-8 mb-4">vitals</h3>
<p>Stores vital signs for patients.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">patient_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>patients</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">medical_record_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>medical_records</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">nurse_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>users</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">recorded_at</td>
            <td class="border px-4 py-2">datetime</td>
            <td class="border px-4 py-2">The date and time when the vitals were recorded.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">tenant_id</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">Foreign key to the <code>tenants</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">temperature_celsius</td>
            <td class="border px-4 py-2">decimal</td>
            <td class="border px-4 py-2">The temperature in Celsius.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">blood_pressure_systolic</td>
            <td class="border px-4 py-2">integer</td>
            <td class="border px-4 py-2">The systolic blood pressure.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">blood_pressure_diastolic</td>
            <td class="border px-4 py-2">integer</td>
            <td class="border px-4 py-2">The diastolic blood pressure.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">heart_rate_bpm</td>
            <td class="border px-4 py-2">integer</td>
            <td class="border px-4 py-2">The heart rate in beats per minute.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">respiratory_rate</td>
            <td class="border px-4 py-2">integer</td>
            <td class="border px-4 py-2">The respiratory rate.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">spo2_percentage</td>
            <td class="border px-4 py-2">integer</td>
            <td class="border px-4 py-2">The SpO2 percentage.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">weight_kg</td>
            <td class="border px-4 py-2">decimal</td>
            <td class="border px-4 py-2">The weight in kilograms.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">bmi</td>
            <td class="border px-4 py-2">decimal</td>
            <td class="border px-4 py-2">The body mass index.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">flag_abnormal</td>
            <td class="border px-4 py-2">boolean</td>
            <td class="border px-4 py-2">Whether the vitals are abnormal.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">notes</td>
            <td class="border px-4 py-2">text</td>
            <td class="border px-4 py-2">Additional notes about the vitals.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
    </tbody>
</table>

<h3 id="wards" class="text-xl font-bold mt-8 mb-4">wards</h3>
<p>Stores information about wards.</p>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Column</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Primary key.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">name</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The name of the ward.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">tenant_id</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">Foreign key to the <code>tenants</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">ward_number</td>
            <td class="border px-4 py-2">varchar</td>
            <td class="border px-4 py-2">The number of the ward.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">department_id</td>
            <td class="border px-4 py-2">bigint</td>
            <td class="border px-4 py-2">Foreign key to the <code>departments</code> table.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">description</td>
            <td class="border px-4 py-2">text</td>
            <td class="border px-4 py-2">A description of the ward.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">created_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was created.</td>
        </tr>
        <tr>
            <td class="border px-4 py-2">updated_at</td>
            <td class="border px-4 py-2">timestamp</td>
            <td class="border px-4 py-2">The timestamp when the record was last updated.</td>
        </tr>
    </tbody>
</table>
@endsection
