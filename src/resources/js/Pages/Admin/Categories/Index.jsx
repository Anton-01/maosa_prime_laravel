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
import ImageDropUpload from '../../../Components/ImageDropUpload';
import useTranslation from '../../../Hooks/useTranslation';

const { Text } = Typography;

export default function Index({ categories, urls }) {
    const { errors } = usePage().props;
    const { t } = useTranslation();
    const [form] = Form.useForm();
    const [modalOpen, setModalOpen] = useState(false);
    const [editingCategory, setEditingCategory] = useState(null);
    const [submitting, setSubmitting] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [search, setSearch] = useState('');
    const [iconFile, setIconFile] = useState(null);
    const [backgroundFile, setBackgroundFile] = useState(null);

    const statusOptions = [
        { value: 1, label: t('common.active') },
        { value: 0, label: t('common.inactive') },
    ];
    const yesNoOptions = [
        { value: 1, label: t('common.yes') },
        { value: 0, label: t('common.no') },
    ];

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
            title: t('categories.icon'),
            dataIndex: 'imageIconUrl',
            key: 'icon',
            width: 90,
            render: (url) =>
                url ? (
                    <Image src={url} alt={t('categories.icon')} width={48} style={{ borderRadius: 6 }} />
                ) : (
                    '—'
                ),
        },
        {
            title: t('users.name'),
            dataIndex: 'name',
            key: 'name',
            sorter: (a, b) => a.name.localeCompare(b.name),
        },
        {
            title: t('catalog.show_at_home'),
            dataIndex: 'showAtHome',
            key: 'showAtHome',
            width: 150,
            filters: [
                { text: t('common.yes'), value: 1 },
                { text: t('common.no'), value: 0 },
            ],
            onFilter: (value, record) => record.showAtHome === value,
            render: (value) =>
                value === 1 ? (
                    <Tag color="blue">{t('common.yes')}</Tag>
                ) : (
                    <Tag>{t('common.no')}</Tag>
                ),
        },
        {
            title: t('common.status'),
            dataIndex: 'status',
            key: 'status',
            width: 120,
            filters: [
                { text: t('common.active'), value: 1 },
                { text: t('common.inactive'), value: 0 },
            ],
            onFilter: (value, record) => record.status === value,
            render: (status) =>
                status === 1 ? (
                    <Tag color="success">{t('common.active')}</Tag>
                ) : (
                    <Tag color="error">{t('common.inactive')}</Tag>
                ),
        },
        {
            title: t('common.actions'),
            key: 'actions',
            width: 120,
            render: (_, record) => (
                <Flex gap="small">
                    <Tooltip title={t('common.edit')}>
                        <Button
                            type="primary"
                            size="small"
                            icon={<EditOutlined />}
                            onClick={() => openEditModal(record)}
                        />
                    </Tooltip>
                    <Popconfirm
                        title={t('categories.delete_title')}
                        description={t('categories.delete_confirm')}
                        okText={t('common.delete')}
                        cancelText={t('common.cancel')}
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
            title={t('categories.title')}
            breadcrumbItems={[{ title: t('nav.suppliers') }, { title: t('categories.title') }]}
            extra={
                <Button type="primary" icon={<PlusOutlined />} onClick={openCreateModal}>
                    {t('categories.new')}
                </Button>
            }
        >
            <Flex style={{ marginBottom: 16 }}>
                <Input
                    allowClear
                    prefix={<SearchOutlined />}
                    placeholder={t('common.search_by_name')}
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
                    showTotal: (total) => t('categories.total', { count: total }),
                }}
                locale={{ emptyText: t('categories.empty') }}
            />

            <Modal
                title={editingCategory ? t('categories.edit') : t('categories.new')}
                open={modalOpen}
                onCancel={() => setModalOpen(false)}
                onOk={() => form.submit()}
                okText={editingCategory ? t('common.update') : t('common.create')}
                cancelText={t('common.cancel')}
                confirmLoading={submitting}
                destroyOnHidden
            >
                <Form form={form} layout="vertical" onFinish={handleSubmit} disabled={submitting}>
                    <Form.Item
                        label={t('categories.icon')}
                        required={!editingCategory}
                        validateStatus={errors.image_icon ? 'error' : undefined}
                        help={errors.image_icon}
                    >
                        <ImageDropUpload
                            value={iconFile}
                            onChange={setIconFile}
                            currentUrl={editingCategory?.imageIconUrl}
                            height={130}
                        />
                    </Form.Item>

                    <Form.Item
                        label={t('categories.background_image')}
                        required={!editingCategory}
                        validateStatus={errors.background_image ? 'error' : undefined}
                        help={errors.background_image}
                    >
                        <ImageDropUpload
                            value={backgroundFile}
                            onChange={setBackgroundFile}
                            currentUrl={editingCategory?.backgroundImageUrl}
                            height={150}
                        />
                    </Form.Item>

                    <Form.Item
                        label={t('users.name')}
                        name="name"
                        rules={[{ required: true, message: t('common.name_required') }]}
                        validateStatus={errors.name ? 'error' : undefined}
                        help={errors.name}
                    >
                        <Input maxLength={255} showCount />
                    </Form.Item>

                    <Form.Item
                        label={t('catalog.show_at_home')}
                        name="show_at_home"
                        rules={[{ required: true, message: t('catalog.field_required') }]}
                        validateStatus={errors.show_at_home ? 'error' : undefined}
                        help={errors.show_at_home}
                    >
                        <Select options={yesNoOptions} />
                    </Form.Item>

                    <Form.Item
                        label={t('common.status')}
                        name="status"
                        rules={[{ required: true, message: t('catalog.status_required') }]}
                        validateStatus={errors.status ? 'error' : undefined}
                        help={errors.status}
                    >
                        <Select options={statusOptions} />
                    </Form.Item>
                </Form>
            </Modal>
        </PageContainer>
    );
}
