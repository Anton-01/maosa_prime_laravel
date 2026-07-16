import React, { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import { Alert, Button, Card, Col, Form, Input, Row, Select, Switch } from 'antd';
import { ArrowLeftOutlined, SaveOutlined } from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';

export default function UserForm({ user, roles, stations, urls }) {
    const { errors } = usePage().props;
    const [form] = Form.useForm();
    const [submitting, setSubmitting] = useState(false);

    const isEditing = Boolean(user);
    const canViewPriceTable = Form.useWatch('can_view_price_table', form);

    const initialValues = isEditing
        ? {
              name: user.name,
              email: user.email,
              role: user.role,
              is_approved: user.is_approved === 1,
              can_view_price_table: user.can_view_price_table,
              id_estacion: user.id_estacion,
          }
        : {
              is_approved: false,
              can_view_price_table: false,
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
            <Card style={{ maxWidth: 860 }}>
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
                                <Input.Password placeholder="Mínimo 8 caracteres" autoComplete="new-password" />
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
                            >
                                <Input.Password placeholder="Repita la contraseña" autoComplete="new-password" />
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
                        <Col xs={12} md={6}>
                            <Form.Item label="Aprobado" name="is_approved" valuePropName="checked">
                                <Switch checkedChildren="Sí" unCheckedChildren="No" />
                            </Form.Item>
                        </Col>
                        <Col xs={12} md={6}>
                            <Form.Item
                                label="Tabla de precios"
                                name="can_view_price_table"
                                valuePropName="checked"
                            >
                                <Switch checkedChildren="Sí" unCheckedChildren="No" />
                            </Form.Item>
                        </Col>
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
                    </Row>

                    <Form.Item style={{ marginBottom: 0 }}>
                        <Button type="primary" htmlType="submit" icon={<SaveOutlined />} loading={submitting}>
                            {isEditing ? 'Actualizar usuario' : 'Crear usuario'}
                        </Button>
                    </Form.Item>
                </Form>
            </Card>
        </PageContainer>
    );
}
