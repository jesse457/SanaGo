import {
    TagIcon,
    ArrowPathIcon,
    CheckIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import FormTextarea from '@/Components/FormTextarea';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import AppLayout from '@/Layouts/AppLayout';

interface Service {
    id: number;
    name: string;
    code: string;
    category: string;
    description: string | null;
    default_price: number;
    is_active: boolean;
    requires_upfront_payment: boolean;
}

const CATEGORY_OPTIONS = [
    { value: 'consultation', label: 'Consultation' },
    { value: 'procedure', label: 'Procédure' },
    { value: 'lab', label: 'Laboratoire' },
    { value: 'radiology', label: 'Radiologie' },
    { value: 'imaging', label: 'Imagerie' },
    { value: 'medication', label: 'Médicament' },
    { value: 'bed', label: 'Lit' },
    { value: 'nursing', label: 'Soins Infirmiers' },
    { value: 'emergency', label: 'Urgence' },
    { value: 'other', label: 'Autre' },
];

export default function ServicesEdit({ service }: { service: Service }) {
    const { data, setData, put, processing, errors } = useForm({
        name: service.name,
        code: service.code,
        category: service.category,
        description: service.description || '',
        default_price: service.default_price,
        is_active: service.is_active,
        requires_upfront_payment: service.requires_upfront_payment,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('finance.services.update', service.id));
    };

    return (
        <AppLayout
            headerTitle="Modifier le Service"
            headerSubtitle={`Modifier ${service.name}`}
        >
            <Head title={`Modifier ${service.name}`} />

            <div className="mx-auto max-w-3xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageBreadcrumb
                    items={[
                        { label: 'Accueil', href: route('dashboard') },
                        {
                            label: 'Services',
                            href: route('finance.services.index'),
                        },
                        { label: service.name },
                    ]}
                />

                <PageSection title="Détails du Service" icon={TagIcon}>
                    <form onSubmit={handleSubmit} className="space-y-6 p-6">
                        <div className="grid gap-6 sm:grid-cols-2">
                            <FormInput
                                name="name"
                                label="Nom du service"
                                required
                                errors={errors}
                                value={data.name}
                                onChange={(v) => setData('name', v)}
                            />
                            <FormInput
                                name="code"
                                label="Code"
                                required
                                errors={errors}
                                value={data.code}
                                onChange={(v) => setData('code', v)}
                            />
                        </div>

                        <FormSelect
                            name="category"
                            label="Catégorie"
                            required
                            value={data.category}
                            onChange={(v) => setData('category', v)}
                            options={CATEGORY_OPTIONS}
                        />

                        <FormInput
                            name="default_price"
                            label="Prix par défaut (XAF)"
                            type="number"
                            required
                            errors={errors}
                            value={data.default_price}
                            onChange={(v) =>
                                setData('default_price', Number(v))
                            }
                        />

                        <FormTextarea
                            name="description"
                            label="Description"
                            errors={errors}
                            value={data.description}
                            onChange={(v) => setData('description', v)}
                            rows={3}
                        />

                        <div className="space-y-4 border-t border-gray-100 pt-4 dark:border-neutral-700">
                            <div className="flex items-center gap-3">
                                <input
                                    type="checkbox"
                                    id="is_active"
                                    checked={data.is_active}
                                    onChange={(e) =>
                                        setData('is_active', e.target.checked)
                                    }
                                    className="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800"
                                />
                                <label
                                    htmlFor="is_active"
                                    className="text-sm font-medium text-gray-700 dark:text-neutral-300"
                                >
                                    Service actif
                                </label>
                            </div>

                            <div className="flex items-center gap-3">
                                <input
                                    type="checkbox"
                                    id="requires_upfront_payment"
                                    checked={data.requires_upfront_payment}
                                    onChange={(e) =>
                                        setData(
                                            'requires_upfront_payment',
                                            e.target.checked,
                                        )
                                    }
                                    className="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800"
                                />
                                <label
                                    htmlFor="requires_upfront_payment"
                                    className="text-sm font-medium text-gray-700 dark:text-neutral-300"
                                >
                                    Paiement avant service requis
                                </label>
                            </div>
                        </div>

                        <div className="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-neutral-700">
                            <Link
                                href={route('finance.services.index')}
                                className="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                            >
                                Annuler
                            </Link>
                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-sm font-bold text-white transition-colors hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {processing ? (
                                    <span className="flex items-center gap-2">
                                        <ArrowPathIcon className="h-4 w-4 animate-spin" />{' '}
                                        Enregistrement...
                                    </span>
                                ) : (
                                    <span className="flex items-center gap-2">
                                        <CheckIcon className="h-4 w-4" />{' '}
                                        Enregistrer
                                    </span>
                                )}
                            </button>
                        </div>
                    </form>
                </PageSection>
            </div>
        </AppLayout>
    );
}
