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
    Input,
    Row,
    Typography,
} from 'antd';
import { ArrowLeftOutlined, SaveOutlined } from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';

const { Text } = Typography;

export default function RoleForm({ role, permissionsGrouped, urls }) {
    const { errors } = usePage().props;
    const [roleName, setRoleName] = useState(role?.name ?? '');
    const [selected, setSelected] = useState(new Set(role?.permissions ?? []));
    const [submitting, setSubmitting] = useState(false);

    const isEditing = Boolean(role);
    const groupedEntries = Object.entries(permissionsGrouped ?? {});

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

    const toggleGroup = (permissions, checked) => {
        setSelected((previous) => {
            const next = new Set(previous);

            permissions.forEach((permission) => {
                if (checked) {
                    next.add(permission);
                } else {
                    next.delete(permission);
                }
            });

            return next;
        });
    };

    const handleSubmit = () => {
        const payload = {
            role_name: roleName,
            permissions: [...selected],
        };

        const visitOptions = {
            preserveScroll: true,
            onStart: () => setSubmitting(true),
            onFinish: () => setSubmitting(false),
        };

        if (isEditing) {
            router.put(`${urls.base}/${role.id}`, payload, visitOptions);
        } else {
            router.post(urls.base, payload, visitOptions);
        }
    };

    return (
        <PageContainer
            title={isEditing ? `Editar rol: ${role.name}` : 'Nuevo rol'}
            breadcrumbItems={[
                { title: 'Gestión de accesos' },
                { title: 'Roles y permisos', href: urls.base },
                { title: isEditing ? 'Editar' : 'Crear' },
            ]}
            extra={
                <Link href={urls.base}>
                    <Button icon={<ArrowLeftOutlined />}>Volver al listado</Button>
                </Link>
            }
            wrapInCard={false}
        >
            <Flex vertical gap={16}>
                {errors.permissions && <Alert type="error" showIcon message={errors.permissions} />}

                <Card>
                    <Flex vertical gap={4} style={{ maxWidth: 420, marginBottom: 8 }}>
                        <Text strong>Nombre del rol</Text>
                        <Input
                            maxLength={40}
                            showCount
                            value={roleName}
                            onChange={(event) => setRoleName(event.target.value)}
                            placeholder="Nombre del rol"
                            status={errors.role_name ? 'error' : undefined}
                        />
                        {errors.role_name && <Text type="danger">{errors.role_name}</Text>}
                    </Flex>

                    {groupedEntries.map(([groupName, permissions]) => {
                        const selectedInGroup = permissions.filter((permission) =>
                            selected.has(permission),
                        ).length;
                        const allSelected = selectedInGroup === permissions.length;

                        return (
                            <React.Fragment key={groupName || 'general'}>
                                <Divider orientation="left" orientationMargin={0}>
                                    {groupName || 'General'}
                                    <Checkbox
                                        style={{ marginLeft: 12, fontWeight: 'normal' }}
                                        checked={allSelected}
                                        indeterminate={selectedInGroup > 0 && !allSelected}
                                        onChange={(event) => toggleGroup(permissions, event.target.checked)}
                                    >
                                        Seleccionar todo
                                    </Checkbox>
                                </Divider>
                                <Row gutter={[16, 8]}>
                                    {permissions.map((permission) => (
                                        <Col xs={24} sm={12} lg={8} key={permission}>
                                            <Checkbox
                                                checked={selected.has(permission)}
                                                onChange={(event) =>
                                                    togglePermission(permission, event.target.checked)
                                                }
                                            >
                                                {permission}
                                            </Checkbox>
                                        </Col>
                                    ))}
                                </Row>
                            </React.Fragment>
                        );
                    })}

                    <Button
                        type="primary"
                        icon={<SaveOutlined />}
                        loading={submitting}
                        onClick={handleSubmit}
                        style={{ marginTop: 24 }}
                    >
                        {isEditing ? 'Actualizar rol' : 'Crear rol'}
                    </Button>
                </Card>
            </Flex>
        </PageContainer>
    );
}
