import React, { useState } from 'react';
import { Head, usePage } from '@inertiajs/react';
import { Alert, Button, Checkbox, Flex, Form, Input } from 'antd';
import { LockOutlined, LoginOutlined, MailOutlined } from '@ant-design/icons';
import AuthLayout from '../../Layouts/AuthLayout';
import classicFormPost from '../../Utils/classicFormPost';

export default function AdminLogin({ urls }) {
    const { errors } = usePage().props;
    const [form] = Form.useForm();
    const [submitting, setSubmitting] = useState(false);

    // Classic POST: after login the server may redirect to a Blade page
    // (non-admin users), which an Inertia visit cannot render.
    const handleSubmit = (values) => {
        setSubmitting(true);

        classicFormPost(urls.login, {
            email: values.email,
            password: values.password,
            ...(values.remember ? { remember: 'on' } : {}),
            // Anti-bot field expected empty by the Honeypot middleware.
            honeypot: '',
        });
    };

    return (
        <AuthLayout title="Iniciar sesión" subtitle="Accede al panel de administración">
            <Head title="Iniciar sesión" />

            {errors.email && (
                <Alert type="error" showIcon message={errors.email} style={{ marginBottom: 16 }} />
            )}

            <Form
                form={form}
                layout="vertical"
                onFinish={handleSubmit}
                disabled={submitting}
                initialValues={{ remember: true }}
            >
                <Form.Item
                    label="Correo"
                    name="email"
                    rules={[
                        { required: true, message: 'El correo es obligatorio.' },
                        { type: 'email', message: 'Debe ser un correo válido.' },
                    ]}
                >
                    <Input
                        prefix={<MailOutlined />}
                        placeholder="correo@ejemplo.com"
                        autoComplete="email"
                        autoFocus
                    />
                </Form.Item>

                <Form.Item
                    label="Contraseña"
                    name="password"
                    rules={[{ required: true, message: 'La contraseña es obligatoria.' }]}
                    validateStatus={errors.password ? 'error' : undefined}
                    help={errors.password}
                >
                    <Input.Password
                        prefix={<LockOutlined />}
                        placeholder="Contraseña"
                        autoComplete="current-password"
                    />
                </Form.Item>

                <Flex justify="space-between" align="center" style={{ marginBottom: 16 }}>
                    <Form.Item name="remember" valuePropName="checked" noStyle>
                        <Checkbox>Recuérdame</Checkbox>
                    </Form.Item>
                    <a href={urls.forgotPassword}>¿Olvidaste tu contraseña?</a>
                </Flex>

                <Button
                    type="primary"
                    htmlType="submit"
                    icon={<LoginOutlined />}
                    loading={submitting}
                    block
                >
                    Iniciar sesión
                </Button>
            </Form>
        </AuthLayout>
    );
}
