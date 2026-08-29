import React from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import { Button, Form, Input } from 'antd';
import { LockOutlined, MailOutlined, SafetyOutlined } from '@ant-design/icons';
import AuthLayout from '../../Layouts/AuthLayout';
import useTranslation from '../../Hooks/useTranslation';

export default function ResetPassword({ token, email }) {
    const { errors } = usePage().props;
    const { t } = useTranslation();
    const { data, setData, post, processing } = useForm({
        token: token || '',
        email: email || '',
        password: '',
        password_confirmation: '',
    });

    const handleSubmit = () => post('/reset-password');

    return (
        <AuthLayout title={t('auth.reset_title')} subtitle={t('auth.reset_subtitle')}>
            <Head title={t('auth.reset_title')} />

            <Form layout="vertical" onFinish={handleSubmit} disabled={processing}>
                <Form.Item
                    label={t('auth.email_electronic')}
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
                    label={t('auth.new_password')}
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

                <Form.Item label={t('auth.confirm_password')} required>
                    <Input.Password
                        prefix={<SafetyOutlined />}
                        placeholder="••••••••"
                        autoComplete="new-password"
                        value={data.password_confirmation}
                        onChange={(e) => setData('password_confirmation', e.target.value)}
                    />
                </Form.Item>

                <Button type="primary" htmlType="submit" loading={processing} block>
                    {t('auth.reset_button')}
                </Button>
            </Form>
        </AuthLayout>
    );
}
