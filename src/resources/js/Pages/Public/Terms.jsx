import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { Card, Col, List, Row, Space, Typography } from 'antd';
import { CheckCircleOutlined } from '@ant-design/icons';
import dayjs from 'dayjs';

import HtmlContent from '../../Components/HtmlContent';
import PageHeader from '../../Components/Frontend/PageHeader';

const { Title, Text } = Typography;

const SUMMARY = [
    'Uso permitido del sitio',
    'Protección de propiedad intelectual',
    'Limitación de responsabilidad',
    'Políticas de cancelación',
    'Resolución de disputas',
];

export default function Terms({ termsAndCondition }) {
    return (
        <>
            <Head title="Términos y Condiciones" />
            <PageHeader
                title="Términos y Condiciones"
                breadcrumb={[{ label: 'Términos y Condiciones' }]}
            />

            <div style={{ padding: '32px 24px' }}>
                <Row gutter={[24, 24]}>
                    <Col xs={24} lg={16}>
                        <Card>
                            <Title level={3}>Condiciones Generales de Uso</Title>
                            <Text type="secondary">Actualizado: {dayjs().format('DD/MM/YYYY')}</Text>
                            <div style={{ marginTop: 24 }}>
                                <HtmlContent html={termsAndCondition?.description} />
                            </div>
                        </Card>
                    </Col>

                    <Col xs={24} lg={8}>
                        <Card title="Resumen" style={{ marginBottom: 24 }}>
                            <List
                                size="small"
                                dataSource={SUMMARY}
                                renderItem={(item) => (
                                    <List.Item style={{ paddingInline: 0 }}>
                                        <Space>
                                            <CheckCircleOutlined style={{ color: '#52c41a' }} />
                                            {item}
                                        </Space>
                                    </List.Item>
                                )}
                            />
                        </Card>

                        <Card title="Enlaces relacionados">
                            <Space direction="vertical">
                                <Link href="/privacy-policy">Aviso de Privacidad</Link>
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
