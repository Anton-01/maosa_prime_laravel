import React from 'react';
import { Button, Card, Col, Empty, Progress, Row, Space, Statistic, Table, Tag, theme } from 'antd';
import { Line, Pie } from '@ant-design/plots';
import {
    AppstoreOutlined,
    ArrowDownOutlined,
    ArrowUpOutlined,
    BarChartOutlined,
    CheckCircleOutlined,
    CoffeeOutlined,
    EnvironmentOutlined,
    EyeOutlined,
    FileTextOutlined,
    LineChartOutlined,
    PlusCircleOutlined,
    RightOutlined,
    SafetyCertificateOutlined,
    ShopOutlined,
    TagsOutlined,
    TeamOutlined,
    ThunderboltOutlined,
    UserAddOutlined,
    UserOutlined,
} from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';

/** Weekly trend vs the previous week, rendered next to the statistic. */
function WeeklyTrend({ current, previous }) {
    if (previous === 0 && current === 0) return null;

    const isUp = current >= previous;
    const percentage =
        previous === 0 ? 100 : Math.abs(Math.round(((current - previous) / previous) * 100));

    return (
        <Tag
            color={isUp ? 'success' : 'error'}
            icon={isUp ? <ArrowUpOutlined /> : <ArrowDownOutlined />}
        >
            {percentage}% vs semana anterior
        </Tag>
    );
}

const LATEST_USER_COLUMNS = [
    { title: 'Nombre', dataIndex: 'name', key: 'name', ellipsis: true },
    { title: 'Email', dataIndex: 'email', key: 'email', ellipsis: true },
    {
        title: 'Fecha',
        dataIndex: 'createdAt',
        key: 'createdAt',
        width: 110,
        render: (value) => <Tag>{value}</Tag>,
    },
];

const LATEST_LISTING_COLUMNS = [
    { title: 'Nombre', dataIndex: 'title', key: 'title', ellipsis: true },
    {
        title: 'Categoría',
        dataIndex: 'category',
        key: 'category',
        render: (value) => <Tag color="blue">{value}</Tag>,
    },
    { title: 'Ubicación', dataIndex: 'location', key: 'location', ellipsis: true },
];

export default function Index({
    providerStats,
    catalogStats,
    userStats,
    weeklyActivity,
    activityChart,
    listingsByCategory,
    latestUsers,
    latestListings,
    urls,
}) {
    const { token } = theme.useToken();

    const verifiedPercentage =
        providerStats.total > 0
            ? Math.round((providerStats.verified / providerStats.total) * 100)
            : 0;
    const activePercentage =
        providerStats.total > 0
            ? Math.round((providerStats.active / providerStats.total) * 100)
            : 0;

    const activitySeries = activityChart.flatMap((day) => [
        { date: day.date, value: day.visits, type: 'Visitas' },
        { date: day.date, value: day.sessions, type: 'Sesiones' },
    ]);

    const hasCategoryData = listingsByCategory.some((category) => category.count > 0);

    const quickAccessItems = [
        { label: 'Nuevo Proveedor', icon: <PlusCircleOutlined />, url: urls.createListing },
        { label: 'Nuevo Usuario', icon: <UserAddOutlined />, url: urls.createUser },
        { label: 'Categorías', icon: <TagsOutlined />, url: urls.categories },
        { label: 'Estadísticas', icon: <BarChartOutlined />, url: urls.statistics },
    ];

    return (
        <PageContainer
            title="Dashboard"
            breadcrumbItems={[{ title: 'Panel de Control' }, { title: 'Resumen General' }]}
            wrapInCard={false}
        >
            {/* Segment: providers */}
            <Row gutter={[16, 16]}>
                <Col xs={24} lg={12}>
                    <Card title={<Space><ShopOutlined /> Proveedores</Space>} style={{ height: '100%' }}>
                        <Row gutter={[16, 16]}>
                            <Col xs={8}>
                                <Statistic title="Total" value={providerStats.total} prefix={<ShopOutlined />} />
                            </Col>
                            <Col xs={8}>
                                <Statistic
                                    title="Verificados"
                                    value={providerStats.verified}
                                    prefix={<SafetyCertificateOutlined />}
                                    valueStyle={{ color: token.colorSuccess }}
                                />
                            </Col>
                            <Col xs={8}>
                                <Statistic
                                    title="Activos"
                                    value={providerStats.active}
                                    prefix={<CheckCircleOutlined />}
                                    valueStyle={{ color: token.colorPrimary }}
                                />
                            </Col>
                            <Col xs={12}>
                                <Progress percent={verifiedPercentage} size="small" status="active" />
                                <small>Proveedores verificados</small>
                            </Col>
                            <Col xs={12}>
                                <Progress
                                    percent={activePercentage}
                                    size="small"
                                    status="active"
                                    strokeColor={token.colorSuccess}
                                />
                                <small>Proveedores activos</small>
                            </Col>
                        </Row>
                    </Card>
                </Col>

                {/* Segment: users */}
                <Col xs={24} lg={12}>
                    <Card title={<Space><TeamOutlined /> Usuarios</Space>} style={{ height: '100%' }}>
                        <Row gutter={[16, 16]}>
                            <Col xs={12} sm={6}>
                                <Statistic title="Total" value={userStats.total} prefix={<TeamOutlined />} />
                            </Col>
                            <Col xs={12} sm={6}>
                                <Statistic
                                    title="Administradores"
                                    value={userStats.admins}
                                    prefix={<UserOutlined />}
                                    valueStyle={{ color: token.colorPrimary }}
                                />
                            </Col>
                            <Col xs={12} sm={6}>
                                <Statistic
                                    title="Acceso a precios"
                                    value={userStats.withPriceAccess}
                                    prefix={<EyeOutlined />}
                                    valueStyle={{ color: token.colorWarning }}
                                />
                            </Col>
                            <Col xs={12} sm={6}>
                                <Statistic
                                    title="Nuevos (7 días)"
                                    value={userStats.newLastWeek}
                                    prefix={<UserAddOutlined />}
                                    valueStyle={{ color: token.colorSuccess }}
                                />
                            </Col>
                        </Row>
                    </Card>
                </Col>

                {/* Segment: catalog & content */}
                <Col xs={24}>
                    <Card title={<Space><AppstoreOutlined /> Catálogos y Contenido</Space>}>
                        <Row gutter={[16, 16]}>
                            <Col xs={12} sm={6}>
                                <Statistic title="Categorías" value={catalogStats.categories} prefix={<TagsOutlined />} />
                            </Col>
                            <Col xs={12} sm={6}>
                                <Statistic
                                    title="Ubicaciones"
                                    value={catalogStats.locations}
                                    prefix={<EnvironmentOutlined />}
                                />
                            </Col>
                            <Col xs={12} sm={6}>
                                <Statistic title="Servicios" value={catalogStats.amenities} prefix={<CoffeeOutlined />} />
                            </Col>
                            <Col xs={12} sm={6}>
                                <Statistic
                                    title="Publicaciones Blog"
                                    value={catalogStats.blogPosts}
                                    prefix={<FileTextOutlined />}
                                />
                            </Col>
                        </Row>
                    </Card>
                </Col>

                {/* Segment: weekly activity */}
                <Col xs={24}>
                    <Card title={<Space><LineChartOutlined /> Actividad de los Últimos 7 Días</Space>}>
                        <Row gutter={[24, 24]}>
                            <Col xs={24} md={8}>
                                <Space direction="vertical" size="large" style={{ display: 'flex' }}>
                                    <div>
                                        <Statistic title="Sesiones" value={weeklyActivity.sessions} />
                                        <WeeklyTrend
                                            current={weeklyActivity.sessions}
                                            previous={weeklyActivity.previous.sessions}
                                        />
                                    </div>
                                    <div>
                                        <Statistic title="Visitas" value={weeklyActivity.visits} />
                                        <WeeklyTrend
                                            current={weeklyActivity.visits}
                                            previous={weeklyActivity.previous.visits}
                                        />
                                    </div>
                                    <div>
                                        <Statistic title="Nuevos Usuarios" value={weeklyActivity.newUsers} />
                                        <WeeklyTrend
                                            current={weeklyActivity.newUsers}
                                            previous={weeklyActivity.previous.newUsers}
                                        />
                                    </div>
                                </Space>
                            </Col>
                            <Col xs={24} md={16}>
                                <Line
                                    data={activitySeries}
                                    xField="date"
                                    yField="value"
                                    colorField="type"
                                    height={280}
                                    smooth
                                    legend={{ color: { position: 'bottom' } }}
                                />
                            </Col>
                        </Row>
                    </Card>
                </Col>

                {/* Segment: latest records */}
                <Col xs={24} lg={12}>
                    <Card
                        title={<Space><UserAddOutlined /> Últimos Usuarios Registrados</Space>}
                        extra={
                            <Button type="link" href={urls.allUsers}>
                                Ver todos <RightOutlined />
                            </Button>
                        }
                    >
                        <Table
                            rowKey="id"
                            size="small"
                            dataSource={latestUsers}
                            columns={LATEST_USER_COLUMNS}
                            pagination={false}
                            locale={{ emptyText: 'No hay usuarios registrados' }}
                        />
                    </Card>
                </Col>
                <Col xs={24} lg={12}>
                    <Card
                        title={<Space><ShopOutlined /> Últimos Proveedores</Space>}
                        extra={
                            <Button type="link" href={urls.allListings}>
                                Ver todos <RightOutlined />
                            </Button>
                        }
                    >
                        <Table
                            rowKey="id"
                            size="small"
                            dataSource={latestListings}
                            columns={LATEST_LISTING_COLUMNS}
                            pagination={false}
                            locale={{ emptyText: 'No hay proveedores registrados' }}
                        />
                    </Card>
                </Col>

                {/* Segment: distribution & shortcuts */}
                <Col xs={24} lg={12}>
                    <Card title={<Space><BarChartOutlined /> Proveedores por Categoría</Space>} style={{ height: '100%' }}>
                        {hasCategoryData ? (
                            <Pie
                                data={listingsByCategory}
                                angleField="count"
                                colorField="name"
                                height={300}
                                innerRadius={0.6}
                                legend={{ color: { position: 'bottom' } }}
                            />
                        ) : (
                            <Empty description="Sin datos de categorías" />
                        )}
                    </Card>
                </Col>
                <Col xs={24} lg={12}>
                    <Card title={<Space><ThunderboltOutlined /> Accesos Rápidos</Space>} style={{ height: '100%' }}>
                        <Row gutter={[16, 16]}>
                            {quickAccessItems.map((item) => (
                                <Col xs={12} key={item.label}>
                                    <Button
                                        href={item.url}
                                        block
                                        size="large"
                                        style={{ height: 80 }}
                                    >
                                        <Space direction="vertical" size={4}>
                                            {item.icon}
                                            {item.label}
                                        </Space>
                                    </Button>
                                </Col>
                            ))}
                        </Row>
                    </Card>
                </Col>
            </Row>
        </PageContainer>
    );
}
