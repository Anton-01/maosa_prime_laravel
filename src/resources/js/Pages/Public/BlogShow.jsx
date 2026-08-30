import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { Card, Col, Row, Space, Typography } from 'antd';
import { CalendarOutlined } from '@ant-design/icons';
import dayjs from 'dayjs';

import asset from '../../Utils/asset';
import HtmlContent from '../../Components/HtmlContent';
import PageHeader from '../../Components/Frontend/PageHeader';
import useTranslation from '../../Hooks/useTranslation';

const { Title, Text } = Typography;

export default function BlogShow({ blog, popularBlogs }) {
    const { t } = useTranslation();

    return (
        <>
            <Head title={blog.title} />
            <PageHeader title={t('public.blog_title')} breadcrumb={[{ label: t('public.blog_breadcrumb') }]} />

            <div style={{ padding: '32px 24px' }}>
                <Row gutter={[24, 24]}>
                    <Col xs={24} lg={16}>
                        <Card>
                            <img
                                src={asset(blog.image)}
                                alt={blog.title}
                                style={{ width: '100%', borderRadius: 8, marginBottom: 16 }}
                            />
                            <Text type="secondary">
                                <CalendarOutlined /> {dayjs(blog.created_at).format('DD MMM YYYY')}
                            </Text>
                            <Title level={2} style={{ marginTop: 8 }}>
                                {blog.title}
                            </Title>
                            <HtmlContent html={blog.description} />
                        </Card>
                    </Col>

                    <Col xs={24} lg={8}>
                        <Card title={t('public.similar_blogs')}>
                            <Space direction="vertical" size="middle" style={{ width: '100%' }}>
                                {popularBlogs?.map((item) => (
                                    <Link
                                        key={item.id}
                                        href={`/information/${item.slug}`}
                                        style={{ display: 'flex', gap: 12, alignItems: 'center' }}
                                    >
                                        <img
                                            src={asset(item.image)}
                                            alt={item.title}
                                            style={{
                                                width: 64,
                                                height: 64,
                                                borderRadius: 8,
                                                objectFit: 'cover',
                                                flexShrink: 0,
                                            }}
                                        />
                                        <div>
                                            <Text strong ellipsis style={{ display: 'block' }}>
                                                {item.title}
                                            </Text>
                                            <Text type="secondary" style={{ fontSize: 12 }}>
                                                {dayjs(item.created_at).format('DD MMM YYYY')}
                                            </Text>
                                        </div>
                                    </Link>
                                ))}
                            </Space>
                        </Card>
                    </Col>
                </Row>
            </div>
        </>
    );
}
