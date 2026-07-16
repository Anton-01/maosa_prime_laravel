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

const STATUS_OPTIONS = [
    { value: 1, label: 'Activo' },
    { value: 0, label: 'Inactivo' },
];

export default function Schedules({ listing, schedules, days, urls }) {
    const { errors } = usePage().props;
    const [form] = Form.useForm();
    const [modalOpen, setModalOpen] = useState(false);
    const [editingSchedule, setEditingSchedule] = useState(null);
    const [submitting, setSubmitting] = useState(false);
    const [working, setWorking] = useState(false);

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
        { title: 'Día', dataIndex: 'day', key: 'day' },
        { title: 'Hora de inicio', dataIndex: 'startTime', key: 'startTime', width: 160 },
        { title: 'Hora de fin', dataIndex: 'endTime', key: 'endTime', width: 160 },
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
                        title="Eliminar horario"
                        description="¿Seguro que deseas eliminar este horario?"
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
            title={`Horarios de ${listing.title}`}
            breadcrumbItems={[
                { title: 'Proveedores' },
                { title: 'Todos los Proveedores', href: urls.listings },
                { title: 'Horarios' },
            ]}
            extra={
                <Flex gap="small" wrap>
                    <Link href={urls.listings}>
                        <Button icon={<ArrowLeftOutlined />}>Volver al listado</Button>
                    </Link>
                    <Button type="primary" icon={<PlusOutlined />} onClick={openCreateModal}>
                        Nuevo horario
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
                locale={{ emptyText: 'Este proveedor no tiene horarios registrados' }}
            />

            <Modal
                title={editingSchedule ? 'Editar horario' : 'Nuevo horario'}
                open={modalOpen}
                onCancel={() => setModalOpen(false)}
                onOk={() => form.submit()}
                okText={editingSchedule ? 'Actualizar' : 'Crear'}
                cancelText="Cancelar"
                confirmLoading={submitting}
                destroyOnHidden
            >
                <Form form={form} layout="vertical" onFinish={handleSubmit} disabled={submitting}>
                    <Form.Item
                        label="Día"
                        name="day"
                        rules={[{ required: true, message: 'El día es obligatorio.' }]}
                        validateStatus={errors.day ? 'error' : undefined}
                        help={errors.day}
                    >
                        <Select
                            placeholder="Selecciona un día"
                            options={days.map((day) => ({ value: day, label: day }))}
                        />
                    </Form.Item>

                    <Form.Item
                        label="Hora de inicio"
                        name="start_time"
                        rules={[{ required: true, message: 'La hora de inicio es obligatoria.' }]}
                        validateStatus={errors.start_time ? 'error' : undefined}
                        help={errors.start_time}
                    >
                        <Input maxLength={20} placeholder="Ej. 09:00 AM" />
                    </Form.Item>

                    <Form.Item
                        label="Hora de fin"
                        name="end_time"
                        rules={[{ required: true, message: 'La hora de fin es obligatoria.' }]}
                        validateStatus={errors.end_time ? 'error' : undefined}
                        help={errors.end_time}
                    >
                        <Input maxLength={20} placeholder="Ej. 06:00 PM" />
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
