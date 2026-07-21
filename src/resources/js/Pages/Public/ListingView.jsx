import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { Badge, Card, Col, Divider, List, Row, Space, Tag, Typography } from 'antd';
import {
    CheckCircleFilled,
    ClockCircleOutlined,
    EnvironmentOutlined,
    GlobalOutlined,
    MailOutlined,
    PhoneOutlined,
    UserOutlined,
} from '@ant-design/icons';

import asset from '../../Utils/asset';
import HtmlContent from '../../Components/HtmlContent';
import PageHeader from '../../Components/Frontend/PageHeader';

const { Title, Text, Paragraph } = Typography;

const DAY_LABELS = {
    monday: 'Lunes',
    tuesday: 'Martes',
    wednesday: 'Miércoles',
    thursday: 'Jueves',
    friday: 'Viernes',
    saturday: 'Sábado',
    sunday: 'Domingo',
};

export default function ListingView({ listing, relatedListings, openStatus }) {
    const amenities = listing.amenities ?? [];
    const schedules = listing.schedules ?? [];
    const socials = listing.social_networks ?? listing.socialNetworks ?? [];

    return (
        <>
            <Head title={listing.title} />
            <PageHeader
                title={listing.title}
                breadcrumb={[{ label: 'Proveedores', href: '/suppliers' }, { label: 'Detalle' }]}
                background={listing.thumbnail_image ? asset(listing.thumbnail_image) : null}
            />

            <div style={{ padding: '32px 24px' }}>
                <Row gutter={[24, 24]}>
                    <Col xs={24} lg={16}>
                        <Card style={{ marginBottom: 24 }}>
                            <Space wrap align="center" style={{ justifyContent: 'space-between', width: '100%' }}>
                                <div>
                                    <Title level={3} style={{ marginBottom: 4 }}>
                                        {listing.title}
                                    </Title>
                                    {listing.user && (
                                        <Text type="secondary">
                                            <UserOutlined /> Organizado por {listing.user.name}
                                        </Text>
                                    )}
                                </div>
                                <Space>
                                    {openStatus === 'open' && <Tag color="success">Abierto ahora</Tag>}
                                    {openStatus === 'close' && <Tag color="error">Cerrado</Tag>}
                                    {!!listing.is_verified && (
                                        <Tag color="green" icon={<CheckCircleFilled />}>
                                            Verificado
                                        </Tag>
                                    )}
                                </Space>
                            </Space>
                        </Card>

                        <Card title="Acerca de la empresa" style={{ marginBottom: 24 }}>
                            <HtmlContent html={listing.description} />
                        </Card>

                        {amenities.length > 0 && (
                            <Card title="Servicios y características" style={{ marginBottom: 24 }}>
                                <Row gutter={[16, 16]}>
                                    {amenities.map((item) => (
                                        <Col xs={24} sm={12} key={item.id}>
                                            <Space
                                                style={{
                                                    background: '#f7f8fa',
                                                    borderRadius: 8,
                                                    padding: '10px 16px',
                                                    width: '100%',
                                                }}
                                            >
                                                <CheckCircleFilled style={{ color: '#52c41a' }} />
                                                <Text>{item.amenity?.name}</Text>
                                            </Space>
                                        </Col>
                                    ))}
                                </Row>
                            </Card>
                        )}

                        {listing.google_map_embed_code && (
                            <Card title="Ubicación">
                                <HtmlContent html={listing.google_map_embed_code} />
                            </Card>
                        )}
                    </Col>

                    <Col xs={24} lg={8}>
                        <Card title="Información de contacto" style={{ marginBottom: 24 }}>
                            <Space direction="vertical" size="middle" style={{ width: '100%' }}>
                                {listing.phone && (
                                    <a href={`tel:${listing.phone}`}>
                                        <PhoneOutlined /> {listing.phone}
                                    </a>
                                )}
                                {listing.email && (
                                    <a href={`mailto:${listing.email}`} style={{ wordBreak: 'break-word' }}>
                                        <MailOutlined /> {listing.email}
                                    </a>
                                )}
                                <Text>
                                    <EnvironmentOutlined /> {listing.address}
                                    {listing.location ? `, ${listing.location.name}` : ''}
                                </Text>
                                {listing.website && (
                                    <a href={listing.website} target="_blank" rel="noreferrer" style={{ wordBreak: 'break-all' }}>
                                        <GlobalOutlined /> {listing.website}
                                    </a>
                                )}
                            </Space>

                            {socials.length > 0 && (
                                <>
                                    <Divider style={{ margin: '16px 0' }} />
                                    <Space wrap>
                                        {socials.map((network) => (
                                            <a
                                                key={network.id}
                                                href={network.pivot?.url}
                                                target="_blank"
                                                rel="noreferrer"
                                            >
                                                <Tag color={network.color || 'default'}>{network.name}</Tag>
                                            </a>
                                        ))}
                                    </Space>
                                </>
                            )}
                        </Card>

                        {schedules.length > 0 && (
                            <Card
                                title={
                                    <span>
                                        <ClockCircleOutlined /> Horario de atención
                                    </span>
                                }
                                style={{ marginBottom: 24 }}
                            >
                                <List
                                    size="small"
                                    dataSource={schedules}
                                    renderItem={(schedule) => (
                                        <List.Item style={{ paddingInline: 0 }}>
                                            <Text strong>
                                                {DAY_LABELS[schedule.day] ?? schedule.day}
                                            </Text>
                                            <Text type="secondary">
                                                {schedule.start_time} - {schedule.end_time}
                                            </Text>
                                        </List.Item>
                                    )}
                                />
                            </Card>
                        )}

                        {relatedListings?.length > 0 && (
                            <Card title="Proveedores similares">
                                <Space direction="vertical" size="middle" style={{ width: '100%' }}>
                                    {relatedListings.map((related) => (
                                        <Link
                                            key={related.id}
                                            href={`/suppliers/${related.slug}`}
                                            style={{ display: 'flex', gap: 12, alignItems: 'center' }}
                                        >
                                            <img
                                                src={asset(related.image)}
                                                alt={related.title}
                                                style={{
                                                    width: 64,
                                                    height: 64,
                                                    borderRadius: 8,
                                                    objectFit: 'cover',
                                                    flexShrink: 0,
                                                }}
                                            />
                                            <Text strong ellipsis>
                                                {related.title}
                                            </Text>
                                        </Link>
                                    ))}
                                </Space>
                            </Card>
                        )}
                    </Col>
                </Row>
            </div>
        </>
    );
}
