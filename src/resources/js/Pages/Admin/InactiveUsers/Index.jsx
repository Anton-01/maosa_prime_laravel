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
import useTranslation from '../../../Hooks/useTranslation';

const { Text } = Typography;

export default function Index({ days, users, urls }) {
    const { errors } = usePage().props;
    const { t } = useTranslation();
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
            title: t('users.name'),
            dataIndex: 'name',
            key: 'name',
            sorter: (a, b) => a.name.localeCompare(b.name),
            ellipsis: true,
        },
        { title: t('users.email'), dataIndex: 'email', key: 'email', ellipsis: true },
        {
            title: t('users.role'),
            dataIndex: 'role',
            key: 'role',
            width: 130,
            render: (role) => (role ? <Tag color="green">{role}</Tag> : <Tag>{t('inactive_users.no_role')}</Tag>),
        },
        {
            title: t('inactive_users.last_session'),
            dataIndex: 'lastSessionAt',
            key: 'lastSessionAt',
            width: 160,
            render: (value) => value ?? <Text type="secondary">{t('inactive_users.never_logged_in')}</Text>,
        },
        {
            title: t('inactive_users.location'),
            dataIndex: 'lastSessionLocation',
            key: 'lastSessionLocation',
            ellipsis: true,
            render: (value) => value ?? <Text type="secondary">{t('inactive_users.unknown_location')}</Text>,
        },
        {
            title: t('inactive_users.days_inactive'),
            dataIndex: 'daysInactive',
            key: 'daysInactive',
            width: 130,
            sorter: (a, b) => a.daysInactive - b.daysInactive,
            render: (value) => (
                <Tag color={value > 90 ? 'error' : 'warning'}>{t('inactive_users.days_count', { count: value })}</Tag>
            ),
        },
        { title: t('inactive_users.registered_at'), dataIndex: 'createdAt', key: 'createdAt', width: 110 },
    ];

    return (
        <PageContainer
            title={t('inactive_users.title')}
            breadcrumbItems={[
                { title: t('users.breadcrumb_access') },
                { title: t('inactive_users.title') },
            ]}
            extra={
                <Space wrap>
                    <Button icon={<DownloadOutlined />} href={`${urls.export}?days=${days}`}>
                        {t('inactive_users.export_excel')}
                    </Button>
                    <Button
                        danger
                        type="primary"
                        icon={<DeleteOutlined />}
                        disabled={selectedRowKeys.length === 0}
                        onClick={() => setDeleteModalOpen(true)}
                    >
                        {t('inactive_users.delete_selected', { count: selectedRowKeys.length })}
                    </Button>
                </Space>
            }
        >
            <Flex gap="small" wrap align="center" style={{ marginBottom: 16 }}>
                <Space>
                    <Text>{t('inactive_users.inactive_since')}</Text>
                    <InputNumber
                        min={1}
                        max={3650}
                        value={daysInput}
                        onChange={(value) => setDaysInput(value ?? 1)}
                    />
                    <Text>{t('inactive_users.days')}</Text>
                    <Button icon={<ReloadOutlined />} loading={reloading} onClick={applyDays}>
                        {t('inactive_users.apply')}
                    </Button>
                </Space>
                <Input
                    allowClear
                    prefix={<SearchOutlined />}
                    placeholder={t('users.search_placeholder')}
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
                    showTotal: (total) => t('inactive_users.total', { count: total }),
                }}
                locale={{ emptyText: t('inactive_users.empty') }}
            />

            <Modal
                title={t('inactive_users.delete_modal_title')}
                open={deleteModalOpen}
                onCancel={() => setDeleteModalOpen(false)}
                onOk={handleBulkDelete}
                okText={t('inactive_users.delete_permanently')}
                cancelText={t('common.cancel')}
                okButtonProps={{ danger: true, disabled: !adminPassword }}
                confirmLoading={deleting}
            >
                <Space direction="vertical" size="middle" style={{ display: 'flex' }}>
                    <Alert
                        type="warning"
                        showIcon
                        message={t('inactive_users.delete_warning', { count: selectedRowKeys.length })}
                        description={t('inactive_users.delete_warning_description')}
                    />
                    <Input.Password
                        placeholder={t('inactive_users.current_password')}
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
