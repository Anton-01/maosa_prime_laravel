import React, { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { Button, DatePicker, Space, Table, Tag } from 'antd';
import { ArrowLeftOutlined, EyeOutlined } from '@ant-design/icons';
import dayjs from 'dayjs';
import PageContainer from '../../../Components/PageContainer';
import useTranslation from '../../../Hooks/useTranslation';

const { RangePicker } = DatePicker;

export default function Sessions({ user, dateFrom, dateTo, sessions, urls }) {
    const { t } = useTranslation();
    const [range, setRange] = useState([dayjs(dateFrom), dayjs(dateTo)]);
    const [loading, setLoading] = useState(false);

    const visit = (params) => {
        router.get(urls.self, {
            date_from: range[0].format('YYYY-MM-DD'),
            date_to: range[1].format('YYYY-MM-DD'),
            ...params,
        }, {
            preserveState: true,
            preserveScroll: true,
            onStart: () => setLoading(true),
            onFinish: () => setLoading(false),
        });
    };

    const handleRangeChange = (value) => {
        if (!value || !value[0] || !value[1]) return;

        setRange(value);
        router.get(urls.self, {
            date_from: value[0].format('YYYY-MM-DD'),
            date_to: value[1].format('YYYY-MM-DD'),
        }, { preserveState: true, preserveScroll: true });
    };

    const columns = [
        {
            title: t('statistics.started_at'),
            dataIndex: 'startedAt',
            key: 'startedAt',
            width: 150,
            render: (value) => value ?? '—',
        },
        {
            title: t('statistics.ended_at'),
            dataIndex: 'endedAt',
            key: 'endedAt',
            width: 150,
            render: (value, record) =>
                record.isActive ? <Tag color="success">{t('statistics.active')}</Tag> : (value ?? '—'),
        },
        {
            title: t('statistics.duration'),
            dataIndex: 'durationMinutes',
            key: 'durationMinutes',
            width: 110,
            render: (value) => (value != null ? `${value} min` : '—'),
        },
        {
            title: t('statistics.device'),
            dataIndex: 'deviceType',
            key: 'deviceType',
            width: 120,
            render: (value) => value ?? '—',
        },
        {
            title: t('statistics.browser'),
            dataIndex: 'browser',
            key: 'browser',
            width: 130,
            render: (value) => value ?? '—',
        },
        {
            title: t('statistics.location'),
            dataIndex: 'location',
            key: 'location',
            ellipsis: true,
            render: (value) => value ?? '—',
        },
        {
            title: t('statistics.visits'),
            dataIndex: 'pageVisitsCount',
            key: 'pageVisitsCount',
            width: 100,
        },
        {
            title: t('statistics.activities'),
            dataIndex: 'activitiesCount',
            key: 'activitiesCount',
            width: 110,
        },
        {
            title: t('statistics.detail'),
            key: 'actions',
            width: 90,
            render: (_, record) => (
                <Link href={`${urls.sessionDetailBase}/${record.id}`}>
                    <Button size="small" icon={<EyeOutlined />} />
                </Link>
            ),
        },
    ];

    return (
        <PageContainer
            title={t('statistics.sessions_title', { name: user.name })}
            breadcrumbItems={[
                { title: t('statistics.breadcrumb') },
                { title: user.name, href: urls.userDetail },
                { title: t('statistics.sessions') },
            ]}
            extra={
                <Space wrap>
                    <RangePicker
                        value={range}
                        onChange={handleRangeChange}
                        allowClear={false}
                        format="DD/MM/YYYY"
                    />
                    <Link href={urls.userDetail}>
                        <Button icon={<ArrowLeftOutlined />}>{t('common.back_to_detail')}</Button>
                    </Link>
                </Space>
            }
        >
            <Table
                rowKey="id"
                columns={columns}
                dataSource={sessions.data}
                loading={loading}
                scroll={{ x: 1100 }}
                onChange={(pagination) => visit({ page: pagination.current })}
                pagination={{
                    position: ['bottomRight'],
                    current: sessions.page,
                    pageSize: sessions.perPage,
                    total: sessions.total,
                    showSizeChanger: false,
                    showTotal: (total) => t('statistics.total_sessions', { count: total }),
                }}
                locale={{ emptyText: t('statistics.no_sessions_period') }}
            />
        </PageContainer>
    );
}
