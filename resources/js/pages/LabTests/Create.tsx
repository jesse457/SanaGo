import {
    BeakerIcon,
    CheckCircleIcon,
    ClipboardDocumentListIcon,
    CurrencyDollarIcon,
    HashtagIcon,
    ScaleIcon,
} from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState } from 'react';
import {
    Alert,
    Button,
    Card,
    CardFooter,
    Field,
    FieldSet,
    Input,
    PageHeader,
    Select,
    Textarea,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface LabTestForm {
    test_name: string;
    code: string;
    price: string;
    units: string;
    description: string;
    is_active: boolean;
}

const EMPTY_FORM: LabTestForm = {
    test_name: '',
    code: '',
    price: '',
    units: '',
    description: '',
    is_active: true,
};

export default function CreateLabTest() {
    const { t } = useTranslation();
    const [formData, setFormData] = useState<LabTestForm>(EMPTY_FORM);
    const [formErrors] = useState<Record<string, string>>({});
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);

    const handleInputChange = <K extends keyof LabTestForm>(
        field: K,
        value: LabTestForm[K],
    ) => {
        setFormData((previous) => ({ ...previous, [field]: value }));
    };

    const handleSubmit = async (event: FormEvent) => {
        event.preventDefault();
        setIsSubmitting(true);
        setErrorMessage(null);
        await new Promise((resolve) => setTimeout(resolve, 1000));
        setSuccessMessage(t('lab_tests.create.success_message'));
        setIsSubmitting(false);
    };

    return (
        <AppLayout
            headerTitle={t('lab_tests.create.title')}
            headerSubtitle={t('lab_tests.create.subtitle')}
        >
            <Head title={t('lab_tests.create.head_title')} />

            <div className="mx-auto max-w-4xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageHeader
                    icon={BeakerIcon}
                    eyebrow={t('lab_tests.create.lab_definitions')}
                    title={t('lab_tests.create.title')}
                    description={t('lab_tests.create.subtitle')}
                    breadcrumbs={[
                        {
                            label: t('lab_tests.create.lab_definitions'),
                            href: route('tests.index'),
                        },
                        { label: t('lab_tests.create.new_configuration') },
                    ]}
                />

                {successMessage && (
                    <Alert
                        tone="positive"
                        title={t('common.success')}
                        action={
                            <Button
                                type="button"
                                variant="ghost"
                                size="sm"
                                onClick={() => setSuccessMessage(null)}
                            >
                                {t('common.dismiss', 'Dismiss')}
                            </Button>
                        }
                    >
                        {successMessage}
                    </Alert>
                )}

                {(errorMessage || Object.keys(formErrors).length > 0) && (
                    <Alert
                        tone="critical"
                        title={t('common.error')}
                        action={
                            <Button
                                type="button"
                                variant="ghost"
                                size="sm"
                                onClick={() => setErrorMessage(null)}
                            >
                                {t('common.dismiss', 'Dismiss')}
                            </Button>
                        }
                    >
                        {errorMessage || t('common.someFieldsRequireAttention')}
                    </Alert>
                )}

                <Card>
                    <form onSubmit={handleSubmit}>
                        <div className="space-y-8 px-5 py-6 sm:px-6">
                            <FieldSet
                                title={t('lab_tests.create.test_details')}
                            >
                                <Field
                                    className="sm:col-span-2"
                                    label={t(
                                        'lab_tests.create.official_test_name',
                                    )}
                                    htmlFor="test_name"
                                    required
                                    error={formErrors.test_name}
                                >
                                    <Input
                                        id="test_name"
                                        icon={ClipboardDocumentListIcon}
                                        value={formData.test_name}
                                        invalid={Boolean(formErrors.test_name)}
                                        placeholder={t(
                                            'lab_tests.create.test_name_placeholder',
                                        )}
                                        onChange={(event) =>
                                            handleInputChange(
                                                'test_name',
                                                event.target.value,
                                            )
                                        }
                                    />
                                </Field>

                                <Field
                                    label={t('lab_tests.create.internal_code')}
                                    htmlFor="code"
                                    required
                                    error={formErrors.code}
                                >
                                    <Input
                                        id="code"
                                        icon={HashtagIcon}
                                        value={formData.code}
                                        invalid={Boolean(formErrors.code)}
                                        placeholder={t(
                                            'lab_tests.create.code_placeholder',
                                        )}
                                        onChange={(event) =>
                                            handleInputChange(
                                                'code',
                                                event.target.value,
                                            )
                                        }
                                    />
                                </Field>

                                <Field
                                    label={t('lab_tests.create.unit_price')}
                                    htmlFor="price"
                                    required
                                    error={formErrors.price}
                                >
                                    <Input
                                        id="price"
                                        type="number"
                                        icon={CurrencyDollarIcon}
                                        value={formData.price}
                                        invalid={Boolean(formErrors.price)}
                                        placeholder={t(
                                            'lab_tests.create.price_placeholder',
                                        )}
                                        onChange={(event) =>
                                            handleInputChange(
                                                'price',
                                                event.target.value,
                                            )
                                        }
                                    />
                                </Field>

                                <Field
                                    label={t('lab_tests.create.result_units')}
                                    htmlFor="units"
                                    required
                                    error={formErrors.units}
                                >
                                    <Input
                                        id="units"
                                        icon={ScaleIcon}
                                        value={formData.units}
                                        invalid={Boolean(formErrors.units)}
                                        placeholder={t(
                                            'lab_tests.create.units_placeholder',
                                        )}
                                        onChange={(event) =>
                                            handleInputChange(
                                                'units',
                                                event.target.value,
                                            )
                                        }
                                    />
                                </Field>

                                <Field
                                    label={t(
                                        'lab_tests.create.availability_status',
                                    )}
                                    htmlFor="is_active"
                                >
                                    <Select
                                        id="is_active"
                                        value={
                                            formData.is_active
                                                ? 'true'
                                                : 'false'
                                        }
                                        onChange={(event) =>
                                            handleInputChange(
                                                'is_active',
                                                event.target.value === 'true',
                                            )
                                        }
                                    >
                                        <option value="true">
                                            {t('lab_tests.create.active')}
                                        </option>
                                        <option value="false">
                                            {t('lab_tests.create.inactive')}
                                        </option>
                                    </Select>
                                </Field>

                                <Field
                                    className="sm:col-span-2"
                                    label={t(
                                        'lab_tests.create.test_description',
                                    )}
                                    htmlFor="description"
                                    optional
                                    error={formErrors.description}
                                >
                                    <Textarea
                                        id="description"
                                        rows={4}
                                        value={formData.description}
                                        invalid={Boolean(
                                            formErrors.description,
                                        )}
                                        placeholder={t(
                                            'lab_tests.create.description_placeholder',
                                        )}
                                        onChange={(event) =>
                                            handleInputChange(
                                                'description',
                                                event.target.value,
                                            )
                                        }
                                    />
                                </Field>
                            </FieldSet>
                        </div>

                        <CardFooter>
                            <Button
                                href={route('tests.index')}
                                variant="secondary"
                            >
                                {t('common.cancel')}
                            </Button>
                            <Button
                                type="submit"
                                loading={isSubmitting}
                                icon={
                                    isSubmitting ? undefined : CheckCircleIcon
                                }
                            >
                                {isSubmitting
                                    ? `${t('lab_tests.create.saving')}…`
                                    : t('lab_tests.create.publish')}
                            </Button>
                        </CardFooter>
                    </form>
                </Card>
            </div>
        </AppLayout>
    );
}
