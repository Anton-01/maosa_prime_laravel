import React from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import { Button, Form, Input, Typography } from 'antd';
import { LockOutlined } from '@ant-design/icons';
import AuthLayout from '../../Layouts/AuthLayout';

const { Paragraph } = Typography;

export default function ConfirmPassword() {
    const { errors } = usePage().props;
    const { data, setData, post, processing } = useForm({ password: '' });

    const handleSubmit = () => post('/confirm-password');

    return (
        <AuthLayout title="Confirma tu contraseña" subtitle="Zona segura">
            <Head title="Confirmar contraseña" />

            <Paragraph type="secondary">
                Esta es un área segura de la aplicación. Confirma tu contraseña para continuar.
            </Paragraph>

            <Form layout="vertical" onFinish={handleSubmit} disabled={processing}>
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
                        autoFocus
                        value={data.password}
                        onChange={(e) => setData('password', e.target.value)}
                    />
                </Form.Item>

                <Button type="primary" htmlType="submit" loading={processing} block>
                    Confirmar
                </Button>
            </Form>
        </AuthLayout>
    );
}
