import React from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import { Button, Form, Input, Typography } from 'antd';
import { LockOutlined } from '@ant-design/icons';
import AuthLayout from '../../Layouts/AuthLayout';
import useTranslation from '../../Hooks/useTranslation';

const { Paragraph } = Typography;

export default function ConfirmPassword() {
    const { errors } = usePage().props;
    const { t } = useTranslation();
    const { data, setData, post, processing } = useForm({ password: '' });

    const handleSubmit = () => post('/confirm-password');

    return (
        <AuthLayout title={t('auth.confirm_title')} subtitle={t('auth.confirm_subtitle')}>
            <Head title={t('auth.confirm_title')} />

            <Paragraph type="secondary">{t('auth.confirm_description')}</Paragraph>

            <Form layout="vertical" onFinish={handleSubmit} disabled={processing}>
                <Form.Item
                    label={t('auth.password')}
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
                    {t('auth.confirm_button')}
                </Button>
            </Form>
        </AuthLayout>
    );
}
