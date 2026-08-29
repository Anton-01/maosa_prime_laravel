import React, { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import { Alert, Button, Card, Col, Form, Input, Row, Select, Space, Tooltip, Typography } from 'antd';
import { ArrowLeftOutlined, KeyOutlined, SaveOutlined } from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import PasswordGeneratorModal from '../../../Components/PasswordGeneratorModal';
import copyToClipboard from '../../../Utils/copyToClipboard';

const { Text } = Typography;

/** Los tres flags del formulario comparten el mismo par de opciones. */
const OPCIONES_SI_NO = [
    { value: true, label: 'Sí' },
    { value: false, label: 'No' },
];

export default function UserForm({ user, roles, stations, nationalStations = [], urls }) {
    const { errors } = usePage().props;
    const [form] = Form.useForm();
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
                ? 'Contraseña copiada al portapapeles. Resguárdala antes de salir.'
                : 'No se pudo copiar automáticamente: cópiala desde el campo antes de guardar.',
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
            title={isEditing ? `Editar usuario: ${user.name}` : 'Nuevo usuario'}
            breadcrumbItems={[
                { title: 'Gestión de accesos' },
                { title: 'Usuarios', href: urls.base },
                { title: isEditing ? 'Editar' : 'Crear' },
            ]}
            extra={
                <Link href={urls.base}>
                    <Button icon={<ArrowLeftOutlined />}>Volver al listado</Button>
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
                        message="Estás editando un usuario Super Admin."
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
                                label="Nombre"
                                name="name"
                                rules={[{ required: true, message: 'El nombre es obligatorio.' }]}
                                validateStatus={errors.name ? 'error' : undefined}
                                help={errors.name}
                            >
                                <Input maxLength={255} placeholder="Nombre completo" />
                            </Form.Item>
                        </Col>
                        <Col xs={24} md={12}>
                            <Form.Item
                                label="Correo"
                                name="email"
                                rules={[
                                    { required: true, message: 'El correo es obligatorio.' },
                                    { type: 'email', message: 'Debe ser un correo válido.' },
                                ]}
                                validateStatus={errors.email ? 'error' : undefined}
                                help={errors.email}
                            >
                                <Input maxLength={255} placeholder="correo@ejemplo.com" />
                            </Form.Item>
                        </Col>
                        <Col xs={24} md={12}>
                            <Form.Item
                                label={isEditing ? 'Nueva contraseña (opcional)' : 'Contraseña'}
                                name="password"
                                rules={[
                                    { required: !isEditing, message: 'La contraseña es obligatoria.' },
                                    { min: 8, message: 'Mínimo 8 caracteres.' },
                                ]}
                                validateStatus={errors.password ? 'error' : undefined}
                                help={errors.password}
                            >
                                <Input.Password
                                    placeholder="Mínimo 8 caracteres"
                                    autoComplete="new-password"
                                    readOnly={passwordLocked}
                                />
                            </Form.Item>
                        </Col>
                        <Col xs={24} md={12}>
                            <Form.Item
                                label="Confirmar contraseña"
                                name="password_confirmation"
                                dependencies={['password']}
                                rules={[
                                    ({ getFieldValue }) => ({
                                        validator(_, value) {
                                            if (!getFieldValue('password') || getFieldValue('password') === value) {
                                                return Promise.resolve();
                                            }

                                            return Promise.reject(new Error('Las contraseñas no coinciden.'));
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
                                                Escribir otra
                                            </Button>
                                        </Space>
                                    ) : undefined
                                }
                            >
                                <Input.Password
                                    placeholder="Repita la contraseña"
                                    autoComplete="new-password"
                                    readOnly={passwordLocked}
                                    addonAfter={
                                        <Tooltip title="Generar contraseña segura">
                                            <Button
                                                type="text"
                                                size="small"
                                                icon={<KeyOutlined />}
                                                aria-label="Generar contraseña segura"
                                                onClick={() => setGeneratorOpen(true)}
                                            />
                                        </Tooltip>
                                    }
                                />
                            </Form.Item>
                        </Col>
                        <Col xs={24} md={12}>
                            <Form.Item
                                label="Rol"
                                name="role"
                                rules={[{ required: true, message: 'El rol es obligatorio.' }]}
                                validateStatus={errors.role ? 'error' : undefined}
                                help={errors.role}
                            >
                                <Select options={roles} placeholder="Selecciona un rol" showSearch optionFilterProp="label" />
                            </Form.Item>
                        </Col>
                        <Col xs={24} md={12}>
                            <Form.Item
                                label="Aprobado"
                                name="is_approved"
                                tooltip="Permite al usuario iniciar sesión en la plataforma."
                                validateStatus={errors.is_approved ? 'error' : undefined}
                                help={errors.is_approved}
                            >
                                <Select options={OPCIONES_SI_NO} placeholder="Selecciona una opción" />
                            </Form.Item>
                        </Col>
                        <Col xs={24} md={12}>
                            <Form.Item
                                label="Precios Internacionales"
                                name="can_view_price_table"
                                tooltip="Habilita la consulta de precios internacionales y la selección de su estación."
                                validateStatus={errors.can_view_price_table ? 'error' : undefined}
                                help={errors.can_view_price_table}
                            >
                                <Select options={OPCIONES_SI_NO} placeholder="Selecciona una opción" />
                            </Form.Item>
                        </Col>
                        <Col xs={24} md={12}>
                            <Form.Item
                                label="PRECIOS PEMEX"
                                name="permiso_precios_pemex"
                                tooltip="Habilita el submódulo de Precios PEMEX y la asignación de estaciones."
                                validateStatus={errors.permiso_precios_pemex ? 'error' : undefined}
                                help={errors.permiso_precios_pemex}
                            >
                                <Select options={OPCIONES_SI_NO} placeholder="Selecciona una opción" />
                            </Form.Item>
                        </Col>
                        {/* Cada selector de estaciones queda bajo su propio permiso. */}
                        {canViewPriceTable && (
                            <Col xs={24} md={12}>
                                <Form.Item
                                    label="Estación"
                                    name="id_estacion"
                                    validateStatus={errors.id_estacion ? 'error' : undefined}
                                    help={errors.id_estacion}
                                >
                                    <Select
                                        allowClear
                                        showSearch
                                        optionFilterProp="label"
                                        options={stations}
                                        placeholder="Selecciona una estación"
                                    />
                                </Form.Item>
                            </Col>
                        )}
                        {hasPemexPermission && (
                            <Col xs={24} md={12}>
                                <Form.Item
                                    label="Estaciones asignadas"
                                    name="estaciones_asignadas"
                                    rules={[
                                        {
                                            required: true,
                                            type: 'array',
                                            min: 1,
                                            message: 'Asigna al menos una estación.',
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
                                        notFoundContent="Sin estaciones activas en el catálogo."
                                        placeholder="Busca y selecciona una o más estaciones"
                                    />
                                </Form.Item>
                            </Col>
                        )}
                    </Row>

                    <Form.Item style={{ marginBottom: 0 }}>
                        <Button type="primary" htmlType="submit" icon={<SaveOutlined />} loading={submitting}>
                            {isEditing ? 'Actualizar usuario' : 'Crear usuario'}
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
