import React, { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { Button, Flex, Popconfirm, Space, Table, Tag, Tooltip } from 'antd';
import { DeleteOutlined, EditOutlined, PlusOutlined } from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';

const VISIBLE_PERMISSION_TAGS = 6;

export default function Index({ roles, urls }) {
    const [deleting, setDeleting] = useState(false);

    const handleDelete = (role) => {
        router.delete(`${urls.base}/${role.id}`, {
            preserveScroll: true,
            onStart: () => setDeleting(true),
            onFinish: () => setDeleting(false),
        });
    };

    const columns = [
        { title: 'ID', dataIndex: 'id', key: 'id', width: 70 },
        {
            title: 'Rol',
            dataIndex: 'name',
            key: 'name',
            width: 200,
            sorter: (a, b) => a.name.localeCompare(b.name),
            render: (name, record) =>
                record.isProtected ? <Tag color="red">{name}</Tag> : <Tag color="green">{name}</Tag>,
        },
        {
            title: 'Permisos',
            dataIndex: 'permissions',
            key: 'permissions',
            render: (permissions, record) => {
                if (record.isProtected) {
                    return <Tag color="red">Todos los permisos</Tag>;
                }

                const visible = permissions.slice(0, VISIBLE_PERMISSION_TAGS);
                const hidden = permissions.length - visible.length;

                return (
                    <Space size={[4, 8]} wrap>
                        {visible.map((permission) => (
                            <Tag key={permission} color="blue">
                                {permission}
                            </Tag>
                        ))}
                        {hidden > 0 && (
                            <Tooltip title={permissions.slice(VISIBLE_PERMISSION_TAGS).join(', ')}>
                                <Tag>+{hidden} más</Tag>
                            </Tooltip>
                        )}
                    </Space>
                );
            },
        },
        {
            title: 'Acciones',
            key: 'actions',
            width: 120,
            render: (_, record) =>
                record.isProtected ? null : (
                    <Flex gap="small">
                        <Tooltip title="Editar">
                            <Link href={`${urls.base}/${record.id}/edit`}>
                                <Button type="primary" size="small" icon={<EditOutlined />} />
                            </Link>
                        </Tooltip>
                        <Popconfirm
                            title="Eliminar rol"
                            description={`¿Seguro que deseas eliminar "${record.name}"?`}
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
            title="Roles y permisos"
            breadcrumbItems={[{ title: 'Gestión de accesos' }, { title: 'Roles y permisos' }]}
            extra={
                <Link href={urls.create}>
                    <Button type="primary" icon={<PlusOutlined />}>
                        Nuevo rol
                    </Button>
                </Link>
            }
        >
            <Table
                rowKey="id"
                columns={columns}
                dataSource={roles}
                loading={deleting}
                pagination={{
                    position: ['bottomRight'],
                    pageSize: 10,
                    showSizeChanger: true,
                    showTotal: (total) => `${total} roles`,
                }}
                locale={{ emptyText: 'No hay roles registrados' }}
            />
        </PageContainer>
    );
}
