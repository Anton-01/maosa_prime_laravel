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
import useTranslation from '../../Hooks/useTranslation';

const { Title, Text, Paragraph } = Typography;

export default function PrivacyPolicy({ privacyPolicy }) {
    const { t } = useTranslation();

    const info = [
        {
            icon: <SafetyOutlined />,
            title: t('public.data_protection_title'),
            text: t('public.data_protection_text'),
        },
        {
            icon: <LockOutlined />,
            title: t('public.confidentiality_title'),
            text: t('public.confidentiality_text'),
        },
        {
            icon: <SyncOutlined />,
            title: t('public.updates_title'),
            text: t('public.updates_text'),
        },
    ];

    return (
        <>
            <Head title={t('public.privacy_title')} />
            <PageHeader title={t('public.privacy_title')} breadcrumb={[{ label: t('public.privacy_title') }]} />

            <div style={{ padding: '32px 24px' }}>
                <Row gutter={[24, 24]}>
                    <Col xs={24} lg={16}>
                        <Card>
                            <Title level={3}>{t('public.privacy_title')}</Title>
                            <Text type="secondary">
                                {t('public.privacy_updated', { date: dayjs().format('DD/MM/YYYY') })}
                            </Text>
                            <div style={{ marginTop: 24 }}>
                                <HtmlContent html={privacyPolicy?.description} />
                            </div>
                        </Card>
                    </Col>

                    <Col xs={24} lg={8}>
                        <Card title={t('frontend.information')} style={{ marginBottom: 24 }}>
                            <List
                                itemLayout="horizontal"
                                dataSource={info}
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

                        <Card title={t('public.related_links')}>
                            <Space direction="vertical">
                                <Link href="/terms-and-condition">{t('public.terms_link')}</Link>
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
