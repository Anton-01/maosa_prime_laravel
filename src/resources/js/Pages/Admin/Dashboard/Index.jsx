import React from 'react';
import { Link } from '@inertiajs/react';
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
import useTranslation from '../../../Hooks/useTranslation';

/** Weekly trend vs the previous week, rendered next to the statistic. */
function WeeklyTrend({ current, previous, t }) {
    if (previous === 0 && current === 0) return null;

    const isUp = current >= previous;
    const percentage =
        previous === 0 ? 100 : Math.abs(Math.round(((current - previous) / previous) * 100));

    return (
        <Tag
            color={isUp ? 'success' : 'error'}
            icon={isUp ? <ArrowUpOutlined /> : <ArrowDownOutlined />}
        >
            {t('dashboard.trend_vs_last_week', { percent: percentage })}
        </Tag>
    );
}

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
    const { t } = useTranslation();
    const { token } = theme.useToken();

    const latestUserColumns = [
        { title: t('users.name'), dataIndex: 'name', key: 'name', ellipsis: true },
        { title: t('users.email'), dataIndex: 'email', key: 'email', ellipsis: true },
        {
            title: t('dashboard.created_at'),
            dataIndex: 'createdAt',
            key: 'createdAt',
            width: 110,
            render: (value) => <Tag>{value}</Tag>,
        },
    ];

    const latestListingColumns = [
        { title: t('users.name'), dataIndex: 'title', key: 'title', ellipsis: true },
        {
            title: t('dashboard.category'),
            dataIndex: 'category',
            key: 'category',
            render: (value) => <Tag color="blue">{value}</Tag>,
        },
        { title: t('dashboard.location'), dataIndex: 'location', key: 'location', ellipsis: true },
    ];

    const verifiedPercentage =
        providerStats.total > 0
            ? Math.round((providerStats.verified / providerStats.total) * 100)
            : 0;
    const activePercentage =
        providerStats.total > 0
            ? Math.round((providerStats.active / providerStats.total) * 100)
            : 0;

    const activitySeries = activityChart.flatMap((day) => [
        { date: day.date, value: day.visits, type: t('dashboard.visits') },
        { date: day.date, value: day.sessions, type: t('dashboard.sessions') },
    ]);

    const hasCategoryData = listingsByCategory.some((category) => category.count > 0);

    const quickAccessItems = [
        { label: t('dashboard.new_supplier'), icon: <PlusCircleOutlined />, url: urls.createListing },
        { label: t('dashboard.new_user'), icon: <UserAddOutlined />, url: urls.createUser },
        { label: t('dashboard.categories'), icon: <TagsOutlined />, url: urls.categories },
        { label: t('nav.statistics'), icon: <BarChartOutlined />, url: urls.statistics },
    ];

    return (
        <PageContainer
            title={t('dashboard.title')}
            breadcrumbItems={[{ title: t('common.control_panel') }, { title: t('dashboard.breadcrumb') }]}
            wrapInCard={false}
        >
            {/* Segment: providers */}
            <Row gutter={[16, 16]}>
                <Col xs={24} lg={12}>
                    <Card
                        title={
                            <Space>
                                <ShopOutlined /> {t('dashboard.suppliers')}
                            </Space>
                        }
                        style={{ height: '100%' }}
                    >
                        <Row gutter={[16, 16]}>
                            <Col xs={8}>
                                <Statistic title={t('dashboard.total')} value={providerStats.total} prefix={<ShopOutlined />} />
                            </Col>
                            <Col xs={8}>
                                <Statistic
                                    title={t('dashboard.verified')}
                                    value={providerStats.verified}
                                    prefix={<SafetyCertificateOutlined />}
                                    valueStyle={{ color: token.colorSuccess }}
                                />
                            </Col>
                            <Col xs={8}>
                                <Statistic
                                    title={t('dashboard.active')}
                                    value={providerStats.active}
                                    prefix={<CheckCircleOutlined />}
                                    valueStyle={{ color: token.colorPrimary }}
                                />
                            </Col>
                            <Col xs={12}>
                                <Progress percent={verifiedPercentage} size="small" status="active" />
                                <small>{t('dashboard.verified_suppliers')}</small>
                            </Col>
                            <Col xs={12}>
                                <Progress
                                    percent={activePercentage}
                                    size="small"
                                    status="active"
                                    strokeColor={token.colorSuccess}
                                />
                                <small>{t('dashboard.active_suppliers')}</small>
                            </Col>
                        </Row>
                    </Card>
                </Col>

                {/* Segment: users */}
                <Col xs={24} lg={12}>
                    <Card
                        title={
                            <Space>
                                <TeamOutlined /> {t('dashboard.users')}
                            </Space>
                        }
                        style={{ height: '100%' }}
                    >
                        <Row gutter={[16, 16]}>
                            <Col xs={12} sm={6}>
                                <Statistic title={t('dashboard.total')} value={userStats.total} prefix={<TeamOutlined />} />
                            </Col>
                            <Col xs={12} sm={6}>
                                <Statistic
                                    title={t('dashboard.admins')}
                                    value={userStats.admins}
                                    prefix={<UserOutlined />}
                                    valueStyle={{ color: token.colorPrimary }}
                                />
                            </Col>
                            <Col xs={12} sm={6}>
                                <Statistic
                                    title={t('dashboard.price_access')}
                                    value={userStats.withPriceAccess}
                                    prefix={<EyeOutlined />}
                                    valueStyle={{ color: token.colorWarning }}
                                />
                            </Col>
                            <Col xs={12} sm={6}>
                                <Statistic
                                    title={t('dashboard.new_last_week')}
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
                    <Card
                        title={
                            <Space>
                                <AppstoreOutlined /> {t('dashboard.catalog_content')}
                            </Space>
                        }
                    >
                        <Row gutter={[16, 16]}>
                            <Col xs={12} sm={6}>
                                <Statistic title={t('dashboard.categories')} value={catalogStats.categories} prefix={<TagsOutlined />} />
                            </Col>
                            <Col xs={12} sm={6}>
                                <Statistic
                                    title={t('dashboard.locations')}
                                    value={catalogStats.locations}
                                    prefix={<EnvironmentOutlined />}
                                />
                            </Col>
                            <Col xs={12} sm={6}>
                                <Statistic title={t('dashboard.amenities')} value={catalogStats.amenities} prefix={<CoffeeOutlined />} />
                            </Col>
                            <Col xs={12} sm={6}>
                                <Statistic
                                    title={t('dashboard.blog_posts')}
                                    value={catalogStats.blogPosts}
                                    prefix={<FileTextOutlined />}
                                />
                            </Col>
                        </Row>
                    </Card>
                </Col>

                {/* Segment: weekly activity */}
                <Col xs={24}>
                    <Card
                        title={
                            <Space>
                                <LineChartOutlined /> {t('dashboard.weekly_activity')}
                            </Space>
                        }
                    >
                        <Row gutter={[24, 24]}>
                            <Col xs={24} md={8}>
                                <Space direction="vertical" size="large" style={{ display: 'flex' }}>
                                    <div>
                                        <Statistic title={t('dashboard.sessions')} value={weeklyActivity.sessions} />
                                        <WeeklyTrend
                                            current={weeklyActivity.sessions}
                                            previous={weeklyActivity.previous.sessions}
                                            t={t}
                                        />
                                    </div>
                                    <div>
                                        <Statistic title={t('dashboard.visits')} value={weeklyActivity.visits} />
                                        <WeeklyTrend
                                            current={weeklyActivity.visits}
                                            previous={weeklyActivity.previous.visits}
                                            t={t}
                                        />
                                    </div>
                                    <div>
                                        <Statistic title={t('dashboard.new_users')} value={weeklyActivity.newUsers} />
                                        <WeeklyTrend
                                            current={weeklyActivity.newUsers}
                                            previous={weeklyActivity.previous.newUsers}
                                            t={t}
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
                        title={
                            <Space>
                                <UserAddOutlined /> {t('dashboard.latest_users')}
                            </Space>
                        }
                        extra={
                            <Link href={urls.allUsers}>
                                <Button type="link">
                                    {t('dashboard.view_all')} <RightOutlined />
                                </Button>
                            </Link>
                        }
                    >
                        <Table
                            rowKey="id"
                            size="small"
                            dataSource={latestUsers}
                            columns={latestUserColumns}
                            pagination={false}
                            locale={{ emptyText: t('dashboard.no_users') }}
                        />
                    </Card>
                </Col>
                <Col xs={24} lg={12}>
                    <Card
                        title={
                            <Space>
                                <ShopOutlined /> {t('dashboard.latest_listings')}
                            </Space>
                        }
                        extra={
                            <Link href={urls.allListings}>
                                <Button type="link">
                                    {t('dashboard.view_all')} <RightOutlined />
                                </Button>
                            </Link>
                        }
                    >
                        <Table
                            rowKey="id"
                            size="small"
                            dataSource={latestListings}
                            columns={latestListingColumns}
                            pagination={false}
                            locale={{ emptyText: t('dashboard.no_listings') }}
                        />
                    </Card>
                </Col>

                {/* Segment: distribution & shortcuts */}
                <Col xs={24} lg={12}>
                    <Card
                        title={
                            <Space>
                                <BarChartOutlined /> {t('dashboard.by_category')}
                            </Space>
                        }
                        style={{ height: '100%' }}
                    >
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
                            <Empty description={t('dashboard.no_category_data')} />
                        )}
                    </Card>
                </Col>
                <Col xs={24} lg={12}>
                    <Card
                        title={
                            <Space>
                                <ThunderboltOutlined /> {t('dashboard.quick_access')}
                            </Space>
                        }
                        style={{ height: '100%' }}
                    >
                        <Row gutter={[16, 16]}>
                            {quickAccessItems.map((item) => (
                                <Col xs={12} key={item.label}>
                                    <Link href={item.url}>
                                        <Button block size="large" style={{ height: 80 }}>
                                            <Space direction="vertical" size={4}>
                                                {item.icon}
                                                {item.label}
                                            </Space>
                                        </Button>
                                    </Link>
                                </Col>
                            ))}
                        </Row>
                    </Card>
                </Col>
            </Row>
        </PageContainer>
    );
}
