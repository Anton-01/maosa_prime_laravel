import React, { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import {
    Alert,
    Button,
    Card,
    Checkbox,
    Col,
    Divider,
    Flex,
    Row,
    Space,
    Tag,
    Tooltip,
    Typography,
} from 'antd';
import { ArrowLeftOutlined, SaveOutlined } from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';

const { Text } = Typography;

/**
 * Assign direct permissions to a user. Permissions inherited from roles
 * are shown checked and disabled; only direct ones are editable.
 */
export default function Permissions({ user, allPermissionsGrouped, directPermissions, rolePermissions, urls }) {
    const { errors } = usePage().props;
    const [selected, setSelected] = useState(new Set(directPermissions));
    const [submitting, setSubmitting] = useState(false);

    const roleSet = new Set(rolePermissions);
    const groupedEntries = Object.entries(allPermissionsGrouped ?? {});

    const togglePermission = (permission, checked) => {
        setSelected((previous) => {
            const next = new Set(previous);

            if (checked) {
                next.add(permission);
            } else {
                next.delete(permission);
            }

            return next;
        });
    };

    const handleSubmit = () => {
        router.put(
            urls.update,
            { permissions: [...selected] },
            {
                preserveScroll: true,
                onStart: () => setSubmitting(true),
                onFinish: () => setSubmitting(false),
            },
        );
    };

    return (
        <PageContainer
            title={`Permisos directos: ${user.name}`}
            breadcrumbItems={[
                { title: 'Gestión de accesos' },
                { title: 'Usuarios', href: urls.base },
                { title: 'Permisos directos' },
            ]}
            extra={
                <Space wrap>
                    <Link href={urls.show}>
                        <Button icon={<ArrowLeftOutlined />}>Volver al detalle</Button>
                    </Link>
                    <Button
                        type="primary"
                        icon={<SaveOutlined />}
                        loading={submitting}
                        disabled={user.isSuperAdmin}
                        onClick={handleSubmit}
                    >
                        Guardar permisos
                    </Button>
                </Space>
            }
            wrapInCard={false}
        >
            <Flex vertical gap={16}>
                {user.isSuperAdmin && (
                    <Alert
                        type="warning"
                        showIcon
                        message="No se pueden modificar permisos directos del Super Admin."
                    />
                )}

                {errors.permissions && <Alert type="error" showIcon message={errors.permissions} />}

                <Card>
                    <Space size={[4, 8]} wrap style={{ marginBottom: 8 }}>
                        <Text strong>{user.email}</Text>
                        {user.roles.map((role) => (
                            <Tag key={role} color="green">
                                {role}
                            </Tag>
                        ))}
                    </Space>
                    <Text type="secondary" style={{ display: 'block' }}>
                        Los permisos deshabilitados ya están otorgados por el rol del usuario.
                        Marca permisos adicionales para asignarlos de forma directa.
                    </Text>

                    {groupedEntries.map(([groupName, permissions]) => (
                        <React.Fragment key={groupName || 'general'}>
                            <Divider orientation="left" orientationMargin={0}>
                                {groupName || 'General'}
                            </Divider>
                            <Row gutter={[16, 8]}>
                                {permissions.map((permission) => {
                                    const inheritedFromRole = roleSet.has(permission);

                                    return (
                                        <Col xs={24} sm={12} lg={8} key={permission}>
                                            <Tooltip
                                                title={
                                                    inheritedFromRole
                                                        ? 'Otorgado por el rol del usuario'
                                                        : undefined
                                                }
                                            >
                                                <Checkbox
                                                    checked={inheritedFromRole || selected.has(permission)}
                                                    disabled={inheritedFromRole || user.isSuperAdmin}
                                                    onChange={(event) =>
                                                        togglePermission(permission, event.target.checked)
                                                    }
                                                >
                                                    {permission}
                                                </Checkbox>
                                            </Tooltip>
                                        </Col>
                                    );
                                })}
                            </Row>
                        </React.Fragment>
                    ))}
                </Card>
            </Flex>
        </PageContainer>
    );
}
