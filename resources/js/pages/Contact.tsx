import { Mail, MapPin, Phone, Clock } from 'lucide-react';
import DemoRequestForm from '@/Components/marketing/DemoRequestForm';
import SectionHeading from '@/Components/marketing/SectionHeading';
import PublicLayout from '@/Layouts/PublicLayout';

const CONTACT_INFO = [
    {
        icon: <Mail size={20} />,
        title: 'Email',
        details: ['sales@sanago.com', 'support@sanago.com'],
    },
    {
        icon: <Phone size={20} />,
        title: 'Phone',
        details: ['+1 (555) 123-4567', 'Mon–Fri, 9am–6pm EST'],
    },
    {
        icon: <MapPin size={20} />,
        title: 'Office',
        details: ['123 Healthcare Drive', 'San Francisco, CA 94102'],
    },
    {
        icon: <Clock size={20} />,
        title: 'Hours',
        details: ['Monday–Friday: 9am–6pm', 'Weekend: Closed'],
    },
];

export default function Contact() {
    return (
        <PublicLayout>
            {/* Hero */}
            <section className="pt-32 pb-20 lg:pt-44">
                <div className="mx-auto max-w-7xl px-6">
                    <SectionHeading
                        title="Get in Touch"
                        subtitle="Have questions about SanaGo? Want to see a demo? We'd love to hear from you."
                    />
                </div>
            </section>

            {/* Contact Section */}
            <section className="pb-24">
                <div className="mx-auto grid max-w-6xl gap-12 px-6 lg:grid-cols-5">
                    {/* Contact Info */}
                    <div className="space-y-6 lg:col-span-2">
                        {CONTACT_INFO.map((item) => (
                            <div
                                key={item.title}
                                className="flex gap-4 rounded-2xl border border-slate-100 bg-white p-6 transition hover:shadow-md dark:border-neutral-700 dark:bg-neutral-900"
                            >
                                <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/30">
                                    {item.icon}
                                </div>
                                <div>
                                    <h3 className="mb-1 font-semibold text-slate-900 dark:text-neutral-100">
                                        {item.title}
                                    </h3>
                                    {item.details.map((detail, i) => (
                                        <p
                                            key={i}
                                            className="text-sm text-slate-500 dark:text-neutral-400"
                                        >
                                            {detail}
                                        </p>
                                    ))}
                                </div>
                            </div>
                        ))}
                    </div>

                    {/* Demo Request Form */}
                    <div className="lg:col-span-3">
                        <h2 className="mb-2 text-2xl font-bold text-slate-900 dark:text-neutral-100">
                            Request a Demo
                        </h2>
                        <p className="mb-6 text-sm text-slate-500 dark:text-neutral-400">
                            Fill out the form below and our team will get back
                            to you within one business day.
                        </p>
                        <DemoRequestForm />
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}
