import React from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import { Alert, Button, Checkbox, Flex, Form, Input } from 'antd';
import { LockOutlined, LoginOutlined, MailOutlined } from '@ant-design/icons';
import AuthLayout from '../../Layouts/AuthLayout';

export default function Login() {
    const { appUrls, errors } = usePage().props;
    const { data, setData, post, processing } = useForm({
        email: '',
        password: '',
        remember: true,
        honeypot: '',
    });

    const handleSubmit = () => {
        post(appUrls.login);
    };

    return (
        <AuthLayout title="Iniciar sesión" subtitle="Accede con tus credenciales para continuar">
            <Head title="Iniciar sesión" />

            {errors.email && (
                <Alert type="error" showIcon message={errors.email} style={{ marginBottom: 16 }} />
            )}

            <Form layout="vertical" onFinish={handleSubmit} disabled={processing}>
                {/* Anti-bot honeypot field expected empty by the middleware. */}
                <input
                    type="text"
                    name="honeypot"
                    value={data.honeypot}
                    onChange={(e) => setData('honeypot', e.target.value)}
                    tabIndex={-1}
                    autoComplete="off"
                    style={{ position: 'absolute', left: '-9999px' }}
                    aria-hidden="true"
                />

                <Form.Item
                    label="Correo electrónico"
                    validateStatus={errors.email ? 'error' : undefined}
                    required
                >
                    <Input
                        prefix={<MailOutlined />}
                        placeholder="correo@empresa.com"
                        autoComplete="email"
                        autoFocus
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                    />
                </Form.Item>

                <Form.Item
                    label="Contraseña"
                    validateStatus={errors.password ? 'error' : undefined}
                    help={errors.password}
                    required
                >
                    <Input.Password
                        prefix={<LockOutlined />}
                        placeholder="••••••••"
                        autoComplete="current-password"
                        value={data.password}
                        onChange={(e) => setData('password', e.target.value)}
                    />
                </Form.Item>

                <Flex justify="space-between" align="center" style={{ marginBottom: 16 }}>
                    <Checkbox
                        checked={data.remember}
                        onChange={(e) => setData('remember', e.target.checked)}
                    >
                        Recordarme
                    </Checkbox>
                    <a href={appUrls.forgotPassword}>¿Olvidó su contraseña?</a>
                </Flex>

                <Button
                    type="primary"
                    htmlType="submit"
                    icon={<LoginOutlined />}
                    loading={processing}
                    block
                >
                    Iniciar sesión
                </Button>
            </Form>
        </AuthLayout>
    );
}
