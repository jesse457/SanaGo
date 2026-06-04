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
      <section className="pt-32 lg:pt-44 pb-20">
        <div className="max-w-7xl mx-auto px-6">
          <SectionHeading
            title="Get in Touch"
            subtitle="Have questions about SanaGo? Want to see a demo? We'd love to hear from you."
          />
        </div>
      </section>

      {/* Contact Section */}
      <section className="pb-24">
        <div className="max-w-6xl mx-auto px-6 grid lg:grid-cols-5 gap-12">
          {/* Contact Info */}
          <div className="lg:col-span-2 space-y-6">
            {CONTACT_INFO.map((item) => (
              <div
                key={item.title}
                className="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-slate-100 dark:border-neutral-700 flex gap-4 hover:shadow-md transition"
              >
                <div className="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-xl flex items-center justify-center shrink-0">
                  {item.icon}
                </div>
                <div>
                  <h3 className="font-semibold text-slate-900 dark:text-neutral-100 mb-1">
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
            <h2 className="text-2xl font-bold text-slate-900 dark:text-neutral-100 mb-2">
              Request a Demo
            </h2>
            <p className="text-slate-500 dark:text-neutral-400 mb-6 text-sm">
              Fill out the form below and our team will get back to you within
              one business day.
            </p>
            <DemoRequestForm />
          </div>
        </div>
      </section>
    </PublicLayout>
  );
}
