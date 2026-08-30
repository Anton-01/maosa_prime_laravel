import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { Card, Col, List, Row, Space, Typography } from 'antd';
import { CheckCircleOutlined } from '@ant-design/icons';
import dayjs from 'dayjs';

import HtmlContent from '../../Components/HtmlContent';
import PageHeader from '../../Components/Frontend/PageHeader';
import useTranslation from '../../Hooks/useTranslation';

const { Title, Text } = Typography;

export default function Terms({ termsAndCondition }) {
    const { t } = useTranslation();

    const summary = [
        t('public.summary_usage'),
        t('public.summary_ip'),
        t('public.summary_liability'),
        t('public.summary_cancellation'),
        t('public.summary_disputes'),
    ];

    return (
        <>
            <Head title={t('public.terms_title')} />
            <PageHeader
                title={t('public.terms_title')}
                breadcrumb={[{ label: t('public.terms_title') }]}
            />

            <div style={{ padding: '32px 24px' }}>
                <Row gutter={[24, 24]}>
                    <Col xs={24} lg={16}>
                        <Card>
                            <Title level={3}>{t('public.terms_general')}</Title>
                            <Text type="secondary">
                                {t('public.privacy_updated', { date: dayjs().format('DD/MM/YYYY') })}
                            </Text>
                            <div style={{ marginTop: 24 }}>
                                <HtmlContent html={termsAndCondition?.description} />
                            </div>
                        </Card>
                    </Col>

                    <Col xs={24} lg={8}>
                        <Card title={t('public.summary')} style={{ marginBottom: 24 }}>
                            <List
                                size="small"
                                dataSource={summary}
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

                        <Card title={t('public.related_links')}>
                            <Space direction="vertical">
                                <Link href="/privacy-policy">{t('public.privacy_link')}</Link>
                                <Link href="/contact">{t('public.support_link')}</Link>
                                <Link href="/">{t('public.home_link')}</Link>
                            </Space>
                        </Card>
                    </Col>
                </Row>
            </div>
        </>
    );
}
