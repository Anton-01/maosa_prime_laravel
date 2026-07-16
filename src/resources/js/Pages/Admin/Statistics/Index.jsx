import React, { useRef, useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { Button, Card, Col, DatePicker, Flex, Input, Row, Space, Statistic, Table, Tabs } from 'antd';
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

const { RangePicker } = DatePicker;

/** Generic two-column breakdown table (label + total) with export. */
function BreakdownTable({ dataUrl, exportUrl, labelTitle, dateRange }) {
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
                    placeholder={`Buscar por ${labelTitle.toLowerCase()}`}
                    style={{ maxWidth: 300 }}
                    value={searchValue}
                    onChange={(event) => handleSearchChange(event.target.value)}
                />
                <Button icon={<DownloadOutlined />} href={exportHref}>
                    Exportar Excel
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
                    showTotal: (totalRows) => `${totalRows} registros`,
                }}
                locale={{ emptyText: 'Sin datos en el periodo seleccionado' }}
            />
        </>
    );
}

/** Server-side table of the most active users in the range. */
function ActiveUsersTable({ dataUrl, exportUrl, userBaseUrl, dateRange }) {
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
            title: 'Nombre',
            dataIndex: 'name',
            key: 'name',
            sorter: true,
            ellipsis: true,
            render: (name, record) => <Link href={`${userBaseUrl}/${record.id}`}>{name}</Link>,
        },
        { title: 'Correo', dataIndex: 'email', key: 'email', sorter: true, ellipsis: true },
        { title: 'Visitas', dataIndex: 'pageVisits', key: 'pageVisits', sorter: true, width: 110 },
        { title: 'Sesiones', dataIndex: 'sessions', key: 'sessions', sorter: true, width: 110 },
        {
            title: 'Última sesión',
            dataIndex: 'lastSessionAt',
            key: 'lastSessionAt',
            sorter: true,
            width: 150,
            render: (value) => value ?? '—',
        },
        {
            title: 'Ubicación',
            dataIndex: 'lastSessionLocation',
            key: 'lastSessionLocation',
            ellipsis: true,
            render: (value) => value ?? '—',
        },
    ];

    return (
        <>
            <Flex justify="space-between" wrap gap="small" style={{ marginBottom: 16 }}>
                <Input
                    allowClear
                    prefix={<SearchOutlined />}
                    placeholder="Buscar por nombre o correo"
                    style={{ maxWidth: 300 }}
                    value={searchValue}
                    onChange={(event) => handleSearchChange(event.target.value)}
                />
                <Button icon={<DownloadOutlined />} href={exportHref}>
                    Exportar Excel
                </Button>
            </Flex>

            <Table
                rowKey="id"
                dataSource={data}
                loading={loading}
                onChange={handleTableChange}
                columns={columns}
                scroll={{ x: 900 }}
                pagination={{
                    position: ['bottomRight'],
                    current: tableParams.page,
                    pageSize: tableParams.perPage,
                    total,
                    showSizeChanger: true,
                    showTotal: (totalRows) => `${totalRows} usuarios`,
                }}
                locale={{ emptyText: 'Sin actividad en el periodo seleccionado' }}
            />
        </>
    );
}

export default function Index({ dateFrom, dateTo, metrics, urls }) {
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
        { title: 'Sesiones', value: metrics.totalSessions, icon: <InteractionOutlined /> },
        { title: 'Visitas de página', value: metrics.totalPageViews, icon: <EyeOutlined /> },
        { title: 'Actividades', value: metrics.totalActivities, icon: <FileTextOutlined /> },
        { title: 'Usuarios únicos', value: metrics.uniqueUsers, icon: <TeamOutlined /> },
    ];

    const tabItems = [
        {
            key: 'active-users',
            label: 'Usuarios más activos',
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
            label: 'Dispositivos',
            icon: <DesktopOutlined />,
            children: (
                <BreakdownTable
                    dataUrl={`${urls.dataBase}/devices`}
                    exportUrl={`${urls.exportBase}/devices`}
                    labelTitle="Dispositivo"
                    dateRange={dateRange}
                />
            ),
        },
        {
            key: 'browsers',
            label: 'Navegadores',
            icon: <CompassOutlined />,
            children: (
                <BreakdownTable
                    dataUrl={`${urls.dataBase}/browsers`}
                    exportUrl={`${urls.exportBase}/browsers`}
                    labelTitle="Navegador"
                    dateRange={dateRange}
                />
            ),
        },
        {
            key: 'countries',
            label: 'Países',
            icon: <GlobalOutlined />,
            children: (
                <BreakdownTable
                    dataUrl={`${urls.dataBase}/countries`}
                    exportUrl={`${urls.exportBase}/countries`}
                    labelTitle="País"
                    dateRange={dateRange}
                />
            ),
        },
        {
            key: 'pages',
            label: 'Páginas',
            icon: <FileTextOutlined />,
            children: (
                <BreakdownTable
                    dataUrl={`${urls.dataBase}/pages`}
                    exportUrl={`${urls.exportBase}/pages`}
                    labelTitle="Página (URL)"
                    dateRange={dateRange}
                />
            ),
        },
    ];

    return (
        <PageContainer
            title="Estadísticas — Panel General"
            breadcrumbItems={[{ title: 'Estadísticas' }, { title: 'Panel General' }]}
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
