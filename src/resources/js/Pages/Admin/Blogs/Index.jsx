import React, { useMemo, useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { Button, Flex, Image, Input, Popconfirm, Table, Tag, Tooltip } from 'antd';
import { DeleteOutlined, EditOutlined, PlusOutlined, SearchOutlined } from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';

export default function Index({ blogs, urls }) {
    const [search, setSearch] = useState('');
    const [deleting, setDeleting] = useState(false);

    const filteredBlogs = useMemo(() => {
        if (!search) return blogs;

        const term = search.toLowerCase();

        return blogs.filter((blog) => blog.title.toLowerCase().includes(term));
    }, [blogs, search]);

    const handleDelete = (blog) => {
        router.delete(`${urls.base}/${blog.id}`, {
            preserveScroll: true,
            onStart: () => setDeleting(true),
            onFinish: () => setDeleting(false),
        });
    };

    const columns = [
        {
            title: 'Imagen',
            dataIndex: 'imageUrl',
            key: 'image',
            width: 90,
            render: (url) =>
                url ? <Image src={url} alt="Imagen" width={60} style={{ borderRadius: 6 }} /> : '—',
        },
        {
            title: 'Título',
            dataIndex: 'title',
            key: 'title',
            sorter: (a, b) => a.title.localeCompare(b.title),
            ellipsis: true,
        },
        { title: 'Autor', dataIndex: 'author', key: 'author', width: 150, ellipsis: true },
        {
            title: 'Estatus',
            dataIndex: 'status',
            key: 'status',
            width: 110,
            filters: [
                { text: 'Activo', value: 1 },
                { text: 'Inactivo', value: 0 },
            ],
            onFilter: (value, record) => record.status === value,
            render: (status) =>
                status === 1 ? <Tag color="success">Activo</Tag> : <Tag color="error">Inactivo</Tag>,
        },
        {
            title: 'Fecha',
            dataIndex: 'createdAt',
            key: 'createdAt',
            width: 110,
        },
        {
            title: 'Acciones',
            key: 'actions',
            width: 120,
            render: (_, record) => (
                <Flex gap="small">
                    <Tooltip title="Editar">
                        <Link href={`${urls.base}/${record.id}/edit`}>
                            <Button type="primary" size="small" icon={<EditOutlined />} />
                        </Link>
                    </Tooltip>
                    <Popconfirm
                        title="Eliminar seminario"
                        description="¿Seguro que deseas eliminar este seminario?"
                        okText="Eliminar"
                        cancelText="Cancelar"
                        okButtonProps={{ danger: true }}
                        onConfirm={() => handleDelete(record)}
                    >
                        <Button danger size="small" icon={<DeleteOutlined />} />
                    </Popconfirm>
                </Flex>
            ),
        },
    ];

    return (
        <PageContainer
            title="Seminarios"
            breadcrumbItems={[{ title: 'Panel de Control' }, { title: 'Seminarios' }]}
            extra={
                <Link href={urls.create}>
                    <Button type="primary" icon={<PlusOutlined />}>
                        Nuevo seminario
                    </Button>
                </Link>
            }
        >
            <Flex style={{ marginBottom: 16 }}>
                <Input
                    allowClear
                    prefix={<SearchOutlined />}
                    placeholder="Buscar por título"
                    style={{ maxWidth: 320 }}
                    value={search}
                    onChange={(event) => setSearch(event.target.value)}
                />
            </Flex>

            <Table
                rowKey="id"
                columns={columns}
                dataSource={filteredBlogs}
                loading={deleting}
                scroll={{ x: 800 }}
                pagination={{
                    position: ['bottomRight'],
                    pageSize: 10,
                    showSizeChanger: true,
                    showTotal: (total) => `${total} seminarios`,
                }}
                locale={{ emptyText: 'No hay seminarios registrados' }}
            />
        </PageContainer>
    );
}
