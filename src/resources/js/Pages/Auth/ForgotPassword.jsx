import React from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import { Alert, Button, Form, Input, Typography } from 'antd';
import { MailOutlined, SendOutlined } from '@ant-design/icons';
import AuthLayout from '../../Layouts/AuthLayout';
import useTranslation from '../../Hooks/useTranslation';

const { Text } = Typography;

export default function ForgotPassword() {
    const { appUrls, errors } = usePage().props;
    const { t } = useTranslation();
    const { data, setData, post, processing } = useForm({ email: '', honeypot: '' });

    const handleSubmit = () => post(appUrls.forgotPassword);

    return (
        <AuthLayout title={t('auth.forgot_title')} subtitle={t('auth.forgot_subtitle')}>
            <Head title={t('auth.forgot_title')} />

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

                <Form.Item label={t('auth.email_electronic')} required>
                    <Input
                        prefix={<MailOutlined />}
                        placeholder={t('auth.email_placeholder_company')}
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
                    {t('auth.send_link')}
                </Button>

                <div style={{ textAlign: 'center', marginTop: 16 }}>
                    <Text type="secondary">
                        <a href={appUrls.login}>{t('auth.back_to_login')}</a>
                    </Text>
                </div>
            </Form>
        </AuthLayout>
    );
}
