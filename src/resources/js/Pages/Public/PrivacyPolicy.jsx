import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { Card, Col, List, Row, Space, Typography } from 'antd';
import {
    LockOutlined,
    SafetyOutlined,
    SyncOutlined,
} from '@ant-design/icons';
import dayjs from 'dayjs';

import HtmlContent from '../../Components/HtmlContent';
import PageHeader from '../../Components/Frontend/PageHeader';

const { Title, Text, Paragraph } = Typography;

const INFO = [
    {
        icon: <SafetyOutlined />,
        title: 'Protección de Datos',
        text: 'Tus datos personales están protegidos bajo las leyes vigentes.',
    },
    {
        icon: <LockOutlined />,
        title: 'Confidencialidad',
        text: 'Mantenemos la confidencialidad de tu información.',
    },
    {
        icon: <SyncOutlined />,
        title: 'Actualizaciones',
        text: 'Este aviso puede actualizarse periódicamente.',
    },
];

export default function PrivacyPolicy({ privacyPolicy }) {
    return (
        <>
            <Head title="Aviso de Privacidad" />
            <PageHeader title="Aviso de Privacidad" breadcrumb={[{ label: 'Aviso de Privacidad' }]} />

            <div style={{ padding: '32px 24px' }}>
                <Row gutter={[24, 24]}>
                    <Col xs={24} lg={16}>
                        <Card>
                            <Title level={3}>Aviso de Privacidad</Title>
                            <Text type="secondary">
                                Actualizado: {dayjs().format('DD/MM/YYYY')}
                            </Text>
                            <div style={{ marginTop: 24 }}>
                                <HtmlContent html={privacyPolicy?.description} />
                            </div>
                        </Card>
                    </Col>

                    <Col xs={24} lg={8}>
                        <Card title="Información" style={{ marginBottom: 24 }}>
                            <List
                                itemLayout="horizontal"
                                dataSource={INFO}
                                renderItem={(item) => (
                                    <List.Item>
                                        <List.Item.Meta
                                            avatar={
                                                <span style={{ fontSize: 22, color: 'var(--brand-color, #6777ef)' }}>
                                                    {item.icon}
                                                </span>
                                            }
                                            title={item.title}
                                            description={item.text}
                                        />
                                    </List.Item>
                                )}
                            />
                        </Card>

                        <Card title="Enlaces relacionados">
                            <Space direction="vertical">
                                <Link href="/terms-and-condition">Términos y Condiciones</Link>
                                <Link href="/contact">Soporte</Link>
                                <Link href="/">Página principal</Link>
                            </Space>
                        </Card>
                    </Col>
                </Row>
            </div>
        </>
    );
}
