import React from 'react';
import { Link } from '@inertiajs/react';
import { Button, Card, Descriptions, Flex, Space, Table, Tabs, Tag, Typography } from 'antd';
import { ArrowLeftOutlined, EyeOutlined, InteractionOutlined } from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import ActivityMetadata from '../../../Components/ActivityMetadata';

const { Text } = Typography;

export default function SessionDetail({ session, pageVisits, activities, urls }) {
    const visitColumns = [
        { title: 'Hora', dataIndex: 'visitedAt', key: 'visitedAt', width: 170 },
        {
            title: 'Página',
            dataIndex: 'pageTitle',
            key: 'pageTitle',
            ellipsis: true,
            render: (title, record) => (
                <Space direction="vertical" size={0}>
                    <Text>{title || 'Sin título'}</Text>
                    <Text type="secondary" style={{ fontSize: 12 }}>
                        {record.url}
                    </Text>
                </Space>
            ),
        },
        {
            title: 'Tiempo en página',
            dataIndex: 'timeOnPage',
            key: 'timeOnPage',
            width: 150,
            render: (value) => (value != null ? `${value} s` : '—'),
        },
    ];

    const activityColumns = [
        { title: 'Hora', dataIndex: 'createdAt', key: 'createdAt', width: 170 },
        {
            title: 'Tipo',
            dataIndex: 'type',
            key: 'type',
            width: 150,
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

    const tabItems = [
        {
            key: 'visits',
            label: `Visitas (${pageVisits.length})`,
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
                    locale={{ emptyText: 'Sin visitas en esta sesión' }}
                />
            ),
        },
        {
            key: 'activities',
            label: `Actividades (${activities.length})`,
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
                    locale={{ emptyText: 'Sin actividades en esta sesión' }}
                />
            ),
        },
    ];

    return (
        <PageContainer
            title={`Sesión #${session.id} — ${session.userName}`}
            breadcrumbItems={[
                { title: 'Estadísticas' },
                { title: session.userName, href: urls.userDetail },
                { title: 'Sesiones', href: urls.userSessions },
                { title: `Sesión #${session.id}` },
            ]}
            extra={
                <Link href={urls.userSessions}>
                    <Button icon={<ArrowLeftOutlined />}>Volver a sesiones</Button>
                </Link>
            }
            wrapInCard={false}
        >
            <Flex vertical gap={16}>
                <Card title="Información de la sesión">
                    <Descriptions
                        bordered
                        size="small"
                        column={{ xs: 1, md: 2, lg: 3 }}
                        items={[
                            { key: 'user', label: 'Usuario', children: session.userName },
                            { key: 'email', label: 'Correo', children: session.userEmail ?? '—' },
                            {
                                key: 'status',
                                label: 'Estado',
                                children: session.isActive ? (
                                    <Tag color="success">Activa</Tag>
                                ) : (
                                    <Tag>Finalizada</Tag>
                                ),
                            },
                            { key: 'startedAt', label: 'Inicio', children: session.startedAt ?? '—' },
                            { key: 'endedAt', label: 'Fin', children: session.endedAt ?? '—' },
                            {
                                key: 'duration',
                                label: 'Duración',
                                children:
                                    session.durationMinutes != null
                                        ? `${session.durationMinutes} min`
                                        : '—',
                            },
                            { key: 'device', label: 'Dispositivo', children: session.deviceType ?? '—' },
                            { key: 'browser', label: 'Navegador', children: session.browser ?? '—' },
                            { key: 'platform', label: 'Plataforma', children: session.platform ?? '—' },
                            { key: 'location', label: 'Ubicación', children: session.location ?? '—' },
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
