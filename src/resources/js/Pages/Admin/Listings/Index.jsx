import React, { useMemo, useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { Button, Dropdown, Flex, Image, Input, Modal, Table, Tag, Typography } from 'antd';
import {
    CheckCircleOutlined,
    ClockCircleOutlined,
    CoffeeOutlined,
    DeleteOutlined,
    EditOutlined,
    ExclamationCircleOutlined,
    MinusCircleOutlined,
    MoreOutlined,
    PlusOutlined,
    SearchOutlined,
} from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';

const { Text } = Typography;

function YesNoTag({ value }) {
    return value === 1 ? <Tag color="blue">Sí</Tag> : <Tag>No</Tag>;
}

export default function Index({ listings, urls }) {
    const [search, setSearch] = useState('');
    const [deleting, setDeleting] = useState(false);
    const [modal, contextHolder] = Modal.useModal();

    const categoryFilters = useMemo(
        () =>
            [...new Set(listings.map((listing) => listing.category))].map((name) => ({
                text: name,
                value: name,
            })),
        [listings],
    );

    const locationFilters = useMemo(
        () =>
            [...new Set(listings.map((listing) => listing.location))].map((name) => ({
                text: name,
                value: name,
            })),
        [listings],
    );

    const filteredListings = useMemo(() => {
        if (!search) return listings;

        const term = search.toLowerCase();

        return listings.filter((listing) => listing.title.toLowerCase().includes(term));
    }, [listings, search]);

    const handleDelete = (listing) => {
        modal.confirm({
            title: 'Eliminar proveedor',
            icon: <ExclamationCircleOutlined />,
            content: `¿Seguro que deseas eliminar "${listing.title}"?`,
            okText: 'Eliminar',
            cancelText: 'Cancelar',
            okButtonProps: { danger: true },
            onOk: () =>
                router.delete(`${urls.base}/${listing.id}`, {
                    preserveScroll: true,
                    onStart: () => setDeleting(true),
                    onFinish: () => setDeleting(false),
                }),
        });
    };

    const columns = [
        {
            title: 'Imagen',
            dataIndex: 'imageUrl',
            key: 'image',
            width: 84,
            render: (url) =>
                url ? (
                    <Image
                        src={url}
                        alt="Imagen"
                        width={56}
                        height={56}
                        style={{ objectFit: 'cover', borderRadius: 8 }}
                        preview={{ mask: 'Ver' }}
                    />
                ) : (
                    '—'
                ),
        },
        {
            title: 'Nombre',
            dataIndex: 'title',
            key: 'title',
            sorter: (a, b) => a.title.localeCompare(b.title),
            render: (title, record) => (
                <div>
                    <div style={{ fontWeight: 500 }}>{title}</div>
                    <Text style={{ fontSize: 12, color: 'rgba(0,0,0,0.45)' }}>
                        {record.status === 1 ? (
                            <>
                                <CheckCircleOutlined style={{ color: '#52c41a', marginRight: 4 }} />
                                Activo
                            </>
                        ) : (
                            <>
                                <MinusCircleOutlined style={{ color: '#bfbfbf', marginRight: 4 }} />
                                Inactivo
                            </>
                        )}
                    </Text>
                </div>
            ),
        },
        {
            title: 'Categoría',
            dataIndex: 'category',
            key: 'category',
            width: 150,
            filters: categoryFilters,
            onFilter: (value, record) => record.category === value,
            render: (value) => <Tag color="blue">{value}</Tag>,
        },
        {
            title: 'Ubicación',
            dataIndex: 'location',
            key: 'location',
            width: 150,
            filters: locationFilters,
            onFilter: (value, record) => record.location === value,
        },
        {
            title: 'Destacado',
            dataIndex: 'isFeatured',
            key: 'isFeatured',
            width: 110,
            filters: [
                { text: 'Sí', value: 1 },
                { text: 'No', value: 0 },
            ],
            onFilter: (value, record) => record.isFeatured === value,
            render: (value) => <YesNoTag value={value} />,
        },
        {
            title: 'Verificado',
            dataIndex: 'isVerified',
            key: 'isVerified',
            width: 110,
            filters: [
                { text: 'Sí', value: 1 },
                { text: 'No', value: 0 },
            ],
            onFilter: (value, record) => record.isVerified === value,
            render: (value) => <YesNoTag value={value} />,
        },
        {
            title: 'Acciones',
            key: 'actions',
            width: 100,
            render: (_, record) => (
                <Dropdown
                    menu={{
                        items: [
                            {
                                key: 'edit',
                                icon: <EditOutlined />,
                                label: <Link href={`${urls.base}/${record.id}/edit`}>Editar</Link>,
                            },
                            {
                                key: 'schedules',
                                icon: <ClockCircleOutlined />,
                                label: (
                                    <Link href={`${urls.schedulesBase}/${record.id}`}>
                                        Gestión de Horarios
                                    </Link>
                                ),
                            },
                            {
                                key: 'amenities',
                                icon: <CoffeeOutlined />,
                                label: (
                                    <Link href={`${urls.base}/${record.id}/amenities`}>
                                        Gestión de Servicios
                                    </Link>
                                ),
                            },
                            { type: 'divider' },
                            {
                                key: 'delete',
                                icon: <DeleteOutlined />,
                                label: 'Eliminar',
                                danger: true,
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
            title="Todos los Proveedores"
            breadcrumbItems={[{ title: 'Proveedores' }, { title: 'Todos los Proveedores' }]}
            extra={
                <Link href={urls.create}>
                    <Button type="primary" icon={<PlusOutlined />}>
                        Nuevo proveedor
                    </Button>
                </Link>
            }
        >
            {contextHolder}

            <Flex style={{ marginBottom: 16 }}>
                <Input
                    allowClear
                    prefix={<SearchOutlined />}
                    placeholder="Buscar por nombre"
                    style={{ maxWidth: 320 }}
                    value={search}
                    onChange={(event) => setSearch(event.target.value)}
                />
            </Flex>

            <Table
                rowKey="id"
                columns={columns}
                dataSource={filteredListings}
                loading={deleting}
                scroll={{ x: 900 }}
                pagination={{
                    position: ['bottomRight'],
                    pageSize: 10,
                    showSizeChanger: true,
                    showTotal: (total) => `${total} proveedores`,
                }}
                locale={{ emptyText: 'No hay proveedores registrados' }}
            />
        </PageContainer>
    );
}
