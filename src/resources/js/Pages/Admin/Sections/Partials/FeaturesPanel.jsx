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
import HtmlEditor from '../../../../Components/HtmlEditor';

const { Text } = Typography;

const STATUS_ACTIVE = 1;

/**
 * "Nuestras funciones" management: reactive table plus a modal form for
 * create/edit. The icon is stored as a FontAwesome class string because
 * the public site renders it; here it is edited as plain text.
 */
export default function FeaturesPanel({ features, baseUrl }) {
    const { errors } = usePage().props;
    const [form] = Form.useForm();
    const [modalOpen, setModalOpen] = useState(false);
    const [editingFeature, setEditingFeature] = useState(null);
    const [submitting, setSubmitting] = useState(false);
    const [reloading, setReloading] = useState(false);
    const [search, setSearch] = useState('');

    const filteredFeatures = useMemo(() => {
        if (!search) return features;

        const term = search.toLowerCase();

        return features.filter(
            (feature) =>
                feature.title.toLowerCase().includes(term) ||
                feature.shortDescription.toLowerCase().includes(term),
        );
    }, [features, search]);

    const openCreateModal = () => {
        setEditingFeature(null);
        form.resetFields();
        form.setFieldsValue({ status: STATUS_ACTIVE });
        setModalOpen(true);
    };

    const openEditModal = (feature) => {
        setEditingFeature(feature);
        form.setFieldsValue({
            icon: feature.icon,
            title: feature.title,
            short_description: feature.shortDescription,
            status: feature.status,
        });
        setModalOpen(true);
    };

    const handleSubmit = (values) => {
        // The controller redirects back, so Inertia re-renders the page
        // with the fresh "features" prop — no manual reload needed.
        const options = {
            preserveScroll: true,
            onStart: () => setSubmitting(true),
            onFinish: () => setSubmitting(false),
            onSuccess: () => {
                setModalOpen(false);
                form.resetFields();
            },
        };

        if (editingFeature) {
            router.put(`${baseUrl}/${editingFeature.id}`, values, options);
        } else {
            router.post(baseUrl, values, options);
        }
    };

    const handleDelete = (feature) => {
        router.delete(`${baseUrl}/${feature.id}`, {
            preserveScroll: true,
            onStart: () => setReloading(true),
            onFinish: () => setReloading(false),
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
            title: 'Título',
            dataIndex: 'title',
            key: 'title',
            sorter: (a, b) => a.title.localeCompare(b.title),
        },
        {
            title: 'Descripción corta',
            dataIndex: 'shortDescription',
            key: 'shortDescription',
            ellipsis: true,
        },
        {
            title: 'Estatus',
            dataIndex: 'status',
            key: 'status',
            width: 120,
            filters: [
                { text: 'Activo', value: STATUS_ACTIVE },
                { text: 'Inactivo', value: 0 },
            ],
            onFilter: (value, record) => record.status === value,
            render: (status) =>
                status === STATUS_ACTIVE ? (
                    <Tag color="success">Activo</Tag>
                ) : (
                    <Tag color="error">Inactivo</Tag>
                ),
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
                        title="Eliminar función"
                        description="¿Seguro que deseas eliminar esta función?"
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
        <>
            <Flex justify="space-between" wrap gap="small" style={{ marginBottom: 16 }}>
                <Input
                    allowClear
                    prefix={<SearchOutlined />}
                    placeholder="Buscar por título o descripción"
                    style={{ maxWidth: 320 }}
                    value={search}
                    onChange={(event) => setSearch(event.target.value)}
                />
                <Button type="primary" icon={<PlusOutlined />} onClick={openCreateModal}>
                    Nueva función
                </Button>
            </Flex>

            <Table
                rowKey="id"
                columns={columns}
                dataSource={filteredFeatures}
                loading={reloading}
                pagination={{
                    position: ['bottomRight'],
                    pageSize: 10,
                    showSizeChanger: true,
                    showTotal: (total) => `${total} funciones`,
                }}
                locale={{ emptyText: 'No hay funciones registradas' }}
            />

            <Modal
                title={editingFeature ? 'Editar función' : 'Nueva función'}
                open={modalOpen}
                onCancel={() => setModalOpen(false)}
                onOk={() => form.submit()}
                okText={editingFeature ? 'Actualizar' : 'Crear'}
                cancelText="Cancelar"
                confirmLoading={submitting}
                destroyOnHidden
            >
                <Form form={form} layout="vertical" onFinish={handleSubmit} disabled={submitting}>
                    <Form.Item
                        label={
                            <>
                                Icono&nbsp;
                                <Tooltip title="Clase de icono FontAwesome que se muestra en el sitio público, por ejemplo: fas fa-star">
                                    <QuestionCircleOutlined />
                                </Tooltip>
                            </>
                        }
                        name="icon"
                        rules={[{ required: true, message: 'El icono es obligatorio.' }]}
                        validateStatus={errors.icon ? 'error' : undefined}
                        help={errors.icon}
                    >
                        <Input placeholder="fas fa-star" maxLength={255} />
                    </Form.Item>

                    <Form.Item
                        label="Título"
                        name="title"
                        rules={[{ required: true, message: 'El título es obligatorio.' }]}
                        validateStatus={errors.title ? 'error' : undefined}
                        help={errors.title}
                    >
                        <Input maxLength={255} showCount />
                    </Form.Item>

                    <Form.Item
                        label="Descripción corta"
                        name="short_description"
                        rules={[{ required: true, message: 'La descripción corta es obligatoria.' }]}
                        validateStatus={errors.short_description ? 'error' : undefined}
                        help={errors.short_description}
                    >
                        <HtmlEditor rows={3} placeholder="Descripción corta" />
                    </Form.Item>

                    <Form.Item
                        label="Estatus"
                        name="status"
                        rules={[{ required: true, message: 'El estatus es obligatorio.' }]}
                        validateStatus={errors.status ? 'error' : undefined}
                        help={errors.status}
                    >
                        <Select
                            options={[
                                { value: STATUS_ACTIVE, label: 'Activo' },
                                { value: 0, label: 'Inactivo' },
                            ]}
                        />
                    </Form.Item>
                </Form>
            </Modal>
        </>
    );
}
