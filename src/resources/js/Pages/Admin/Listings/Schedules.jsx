import React, { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
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
import { ArrowLeftOutlined, DeleteOutlined, EditOutlined, PlusOutlined } from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import useTranslation from '../../../Hooks/useTranslation';

export default function Schedules({ listing, schedules, days, urls }) {
    const { errors } = usePage().props;
    const { t } = useTranslation();
    const [form] = Form.useForm();
    const [modalOpen, setModalOpen] = useState(false);
    const [editingSchedule, setEditingSchedule] = useState(null);
    const [submitting, setSubmitting] = useState(false);
    const [working, setWorking] = useState(false);

    const statusOptions = [
        { value: 1, label: t('common.active') },
        { value: 0, label: t('common.inactive') },
    ];

    const openCreateModal = () => {
        setEditingSchedule(null);
        form.resetFields();
        form.setFieldsValue({ status: 1 });
        setModalOpen(true);
    };

    const openEditModal = (schedule) => {
        setEditingSchedule(schedule);
        form.setFieldsValue({
            day: schedule.day,
            start_time: schedule.startTime,
            end_time: schedule.endTime,
            status: schedule.status,
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

        if (editingSchedule) {
            router.put(`${urls.itemsBase}/${editingSchedule.id}`, values, options);
        } else {
            router.post(urls.store, values, options);
        }
    };

    const handleDelete = (schedule) => {
        router.delete(`${urls.itemsBase}/${schedule.id}`, {
            preserveScroll: true,
            onStart: () => setWorking(true),
            onFinish: () => setWorking(false),
        });
    };

    const columns = [
        { title: t('listings.day'), dataIndex: 'day', key: 'day' },
        { title: t('listings.start_time'), dataIndex: 'startTime', key: 'startTime', width: 160 },
        { title: t('listings.end_time'), dataIndex: 'endTime', key: 'endTime', width: 160 },
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
                        title={t('listings.delete_schedule_title')}
                        description={t('listings.delete_schedule_confirm')}
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
            title={t('listings.schedules_title', { title: listing.title })}
            breadcrumbItems={[
                { title: t('nav.suppliers') },
                { title: t('listings.title'), href: urls.listings },
                { title: t('listings.schedules') },
            ]}
            extra={
                <Flex gap="small" wrap>
                    <Link href={urls.listings}>
                        <Button icon={<ArrowLeftOutlined />}>{t('common.back_to_list')}</Button>
                    </Link>
                    <Button type="primary" icon={<PlusOutlined />} onClick={openCreateModal}>
                        {t('listings.new_schedule')}
                    </Button>
                </Flex>
            }
        >
            <Table
                rowKey="id"
                columns={columns}
                dataSource={schedules}
                loading={working}
                pagination={{
                    position: ['bottomRight'],
                    pageSize: 10,
                    hideOnSinglePage: true,
                }}
                locale={{ emptyText: t('listings.no_schedules') }}
            />

            <Modal
                title={editingSchedule ? t('listings.edit_schedule') : t('listings.new_schedule')}
                open={modalOpen}
                onCancel={() => setModalOpen(false)}
                onOk={() => form.submit()}
                okText={editingSchedule ? t('common.update') : t('common.create')}
                cancelText={t('common.cancel')}
                confirmLoading={submitting}
                destroyOnHidden
            >
                <Form form={form} layout="vertical" onFinish={handleSubmit} disabled={submitting}>
                    <Form.Item
                        label={t('listings.day')}
                        name="day"
                        rules={[{ required: true, message: t('listings.day_required') }]}
                        validateStatus={errors.day ? 'error' : undefined}
                        help={errors.day}
                    >
                        <Select
                            placeholder={t('listings.day_placeholder')}
                            options={days.map((day) => ({ value: day, label: day }))}
                        />
                    </Form.Item>

                    <Form.Item
                        label={t('listings.start_time')}
                        name="start_time"
                        rules={[{ required: true, message: t('listings.start_time_required') }]}
                        validateStatus={errors.start_time ? 'error' : undefined}
                        help={errors.start_time}
                    >
                        <Input maxLength={20} placeholder="Ej. 09:00 AM" />
                    </Form.Item>

                    <Form.Item
                        label={t('listings.end_time')}
                        name="end_time"
                        rules={[{ required: true, message: t('listings.end_time_required') }]}
                        validateStatus={errors.end_time ? 'error' : undefined}
                        help={errors.end_time}
                    >
                        <Input maxLength={20} placeholder="Ej. 06:00 PM" />
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
