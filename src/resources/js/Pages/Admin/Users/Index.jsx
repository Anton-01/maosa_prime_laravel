import React, { useRef, useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { Button, Dropdown, Flex, Input, Modal, Select, Space, Switch, Table, Tag, Typography } from 'antd';
import {
    BarChartOutlined,
    DeleteOutlined,
    DownloadOutlined,
    EditOutlined,
    ExclamationCircleOutlined,
    EyeOutlined,
    KeyOutlined,
    MoreOutlined,
    PlusOutlined,
    SearchOutlined,
    UploadOutlined,
} from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import useDataTable from '../../../Hooks/useDataTable';
import useTranslation from '../../../Hooks/useTranslation';

const { Text } = Typography;

export default function Index({ stations, urls }) {
    const { t } = useTranslation();
    const [modal, contextHolder] = Modal.useModal();
    const [searchValue, setSearchValue] = useState('');
    const [stationFilter, setStationFilter] = useState(null);
    const searchTimeoutRef = useRef(null);

    const {
        data,
        total,
        loading,
        tableParams,
        handleTableChange,
        setSearch,
        setFilter,
        refresh,
    } = useDataTable(urls.data);

    const handleSearchChange = (value) => {
        setSearchValue(value);

        // Debounce so we don't hit the backend on every keystroke.
        clearTimeout(searchTimeoutRef.current);
        searchTimeoutRef.current = setTimeout(() => setSearch(value), 400);
    };

    const handleStationChange = (value) => {
        setStationFilter(value);
        setFilter('estacion_id', value ?? null);
    };

    const handleToggleApproval = (user) => {
        router.post(`${urls.base}/${user.id}/toggle-approval`, {}, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => refresh(),
        });
    };

    const handleDelete = (user) => {
        modal.confirm({
            title: t('users.delete_title'),
            icon: <ExclamationCircleOutlined />,
            content: t('users.delete_confirm', { name: user.name }),
            okText: t('common.delete'),
            cancelText: t('common.cancel'),
            okButtonProps: { danger: true },
            onOk: () =>
                router.delete(`${urls.base}/${user.id}`, {
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => refresh(),
                }),
        });
    };

    const columns = [
        {
            title: t('users.name'),
            dataIndex: 'name',
            key: 'name',
            sorter: true,
            render: (name, record) => (
                <div>
                    <div style={{ fontWeight: 500 }}>{name}</div>
                    <Text style={{ fontSize: 12, color: 'rgba(0,0,0,0.45)' }}>ID: {record.id}</Text>
                </div>
            ),
        },
        {
            title: t('users.email'),
            dataIndex: 'email',
            key: 'email',
            sorter: true,
            render: (email, record) => (
                <div>
                    <div>{email}</div>
                    <div style={{ marginTop: 4 }}>
                        {record.role ? (
                            <Tag color="green" style={{ marginInlineEnd: 0 }}>
                                {record.role}
                            </Tag>
                        ) : (
                            <Tag style={{ marginInlineEnd: 0 }}>{t('users.no_role')}</Tag>
                        )}
                    </div>
                </div>
            ),
        },
        {
            title: t('users.approved'),
            dataIndex: 'isApproved',
            key: 'isApproved',
            width: 110,
            render: (isApproved, record) => (
                <Switch
                    checked={isApproved}
                    checkedChildren={t('common.yes')}
                    unCheckedChildren={t('common.no')}
                    onChange={() => handleToggleApproval(record)}
                />
            ),
        },
        {
            title: t('common.actions'),
            key: 'actions',
            width: 110,
            render: (_, record) => (
                <Dropdown
                    menu={{
                        items: [
                            {
                                key: 'show',
                                icon: <EyeOutlined />,
                                label: (
                                    <Link href={`${urls.base}/${record.id}`}>
                                        {t('users.view_detail')}
                                    </Link>
                                ),
                            },
                            {
                                key: 'edit',
                                icon: <EditOutlined />,
                                label: (
                                    <Link href={`${urls.base}/${record.id}/edit`}>
                                        {t('common.edit')}
                                    </Link>
                                ),
                            },
                            {
                                key: 'permissions',
                                icon: <KeyOutlined />,
                                label: (
                                    <Link href={`${urls.permissionsBase}/${record.id}/edit`}>
                                        {t('users.direct_permissions')}
                                    </Link>
                                ),
                            },
                            {
                                key: 'statistics',
                                icon: <BarChartOutlined />,
                                label: (
                                    <Link href={`${urls.statisticsBase}/${record.id}`}>
                                        {t('nav.statistics')}
                                    </Link>
                                ),
                            },
                            { type: 'divider' },
                            {
                                key: 'delete',
                                icon: <DeleteOutlined />,
                                label: t('common.delete'),
                                danger: true,
                                disabled: record.isSuperAdmin,
                                onClick: () => handleDelete(record),
                            },
                        ],
                    }}
                >
                    <Button size="small" icon={<MoreOutlined />}>
                        {t('common.actions')}
                    </Button>
                </Dropdown>
            ),
        },
    ];

    return (
        <PageContainer
            title={t('users.title')}
            breadcrumbItems={[
                { title: t('users.breadcrumb_access') },
                { title: t('users.breadcrumb_users') },
            ]}
            extra={
                <Space wrap>
                    <Button icon={<DownloadOutlined />} href={urls.export}>
                        {t('users.export_excel')}
                    </Button>
                    <Link href={urls.import}>
                        <Button icon={<UploadOutlined />}>{t('users.import')}</Button>
                    </Link>
                    <Link href={urls.create}>
                        <Button type="primary" icon={<PlusOutlined />}>
                            {t('users.create_title')}
                        </Button>
                    </Link>
                </Space>
            }
        >
            {contextHolder}

            <Flex gap="small" wrap style={{ marginBottom: 16 }}>
                <Input
                    allowClear
                    prefix={<SearchOutlined />}
                    placeholder={t('users.search_placeholder')}
                    style={{ maxWidth: 320 }}
                    value={searchValue}
                    onChange={(event) => handleSearchChange(event.target.value)}
                />
                <Select
                    allowClear
                    showSearch
                    optionFilterProp="label"
                    placeholder={t('users.all_stations')}
                    style={{ minWidth: 240 }}
                    options={stations}
                    value={stationFilter}
                    onChange={handleStationChange}
                />
            </Flex>

            <Table
                rowKey="id"
                columns={columns}
                dataSource={data}
                loading={loading}
                onChange={handleTableChange}
                scroll={{ x: 900 }}
                pagination={{
                    position: ['bottomRight'],
                    current: tableParams.page,
                    pageSize: tableParams.perPage,
                    total,
                    showSizeChanger: true,
                    showTotal: (totalRows) => t('users.total', { count: totalRows }),
                }}
                locale={{ emptyText: t('users.empty') }}
            />
        </PageContainer>
    );
}
