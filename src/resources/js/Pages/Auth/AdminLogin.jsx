import React, { useState } from 'react';
import { Head, usePage } from '@inertiajs/react';
import { Alert, Button, Checkbox, Flex, Form, Input } from 'antd';
import { LockOutlined, LoginOutlined, MailOutlined } from '@ant-design/icons';
import AuthLayout from '../../Layouts/AuthLayout';
import classicFormPost from '../../Utils/classicFormPost';
import useTranslation from '../../Hooks/useTranslation';

export default function AdminLogin({ urls }) {
    const { errors } = usePage().props;
    const { t } = useTranslation();
    const [form] = Form.useForm();
    const [submitting, setSubmitting] = useState(false);

    // Classic POST: after login the server may redirect to a Blade page
    // (non-admin users), which an Inertia visit cannot render.
    const handleSubmit = (values) => {
        setSubmitting(true);

        classicFormPost(urls.login, {
            email: values.email,
            password: values.password,
            ...(values.remember ? { remember: 'on' } : {}),
            // Anti-bot field expected empty by the Honeypot middleware.
            honeypot: '',
        });
    };

    return (
        <AuthLayout title={t('auth.login_title')} subtitle={t('auth.admin_login_subtitle')}>
            <Head title={t('auth.login_title')} />

            {errors.email && (
                <Alert type="error" showIcon message={errors.email} style={{ marginBottom: 16 }} />
            )}

            <Form
                form={form}
                layout="vertical"
                onFinish={handleSubmit}
                disabled={submitting}
                initialValues={{ remember: true }}
            >
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

                <Form.Item
                    label={t('auth.password')}
                    name="password"
                    rules={[{ required: true, message: t('auth.password_required') }]}
                    validateStatus={errors.password ? 'error' : undefined}
                    help={errors.password}
                >
                    <Input.Password
                        prefix={<LockOutlined />}
                        placeholder={t('auth.password')}
                        autoComplete="current-password"
                    />
                </Form.Item>

                <Flex justify="space-between" align="center" style={{ marginBottom: 16 }}>
                    <Form.Item name="remember" valuePropName="checked" noStyle>
                        <Checkbox>{t('auth.remember_me_admin')}</Checkbox>
                    </Form.Item>
                    <a href={urls.forgotPassword}>{t('auth.forgot_password_admin')}</a>
                </Flex>

                <Button
                    type="primary"
                    htmlType="submit"
                    icon={<LoginOutlined />}
                    loading={submitting}
                    block
                >
                    {t('auth.login_button')}
                </Button>
            </Form>
        </AuthLayout>
    );
}
