import React from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import { Alert, Button, Space, Typography } from 'antd';
import { MailOutlined, LogoutOutlined } from '@ant-design/icons';
import AuthLayout from '../../Layouts/AuthLayout';
import classicFormPost from '../../Utils/classicFormPost';
import useTranslation from '../../Hooks/useTranslation';

const { Paragraph } = Typography;

export default function VerifyEmail({ status }) {
    const { appUrls } = usePage().props;
    const { t } = useTranslation();
    const { post, processing } = useForm({});

    const resend = () => post('/email/verification-notification');

    return (
        <AuthLayout title={t('auth.verify_title')} subtitle={t('auth.verify_subtitle')}>
            <Head title={t('auth.verify_title')} />

            {status === 'verification-link-sent' && (
                <Alert
                    type="success"
                    showIcon
                    message={t('auth.verify_sent')}
                    style={{ marginBottom: 16 }}
                />
            )}

            <Paragraph type="secondary">{t('auth.verify_description')}</Paragraph>

            <Space direction="vertical" style={{ width: '100%' }}>
                <Button
                    type="primary"
                    icon={<MailOutlined />}
                    loading={processing}
                    onClick={resend}
                    block
                >
                    {t('auth.resend_email')}
                </Button>
                <Button
                    icon={<LogoutOutlined />}
                    onClick={() => classicFormPost(appUrls.logout)}
                    block
                >
                    {t('header.logout')}
                </Button>
            </Space>
        </AuthLayout>
    );
}
