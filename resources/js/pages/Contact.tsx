import {
    ClockIcon,
    EnvelopeIcon,
    MapPinIcon,
    PhoneIcon,
} from '@heroicons/react/24/outline';
import DemoRequestForm from '@/Components/marketing/DemoRequestForm';
import SectionHeading from '@/Components/marketing/SectionHeading';
import PublicLayout from '@/Layouts/PublicLayout';

const CONTACT_INFO = [
    {
        icon: <EnvelopeIcon className="h-5 w-5" />,
        title: 'Email',
        details: ['sales@sanago.com', 'support@sanago.com'],
    },
    {
        icon: <PhoneIcon className="h-5 w-5" />,
        title: 'Phone',
        details: ['+1 (555) 123-4567', 'Mon–Fri, 9am–6pm EST'],
    },
    {
        icon: <MapPinIcon className="h-5 w-5" />,
        title: 'Office',
        details: ['123 Healthcare Drive', 'San Francisco, CA 94102'],
    },
    {
        icon: <ClockIcon className="h-5 w-5" />,
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
                                className="flex gap-4 rounded-2xl border border-line bg-surface p-6 transition hover:shadow-surface"
                            >
                                <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-accent-ink">
                                    {item.icon}
                                </div>
                                <div>
                                    <h3 className="mb-1 font-semibold text-ink">
                                        {item.title}
                                    </h3>
                                    {item.details.map((detail, i) => (
                                        <p
                                            key={i}
                                            className="text-sm text-ink-muted"
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
                        <h2 className="mb-2 text-2xl font-bold text-ink">
                            Request a Demo
                        </h2>
                        <p className="mb-6 text-sm text-ink-muted">
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
