import React, { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import { Alert, Button, Card, Col, Form, Input, Row, Select, Space, Tooltip, Typography } from 'antd';
import { ArrowLeftOutlined, KeyOutlined, SaveOutlined } from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import PasswordGeneratorModal from '../../../Components/PasswordGeneratorModal';
import copyToClipboard from '../../../Utils/copyToClipboard';
import useTranslation from '../../../Hooks/useTranslation';

const { Text } = Typography;

/** Los tres flags del formulario comparten el mismo par de opciones. */
const opcionesSiNo = (t) => [
    { value: true, label: t('common.yes') },
    { value: false, label: t('common.no') },
];

export default function UserForm({ user, roles, stations, nationalStations = [], urls }) {
    const { errors } = usePage().props;
    const { t } = useTranslation();
    const [form] = Form.useForm();
    const yesNoOptions = opcionesSiNo(t);
    const [submitting, setSubmitting] = useState(false);
    const [generatorOpen, setGeneratorOpen] = useState(false);
    // Una contraseña generada se fija en ambos campos y los bloquea, para que
    // no se edite a medias y deje de coincidir con la que se copió.
    const [passwordLocked, setPasswordLocked] = useState(false);
    const [clipboardNotice, setClipboardNotice] = useState(null);

    const isEditing = Boolean(user);
    const canViewPriceTable = Form.useWatch('can_view_price_table', form);
    // REQ-03: el multiselect de estaciones sólo se habilita con el permiso activo.
    const hasPemexPermission = Form.useWatch('permiso_precios_pemex', form);

    const initialValues = isEditing
        ? {
              name: user.name,
              email: user.email,
              role: user.role,
              is_approved: user.is_approved === 1,
              can_view_price_table: user.can_view_price_table,
              id_estacion: user.id_estacion,
              permiso_precios_pemex: user.permiso_precios_pemex,
              estaciones_asignadas: user.estaciones_asignadas ?? [],
          }
        : {
              is_approved: false,
              can_view_price_table: false,
              permiso_precios_pemex: false,
              estaciones_asignadas: [],
          };

    const handleUseGeneratedPassword = async (password) => {
        form.setFieldsValue({ password, password_confirmation: password });
        setPasswordLocked(true);
        setGeneratorOpen(false);

        const copied = await copyToClipboard(password);
        setClipboardNotice(
            copied
                ? t('password_generator.copied')
                : t('password_generator.copy_failed'),
        );

        // Limpia los errores previos de "contraseñas no coinciden".
        form.validateFields(['password', 'password_confirmation']).catch(() => {});
    };

    const handleUnlockPassword = () => {
        form.setFieldsValue({ password: '', password_confirmation: '' });
        setPasswordLocked(false);
        setClipboardNotice(null);
    };

    const handleSubmit = (values) => {
        const payload = {
            name: values.name,
            email: values.email,
            password: values.password ?? '',
            password_confirmation: values.password_confirmation ?? '',
            role: values.role,
            is_approved: values.is_approved ? 1 : 0,
            can_view_price_table: values.can_view_price_table ? 1 : 0,
            id_estacion: values.can_view_price_table ? values.id_estacion ?? '' : '',
            permiso_precios_pemex: values.permiso_precios_pemex ? 1 : 0,
            // Sin permiso no se envía asignación: el backend limpia la pivote.
            estaciones_asignadas: values.permiso_precios_pemex ? values.estaciones_asignadas ?? [] : [],
        };

        const visitOptions = {
            preserveScroll: true,
            onStart: () => setSubmitting(true),
            onFinish: () => setSubmitting(false),
        };

        if (isEditing) {
            router.put(`${urls.base}/${user.id}`, payload, visitOptions);
        } else {
            router.post(urls.base, payload, visitOptions);
        }
    };

    return (
        <PageContainer
            title={
                isEditing
                    ? t('users.edit_title', { name: user.name })
                    : t('users.create_title')
            }
            breadcrumbItems={[
                { title: t('users.breadcrumb_access') },
                { title: t('users.breadcrumb_users'), href: urls.base },
                { title: isEditing ? t('users.breadcrumb_edit') : t('users.breadcrumb_create') },
            ]}
            extra={
                <Link href={urls.base}>
                    <Button icon={<ArrowLeftOutlined />}>{t('common.back_to_list')}</Button>
                </Link>
            }
            wrapInCard={false}
        >
            <Card>
                {user?.isSuperAdmin && (
                    <Alert
                        type="warning"
                        showIcon
                        style={{ marginBottom: 16 }}
                        message={t('users.super_admin_warning')}
                    />
                )}

                <Form
                    form={form}
                    layout="vertical"
                    onFinish={handleSubmit}
                    disabled={submitting}
                    initialValues={initialValues}
                >
                    <Row gutter={24}>
                        <Col xs={24} md={12}>
                            <Form.Item
                                label={t('users.name')}
                                name="name"
                                rules={[{ required: true, message: t('users.name_required') }]}
                                validateStatus={errors.name ? 'error' : undefined}
                                help={errors.name}
                            >
                                <Input maxLength={255} placeholder={t('users.name_placeholder')} />
                            </Form.Item>
                        </Col>
                        <Col xs={24} md={12}>
                            <Form.Item
                                label={t('users.email')}
                                name="email"
                                rules={[
                                    { required: true, message: t('users.email_required') },
                                    { type: 'email', message: t('users.email_invalid') },
                                ]}
                                validateStatus={errors.email ? 'error' : undefined}
                                help={errors.email}
                            >
                                <Input maxLength={255} placeholder={t('users.email_placeholder')} />
                            </Form.Item>
                        </Col>
                        <Col xs={24} md={12}>
                            <Form.Item
                                label={isEditing ? t('users.password_optional') : t('users.password')}
                                name="password"
                                rules={[
                                    { required: !isEditing, message: t('users.password_required') },
                                    { min: 8, message: t('users.password_min') },
                                ]}
                                validateStatus={errors.password ? 'error' : undefined}
                                help={errors.password}
                            >
                                <Input.Password
                                    placeholder={t('users.password_placeholder')}
                                    autoComplete="new-password"
                                    readOnly={passwordLocked}
                                />
                            </Form.Item>
                        </Col>
                        <Col xs={24} md={12}>
                            <Form.Item
                                label={t('users.password_confirm')}
                                name="password_confirmation"
                                dependencies={['password']}
                                rules={[
                                    ({ getFieldValue }) => ({
                                        validator(_, value) {
                                            if (!getFieldValue('password') || getFieldValue('password') === value) {
                                                return Promise.resolve();
                                            }

                                            return Promise.reject(new Error(t('users.password_mismatch')));
                                        },
                                    }),
                                ]}
                                extra={
                                    clipboardNotice ? (
                                        <Space size={6} wrap>
                                            <Text type="secondary" style={{ fontSize: 12 }}>
                                                {clipboardNotice}
                                            </Text>
                                            <Button
                                                type="link"
                                                size="small"
                                                style={{ padding: 0, height: 'auto', fontSize: 12 }}
                                                onClick={handleUnlockPassword}
                                            >
                                                {t('password_generator.write_another')}
                                            </Button>
                                        </Space>
                                    ) : undefined
                                }
                            >
                                <Input.Password
                                    placeholder={t('users.password_confirm_placeholder')}
                                    autoComplete="new-password"
                                    readOnly={passwordLocked}
                                    addonAfter={
                                        <Tooltip title={t('password_generator.button')}>
                                            <Button
                                                type="text"
                                                size="small"
                                                icon={<KeyOutlined />}
                                                aria-label={t('password_generator.button')}
                                                onClick={() => setGeneratorOpen(true)}
                                            />
                                        </Tooltip>
                                    }
                                />
                            </Form.Item>
                        </Col>
                        <Col xs={24} md={12}>
                            <Form.Item
                                label={t('users.role')}
                                name="role"
                                rules={[{ required: true, message: t('users.role_required') }]}
                                validateStatus={errors.role ? 'error' : undefined}
                                help={errors.role}
                            >
                                <Select
                                    options={roles}
                                    placeholder={t('users.role_placeholder')}
                                    showSearch
                                    optionFilterProp="label"
                                />
                            </Form.Item>
                        </Col>
                        <Col xs={24} md={12}>
                            <Form.Item
                                label={t('users.approved')}
                                name="is_approved"
                                tooltip={t('users.approved_tooltip')}
                                validateStatus={errors.is_approved ? 'error' : undefined}
                                help={errors.is_approved}
                            >
                                <Select options={yesNoOptions} placeholder={t('common.select_option')} />
                            </Form.Item>
                        </Col>
                        <Col xs={24} md={12}>
                            <Form.Item
                                label={t('users.international_prices')}
                                name="can_view_price_table"
                                tooltip={t('users.international_prices_tooltip')}
                                validateStatus={errors.can_view_price_table ? 'error' : undefined}
                                help={errors.can_view_price_table}
                            >
                                <Select options={yesNoOptions} placeholder={t('common.select_option')} />
                            </Form.Item>
                        </Col>
                        <Col xs={24} md={12}>
                            <Form.Item
                                label={t('users.pemex_prices')}
                                name="permiso_precios_pemex"
                                tooltip={t('users.pemex_prices_tooltip')}
                                validateStatus={errors.permiso_precios_pemex ? 'error' : undefined}
                                help={errors.permiso_precios_pemex}
                            >
                                <Select options={yesNoOptions} placeholder={t('common.select_option')} />
                            </Form.Item>
                        </Col>
                        {/* Cada selector de estaciones queda bajo su propio permiso. */}
                        {canViewPriceTable && (
                            <Col xs={24} md={12}>
                                <Form.Item
                                    label={t('users.station')}
                                    name="id_estacion"
                                    validateStatus={errors.id_estacion ? 'error' : undefined}
                                    help={errors.id_estacion}
                                >
                                    <Select
                                        allowClear
                                        showSearch
                                        optionFilterProp="label"
                                        options={stations}
                                        placeholder={t('users.station_placeholder')}
                                    />
                                </Form.Item>
                            </Col>
                        )}
                        {hasPemexPermission && (
                            <Col xs={24} md={12}>
                                <Form.Item
                                    label={t('users.assigned_stations')}
                                    name="estaciones_asignadas"
                                    rules={[
                                        {
                                            required: true,
                                            type: 'array',
                                            min: 1,
                                            message: t('users.assigned_stations_required'),
                                        },
                                    ]}
                                    validateStatus={
                                        errors.estaciones_asignadas || errors['estaciones_asignadas.0']
                                            ? 'error'
                                            : undefined
                                    }
                                    help={errors.estaciones_asignadas || errors['estaciones_asignadas.0']}
                                >
                                    <Select
                                        mode="multiple"
                                        allowClear
                                        showSearch
                                        optionFilterProp="label"
                                        maxTagCount="responsive"
                                        options={nationalStations}
                                        notFoundContent={t('users.assigned_stations_empty')}
                                        placeholder={t('users.assigned_stations_placeholder')}
                                    />
                                </Form.Item>
                            </Col>
                        )}
                    </Row>

                    <Form.Item style={{ marginBottom: 0 }}>
                        <Button type="primary" htmlType="submit" icon={<SaveOutlined />} loading={submitting}>
                            {isEditing ? t('users.submit_update') : t('users.submit_create')}
                        </Button>
                    </Form.Item>
                </Form>
            </Card>

            <PasswordGeneratorModal
                open={generatorOpen}
                onCancel={() => setGeneratorOpen(false)}
                onUse={handleUseGeneratedPassword}
            />
        </PageContainer>
    );
}
