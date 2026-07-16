import React, { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import {
    Button,
    Card,
    Col,
    DatePicker,
    Empty,
    Row,
    Space,
    Statistic,
    Table,
    Tag,
    Typography,
} from 'antd';
import {
    ArrowLeftOutlined,
    ClockCircleOutlined,
    EyeOutlined,
    FileTextOutlined,
    HistoryOutlined,
    InteractionOutlined,
} from '@ant-design/icons';
import { Line, Pie } from '@ant-design/plots';
import dayjs from 'dayjs';
import PageContainer from '../../../Components/PageContainer';

const { RangePicker } = DatePicker;
const { Text } = Typography;

export default function UserDetail({
    user,
    dateFrom,
    dateTo,
    metrics,
    topPages,
    recentSessions,
    activityTypes,
    activityByDay,
    navigationFlows,
    urls,
}) {
    const [range, setRange] = useState([dayjs(dateFrom), dayjs(dateTo)]);

    const handleRangeChange = (value) => {
        if (!value || !value[0] || !value[1]) return;

        setRange(value);

        router.get(urls.self, {
            date_from: value[0].format('YYYY-MM-DD'),
            date_to: value[1].format('YYYY-MM-DD'),
        }, { preserveState: true, preserveScroll: true });
    };

    const dateQuery = `date_from=${range[0].format('YYYY-MM-DD')}&date_to=${range[1].format('YYYY-MM-DD')}`;

    const kpis = [
        { title: 'Sesiones', value: metrics.totalSessions, icon: <InteractionOutlined /> },
        { title: 'Visitas de página', value: metrics.totalPageViews, icon: <EyeOutlined /> },
        { title: 'Actividades', value: metrics.totalActivities, icon: <FileTextOutlined /> },
        {
            title: 'Duración media de sesión',
            value: metrics.avgSessionDurationMinutes ?? '—',
            suffix: metrics.avgSessionDurationMinutes ? 'min' : undefined,
            icon: <ClockCircleOutlined />,
        },
    ];

    return (
        <PageContainer
            title={`Estadísticas de ${user.name}`}
            breadcrumbItems={[
                { title: 'Estadísticas', href: urls.base },
                { title: user.name },
            ]}
            extra={
                <Space wrap>
                    <RangePicker
                        value={range}
                        onChange={handleRangeChange}
                        allowClear={false}
                        format="DD/MM/YYYY"
                    />
                    <Link href={`${urls.sessions}?${dateQuery}`}>
                        <Button icon={<HistoryOutlined />}>Sesiones</Button>
                    </Link>
                    <Link href={`${urls.activities}?${dateQuery}`}>
                        <Button icon={<FileTextOutlined />}>Actividades</Button>
                    </Link>
                    <Link href={urls.base}>
                        <Button icon={<ArrowLeftOutlined />}>Panel General</Button>
                    </Link>
                </Space>
            }
            wrapInCard={false}
        >
            <Row gutter={[16, 16]}>
                {kpis.map((kpi) => (
                    <Col xs={12} lg={6} key={kpi.title}>
                        <Card>
                            <Statistic
                                title={kpi.title}
                                value={kpi.value}
                                suffix={kpi.suffix}
                                prefix={kpi.icon}
                            />
                        </Card>
                    </Col>
                ))}

                <Col xs={24} lg={14}>
                    <Card title="Visitas por día" style={{ height: '100%' }}>
                        {activityByDay.length > 0 ? (
                            <Line
                                data={activityByDay}
                                xField="date"
                                yField="visits"
                                height={260}
                                smooth
                            />
                        ) : (
                            <Empty description="Sin visitas en el periodo" />
                        )}
                    </Card>
                </Col>
                <Col xs={24} lg={10}>
                    <Card title="Tipos de actividad" style={{ height: '100%' }}>
                        {activityTypes.length > 0 ? (
                            <Pie
                                data={activityTypes}
                                angleField="count"
                                colorField="type"
                                height={260}
                                innerRadius={0.6}
                                legend={{ color: { position: 'bottom' } }}
                            />
                        ) : (
                            <Empty description="Sin actividades en el periodo" />
                        )}
                    </Card>
                </Col>

                <Col xs={24} lg={12}>
                    <Card title="Páginas más visitadas">
                        <Table
                            rowKey={(record) => record.url}
                            size="small"
                            dataSource={topPages}
                            pagination={false}
                            columns={[
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
                                { title: 'Visitas', dataIndex: 'visits', key: 'visits', width: 100 },
                            ]}
                            locale={{ emptyText: 'Sin visitas registradas' }}
                        />
                    </Card>
                </Col>

                <Col xs={24} lg={12}>
                    <Card title="Sesiones recientes">
                        <Table
                            rowKey="id"
                            size="small"
                            dataSource={recentSessions}
                            pagination={false}
                            columns={[
                                {
                                    title: 'Inicio',
                                    dataIndex: 'startedAt',
                                    key: 'startedAt',
                                    width: 140,
                                    render: (value, record) => (
                                        <Link href={`${urls.sessionDetailBase}/${record.id}`}>
                                            {value ?? '—'}
                                        </Link>
                                    ),
                                },
                                {
                                    title: 'Duración',
                                    dataIndex: 'durationMinutes',
                                    key: 'durationMinutes',
                                    width: 100,
                                    render: (value, record) =>
                                        record.isActive ? (
                                            <Tag color="success">Activa</Tag>
                                        ) : (
                                            (value != null ? `${value} min` : '—')
                                        ),
                                },
                                {
                                    title: 'Dispositivo',
                                    dataIndex: 'deviceType',
                                    key: 'deviceType',
                                    width: 110,
                                    render: (value) => value ?? '—',
                                },
                                {
                                    title: 'Ubicación',
                                    dataIndex: 'location',
                                    key: 'location',
                                    ellipsis: true,
                                    render: (value) => value ?? '—',
                                },
                            ]}
                            locale={{ emptyText: 'Sin sesiones registradas' }}
                        />
                    </Card>
                </Col>

                <Col xs={24}>
                    <Card title="Flujos de navegación más frecuentes">
                        <Table
                            rowKey={(record) => `${record.fromUrl}-${record.toUrl}`}
                            size="small"
                            dataSource={navigationFlows}
                            pagination={false}
                            columns={[
                                { title: 'Desde', dataIndex: 'fromUrl', key: 'fromUrl', ellipsis: true },
                                { title: 'Hacia', dataIndex: 'toUrl', key: 'toUrl', ellipsis: true },
                                { title: 'Veces', dataIndex: 'count', key: 'count', width: 100 },
                            ]}
                            locale={{ emptyText: 'Sin flujos registrados' }}
                        />
                    </Card>
                </Col>
            </Row>
        </PageContainer>
    );
}
