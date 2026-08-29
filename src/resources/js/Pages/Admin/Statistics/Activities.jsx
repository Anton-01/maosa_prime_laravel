import React, { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { Button, DatePicker, Flex, Select, Space, Table, Tag, Typography } from 'antd';
import { ArrowLeftOutlined } from '@ant-design/icons';
import dayjs from 'dayjs';
import PageContainer from '../../../Components/PageContainer';
import ActivityMetadata from '../../../Components/ActivityMetadata';

const { RangePicker } = DatePicker;
const { Text } = Typography;

export default function Activities({ user, dateFrom, dateTo, activityType, activities, urls }) {
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
        { title: 'Fecha', dataIndex: 'createdAt', key: 'createdAt', width: 170 },
        {
            title: 'Tipo',
            dataIndex: 'type',
            key: 'type',
            width: 160,
            render: (value) => <Tag color="blue">{value}</Tag>,
        },
        {
            title: 'Descripción',
            dataIndex: 'description',
            key: 'description',
            ellipsis: true,
            render: (value) => value ?? '—',
        },
        {
            title: 'Detalle',
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
            title={`Actividades de ${user.name}`}
            breadcrumbItems={[
                { title: 'Estadísticas' },
                { title: user.name, href: urls.userDetail },
                { title: 'Actividades' },
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
                        <Button icon={<ArrowLeftOutlined />}>Volver al detalle</Button>
                    </Link>
                </Space>
            }
        >
            <Flex style={{ marginBottom: 16 }}>
                <Select
                    allowClear
                    placeholder="Todos los tipos de actividad"
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
                    showTotal: (total) => `${total} actividades`,
                }}
                locale={{ emptyText: 'Sin actividades en el periodo seleccionado' }}
            />
        </PageContainer>
    );
}
