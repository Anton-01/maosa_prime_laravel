import React, { useMemo, useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import {
    Button,
    Flex,
    Form,
    Input,
    Modal,
    Popconfirm,
    Select,
    Table,
    Tag,
    Tooltip,
} from 'antd';
import { DeleteOutlined, EditOutlined, PlusOutlined, SearchOutlined } from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';

const STATUS_OPTIONS = [
    { value: 1, label: 'Activo' },
    { value: 0, label: 'Inactivo' },
];

const YES_NO_OPTIONS = [
    { value: 1, label: 'Sí' },
    { value: 0, label: 'No' },
];

export default function Index({ locations, urls }) {
    const { errors } = usePage().props;
    const [form] = Form.useForm();
    const [modalOpen, setModalOpen] = useState(false);
    const [editingLocation, setEditingLocation] = useState(null);
    const [submitting, setSubmitting] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [search, setSearch] = useState('');

    const filteredLocations = useMemo(() => {
        if (!search) return locations;

        const term = search.toLowerCase();

        return locations.filter((location) => location.name.toLowerCase().includes(term));
    }, [locations, search]);

    const openCreateModal = () => {
        setEditingLocation(null);
        form.resetFields();
        form.setFieldsValue({ show_at_home: 1, status: 1 });
        setModalOpen(true);
    };

    const openEditModal = (location) => {
        setEditingLocation(location);
        form.setFieldsValue({
            name: location.name,
            show_at_home: location.showAtHome,
            status: location.status,
        });
        setModalOpen(true);
    };

    const handleSubmit = (values) => {
        const options = {
            preserveScroll: true,
            onStart: () => setSubmitting(true),
            onFinish: () => setSubmitting(false),
            onSuccess: () => {
                setModalOpen(false);
                form.resetFields();
            },
        };

        if (editingLocation) {
            router.put(`${urls.base}/${editingLocation.id}`, values, options);
        } else {
            router.post(urls.base, values, options);
        }
    };

    const handleDelete = (location) => {
        router.delete(`${urls.base}/${location.id}`, {
            preserveScroll: true,
            onStart: () => setDeleting(true),
            onFinish: () => setDeleting(false),
        });
    };

    const columns = [
        {
            title: 'Nombre',
            dataIndex: 'name',
            key: 'name',
            sorter: (a, b) => a.name.localeCompare(b.name),
        },
        {
            title: 'Mostrar en inicio',
            dataIndex: 'showAtHome',
            key: 'showAtHome',
            width: 160,
            filters: [
                { text: 'Sí', value: 1 },
                { text: 'No', value: 0 },
            ],
            onFilter: (value, record) => record.showAtHome === value,
            render: (value) => (value === 1 ? <Tag color="blue">Sí</Tag> : <Tag>No</Tag>),
        },
        {
            title: 'Estatus',
            dataIndex: 'status',
            key: 'status',
            width: 130,
            filters: [
                { text: 'Activo', value: 1 },
                { text: 'Inactivo', value: 0 },
            ],
            onFilter: (value, record) => record.status === value,
            render: (status) =>
                status === 1 ? <Tag color="success">Activo</Tag> : <Tag color="error">Inactivo</Tag>,
        },
        {
            title: 'Acciones',
            key: 'actions',
            width: 120,
            render: (_, record) => (
                <Flex gap="small">
                    <Tooltip title="Editar">
                        <Button
                            type="primary"
                            size="small"
                            icon={<EditOutlined />}
                            onClick={() => openEditModal(record)}
                        />
                    </Tooltip>
                    <Popconfirm
                        title="Eliminar ubicación"
                        description="¿Seguro que deseas eliminar esta ubicación?"
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
            title="Ubicaciones"
            breadcrumbItems={[{ title: 'Proveedores' }, { title: 'Ubicaciones' }]}
            extra={
                <Button type="primary" icon={<PlusOutlined />} onClick={openCreateModal}>
                    Nueva ubicación
                </Button>
            }
        >
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
                dataSource={filteredLocations}
                loading={deleting}
                pagination={{
                    position: ['bottomRight'],
                    pageSize: 10,
                    showSizeChanger: true,
                    showTotal: (total) => `${total} ubicaciones`,
                }}
                locale={{ emptyText: 'No hay ubicaciones registradas' }}
            />

            <Modal
                title={editingLocation ? 'Editar ubicación' : 'Nueva ubicación'}
                open={modalOpen}
                onCancel={() => setModalOpen(false)}
                onOk={() => form.submit()}
                okText={editingLocation ? 'Actualizar' : 'Crear'}
                cancelText="Cancelar"
                confirmLoading={submitting}
                destroyOnHidden
            >
                <Form form={form} layout="vertical" onFinish={handleSubmit} disabled={submitting}>
                    <Form.Item
                        label="Nombre"
                        name="name"
                        rules={[{ required: true, message: 'El nombre es obligatorio.' }]}
                        validateStatus={errors.name ? 'error' : undefined}
                        help={errors.name}
                    >
                        <Input maxLength={255} showCount />
                    </Form.Item>

                    <Form.Item
                        label="Mostrar en inicio"
                        name="show_at_home"
                        rules={[{ required: true, message: 'Este campo es obligatorio.' }]}
                        validateStatus={errors.show_at_home ? 'error' : undefined}
                        help={errors.show_at_home}
                    >
                        <Select options={YES_NO_OPTIONS} />
                    </Form.Item>

                    <Form.Item
                        label="Estatus"
                        name="status"
                        rules={[{ required: true, message: 'El estatus es obligatorio.' }]}
                        validateStatus={errors.status ? 'error' : undefined}
                        help={errors.status}
                    >
                        <Select options={STATUS_OPTIONS} />
                    </Form.Item>
                </Form>
            </Modal>
        </PageContainer>
    );
}
