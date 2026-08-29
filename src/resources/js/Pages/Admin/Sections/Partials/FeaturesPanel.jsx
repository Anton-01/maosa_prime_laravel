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
import useTranslation from '../../../../Hooks/useTranslation';

const { Text } = Typography;

const STATUS_ACTIVE = 1;

/**
 * "Nuestras funciones" management: reactive table plus a modal form for
 * create/edit. The icon is stored as a FontAwesome class string because
 * the public site renders it; here it is edited as plain text.
 */
export default function FeaturesPanel({ features, baseUrl }) {
    const { errors } = usePage().props;
    const { t } = useTranslation();
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
            title: t('sections.icon'),
            dataIndex: 'icon',
            key: 'icon',
            width: 220,
            render: (icon) => <Text code>{icon}</Text>,
        },
        {
            title: t('common.title'),
            dataIndex: 'title',
            key: 'title',
            sorter: (a, b) => a.title.localeCompare(b.title),
        },
        {
            title: t('sections.short_description'),
            dataIndex: 'shortDescription',
            key: 'shortDescription',
            ellipsis: true,
        },
        {
            title: t('common.status'),
            dataIndex: 'status',
            key: 'status',
            width: 120,
            filters: [
                { text: t('common.active'), value: STATUS_ACTIVE },
                { text: t('common.inactive'), value: 0 },
            ],
            onFilter: (value, record) => record.status === value,
            render: (status) =>
                status === STATUS_ACTIVE ? (
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
                        title={t('sections.delete_feature')}
                        description={t('sections.delete_feature_confirm')}
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
        <>
            <Flex justify="space-between" wrap gap="small" style={{ marginBottom: 16 }}>
                <Input
                    allowClear
                    prefix={<SearchOutlined />}
                    placeholder={t('sections.search_by_title_or_description')}
                    style={{ maxWidth: 320 }}
                    value={search}
                    onChange={(event) => setSearch(event.target.value)}
                />
                <Button type="primary" icon={<PlusOutlined />} onClick={openCreateModal}>
                    {t('sections.new_feature')}
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
                    showTotal: (total) => t('sections.total_features', { count: total }),
                }}
                locale={{ emptyText: t('sections.no_features') }}
            />

            <Modal
                title={editingFeature ? t('sections.edit_feature') : t('sections.new_feature')}
                open={modalOpen}
                onCancel={() => setModalOpen(false)}
                onOk={() => form.submit()}
                okText={editingFeature ? t('common.update') : t('common.create')}
                cancelText={t('common.cancel')}
                confirmLoading={submitting}
                destroyOnHidden
            >
                <Form form={form} layout="vertical" onFinish={handleSubmit} disabled={submitting}>
                    <Form.Item
                        label={
                            <>
                                {t('sections.icon')}&nbsp;
                                <Tooltip title={t('sections.icon_tooltip')}>
                                    <QuestionCircleOutlined />
                                </Tooltip>
                            </>
                        }
                        name="icon"
                        rules={[{ required: true, message: t('sections.icon_required') }]}
                        validateStatus={errors.icon ? 'error' : undefined}
                        help={errors.icon}
                    >
                        <Input placeholder="fas fa-star" maxLength={255} />
                    </Form.Item>

                    <Form.Item
                        label={t('common.title')}
                        name="title"
                        rules={[{ required: true, message: t('sections.title_required') }]}
                        validateStatus={errors.title ? 'error' : undefined}
                        help={errors.title}
                    >
                        <Input maxLength={255} showCount />
                    </Form.Item>

                    <Form.Item
                        label={t('sections.short_description')}
                        name="short_description"
                        rules={[{ required: true, message: t('sections.short_description_required') }]}
                        validateStatus={errors.short_description ? 'error' : undefined}
                        help={errors.short_description}
                    >
                        <HtmlEditor rows={3} placeholder={t('sections.short_description')} />
                    </Form.Item>

                    <Form.Item
                        label={t('common.status')}
                        name="status"
                        rules={[{ required: true, message: t('catalog.status_required') }]}
                        validateStatus={errors.status ? 'error' : undefined}
                        help={errors.status}
                    >
                        <Select
                            options={[
                                { value: STATUS_ACTIVE, label: t('common.active') },
                                { value: 0, label: t('common.inactive') },
                            ]}
                        />
                    </Form.Item>
                </Form>
            </Modal>
        </>
    );
}
