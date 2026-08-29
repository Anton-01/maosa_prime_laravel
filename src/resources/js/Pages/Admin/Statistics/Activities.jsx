import React, { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { Button, DatePicker, Flex, Select, Space, Table, Tag, Typography } from 'antd';
import { ArrowLeftOutlined } from '@ant-design/icons';
import dayjs from 'dayjs';
import PageContainer from '../../../Components/PageContainer';
import ActivityMetadata from '../../../Components/ActivityMetadata';
import useTranslation from '../../../Hooks/useTranslation';

const { RangePicker } = DatePicker;
const { Text } = Typography;

export default function Activities({ user, dateFrom, dateTo, activityType, activities, urls }) {
    const { t } = useTranslation();
    const [range, setRange] = useState([dayjs(dateFrom), dayjs(dateTo)]);
    const [loading, setLoading] = useState(false);

    const visit = (params) => {
        router.get(urls.self, {
            date_from: range[0].format('YYYY-MM-DD'),
            date_to: range[1].format('YYYY-MM-DD'),
            activity_type: activityType ?? '',
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
        visit({
            date_from: value[0].format('YYYY-MM-DD'),
            date_to: value[1].format('YYYY-MM-DD'),
            page: 1,
        });
    };

    const columns = [
        { title: t('statistics.date'), dataIndex: 'createdAt', key: 'createdAt', width: 170 },
        {
            title: t('statistics.type'),
            dataIndex: 'type',
            key: 'type',
            width: 160,
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

    return (
        <PageContainer
            title={t('statistics.activities_title', { name: user.name })}
            breadcrumbItems={[
                { title: t('statistics.breadcrumb') },
                { title: user.name, href: urls.userDetail },
                { title: t('statistics.activities') },
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
            <Flex style={{ marginBottom: 16 }}>
                <Select
                    allowClear
                    placeholder={t('statistics.all_types')}
                    style={{ minWidth: 260 }}
                    value={activityType || undefined}
                    onChange={(value) => visit({ activity_type: value ?? '', page: 1 })}
                    options={activities.availableTypes.map((type) => ({ value: type, label: type }))}
                />
            </Flex>

            <Table
                rowKey="id"
                columns={columns}
                dataSource={activities.data}
                loading={loading}
                onChange={(pagination) => visit({ page: pagination.current })}
                pagination={{
                    position: ['bottomRight'],
                    current: activities.page,
                    pageSize: activities.perPage,
                    total: activities.total,
                    showSizeChanger: false,
                    showTotal: (total) => t('statistics.total_activities', { count: total }),
                }}
                locale={{ emptyText: t('statistics.no_activities_period') }}
            />
        </PageContainer>
    );
}
