import React, { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { Button, DatePicker, Space, Table, Tag } from 'antd';
import { ArrowLeftOutlined, EyeOutlined } from '@ant-design/icons';
import dayjs from 'dayjs';
import PageContainer from '../../../Components/PageContainer';

const { RangePicker } = DatePicker;

export default function Sessions({ user, dateFrom, dateTo, sessions, urls }) {
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
            title: 'Inicio',
            dataIndex: 'startedAt',
            key: 'startedAt',
            width: 150,
            render: (value) => value ?? '—',
        },
        {
            title: 'Fin',
            dataIndex: 'endedAt',
            key: 'endedAt',
            width: 150,
            render: (value, record) =>
                record.isActive ? <Tag color="success">Activa</Tag> : (value ?? '—'),
        },
        {
            title: 'Duración',
            dataIndex: 'durationMinutes',
            key: 'durationMinutes',
            width: 110,
            render: (value) => (value != null ? `${value} min` : '—'),
        },
        {
            title: 'Dispositivo',
            dataIndex: 'deviceType',
            key: 'deviceType',
            width: 120,
            render: (value) => value ?? '—',
        },
        {
            title: 'Navegador',
            dataIndex: 'browser',
            key: 'browser',
            width: 130,
            render: (value) => value ?? '—',
        },
        {
            title: 'Ubicación',
            dataIndex: 'location',
            key: 'location',
            ellipsis: true,
            render: (value) => value ?? '—',
        },
        { title: 'Visitas', dataIndex: 'pageVisitsCount', key: 'pageVisitsCount', width: 100 },
        { title: 'Actividades', dataIndex: 'activitiesCount', key: 'activitiesCount', width: 110 },
        {
            title: 'Detalle',
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
            title={`Sesiones de ${user.name}`}
            breadcrumbItems={[
                { title: 'Estadísticas' },
                { title: user.name, href: urls.userDetail },
                { title: 'Sesiones' },
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
                    showTotal: (total) => `${total} sesiones`,
                }}
                locale={{ emptyText: 'Sin sesiones en el periodo seleccionado' }}
            />
        </PageContainer>
    );
}
