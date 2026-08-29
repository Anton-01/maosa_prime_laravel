import React, { useMemo, useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import {
    AutoComplete,
    Button,
    Empty,
    Flex,
    Form,
    Input,
    Modal,
    Popconfirm,
    Select,
    Space,
    Table,
    Tag,
    Tooltip,
    Typography,
} from 'antd';
import {
    ArrowDownOutlined,
    ArrowUpOutlined,
    DeleteOutlined,
    EditOutlined,
    PlusOutlined,
} from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import useTranslation from '../../../Hooks/useTranslation';

const { Text } = Typography;

const TOP_LEVEL_PARENT_ID = 0;

export default function Index({ menus, linkSuggestions, urls }) {
    const { errors } = usePage().props;
    const { t } = useTranslation();
    const [menuForm] = Form.useForm();
    const [itemForm] = Form.useForm();
    const [selectedMenuId, setSelectedMenuId] = useState(menus[0]?.id ?? null);
    const [menuModalOpen, setMenuModalOpen] = useState(false);
    const [itemModalOpen, setItemModalOpen] = useState(false);
    const [editingItem, setEditingItem] = useState(null);
    const [submitting, setSubmitting] = useState(false);
    const [working, setWorking] = useState(false);

    const selectedMenu = useMemo(
        () => menus.find((menu) => menu.id === selectedMenuId) ?? menus[0] ?? null,
        [menus, selectedMenuId],
    );

    const parentOptions = useMemo(() => {
        if (!selectedMenu) return [];

        const options = [{ value: TOP_LEVEL_PARENT_ID, label: t('menus.top_level') }];

        selectedMenu.items.forEach((item) => {
            if (editingItem && item.id === editingItem.id) return;

            options.push({
                value: item.id,
                label: `${'— '.repeat(item.depth)}${item.label}`,
            });
        });

        return options;
    }, [selectedMenu, editingItem]);

    const visitOptions = (extra = {}) => ({
        preserveScroll: true,
        onStart: () => setWorking(true),
        onFinish: () => setWorking(false),
        ...extra,
    });

    const handleCreateMenu = (values) => {
        router.post(urls.menusStore, values, {
            preserveScroll: true,
            onStart: () => setSubmitting(true),
            onFinish: () => setSubmitting(false),
            onSuccess: () => {
                setMenuModalOpen(false);
                menuForm.resetFields();
            },
        });
    };

    const handleDeleteMenu = () => {
        if (!selectedMenu) return;

        router.delete(`${urls.menusBase}/${selectedMenu.id}`, visitOptions({
            onSuccess: () => setSelectedMenuId(null),
        }));
    };

    const openCreateItemModal = () => {
        setEditingItem(null);
        itemForm.resetFields();
        itemForm.setFieldsValue({ parent_id: TOP_LEVEL_PARENT_ID });
        setItemModalOpen(true);
    };

    const openEditItemModal = (item) => {
        setEditingItem(item);
        itemForm.setFieldsValue({
            label: item.label,
            link: item.link,
            class: item.class,
            parent_id: item.parentId,
        });
        setItemModalOpen(true);
    };

    const handleSubmitItem = (values) => {
        const payload = {
            label: values.label,
            link: values.link,
            class: values.class ?? '',
            parent_id: values.parent_id ?? TOP_LEVEL_PARENT_ID,
        };

        const options = {
            preserveScroll: true,
            onStart: () => setSubmitting(true),
            onFinish: () => setSubmitting(false),
            onSuccess: () => {
                setItemModalOpen(false);
                itemForm.resetFields();
            },
        };

        if (editingItem) {
            router.put(`${urls.itemsBase}/${editingItem.id}`, payload, options);
        } else {
            router.post(urls.itemsStore, { ...payload, menu_id: selectedMenu.id }, options);
        }
    };

    const handleDeleteItem = (item) => {
        router.delete(`${urls.itemsBase}/${item.id}`, visitOptions());
    };

    const handleMoveItem = (item, direction) => {
        router.post(`${urls.itemsBase}/${item.id}/move`, { direction }, visitOptions());
    };

    const columns = [
        {
            title: t('menus.label'),
            dataIndex: 'label',
            key: 'label',
            render: (label, record) => (
                <Space size="small">
                    <span style={{ paddingLeft: record.depth * 24 }} />
                    {record.depth > 0 && <Text type="secondary">↳</Text>}
                    <Text strong={record.depth === 0}>{label}</Text>
                </Space>
            ),
        },
        {
            title: t('menus.link'),
            dataIndex: 'link',
            key: 'link',
            render: (link) => <Text code>{link}</Text>,
        },
        {
            title: t('menus.css_class'),
            dataIndex: 'class',
            key: 'class',
            width: 140,
            render: (value) => (value ? <Tag>{value}</Tag> : '—'),
        },
        {
            title: t('common.actions'),
            key: 'actions',
            width: 190,
            render: (_, record) => (
                <Flex gap={4}>
                    <Tooltip title={t('menus.move_up')}>
                        <Button
                            size="small"
                            icon={<ArrowUpOutlined />}
                            onClick={() => handleMoveItem(record, 'up')}
                        />
                    </Tooltip>
                    <Tooltip title={t('menus.move_down')}>
                        <Button
                            size="small"
                            icon={<ArrowDownOutlined />}
                            onClick={() => handleMoveItem(record, 'down')}
                        />
                    </Tooltip>
                    <Tooltip title={t('common.edit')}>
                        <Button
                            type="primary"
                            size="small"
                            icon={<EditOutlined />}
                            onClick={() => openEditItemModal(record)}
                        />
                    </Tooltip>
                    <Popconfirm
                        title={t('menus.delete_item')}
                        description={t('menus.delete_item_confirm')}
                        okText={t('common.delete')}
                        cancelText={t('common.cancel')}
                        okButtonProps={{ danger: true }}
                        onConfirm={() => handleDeleteItem(record)}
                    >
                        <Button danger size="small" icon={<DeleteOutlined />} />
                    </Popconfirm>
                </Flex>
            ),
        },
    ];

    return (
        <PageContainer
            title={t('menus.title')}
            breadcrumbItems={[{ title: t('common.control_panel') }, { title: t('menus.title') }]}
            extra={
                <Space wrap>
                    <Button icon={<PlusOutlined />} onClick={() => setMenuModalOpen(true)}>
                        {t('menus.new_menu')}
                    </Button>
                    {selectedMenu && (
                        <Button
                            type="primary"
                            icon={<PlusOutlined />}
                            onClick={openCreateItemModal}
                        >
                            {t('menus.new_item')}
                        </Button>
                    )}
                </Space>
            }
        >
            {menus.length === 0 ? (
                <Empty description={t('menus.no_menus')}>
                    <Button type="primary" icon={<PlusOutlined />} onClick={() => setMenuModalOpen(true)}>
                        {t('menus.new_menu')}
                    </Button>
                </Empty>
            ) : (
                <>
                    <Flex gap="small" wrap align="center" style={{ marginBottom: 16 }}>
                        <Text strong>{t('menus.menu_label')}</Text>
                        <Select
                            style={{ minWidth: 240 }}
                            value={selectedMenu?.id}
                            onChange={setSelectedMenuId}
                            options={menus.map((menu) => ({ value: menu.id, label: menu.name }))}
                        />
                        {selectedMenu && (
                            <Popconfirm
                                title={t('menus.delete_menu')}
                                description={t('menus.delete_menu_confirm', { name: selectedMenu.name })}
                                okText={t('common.delete')}
                                cancelText={t('common.cancel')}
                                okButtonProps={{ danger: true }}
                                onConfirm={handleDeleteMenu}
                            >
                                <Button danger icon={<DeleteOutlined />}>
                                    {t('menus.delete_menu')}
                                </Button>
                            </Popconfirm>
                        )}
                    </Flex>

                    <Table
                        rowKey="id"
                        columns={columns}
                        dataSource={selectedMenu?.items ?? []}
                        loading={working}
                        pagination={{
                            position: ['bottomRight'],
                            pageSize: 20,
                            hideOnSinglePage: true,
                        }}
                        locale={{ emptyText: t('menus.no_items') }}
                    />
                </>
            )}

            <Modal
                title={t('menus.new_menu')}
                open={menuModalOpen}
                onCancel={() => setMenuModalOpen(false)}
                onOk={() => menuForm.submit()}
                okText={t('common.create')}
                cancelText={t('common.cancel')}
                confirmLoading={submitting}
                destroyOnHidden
            >
                <Form form={menuForm} layout="vertical" onFinish={handleCreateMenu} disabled={submitting}>
                    <Form.Item
                        label={t('menus.menu_name')}
                        name="name"
                        rules={[{ required: true, message: t('menus.menu_name_required') }]}
                        validateStatus={errors.name ? 'error' : undefined}
                        help={errors.name}
                    >
                        <Input maxLength={255} placeholder="Ej. Main Menu" />
                    </Form.Item>
                </Form>
            </Modal>

            <Modal
                title={editingItem ? t('menus.edit_item') : t('menus.new_item')}
                open={itemModalOpen}
                onCancel={() => setItemModalOpen(false)}
                onOk={() => itemForm.submit()}
                okText={editingItem ? t('menus.update') : t('menus.add')}
                cancelText={t('common.cancel')}
                confirmLoading={submitting}
                destroyOnHidden
            >
                <Form form={itemForm} layout="vertical" onFinish={handleSubmitItem} disabled={submitting}>
                    <Form.Item
                        label={t('menus.label')}
                        name="label"
                        rules={[{ required: true, message: t('menus.label_required') }]}
                        validateStatus={errors.label ? 'error' : undefined}
                        help={errors.label}
                    >
                        <Input maxLength={255} placeholder={t('menus.label_placeholder')} />
                    </Form.Item>

                    <Form.Item
                        label={t('menus.link')}
                        name="link"
                        rules={[{ required: true, message: t('menus.link_required') }]}
                        validateStatus={errors.link ? 'error' : undefined}
                        help={errors.link}
                    >
                        <AutoComplete
                            options={linkSuggestions}
                            filterOption={(input, option) =>
                                option.value.toLowerCase().includes(input.toLowerCase()) ||
                                option.label.toLowerCase().includes(input.toLowerCase())
                            }
                        >
                            <Input maxLength={255} placeholder={t('menus.link_placeholder')} />
                        </AutoComplete>
                    </Form.Item>

                    <Form.Item
                        label={t('menus.parent_item')}
                        name="parent_id"
                        validateStatus={errors.parent_id ? 'error' : undefined}
                        help={errors.parent_id}
                    >
                        <Select options={parentOptions} />
                    </Form.Item>

                    <Form.Item
                        label={t('menus.css_class_optional')}
                        name="class"
                        validateStatus={errors.class ? 'error' : undefined}
                        help={errors.class}
                    >
                        <Input maxLength={255} />
                    </Form.Item>
                </Form>
            </Modal>
        </PageContainer>
    );
}
