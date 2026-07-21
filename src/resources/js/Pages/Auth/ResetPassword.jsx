import React from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import { Button, Form, Input } from 'antd';
import { LockOutlined, MailOutlined, SafetyOutlined } from '@ant-design/icons';
import AuthLayout from '../../Layouts/AuthLayout';

export default function ResetPassword({ token, email }) {
    const { errors } = usePage().props;
    const { data, setData, post, processing } = useForm({
        token: token || '',
        email: email || '',
        password: '',
        password_confirmation: '',
    });

    const handleSubmit = () => post('/reset-password');

    return (
        <AuthLayout title="Restablecer contraseña" subtitle="Define una nueva contraseña para tu cuenta">
            <Head title="Restablecer contraseña" />

            <Form layout="vertical" onFinish={handleSubmit} disabled={processing}>
                <Form.Item
                    label="Correo electrónico"
                    validateStatus={errors.email ? 'error' : undefined}
                    help={errors.email}
                    required
                >
                    <Input
                        prefix={<MailOutlined />}
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        autoComplete="email"
                    />
                </Form.Item>

                <Form.Item
                    label="Nueva contraseña"
                    validateStatus={errors.password ? 'error' : undefined}
                    help={errors.password}
                    required
                >
                    <Input.Password
                        prefix={<LockOutlined />}
                        placeholder="••••••••"
                        autoComplete="new-password"
                        value={data.password}
                        onChange={(e) => setData('password', e.target.value)}
                    />
                </Form.Item>

                <Form.Item label="Confirmar contraseña" required>
                    <Input.Password
                        prefix={<SafetyOutlined />}
                        placeholder="••••••••"
                        autoComplete="new-password"
                        value={data.password_confirmation}
                        onChange={(e) => setData('password_confirmation', e.target.value)}
                    />
                </Form.Item>

                <Button type="primary" htmlType="submit" loading={processing} block>
                    Restablecer contraseña
                </Button>
            </Form>
        </AuthLayout>
    );
}
