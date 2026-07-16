import React, { useRef, useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { Button, Dropdown, Flex, Input, Modal, Select, Space, Switch, Table, Tag } from 'antd';
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
} from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import useDataTable from '../../../Hooks/useDataTable';

export default function Index({ stations, urls }) {
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
            title: 'Eliminar usuario',
            icon: <ExclamationCircleOutlined />,
            content: `¿Seguro que deseas eliminar a "${user.name}"?`,
            okText: 'Eliminar',
            cancelText: 'Cancelar',
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
        { title: 'ID', dataIndex: 'id', key: 'id', width: 70, sorter: true },
        { title: 'Nombre', dataIndex: 'name', key: 'name', sorter: true, ellipsis: true },
        { title: 'Correo', dataIndex: 'email', key: 'email', sorter: true, ellipsis: true },
        {
            title: 'Rol',
            dataIndex: 'role',
            key: 'role',
            width: 140,
            render: (role) => (role ? <Tag color="green">{role}</Tag> : <Tag>Sin rol</Tag>),
        },
        {
            title: 'Aprobado',
            dataIndex: 'isApproved',
            key: 'isApproved',
            width: 110,
            render: (isApproved, record) => (
                <Switch
                    checked={isApproved}
                    checkedChildren="Sí"
                    unCheckedChildren="No"
                    onChange={() => handleToggleApproval(record)}
                />
            ),
        },
        {
            title: 'Permisos Extra',
            dataIndex: 'directPermissionsCount',
            key: 'directPermissionsCount',
            width: 130,
            render: (count) =>
                count > 0 ? <Tag color="warning">{count} directo(s)</Tag> : <Tag>Ninguno</Tag>,
        },
        {
            title: 'Acciones',
            key: 'actions',
            width: 110,
            render: (_, record) => (
                <Dropdown
                    menu={{
                        items: [
                            {
                                key: 'show',
                                icon: <EyeOutlined />,
                                label: <Link href={`${urls.base}/${record.id}`}>Ver detalle</Link>,
                            },
                            {
                                key: 'edit',
                                icon: <EditOutlined />,
                                label: <Link href={`${urls.base}/${record.id}/edit`}>Editar</Link>,
                            },
                            {
                                key: 'permissions',
                                icon: <KeyOutlined />,
                                label: (
                                    <Link href={`${urls.permissionsBase}/${record.id}/edit`}>
                                        Permisos directos
                                    </Link>
                                ),
                            },
                            {
                                key: 'statistics',
                                icon: <BarChartOutlined />,
                                label: (
                                    <Link href={`${urls.statisticsBase}/${record.id}`}>
                                        Estadísticas
                                    </Link>
                                ),
                            },
                            { type: 'divider' },
                            {
                                key: 'delete',
                                icon: <DeleteOutlined />,
                                label: 'Eliminar',
                                danger: true,
                                disabled: record.isSuperAdmin,
                                onClick: () => handleDelete(record),
                            },
                        ],
                    }}
                >
                    <Button size="small" icon={<MoreOutlined />}>
                        Acciones
                    </Button>
                </Dropdown>
            ),
        },
    ];

    return (
        <PageContainer
            title="Usuarios"
            breadcrumbItems={[{ title: 'Gestión de accesos' }, { title: 'Usuarios' }]}
            extra={
                <Space wrap>
                    <Button icon={<DownloadOutlined />} href={urls.export}>
                        Exportar Excel
                    </Button>
                    <Link href={urls.create}>
                        <Button type="primary" icon={<PlusOutlined />}>
                            Nuevo usuario
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
                    placeholder="Buscar por nombre o correo"
                    style={{ maxWidth: 320 }}
                    value={searchValue}
                    onChange={(event) => handleSearchChange(event.target.value)}
                />
                <Select
                    allowClear
                    showSearch
                    optionFilterProp="label"
                    placeholder="Todas las estaciones"
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
                    showTotal: (totalRows) => `${totalRows} usuarios`,
                }}
                locale={{ emptyText: 'No hay usuarios registrados' }}
            />
        </PageContainer>
    );
}
