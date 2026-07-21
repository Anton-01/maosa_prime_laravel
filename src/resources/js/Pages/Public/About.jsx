import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { Card, Col, Row, Tag, Typography } from 'antd';
import { CalendarOutlined, SafetyCertificateOutlined, UserOutlined } from '@ant-design/icons';
import dayjs from 'dayjs';

import asset from '../../Utils/asset';
import HtmlContent from '../../Components/HtmlContent';
import PageHeader from '../../Components/Frontend/PageHeader';
import SectionHeading from '../../Components/Frontend/SectionHeading';

const { Title, Text, Paragraph } = Typography;

function stripHtml(html) {
    if (!html) return '';
    return html.replace(/<[^>]*>/g, '');
}

export default function About({ about, blogs, categories, aboutVideoThumbnail }) {
    return (
        <>
            <Head title="Quiénes somos" />
            <PageHeader title="Quiénes somos" breadcrumb={[{ label: 'Sobre nosotros' }]} />

            <div style={{ padding: '56px 24px' }}>
                <Row gutter={[40, 40]} align="middle">
                    <Col xs={24} lg={12}>
                        <Tag color="blue" style={{ marginBottom: 16 }}>
                            Nuestra Historia
                        </Tag>
                        <HtmlContent html={about?.description} />
                    </Col>
                    <Col xs={24} lg={12}>
                        <div style={{ position: 'relative' }}>
                            {(aboutVideoThumbnail || about?.image) && (
                                <img
                                    src={aboutVideoThumbnail || asset(about?.image)}
                                    alt="Sobre nosotros"
                                    style={{
                                        width: '100%',
                                        height: 380,
                                        objectFit: 'cover',
                                        borderRadius: 16,
                                        boxShadow: '0 20px 60px rgba(0,0,0,0.15)',
                                    }}
                                />
                            )}
                            {about?.video_url && (
                                <a
                                    href={about.video_url}
                                    target="_blank"
                                    rel="noreferrer"
                                    style={{
                                        position: 'absolute',
                                        top: 16,
                                        right: 16,
                                        background: '#fff',
                                        padding: '8px 16px',
                                        borderRadius: 50,
                                        display: 'inline-flex',
                                        alignItems: 'center',
                                        gap: 8,
                                        boxShadow: '0 8px 24px rgba(0,0,0,0.12)',
                                    }}
                                >
                                    <SafetyCertificateOutlined style={{ color: '#22c55e' }} /> Empresa
                                    Verificada
                                </a>
                            )}
                        </div>
                    </Col>
                </Row>
            </div>

            {blogs?.length > 0 && (
                <div style={{ padding: '56px 24px', background: '#fff' }}>
                    <SectionHeading
                        title="Nuestros Seminarios"
                        subtitle="Mantente actualizado con nuestros eventos y capacitaciones."
                    />
                    <Row gutter={[24, 24]}>
                        {blogs.map((blog) => (
                            <Col xs={24} md={12} key={blog.id}>
                                <Card
                                    hoverable
                                    styles={{ body: { padding: 20 } }}
                                    cover={
                                        <Link href={`/information/${blog.slug}`}>
                                            <img
                                                src={asset(blog.image)}
                                                alt={blog.title}
                                                style={{ height: 220, width: '100%', objectFit: 'cover' }}
                                            />
                                        </Link>
                                    }
                                >
                                    <Text type="secondary" style={{ fontSize: 13 }}>
                                        <CalendarOutlined /> {dayjs(blog.created_at).format('DD MMM YYYY')}
                                        {blog.author && (
                                            <>
                                                {'  ·  '}
                                                <UserOutlined /> {blog.author.name}
                                            </>
                                        )}
                                    </Text>
                                    <Link href={`/information/${blog.slug}`}>
                                        <Title level={4} style={{ margin: '8px 0' }} ellipsis={{ rows: 2 }}>
                                            {blog.title}
                                        </Title>
                                    </Link>
                                    <Paragraph type="secondary" ellipsis={{ rows: 3 }}>
                                        {stripHtml(blog.description)}
                                    </Paragraph>
                                    <Link href={`/information/${blog.slug}`}>Leer más →</Link>
                                </Card>
                            </Col>
                        ))}
                    </Row>
                </div>
            )}

            {categories?.length > 0 && (
                <div style={{ padding: '56px 24px' }}>
                    <SectionHeading title="Categorías destacadas" />
                    <Row gutter={[24, 24]}>
                        {categories.map((category) => (
                            <Col xs={24} sm={12} lg={8} key={category.id}>
                                <Link href={`/suppliers?category=${category.slug}`}>
                                    <Card
                                        hoverable
                                        styles={{ body: { padding: 0 } }}
                                        cover={
                                            <div style={{ position: 'relative', height: 160 }}>
                                                <img
                                                    src={asset(category.background_image)}
                                                    alt={category.name}
                                                    style={{ width: '100%', height: '100%', objectFit: 'cover' }}
                                                />
                                                <div
                                                    style={{
                                                        position: 'absolute',
                                                        inset: 0,
                                                        background:
                                                            'linear-gradient(180deg, rgba(0,0,0,0) 40%, rgba(0,0,0,0.7))',
                                                        display: 'flex',
                                                        alignItems: 'flex-end',
                                                        padding: 16,
                                                    }}
                                                >
                                                    <Text strong style={{ color: '#fff', fontSize: 17 }}>
                                                        {category.name}
                                                    </Text>
                                                </div>
                                            </div>
                                        }
                                    />
                                </Link>
                            </Col>
                        ))}
                    </Row>
                </div>
            )}
        </>
    );
}
