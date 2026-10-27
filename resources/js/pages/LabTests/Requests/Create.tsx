import {
    CheckBadgeIcon,
    ClipboardDocumentListIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import {
    Alert,
    Button,
    Card,
    CardFooter,
    Field,
    FieldSet,
    PageHeader,
    SegmentedControl,
    Select,
    Textarea,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { formatMoney } from '@/pages/Finance/components/format';

interface PatientPick {
    id: number;
    first_name: string;
    last_name: string;
    patient_uid: string;
}

interface LabTestPick {
    id: number;
    test_name: string;
    code: string | null;
    price: string | number | null;
    units: string | null;
}

interface Urgency {
    value: string;
    label: string;
}

interface Props {
    patients: PatientPick[];
    tests: LabTestPick[];
    urgencies: Urgency[];
}

const EMPTY_FORM = {
    patient_id: '',
    test_definition_id: '',
    urgency: 'normal',
    reason_for_test: '',
};

/**
 * The lab order form. Ordering is a clinical act on its own: pick the patient,
 * the test and the urgency — no consultation is needed to raise an order, and
 * the charge lives at the counter, not here.
 */
export default function CreateLabRequest({
    patients,
    tests,
    urgencies,
}: Props) {
    const { t } = useTranslation();

    const { data, setData, post, processing, errors, reset } =
        useForm(EMPTY_FORM);

    const selectedTest = tests.find(
        (test) => String(test.id) === String(data.test_definition_id),
    );

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();

        post(route('requests.store'), {
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    };

    return (
        <AppLayout
            headerTitle={t('lab_tests.requests.order_title')}
            headerSubtitle={t('lab_tests.requests.order_subtitle')}
        >
            <Head title={t('lab_tests.requests.order_title')} />

            <div className="mx-auto max-w-4xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageHeader
                    eyebrow={t('lab_tests.requests.lab_requests')}
                    title={t('lab_tests.requests.order_title')}
                    description={t('lab_tests.requests.order_subtitle')}
                    breadcrumbs={[
                        {
                            label: t('lab_tests.requests.lab_requests'),
                            href: route('requests.index'),
                        },
                        { label: t('lab_tests.requests.order_new') },
                    ]}
                />

                <Alert tone="info">
                    <p className="text-[12.5px] text-accent-ink">
                        {t('lab_tests.requests.order_standalone')}
                    </p>
                </Alert>

                <Card>
                    <form onSubmit={handleSubmit}>
                        <div className="space-y-8 px-5 py-6 sm:px-6">
                            <FieldSet
                                title={t('lab_tests.requests.order_details')}
                            >
                                <Field
                                    className="sm:col-span-2"
                                    label={t('lab_tests.requests.patient')}
                                    htmlFor="patient_id"
                                    required
                                    error={errors.patient_id}
                                >
                                    <Select
                                        id="patient_id"
                                        value={data.patient_id}
                                        invalid={Boolean(errors.patient_id)}
                                        placeholder={t(
                                            'lab_tests.requests.pick_patient',
                                        )}
                                        onChange={(event) =>
                                            setData(
                                                'patient_id',
                                                event.target.value,
                                            )
                                        }
                                    >
                                        <option value="">
                                            {t(
                                                'lab_tests.requests.pick_patient',
                                            )}
                                        </option>
                                        {patients.map((patient) => (
                                            <option
                                                key={patient.id}
                                                value={patient.id}
                                            >
                                                {patient.first_name}{' '}
                                                {patient.last_name}
                                                {' · '}
                                                {patient.patient_uid}
                                            </option>
                                        ))}
                                    </Select>
                                </Field>

                                <Field
                                    className="sm:col-span-2"
                                    label={t('lab_tests.requests.lab_test')}
                                    htmlFor="test_definition_id"
                                    required
                                    error={errors.test_definition_id}
                                >
                                    <Select
                                        id="test_definition_id"
                                        value={data.test_definition_id}
                                        invalid={Boolean(
                                            errors.test_definition_id,
                                        )}
                                        placeholder={t(
                                            'lab_tests.requests.pick_test',
                                        )}
                                        onChange={(event) =>
                                            setData(
                                                'test_definition_id',
                                                event.target.value,
                                            )
                                        }
                                    >
                                        <option value="">
                                            {t('lab_tests.requests.pick_test')}
                                        </option>
                                        {tests.map((test) => (
                                            <option
                                                key={test.id}
                                                value={test.id}
                                            >
                                                {test.test_name}
                                                {test.code
                                                    ? ` (${test.code})`
                                                    : ''}
                                            </option>
                                        ))}
                                    </Select>
                                </Field>

                                <Field
                                    label={t('lab_tests.requests.urgency')}
                                    htmlFor="urgency"
                                    required
                                    error={errors.urgency}
                                >
                                    <SegmentedControl
                                        size="sm"
                                        value={data.urgency}
                                        onChange={(value) =>
                                            setData('urgency', value)
                                        }
                                        options={urgencies.map((urgency) => ({
                                            value: urgency.value,
                                            label: t(urgency.label),
                                        }))}
                                    />
                                </Field>

                                <Field
                                    className="sm:col-span-2"
                                    label={t('lab_tests.requests.reason')}
                                    htmlFor="reason_for_test"
                                    optional
                                    error={errors.reason_for_test}
                                >
                                    <Textarea
                                        id="reason_for_test"
                                        rows={3}
                                        value={data.reason_for_test}
                                        invalid={Boolean(
                                            errors.reason_for_test,
                                        )}
                                        placeholder={t(
                                            'lab_tests.requests.reason_placeholder',
                                        )}
                                        onChange={(event) =>
                                            setData(
                                                'reason_for_test',
                                                event.target.value,
                                            )
                                        }
                                    />
                                </Field>
                            </FieldSet>
                        </div>

                        {selectedTest && (
                            <div className="border-t border-line bg-surface-muted/60 px-5 py-4 sm:px-6">
                                <div className="flex flex-wrap items-center justify-between gap-3">
                                    <div className="flex items-center gap-2 text-[13px] text-ink-muted">
                                        <ClipboardDocumentListIcon className="h-4 w-4 text-ink-subtle" />
                                        <span className="font-medium text-ink">
                                            {selectedTest.test_name}
                                        </span>
                                        {selectedTest.units && (
                                            <span className="text-ink-subtle">
                                                · {selectedTest.units}
                                            </span>
                                        )}
                                    </div>
                                    <p className="flex items-center gap-1.5 text-[13px] font-semibold text-ink tabular-nums">
                                        {formatMoney(
                                            Number(selectedTest.price ?? 0),
                                        )}
                                        <span className="text-[11px] font-normal text-ink-subtle">
                                            {t(
                                                'lab_tests.requests.counter_collect',
                                            )}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        )}

                        <CardFooter>
                            <Button
                                href={route('requests.index')}
                                variant="secondary"
                            >
                                {t('common.cancel')}
                            </Button>
                            <Button
                                type="submit"
                                loading={processing}
                                icon={processing ? undefined : CheckBadgeIcon}
                            >
                                {t('lab_tests.requests.file_order')}
                            </Button>
                        </CardFooter>
                    </form>
                </Card>
            </div>
        </AppLayout>
    );
}
