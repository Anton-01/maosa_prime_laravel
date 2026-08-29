import React, { useState } from 'react';
import { Head, usePage } from '@inertiajs/react';
import { Alert, Button, Form, Input } from 'antd';
import { MailOutlined, SendOutlined } from '@ant-design/icons';
import AuthLayout from '../../Layouts/AuthLayout';
import classicFormPost from '../../Utils/classicFormPost';
import useTranslation from '../../Hooks/useTranslation';

export default function AdminForgotPassword({ urls }) {
    const { errors, flash } = usePage().props;
    const { t } = useTranslation();
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
        <AuthLayout title={t('auth.forgot_title')} subtitle={t('auth.forgot_subtitle')}>
            <Head title={t('auth.forgot_title')} />

            {flash?.status && (
                <Alert type="success" showIcon message={flash.status} style={{ marginBottom: 16 }} />
            )}

            {errors.email && (
                <Alert type="error" showIcon message={errors.email} style={{ marginBottom: 16 }} />
            )}

            <Form form={form} layout="vertical" onFinish={handleSubmit} disabled={submitting}>
                <Form.Item
                    label={t('auth.email')}
                    name="email"
                    rules={[
                        { required: true, message: t('auth.email_required') },
                        { type: 'email', message: t('auth.email_invalid') },
                    ]}
                >
                    <Input
                        prefix={<MailOutlined />}
                        placeholder={t('auth.email_placeholder')}
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
                    {t('auth.send_link')}
                </Button>

                <Button block href={urls.login}>
                    {t('auth.back_to_login')}
                </Button>
            </Form>
        </AuthLayout>
    );
}
