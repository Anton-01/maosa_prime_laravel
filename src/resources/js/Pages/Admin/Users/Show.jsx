import React from 'react';
import { Link } from '@inertiajs/react';
import { Button, Card, Collapse, Descriptions, Empty, Flex, Space, Tag, Typography } from 'antd';
import { ArrowLeftOutlined, EditOutlined, KeyOutlined } from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';

const { Text } = Typography;

export default function Show({ user, urls }) {
    const groupedEntries = Object.entries(user.allPermissionsGrouped ?? {});
    const directSet = new Set(user.directPermissions);

    const collapseItems = groupedEntries.map(([groupName, permissions]) => ({
        key: groupName || 'general',
        label: `${groupName || 'General'} (${permissions.length})`,
        children: (
            <Space size={[4, 8]} wrap>
                {permissions.map((permission) => (
                    <Tag key={permission} color={directSet.has(permission) ? 'default' : 'blue'}>
                        {permission}
                    </Tag>
                ))}
            </Space>
        ),
    }));

    return (
        <PageContainer
            title={`Usuario: ${user.name}`}
            breadcrumbItems={[
                { title: 'Gestión de accesos' },
                { title: 'Usuarios', href: urls.base },
                { title: 'Detalle' },
            ]}
            extra={
                <Space wrap>
                    <Link href={urls.base}>
                        <Button icon={<ArrowLeftOutlined />}>Volver al listado</Button>
                    </Link>
                    <Link href={`${urls.base}/${user.id}/edit`}>
                        <Button icon={<EditOutlined />}>Editar</Button>
                    </Link>
                    <Link href={`${urls.permissionsBase}/${user.id}/edit`}>
                        <Button type="primary" icon={<KeyOutlined />}>
                            Permisos directos
                        </Button>
                    </Link>
                </Space>
            }
            wrapInCard={false}
        >
            <Flex vertical gap={16}>
                <Card title="Información general">
                    <Descriptions
                        bordered
                        size="small"
                        column={{ xs: 1, md: 2 }}
                        items={[
                            { key: 'id', label: 'ID', children: user.id },
                            { key: 'name', label: 'Nombre', children: user.name },
                            { key: 'email', label: 'Correo', children: user.email },
                            { key: 'userType', label: 'Tipo', children: user.userType || '—' },
                            { key: 'phone', label: 'Teléfono', children: user.phone || '—' },
                            { key: 'address', label: 'Dirección', children: user.address || '—' },
                            {
                                key: 'station',
                                label: 'Estación (id_estacion)',
                                children: user.stationId ?? '—',
                            },
                            {
                                key: 'partner',
                                label: 'Socio (id_socio)',
                                children: user.partnerId ?? '—',
                            },
                            {
                                key: 'roles',
                                label: 'Roles',
                                children: user.roles.length ? (
                                    <Space size={[4, 8]} wrap>
                                        {user.roles.map((role) => (
                                            <Tag key={role} color="green">
                                                {role}
                                            </Tag>
                                        ))}
                                    </Space>
                                ) : (
                                    <Tag>Sin rol</Tag>
                                ),
                            },
                            {
                                key: 'approved',
                                label: 'Aprobado',
                                children: user.isApproved ? (
                                    <Tag color="success">Sí</Tag>
                                ) : (
                                    <Tag color="error">No</Tag>
                                ),
                            },
                            {
                                key: 'priceTable',
                                label: 'Tabla de precios',
                                children: user.canViewPriceTable ? (
                                    <Tag color="success">Sí</Tag>
                                ) : (
                                    <Tag>No</Tag>
                                ),
                            },
                            { key: 'createdAt', label: 'Fecha de registro', children: user.createdAt },
                        ]}
                    />
                </Card>

                <Card
                    title="Permisos"
                    extra={
                        <Space size="small">
                            <Tag color="blue">Por rol ({user.rolePermissions.length})</Tag>
                            <Tag color="default">Directos ({user.directPermissions.length})</Tag>
                        </Space>
                    }
                >
                    {collapseItems.length > 0 ? (
                        <>
                            <Text type="secondary" style={{ display: 'block', marginBottom: 12 }}>
                                Los permisos en gris fueron asignados directamente al usuario;
                                los azules provienen de sus roles.
                            </Text>
                            <Collapse items={collapseItems} />
                        </>
                    ) : (
                        <Empty description="El usuario no tiene permisos asignados" />
                    )}
                </Card>
            </Flex>
        </PageContainer>
    );
}
