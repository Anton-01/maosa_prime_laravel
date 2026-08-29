import React from 'react';
import { Link } from '@inertiajs/react';
import { Button, Card, Descriptions, Flex, Space, Table, Tabs, Tag, Typography } from 'antd';
import { ArrowLeftOutlined, EyeOutlined, InteractionOutlined } from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import ActivityMetadata from '../../../Components/ActivityMetadata';
import useTranslation from '../../../Hooks/useTranslation';

const { Text } = Typography;

export default function SessionDetail({ session, pageVisits, activities, urls }) {
    const { t } = useTranslation();

    const visitColumns = [
        { title: t('statistics.time'), dataIndex: 'visitedAt', key: 'visitedAt', width: 170 },
        {
            title: t('statistics.page'),
            dataIndex: 'pageTitle',
            key: 'pageTitle',
            ellipsis: true,
            render: (title, record) => (
                <Space direction="vertical" size={0}>
                    <Text>{title || t('statistics.untitled')}</Text>
                    <Text type="secondary" style={{ fontSize: 12 }}>
                        {record.url}
                    </Text>
                </Space>
            ),
        },
        {
            title: t('statistics.time_on_page'),
            dataIndex: 'timeOnPage',
            key: 'timeOnPage',
            width: 150,
            render: (value) => (value != null ? `${value} s` : '—'),
        },
    ];

    const activityColumns = [
        { title: t('statistics.time'), dataIndex: 'createdAt', key: 'createdAt', width: 170 },
        {
            title: t('statistics.type'),
            dataIndex: 'type',
            key: 'type',
            width: 150,
            render: (value) => <Tag color="blue">{value}</Tag>,
        },
        {
            title: t('statistics.description'),
            dataIndex: 'description',
            key: 'description',
            ellipsis: true,
            render: (value) => value ?? '—',
        },
        {
            title: t('statistics.detail'),
            dataIndex: 'metadata',
            key: 'metadata',
            width: 320,
            render: (value) => <ActivityMetadata metadata={value} />,
        },
        {
            title: 'URL',
            dataIndex: 'url',
            key: 'url',
            ellipsis: true,
            render: (value) => (value ? <Text code>{value}</Text> : '—'),
        },
    ];

    const tabItems = [
        {
            key: 'visits',
            label: t('statistics.visits_tab', { count: pageVisits.length }),
            icon: <EyeOutlined />,
            children: (
                <Table
                    rowKey="id"
                    size="small"
                    columns={visitColumns}
                    dataSource={pageVisits}
                    pagination={{
                        position: ['bottomRight'],
                        pageSize: 20,
                        hideOnSinglePage: true,
                    }}
                    locale={{ emptyText: t('statistics.no_visits_session') }}
                />
            ),
        },
        {
            key: 'activities',
            label: t('statistics.activities_tab', { count: activities.length }),
            icon: <InteractionOutlined />,
            children: (
                <Table
                    rowKey="id"
                    size="small"
                    columns={activityColumns}
                    dataSource={activities}
                    pagination={{
                        position: ['bottomRight'],
                        pageSize: 20,
                        hideOnSinglePage: true,
                    }}
                    locale={{ emptyText: t('statistics.no_activities_session') }}
                />
            ),
        },
    ];

    return (
        <PageContainer
            title={t('statistics.session_title', { id: session.id, name: session.userName })}
            breadcrumbItems={[
                { title: t('statistics.breadcrumb') },
                { title: session.userName, href: urls.userDetail },
                { title: t('statistics.sessions'), href: urls.userSessions },
                { title: t('statistics.session_breadcrumb', { id: session.id }) },
            ]}
            extra={
                <Link href={urls.userSessions}>
                    <Button icon={<ArrowLeftOutlined />}>{t('statistics.back_to_sessions')}</Button>
                </Link>
            }
            wrapInCard={false}
        >
            <Flex vertical gap={16}>
                <Card title={t('statistics.session_info')}>
                    <Descriptions
                        bordered
                        size="small"
                        column={{ xs: 1, md: 2, lg: 3 }}
                        items={[
                            { key: 'user', label: t('statistics.user'), children: session.userName },
                            { key: 'email', label: t('users.email'), children: session.userEmail ?? '—' },
                            {
                                key: 'status',
                                label: t('statistics.status'),
                                children: session.isActive ? (
                                    <Tag color="success">{t('statistics.active')}</Tag>
                                ) : (
                                    <Tag>{t('statistics.finished')}</Tag>
                                ),
                            },
                            {
                                key: 'startedAt',
                                label: t('statistics.started_at'),
                                children: session.startedAt ?? '—',
                            },
                            {
                                key: 'endedAt',
                                label: t('statistics.ended_at'),
                                children: session.endedAt ?? '—',
                            },
                            {
                                key: 'duration',
                                label: t('statistics.duration'),
                                children:
                                    session.durationMinutes != null
                                        ? `${session.durationMinutes} min`
                                        : '—',
                            },
                            {
                                key: 'device',
                                label: t('statistics.device'),
                                children: session.deviceType ?? '—',
                            },
                            {
                                key: 'browser',
                                label: t('statistics.browser'),
                                children: session.browser ?? '—',
                            },
                            {
                                key: 'platform',
                                label: t('statistics.platform'),
                                children: session.platform ?? '—',
                            },
                            {
                                key: 'location',
                                label: t('statistics.location'),
                                children: session.location ?? '—',
                            },
                            { key: 'ip', label: 'IP', children: session.ipAddress ?? '—' },
                        ]}
                    />
                </Card>

                <Card>
                    <Tabs items={tabItems} />
                </Card>
            </Flex>
        </PageContainer>
    );
}
