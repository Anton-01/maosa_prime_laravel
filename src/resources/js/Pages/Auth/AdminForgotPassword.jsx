import React, { useState } from 'react';
import { Head, usePage } from '@inertiajs/react';
import { Alert, Button, Form, Input } from 'antd';
import { MailOutlined, SendOutlined } from '@ant-design/icons';
import AuthLayout from '../../Layouts/AuthLayout';
import classicFormPost from '../../Utils/classicFormPost';

export default function AdminForgotPassword({ urls }) {
    const { errors, flash } = usePage().props;
    const [form] = Form.useForm();
    const [submitting, setSubmitting] = useState(false);

    // Classic POST so the redirect back (status message or errors in the
    // session) is rendered with a full page load.
    const handleSubmit = (values) => {
        setSubmitting(true);

        classicFormPost(urls.sendResetLink, {
            email: values.email,
            // Anti-bot field expected empty by the Honeypot middleware.
            honeypot: '',
        });
    };

    return (
        <AuthLayout
            title="Recuperar contraseña"
            subtitle="Te enviaremos un enlace para restablecer tu contraseña"
        >
            <Head title="Recuperar contraseña" />

            {flash?.status && (
                <Alert type="success" showIcon message={flash.status} style={{ marginBottom: 16 }} />
            )}

            {errors.email && (
                <Alert type="error" showIcon message={errors.email} style={{ marginBottom: 16 }} />
            )}

            <Form form={form} layout="vertical" onFinish={handleSubmit} disabled={submitting}>
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

                <Button
                    type="primary"
                    htmlType="submit"
                    icon={<SendOutlined />}
                    loading={submitting}
                    block
                    style={{ marginBottom: 12 }}
                >
                    Enviar enlace
                </Button>

                <Button block href={urls.login}>
                    Volver a iniciar sesión
                </Button>
            </Form>
        </AuthLayout>
    );
}
