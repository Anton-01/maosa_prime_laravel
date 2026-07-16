import React, { useMemo, useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import {
    Button,
    Flex,
    Form,
    Image,
    Input,
    Modal,
    Popconfirm,
    Select,
    Space,
    Table,
    Tag,
    Tooltip,
    Typography,
    Upload,
} from 'antd';
import {
    DeleteOutlined,
    EditOutlined,
    PlusOutlined,
    SearchOutlined,
    UploadOutlined,
} from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';

const { Text } = Typography;

const STATUS_OPTIONS = [
    { value: 1, label: 'Activo' },
    { value: 0, label: 'Inactivo' },
];

const YES_NO_OPTIONS = [
    { value: 1, label: 'Sí' },
    { value: 0, label: 'No' },
];

export default function Index({ categories, urls }) {
    const { errors } = usePage().props;
    const [form] = Form.useForm();
    const [modalOpen, setModalOpen] = useState(false);
    const [editingCategory, setEditingCategory] = useState(null);
    const [submitting, setSubmitting] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [search, setSearch] = useState('');
    const [iconFile, setIconFile] = useState(null);
    const [backgroundFile, setBackgroundFile] = useState(null);

    const filteredCategories = useMemo(() => {
        if (!search) return categories;

        const term = search.toLowerCase();

        return categories.filter((category) => category.name.toLowerCase().includes(term));
    }, [categories, search]);

    const resetFiles = () => {
        setIconFile(null);
        setBackgroundFile(null);
    };

    const openCreateModal = () => {
        setEditingCategory(null);
        resetFiles();
        form.resetFields();
        form.setFieldsValue({ show_at_home: 1, status: 1 });
        setModalOpen(true);
    };

    const openEditModal = (category) => {
        setEditingCategory(category);
        resetFiles();
        form.setFieldsValue({
            name: category.name,
            show_at_home: category.showAtHome,
            status: category.status,
        });
        setModalOpen(true);
    };

    const handleSubmit = (values) => {
        const payload = {
            name: values.name,
            show_at_home: values.show_at_home,
            status: values.status,
            image_icon: iconFile,
            background_image: backgroundFile,
        };

        const options = {
            forceFormData: true,
            preserveScroll: true,
            onStart: () => setSubmitting(true),
            onFinish: () => setSubmitting(false),
            onSuccess: () => {
                setModalOpen(false);
                form.resetFields();
                resetFiles();
            },
        };

        if (editingCategory) {
            router.post(`${urls.base}/${editingCategory.id}`, { _method: 'put', ...payload }, options);
        } else {
            router.post(urls.base, payload, options);
        }
    };

    const handleDelete = (category) => {
        router.delete(`${urls.base}/${category.id}`, {
            preserveScroll: true,
            onStart: () => setDeleting(true),
            onFinish: () => setDeleting(false),
        });
    };

    const columns = [
        {
            title: 'Icono',
            dataIndex: 'imageIconUrl',
            key: 'icon',
            width: 90,
            render: (url) =>
                url ? <Image src={url} alt="Icono" width={48} style={{ borderRadius: 6 }} /> : '—',
        },
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
            width: 150,
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
            width: 120,
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
                        title="Eliminar categoría"
                        description="¿Seguro que deseas eliminar esta categoría?"
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
            title="Categorías"
            breadcrumbItems={[{ title: 'Proveedores' }, { title: 'Categorías' }]}
            extra={
                <Button type="primary" icon={<PlusOutlined />} onClick={openCreateModal}>
                    Nueva categoría
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
                dataSource={filteredCategories}
                loading={deleting}
                pagination={{
                    position: ['bottomRight'],
                    pageSize: 10,
                    showSizeChanger: true,
                    showTotal: (total) => `${total} categorías`,
                }}
                locale={{ emptyText: 'No hay categorías registradas' }}
            />

            <Modal
                title={editingCategory ? 'Editar categoría' : 'Nueva categoría'}
                open={modalOpen}
                onCancel={() => setModalOpen(false)}
                onOk={() => form.submit()}
                okText={editingCategory ? 'Actualizar' : 'Crear'}
                cancelText="Cancelar"
                confirmLoading={submitting}
                destroyOnHidden
            >
                <Form form={form} layout="vertical" onFinish={handleSubmit} disabled={submitting}>
                    <Form.Item
                        label="Icono"
                        required={!editingCategory}
                        validateStatus={errors.image_icon ? 'error' : undefined}
                        help={errors.image_icon}
                    >
                        <Space direction="vertical" size="small">
                            {editingCategory?.imageIconUrl && !iconFile && (
                                <Image src={editingCategory.imageIconUrl} alt="Icono actual" width={64} />
                            )}
                            <Upload
                                accept="image/*"
                                maxCount={1}
                                showUploadList={false}
                                beforeUpload={(file) => {
                                    setIconFile(file);
                                    return false;
                                }}
                            >
                                <Button icon={<UploadOutlined />}>Seleccionar icono</Button>
                            </Upload>
                            {iconFile && <Text type="secondary">{iconFile.name}</Text>}
                        </Space>
                    </Form.Item>

                    <Form.Item
                        label="Imagen de fondo"
                        required={!editingCategory}
                        validateStatus={errors.background_image ? 'error' : undefined}
                        help={errors.background_image}
                    >
                        <Space direction="vertical" size="small">
                            {editingCategory?.backgroundImageUrl && !backgroundFile && (
                                <Image
                                    src={editingCategory.backgroundImageUrl}
                                    alt="Imagen de fondo actual"
                                    width={120}
                                />
                            )}
                            <Upload
                                accept="image/*"
                                maxCount={1}
                                showUploadList={false}
                                beforeUpload={(file) => {
                                    setBackgroundFile(file);
                                    return false;
                                }}
                            >
                                <Button icon={<UploadOutlined />}>Seleccionar imagen</Button>
                            </Upload>
                            {backgroundFile && <Text type="secondary">{backgroundFile.name}</Text>}
                        </Space>
                    </Form.Item>

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
