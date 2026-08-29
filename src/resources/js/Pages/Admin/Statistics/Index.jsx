import React, { useRef, useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { Button, Card, Col, DatePicker, Flex, Input, Row, Space, Statistic, Table, Tabs, Tag } from 'antd';
import {
    CompassOutlined,
    DesktopOutlined,
    DownloadOutlined,
    EyeOutlined,
    FileTextOutlined,
    GlobalOutlined,
    InteractionOutlined,
    SearchOutlined,
    TeamOutlined,
    UserOutlined,
} from '@ant-design/icons';
import dayjs from 'dayjs';
import PageContainer from '../../../Components/PageContainer';
import useDataTable from '../../../Hooks/useDataTable';
import useTranslation from '../../../Hooks/useTranslation';

const { RangePicker } = DatePicker;

/** Generic two-column breakdown table (label + total) with export. */
function BreakdownTable({ dataUrl, exportUrl, labelTitle, dateRange }) {
    const { t } = useTranslation();
    const [searchValue, setSearchValue] = useState('');
    const searchTimeoutRef = useRef(null);

    const { data, total, loading, tableParams, handleTableChange, setSearch } = useDataTable(
        dataUrl,
        { extraParams: dateRange },
    );

    const handleSearchChange = (value) => {
        setSearchValue(value);
        clearTimeout(searchTimeoutRef.current);
        searchTimeoutRef.current = setTimeout(() => setSearch(value), 400);
    };

    const exportHref = `${exportUrl}?date_from=${dateRange.date_from}&date_to=${dateRange.date_to}`;

    return (
        <>
            <Flex justify="space-between" wrap gap="small" style={{ marginBottom: 16 }}>
                <Input
                    allowClear
                    prefix={<SearchOutlined />}
                    placeholder={t('statistics.search_by', { label: labelTitle.toLowerCase() })}
                    style={{ maxWidth: 300 }}
                    value={searchValue}
                    onChange={(event) => handleSearchChange(event.target.value)}
                />
                <Button icon={<DownloadOutlined />} href={exportHref}>
                    {t('common.export_excel')}
                </Button>
            </Flex>

            <Table
                rowKey="label"
                dataSource={data}
                loading={loading}
                onChange={handleTableChange}
                columns={[
                    { title: labelTitle, dataIndex: 'label', key: 'label', sorter: true, ellipsis: true },
                    { title: 'Total', dataIndex: 'total', key: 'total', sorter: true, width: 140 },
                ]}
                pagination={{
                    position: ['bottomRight'],
                    current: tableParams.page,
                    pageSize: tableParams.perPage,
                    total,
                    showSizeChanger: true,
                    showTotal: (totalRows) => t('statistics.total_records', { count: totalRows }),
                }}
                locale={{ emptyText: t('statistics.empty_period') }}
            />
        </>
    );
}

/** Server-side table of the most active users in the range. */
function ActiveUsersTable({ dataUrl, exportUrl, userBaseUrl, dateRange }) {
    const { t } = useTranslation();
    const [searchValue, setSearchValue] = useState('');
    const searchTimeoutRef = useRef(null);

    const { data, total, loading, tableParams, handleTableChange, setSearch } = useDataTable(
        dataUrl,
        { extraParams: dateRange },
    );

    const handleSearchChange = (value) => {
        setSearchValue(value);
        clearTimeout(searchTimeoutRef.current);
        searchTimeoutRef.current = setTimeout(() => setSearch(value), 400);
    };

    const exportHref = `${exportUrl}?date_from=${dateRange.date_from}&date_to=${dateRange.date_to}`;

    const columns = [
        {
            title: t('users.name'),
            dataIndex: 'name',
            key: 'name',
            sorter: true,
            ellipsis: true,
            render: (name, record) => <Link href={`${userBaseUrl}/${record.id}`}>{name}</Link>,
        },
        { title: t('users.email'), dataIndex: 'email', key: 'email', sorter: true, ellipsis: true },
        {
            title: t('statistics.visits'),
            dataIndex: 'pageVisits',
            key: 'pageVisits',
            sorter: true,
            width: 110,
        },
        {
            title: t('statistics.sessions'),
            dataIndex: 'sessions',
            key: 'sessions',
            sorter: true,
            width: 110,
        },
        {
            title: t('statistics.last_session'),
            dataIndex: 'lastSessionAt',
            key: 'lastSessionAt',
            sorter: true,
            width: 150,
            render: (value) => value ?? '—',
        },
        {
            title: t('statistics.location'),
            dataIndex: 'lastSessionLocation',
            key: 'lastSessionLocation',
            ellipsis: true,
            render: (value) => value ?? '—',
        },
        {
            // SR-016: columna de monitoreo de seguridad. El backend entrega las
            // IPs ya ordenadas por frecuencia; aquí solo se renderizan.
            title: t('statistics.top_ips'),
            dataIndex: 'topSessionIps',
            key: 'topSessionIps',
            width: 240,
            render: (ips) => (ips?.length
                ? (
                    <Space size={[4, 4]} wrap>
                        {ips.map((ip) => <Tag key={ip}>{ip}</Tag>)}
                    </Space>
                )
                : '—'),
        },
    ];

    return (
        <>
            <Flex justify="space-between" wrap gap="small" style={{ marginBottom: 16 }}>
                <Input
                    allowClear
                    prefix={<SearchOutlined />}
                    placeholder={t('users.search_placeholder')}
                    style={{ maxWidth: 300 }}
                    value={searchValue}
                    onChange={(event) => handleSearchChange(event.target.value)}
                />
                <Button icon={<DownloadOutlined />} href={exportHref}>
                    {t('common.export_excel')}
                </Button>
            </Flex>

            <Table
                rowKey="id"
                dataSource={data}
                loading={loading}
                onChange={handleTableChange}
                columns={columns}
                scroll={{ x: 1140 }}
                pagination={{
                    position: ['bottomRight'],
                    current: tableParams.page,
                    pageSize: tableParams.perPage,
                    total,
                    showSizeChanger: true,
                    showTotal: (totalRows) => t('users.total', { count: totalRows }),
                }}
                locale={{ emptyText: t('statistics.no_activity_period') }}
            />
        </>
    );
}

export default function Index({ dateFrom, dateTo, metrics, urls }) {
    const { t } = useTranslation();
    const [range, setRange] = useState([dayjs(dateFrom), dayjs(dateTo)]);

    const dateRange = {
        date_from: range[0].format('YYYY-MM-DD'),
        date_to: range[1].format('YYYY-MM-DD'),
    };

    const handleRangeChange = (value) => {
        if (!value || !value[0] || !value[1]) return;

        setRange(value);

        // Refresh the KPI cards; the tables react on their own via extraParams.
        router.get(urls.base, {
            date_from: value[0].format('YYYY-MM-DD'),
            date_to: value[1].format('YYYY-MM-DD'),
        }, {
            preserveState: true,
            preserveScroll: true,
            only: ['metrics', 'dateFrom', 'dateTo'],
        });
    };

    const kpis = [
        { title: t('statistics.sessions'), value: metrics.totalSessions, icon: <InteractionOutlined /> },
        { title: t('statistics.page_views'), value: metrics.totalPageViews, icon: <EyeOutlined /> },
        { title: t('statistics.activities'), value: metrics.totalActivities, icon: <FileTextOutlined /> },
        { title: t('statistics.unique_users'), value: metrics.uniqueUsers, icon: <TeamOutlined /> },
    ];

    const tabItems = [
        {
            key: 'active-users',
            label: t('statistics.active_users'),
            icon: <UserOutlined />,
            children: (
                <ActiveUsersTable
                    dataUrl={`${urls.dataBase}/active-users`}
                    exportUrl={`${urls.exportBase}/active-users`}
                    userBaseUrl={urls.userBase}
                    dateRange={dateRange}
                />
            ),
        },
        {
            key: 'devices',
            label: t('statistics.devices'),
            icon: <DesktopOutlined />,
            children: (
                <BreakdownTable
                    dataUrl={`${urls.dataBase}/devices`}
                    exportUrl={`${urls.exportBase}/devices`}
                    labelTitle={t('statistics.device')}
                    dateRange={dateRange}
                />
            ),
        },
        {
            key: 'browsers',
            label: t('statistics.browsers'),
            icon: <CompassOutlined />,
            children: (
                <BreakdownTable
                    dataUrl={`${urls.dataBase}/browsers`}
                    exportUrl={`${urls.exportBase}/browsers`}
                    labelTitle={t('statistics.browser')}
                    dateRange={dateRange}
                />
            ),
        },
        {
            key: 'countries',
            label: t('statistics.countries'),
            icon: <GlobalOutlined />,
            children: (
                <BreakdownTable
                    dataUrl={`${urls.dataBase}/countries`}
                    exportUrl={`${urls.exportBase}/countries`}
                    labelTitle={t('statistics.country')}
                    dateRange={dateRange}
                />
            ),
        },
        {
            key: 'pages',
            label: t('statistics.pages'),
            icon: <FileTextOutlined />,
            children: (
                <BreakdownTable
                    dataUrl={`${urls.dataBase}/pages`}
                    exportUrl={`${urls.exportBase}/pages`}
                    labelTitle={t('statistics.page')}
                    dateRange={dateRange}
                />
            ),
        },
    ];

    return (
        <PageContainer
            title={t('statistics.title')}
            breadcrumbItems={[
                { title: t('statistics.breadcrumb') },
                { title: t('statistics.overview') },
            ]}
            extra={
                <Space>
                    <RangePicker
                        value={range}
                        onChange={handleRangeChange}
                        allowClear={false}
                        format="DD/MM/YYYY"
                    />
                </Space>
            }
            wrapInCard={false}
        >
            <Row gutter={[16, 16]} style={{ marginBottom: 16 }}>
                {kpis.map((kpi) => (
                    <Col xs={12} lg={6} key={kpi.title}>
                        <Card>
                            <Statistic title={kpi.title} value={kpi.value} prefix={kpi.icon} />
                        </Card>
                    </Col>
                ))}
            </Row>

            <Card>
                <Tabs items={tabItems} />
            </Card>
        </PageContainer>
    );
}
