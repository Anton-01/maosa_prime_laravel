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
import useTranslation from '../../../Hooks/useTranslation';

export default function Index({ locations, urls }) {
    const { errors } = usePage().props;
    const { t } = useTranslation();
    const [form] = Form.useForm();
    const [modalOpen, setModalOpen] = useState(false);
    const [editingLocation, setEditingLocation] = useState(null);
    const [submitting, setSubmitting] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [search, setSearch] = useState('');

    const statusOptions = [
        { value: 1, label: t('common.active') },
        { value: 0, label: t('common.inactive') },
    ];
    const yesNoOptions = [
        { value: 1, label: t('common.yes') },
        { value: 0, label: t('common.no') },
    ];

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
            title: t('users.name'),
            dataIndex: 'name',
            key: 'name',
            sorter: (a, b) => a.name.localeCompare(b.name),
        },
        {
            title: t('catalog.show_at_home'),
            dataIndex: 'showAtHome',
            key: 'showAtHome',
            width: 160,
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
                        title={t('locations.delete_title')}
                        description={t('locations.delete_confirm')}
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
            title={t('locations.title')}
            breadcrumbItems={[{ title: t('nav.suppliers') }, { title: t('locations.title') }]}
            extra={
                <Button type="primary" icon={<PlusOutlined />} onClick={openCreateModal}>
                    {t('locations.new')}
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
                dataSource={filteredLocations}
                loading={deleting}
                pagination={{
                    position: ['bottomRight'],
                    pageSize: 10,
                    showSizeChanger: true,
                    showTotal: (total) => t('locations.total', { count: total }),
                }}
                locale={{ emptyText: t('locations.empty') }}
            />

            <Modal
                title={editingLocation ? t('locations.edit') : t('locations.new')}
                open={modalOpen}
                onCancel={() => setModalOpen(false)}
                onOk={() => form.submit()}
                okText={editingLocation ? t('common.update') : t('common.create')}
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
