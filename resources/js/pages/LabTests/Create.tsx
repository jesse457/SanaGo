import {
    CheckCircleIcon,
    ClipboardDocumentListIcon,
    CurrencyDollarIcon,
    HashtagIcon,
    ScaleIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import {
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

    /* This form used to fake a save — a timer, then a page-local "Success"
       banner. It now posts to `tests.store`, which validates the same fields
       and redirects to the catalogue with a flash. Feedback is global: the
       flash becomes an animated SweetAlert toast, and a 422 opens the
       validation alert, so nothing is rendered here. */
    const { data, setData, post, processing, errors, reset } =
        useForm<LabTestForm>(EMPTY_FORM);

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();

        post(route('tests.store'), {
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    };

    return (
        <AppLayout
            headerTitle={t('lab_tests.create.title')}
            headerSubtitle={t('lab_tests.create.subtitle')}
        >
            <Head title={t('lab_tests.create.head_title')} />

            <div className="mx-auto max-w-4xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageHeader
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
                                    error={errors.test_name}
                                >
                                    <Input
                                        id="test_name"
                                        icon={ClipboardDocumentListIcon}
                                        value={data.test_name}
                                        invalid={Boolean(errors.test_name)}
                                        placeholder={t(
                                            'lab_tests.create.test_name_placeholder',
                                        )}
                                        onChange={(event) =>
                                            setData(
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
                                    error={errors.code}
                                >
                                    <Input
                                        id="code"
                                        icon={HashtagIcon}
                                        value={data.code}
                                        invalid={Boolean(errors.code)}
                                        placeholder={t(
                                            'lab_tests.create.code_placeholder',
                                        )}
                                        onChange={(event) =>
                                            setData('code', event.target.value)
                                        }
                                    />
                                </Field>

                                <Field
                                    label={t('lab_tests.create.unit_price')}
                                    htmlFor="price"
                                    required
                                    error={errors.price}
                                >
                                    <Input
                                        id="price"
                                        type="number"
                                        icon={CurrencyDollarIcon}
                                        value={data.price}
                                        invalid={Boolean(errors.price)}
                                        placeholder={t(
                                            'lab_tests.create.price_placeholder',
                                        )}
                                        onChange={(event) =>
                                            setData('price', event.target.value)
                                        }
                                    />
                                </Field>

                                <Field
                                    label={t('lab_tests.create.result_units')}
                                    htmlFor="units"
                                    required
                                    error={errors.units}
                                >
                                    <Input
                                        id="units"
                                        icon={ScaleIcon}
                                        value={data.units}
                                        invalid={Boolean(errors.units)}
                                        placeholder={t(
                                            'lab_tests.create.units_placeholder',
                                        )}
                                        onChange={(event) =>
                                            setData('units', event.target.value)
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
                                            data.is_active ? 'true' : 'false'
                                        }
                                        onChange={(event) =>
                                            setData(
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
                                    error={errors.description}
                                >
                                    <Textarea
                                        id="description"
                                        rows={4}
                                        value={data.description}
                                        invalid={Boolean(errors.description)}
                                        placeholder={t(
                                            'lab_tests.create.description_placeholder',
                                        )}
                                        onChange={(event) =>
                                            setData(
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
                                loading={processing}
                                icon={processing ? undefined : CheckCircleIcon}
                            >
                                {processing
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
