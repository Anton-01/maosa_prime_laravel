import React, { useMemo, useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import {
    Alert,
    Button,
    Flex,
    Input,
    InputNumber,
    Modal,
    Space,
    Table,
    Tag,
    Typography,
} from 'antd';
import {
    DeleteOutlined,
    DownloadOutlined,
    ReloadOutlined,
    SearchOutlined,
} from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';

const { Text } = Typography;

export default function Index({ days, users, urls }) {
    const { errors } = usePage().props;
    const [daysInput, setDaysInput] = useState(days);
    const [search, setSearch] = useState('');
    const [selectedRowKeys, setSelectedRowKeys] = useState([]);
    const [reloading, setReloading] = useState(false);
    const [deleteModalOpen, setDeleteModalOpen] = useState(false);
    const [adminPassword, setAdminPassword] = useState('');
    const [deleting, setDeleting] = useState(false);

    const filteredUsers = useMemo(() => {
        if (!search) return users;

        const term = search.toLowerCase();

        return users.filter(
            (user) =>
                user.name.toLowerCase().includes(term) || user.email.toLowerCase().includes(term),
        );
    }, [users, search]);

    const applyDays = () => {
        router.get(urls.base, { days: daysInput }, {
            preserveState: true,
            preserveScroll: true,
            only: ['users', 'days'],
            onStart: () => setReloading(true),
            onFinish: () => {
                setReloading(false);
                setSelectedRowKeys([]);
            },
        });
    };

    const handleBulkDelete = () => {
        router.delete(urls.destroy, {
            data: {
                user_ids: selectedRowKeys,
                admin_password: adminPassword,
            },
            preserveScroll: true,
            onStart: () => setDeleting(true),
            onFinish: () => setDeleting(false),
            onSuccess: () => {
                setDeleteModalOpen(false);
                setAdminPassword('');
                setSelectedRowKeys([]);
            },
        });
    };

    const columns = [
        { title: 'ID', dataIndex: 'id', key: 'id', width: 70, sorter: (a, b) => a.id - b.id },
        {
            title: 'Nombre',
            dataIndex: 'name',
            key: 'name',
            sorter: (a, b) => a.name.localeCompare(b.name),
            ellipsis: true,
        },
        { title: 'Correo', dataIndex: 'email', key: 'email', ellipsis: true },
        {
            title: 'Rol',
            dataIndex: 'role',
            key: 'role',
            width: 130,
            render: (role) => (role ? <Tag color="green">{role}</Tag> : <Tag>Sin rol</Tag>),
        },
        {
            title: 'Última sesión',
            dataIndex: 'lastSessionAt',
            key: 'lastSessionAt',
            width: 160,
            render: (value) => value ?? <Text type="secondary">Nunca ha iniciado sesión</Text>,
        },
        {
            title: 'Ubicación',
            dataIndex: 'lastSessionLocation',
            key: 'lastSessionLocation',
            ellipsis: true,
            render: (value) => value ?? <Text type="secondary">Desconocida</Text>,
        },
        {
            title: 'Días inactivo',
            dataIndex: 'daysInactive',
            key: 'daysInactive',
            width: 130,
            sorter: (a, b) => a.daysInactive - b.daysInactive,
            render: (value) => <Tag color={value > 90 ? 'error' : 'warning'}>{value} días</Tag>,
        },
        { title: 'Registro', dataIndex: 'createdAt', key: 'createdAt', width: 110 },
    ];

    return (
        <PageContainer
            title="Usuarios inactivos"
            breadcrumbItems={[{ title: 'Gestión de accesos' }, { title: 'Usuarios inactivos' }]}
            extra={
                <Space wrap>
                    <Button icon={<DownloadOutlined />} href={`${urls.export}?days=${days}`}>
                        Exportar Excel
                    </Button>
                    <Button
                        danger
                        type="primary"
                        icon={<DeleteOutlined />}
                        disabled={selectedRowKeys.length === 0}
                        onClick={() => setDeleteModalOpen(true)}
                    >
                        Eliminar seleccionados ({selectedRowKeys.length})
                    </Button>
                </Space>
            }
        >
            <Flex gap="small" wrap align="center" style={{ marginBottom: 16 }}>
                <Space>
                    <Text>Sin actividad desde hace</Text>
                    <InputNumber
                        min={1}
                        max={3650}
                        value={daysInput}
                        onChange={(value) => setDaysInput(value ?? 1)}
                    />
                    <Text>días</Text>
                    <Button icon={<ReloadOutlined />} loading={reloading} onClick={applyDays}>
                        Aplicar
                    </Button>
                </Space>
                <Input
                    allowClear
                    prefix={<SearchOutlined />}
                    placeholder="Buscar por nombre o correo"
                    style={{ maxWidth: 300 }}
                    value={search}
                    onChange={(event) => setSearch(event.target.value)}
                />
            </Flex>

            <Table
                rowKey="id"
                columns={columns}
                dataSource={filteredUsers}
                loading={reloading}
                rowSelection={{
                    selectedRowKeys,
                    onChange: setSelectedRowKeys,
                }}
                scroll={{ x: 1000 }}
                pagination={{
                    position: ['bottomRight'],
                    pageSize: 10,
                    showSizeChanger: true,
                    showTotal: (total) => `${total} usuarios inactivos`,
                }}
                locale={{ emptyText: 'No hay usuarios inactivos para el periodo seleccionado' }}
            />

            <Modal
                title="Eliminar usuarios seleccionados"
                open={deleteModalOpen}
                onCancel={() => setDeleteModalOpen(false)}
                onOk={handleBulkDelete}
                okText="Eliminar definitivamente"
                cancelText="Cancelar"
                okButtonProps={{ danger: true, disabled: !adminPassword }}
                confirmLoading={deleting}
            >
                <Space direction="vertical" size="middle" style={{ display: 'flex' }}>
                    <Alert
                        type="warning"
                        showIcon
                        message={`Se eliminarán permanentemente ${selectedRowKeys.length} usuario(s).`}
                        description="Esta acción no se puede deshacer. Confirma con tu contraseña actual."
                    />
                    <Input.Password
                        placeholder="Tu contraseña actual"
                        value={adminPassword}
                        onChange={(event) => setAdminPassword(event.target.value)}
                        status={errors.admin_password ? 'error' : undefined}
                    />
                    {errors.admin_password && <Text type="danger">{errors.admin_password}</Text>}
                    {errors.user_ids && <Text type="danger">{errors.user_ids}</Text>}
                </Space>
            </Modal>
        </PageContainer>
    );
}
