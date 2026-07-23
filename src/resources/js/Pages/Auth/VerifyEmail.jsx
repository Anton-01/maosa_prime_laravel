import React from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import { Alert, Button, Space, Typography } from 'antd';
import { MailOutlined, LogoutOutlined } from '@ant-design/icons';
import AuthLayout from '../../Layouts/AuthLayout';
import classicFormPost from '../../Utils/classicFormPost';

const { Paragraph } = Typography;

export default function VerifyEmail({ status }) {
    const { appUrls } = usePage().props;
    const { post, processing } = useForm({});

    const resend = () => post('/email/verification-notification');

    return (
        <AuthLayout title="Verifica tu correo" subtitle="Confirma tu dirección de correo electrónico">
            <Head title="Verificar correo" />

            {status === 'verification-link-sent' && (
                <Alert
                    type="success"
                    showIcon
                    message="Se ha enviado un nuevo enlace de verificación a tu correo."
                    style={{ marginBottom: 16 }}
                />
            )}

            <Paragraph type="secondary">
                Antes de continuar, revisa tu correo para encontrar el enlace de verificación. Si no
                lo recibiste, podemos enviarte otro.
            </Paragraph>

            <Space direction="vertical" style={{ width: '100%' }}>
                <Button
                    type="primary"
                    icon={<MailOutlined />}
                    loading={processing}
                    onClick={resend}
                    block
                >
                    Reenviar correo de verificación
                </Button>
                <Button
                    icon={<LogoutOutlined />}
                    onClick={() => classicFormPost(appUrls.logout)}
                    block
                >
                    Cerrar sesión
                </Button>
            </Space>
        </AuthLayout>
    );
}
