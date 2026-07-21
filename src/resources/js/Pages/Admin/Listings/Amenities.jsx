import React, { useEffect, useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import {
    Button,
    Flex,
    Form,
    Input,
    Modal,
    Transfer,
    Typography,
} from 'antd';
import {
    ArrowLeftOutlined,
    PlusOutlined,
    SaveOutlined,
} from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';

const { Text } = Typography;

export default function Amenities({ listing, amenities, assignedAmenityIds, urls }) {
    const { errors } = usePage().props;
    const [form] = Form.useForm();
    const [targetKeys, setTargetKeys] = useState(assignedAmenityIds.map(String));
    const [saving, setSaving] = useState(false);
    const [createModalOpen, setCreateModalOpen] = useState(false);
    const [creating, setCreating] = useState(false);

    // Refresh the selection when the server sends fresh props
    // (e.g. after creating a new amenity).
    useEffect(() => {
        setTargetKeys(assignedAmenityIds.map(String));
    }, [assignedAmenityIds]);

    const dataSource = amenities.map((amenity) => ({
        key: String(amenity.id),
        title: amenity.name,
    }));

    const handleSave = () => {
        router.put(urls.update, { amenities: targetKeys.map(Number) }, {
            preserveScroll: true,
            onStart: () => setSaving(true),
            onFinish: () => setSaving(false),
        });
    };

    const handleCreate = (values) => {
        router.post(urls.create, values, {
            preserveScroll: true,
            onStart: () => setCreating(true),
            onFinish: () => setCreating(false),
            onSuccess: () => {
                setCreateModalOpen(false);
                form.resetFields();
            },
        });
    };

    return (
        <PageContainer
            title={`Servicios de ${listing.title}`}
            breadcrumbItems={[
                { title: 'Proveedores' },
                { title: 'Todos los Proveedores', href: urls.listings },
                { title: 'Servicios' },
            ]}
            extra={
                <Flex gap="small" wrap>
                    <Link href={urls.listings}>
                        <Button icon={<ArrowLeftOutlined />}>Volver al listado</Button>
                    </Link>
                    <Button icon={<PlusOutlined />} onClick={() => setCreateModalOpen(true)}>
                        Nuevo servicio
                    </Button>
                    <Button type="primary" icon={<SaveOutlined />} loading={saving} onClick={handleSave}>
                        Guardar cambios
                    </Button>
                </Flex>
            }
        >
            <Text type="secondary" style={{ display: 'block', marginBottom: 16 }}>
                Mueve a la derecha los servicios que ofrece este proveedor y presiona
                &quot;Guardar cambios&quot;.
            </Text>

            <Transfer
                dataSource={dataSource}
                targetKeys={targetKeys}
                onChange={(keys) => setTargetKeys(keys.map(String))}
                render={(item) => item.title}
                showSearch
                filterOption={(input, item) => item.title.toLowerCase().includes(input.toLowerCase())}
                titles={['Disponibles', 'Asignados']}
                listStyle={{ width: '100%', minWidth: 260, height: 420 }}
                locale={{
                    itemUnit: 'servicio',
                    itemsUnit: 'servicios',
                    notFoundContent: 'Sin servicios',
                    searchPlaceholder: 'Buscar servicio',
                }}
            />

            <Modal
                title="Nuevo servicio"
                open={createModalOpen}
                onCancel={() => setCreateModalOpen(false)}
                onOk={() => form.submit()}
                okText="Crear y asignar"
                cancelText="Cancelar"
                confirmLoading={creating}
                destroyOnHidden
            >
                <Form form={form} layout="vertical" onFinish={handleCreate} disabled={creating}>
                    <Form.Item
                        label="Nombre"
                        name="name"
                        rules={[{ required: true, message: 'El nombre es obligatorio.' }]}
                        validateStatus={errors.name ? 'error' : undefined}
                        help={errors.name}
                    >
                        <Input maxLength={255} />
                    </Form.Item>
                </Form>
            </Modal>
        </PageContainer>
    );
}
