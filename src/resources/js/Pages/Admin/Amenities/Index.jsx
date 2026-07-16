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
    Typography,
} from 'antd';
import {
    DeleteOutlined,
    EditOutlined,
    PlusOutlined,
    QuestionCircleOutlined,
    SearchOutlined,
} from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';

const { Text } = Typography;

const STATUS_OPTIONS = [
    { value: 1, label: 'Activo' },
    { value: 0, label: 'Inactivo' },
];

export default function Index({ amenities, urls }) {
    const { errors } = usePage().props;
    const [form] = Form.useForm();
    const [modalOpen, setModalOpen] = useState(false);
    const [editingAmenity, setEditingAmenity] = useState(null);
    const [submitting, setSubmitting] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [search, setSearch] = useState('');

    const filteredAmenities = useMemo(() => {
        if (!search) return amenities;

        const term = search.toLowerCase();

        return amenities.filter((amenity) => amenity.name.toLowerCase().includes(term));
    }, [amenities, search]);

    const openCreateModal = () => {
        setEditingAmenity(null);
        form.resetFields();
        form.setFieldsValue({ status: 1 });
        setModalOpen(true);
    };

    const openEditModal = (amenity) => {
        setEditingAmenity(amenity);
        form.setFieldsValue({
            name: amenity.name,
            icon: amenity.icon,
            status: amenity.status,
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

        if (editingAmenity) {
            router.put(`${urls.base}/${editingAmenity.id}`, values, options);
        } else {
            router.post(urls.base, values, options);
        }
    };

    const handleDelete = (amenity) => {
        router.delete(`${urls.base}/${amenity.id}`, {
            preserveScroll: true,
            onStart: () => setDeleting(true),
            onFinish: () => setDeleting(false),
        });
    };

    const columns = [
        {
            title: 'Icono',
            dataIndex: 'icon',
            key: 'icon',
            width: 220,
            render: (icon) => <Text code>{icon}</Text>,
        },
        {
            title: 'Nombre',
            dataIndex: 'name',
            key: 'name',
            sorter: (a, b) => a.name.localeCompare(b.name),
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
                        title="Eliminar servicio"
                        description="¿Seguro que deseas eliminar este servicio?"
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
            title="Servicios"
            breadcrumbItems={[{ title: 'Proveedores' }, { title: 'Servicios' }]}
            extra={
                <Button type="primary" icon={<PlusOutlined />} onClick={openCreateModal}>
                    Nuevo servicio
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
                dataSource={filteredAmenities}
                loading={deleting}
                pagination={{
                    position: ['bottomRight'],
                    pageSize: 10,
                    showSizeChanger: true,
                    showTotal: (total) => `${total} servicios`,
                }}
                locale={{ emptyText: 'No hay servicios registrados' }}
            />

            <Modal
                title={editingAmenity ? 'Editar servicio' : 'Nuevo servicio'}
                open={modalOpen}
                onCancel={() => setModalOpen(false)}
                onOk={() => form.submit()}
                okText={editingAmenity ? 'Actualizar' : 'Crear'}
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
                        label={
                            <>
                                Icono&nbsp;
                                <Tooltip title="Clase de icono FontAwesome que se muestra en el sitio público, por ejemplo: fas fa-wifi">
                                    <QuestionCircleOutlined />
                                </Tooltip>
                            </>
                        }
                        name="icon"
                        rules={[{ required: !editingAmenity, message: 'El icono es obligatorio.' }]}
                        validateStatus={errors.icon ? 'error' : undefined}
                        help={errors.icon}
                    >
                        <Input maxLength={255} placeholder="fas fa-wifi" />
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
