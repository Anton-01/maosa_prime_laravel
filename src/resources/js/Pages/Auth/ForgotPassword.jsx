import React from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import { Alert, Button, Form, Input, Typography } from 'antd';
import { MailOutlined, SendOutlined } from '@ant-design/icons';
import AuthLayout from '../../Layouts/AuthLayout';

const { Text } = Typography;

export default function ForgotPassword() {
    const { appUrls, errors } = usePage().props;
    const { data, setData, post, processing } = useForm({ email: '', honeypot: '' });

    const handleSubmit = () => post(appUrls.forgotPassword);

    return (
        <AuthLayout
            title="Recuperar contraseña"
            subtitle="Te enviaremos un enlace para restablecer tu contraseña"
        >
            <Head title="Recuperar contraseña" />

            {errors.email && (
                <Alert type="error" showIcon message={errors.email} style={{ marginBottom: 16 }} />
            )}

            <Form layout="vertical" onFinish={handleSubmit} disabled={processing}>
                <input
                    type="text"
                    value={data.honeypot}
                    onChange={(e) => setData('honeypot', e.target.value)}
                    tabIndex={-1}
                    autoComplete="off"
                    style={{ position: 'absolute', left: '-9999px' }}
                    aria-hidden="true"
                />

                <Form.Item label="Correo electrónico" required>
                    <Input
                        prefix={<MailOutlined />}
                        placeholder="correo@empresa.com"
                        autoComplete="email"
                        autoFocus
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                    />
                </Form.Item>

                <Button
                    type="primary"
                    htmlType="submit"
                    icon={<SendOutlined />}
                    loading={processing}
                    block
                >
                    Enviar enlace
                </Button>

                <div style={{ textAlign: 'center', marginTop: 16 }}>
                    <Text type="secondary">
                        <a href={appUrls.login}>Volver a iniciar sesión</a>
                    </Text>
                </div>
            </Form>
        </AuthLayout>
    );
}
