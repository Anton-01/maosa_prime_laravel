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
import {
    DeleteOutlined,
    EditOutlined,
    PlusOutlined,
    SearchOutlined,
} from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import useTranslation from '../../../Hooks/useTranslation';

export default function Index({ amenities, urls }) {
    const { errors } = usePage().props;
    const { t } = useTranslation();
    const [form] = Form.useForm();
    const [modalOpen, setModalOpen] = useState(false);
    const [editingAmenity, setEditingAmenity] = useState(null);
    const [submitting, setSubmitting] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [search, setSearch] = useState('');

    const statusOptions = [
        { value: 1, label: t('common.active') },
        { value: 0, label: t('common.inactive') },
    ];

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
            title: t('users.name'),
            dataIndex: 'name',
            key: 'name',
            sorter: (a, b) => a.name.localeCompare(b.name),
        },
        {
            title: t('common.status'),
            dataIndex: 'status',
            key: 'status',
            width: 130,
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
                        title={t('amenities.delete_title')}
                        description={t('amenities.delete_confirm')}
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
            title={t('amenities.title')}
            breadcrumbItems={[{ title: t('nav.suppliers') }, { title: t('amenities.title') }]}
            extra={
                <Button type="primary" icon={<PlusOutlined />} onClick={openCreateModal}>
                    {t('amenities.new')}
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
                dataSource={filteredAmenities}
                loading={deleting}
                pagination={{
                    position: ['bottomRight'],
                    pageSize: 10,
                    showSizeChanger: true,
                    showTotal: (total) => t('amenities.total', { count: total }),
                }}
                locale={{ emptyText: t('amenities.empty') }}
            />

            <Modal
                title={editingAmenity ? t('amenities.edit') : t('amenities.new')}
                open={modalOpen}
                onCancel={() => setModalOpen(false)}
                onOk={() => form.submit()}
                okText={editingAmenity ? t('common.update') : t('common.create')}
                cancelText={t('common.cancel')}
                confirmLoading={submitting}
                destroyOnHidden
            >
                <Form form={form} layout="vertical" onFinish={handleSubmit} disabled={submitting}>
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
