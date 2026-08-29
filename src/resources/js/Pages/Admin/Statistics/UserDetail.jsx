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
import useTranslation from '../../../Hooks/useTranslation';

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
    const { t } = useTranslation();
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
        { title: t('statistics.sessions'), value: metrics.totalSessions, icon: <InteractionOutlined /> },
        { title: t('statistics.page_views'), value: metrics.totalPageViews, icon: <EyeOutlined /> },
        { title: t('statistics.activities'), value: metrics.totalActivities, icon: <FileTextOutlined /> },
        {
            title: t('statistics.avg_session'),
            value: metrics.avgSessionDurationMinutes ?? '—',
            suffix: metrics.avgSessionDurationMinutes ? 'min' : undefined,
            icon: <ClockCircleOutlined />,
        },
    ];

    return (
        <PageContainer
            title={t('statistics.user_title', { name: user.name })}
            breadcrumbItems={[
                { title: t('statistics.breadcrumb'), href: urls.base },
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
                        <Button icon={<HistoryOutlined />}>{t('statistics.sessions')}</Button>
                    </Link>
                    <Link href={`${urls.activities}?${dateQuery}`}>
                        <Button icon={<FileTextOutlined />}>{t('statistics.activities')}</Button>
                    </Link>
                    <Link href={urls.base}>
                        <Button icon={<ArrowLeftOutlined />}>{t('statistics.overview')}</Button>
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
                    <Card title={t('statistics.visits_by_day')} style={{ height: '100%' }}>
                        {activityByDay.length > 0 ? (
                            <Line
                                data={activityByDay}
                                xField="date"
                                yField="visits"
                                height={260}
                                smooth
                            />
                        ) : (
                            <Empty description={t('statistics.no_visits_period')} />
                        )}
                    </Card>
                </Col>
                <Col xs={24} lg={10}>
                    <Card title={t('statistics.activity_types')} style={{ height: '100%' }}>
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
                            <Empty description={t('statistics.no_activities_period')} />
                        )}
                    </Card>
                </Col>

                <Col xs={24} lg={12}>
                    <Card title={t('statistics.top_pages')}>
                        <Table
                            rowKey={(record) => record.url}
                            size="small"
                            dataSource={topPages}
                            pagination={false}
                            columns={[
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
                                    title: t('statistics.visits'),
                                    dataIndex: 'visits',
                                    key: 'visits',
                                    width: 100,
                                },
                            ]}
                            locale={{ emptyText: t('statistics.no_visits') }}
                        />
                    </Card>
                </Col>

                <Col xs={24} lg={12}>
                    <Card title={t('statistics.recent_sessions')}>
                        <Table
                            rowKey="id"
                            size="small"
                            dataSource={recentSessions}
                            pagination={false}
                            columns={[
                                {
                                    title: t('statistics.started_at'),
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
                                    title: t('statistics.duration'),
                                    dataIndex: 'durationMinutes',
                                    key: 'durationMinutes',
                                    width: 100,
                                    render: (value, record) =>
                                        record.isActive ? (
                                            <Tag color="success">{t('statistics.active')}</Tag>
                                        ) : (
                                            (value != null ? `${value} min` : '—')
                                        ),
                                },
                                {
                                    title: t('statistics.device'),
                                    dataIndex: 'deviceType',
                                    key: 'deviceType',
                                    width: 110,
                                    render: (value) => value ?? '—',
                                },
                                {
                                    title: t('statistics.location'),
                                    dataIndex: 'location',
                                    key: 'location',
                                    ellipsis: true,
                                    render: (value) => value ?? '—',
                                },
                            ]}
                            locale={{ emptyText: t('statistics.no_sessions') }}
                        />
                    </Card>
                </Col>

                <Col xs={24}>
                    <Card title={t('statistics.nav_flows')}>
                        <Table
                            rowKey={(record) => `${record.fromUrl}-${record.toUrl}`}
                            size="small"
                            dataSource={navigationFlows}
                            pagination={false}
                            columns={[
                                {
                                    title: t('statistics.from'),
                                    dataIndex: 'fromUrl',
                                    key: 'fromUrl',
                                    ellipsis: true,
                                },
                                {
                                    title: t('statistics.to'),
                                    dataIndex: 'toUrl',
                                    key: 'toUrl',
                                    ellipsis: true,
                                },
                                { title: t('statistics.times'), dataIndex: 'count', key: 'count', width: 100 },
                            ]}
                            locale={{ emptyText: t('statistics.no_flows') }}
                        />
                    </Card>
                </Col>
            </Row>
        </PageContainer>
    );
}
