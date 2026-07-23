import React, { useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import {
    Avatar,
    Button,
    Card,
    Col,
    Form,
    Image,
    Input,
    Row,
    Space,
    Tabs,
    Typography,
    Upload,
} from 'antd';
import { LockOutlined, SaveOutlined, UploadOutlined, UserOutlined } from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import ImageDropUpload from '../../../Components/ImageDropUpload';

const { Text } = Typography;

function ProfileForm({ profile, submitUrl }) {
    const { errors } = usePage().props;
    const [form] = Form.useForm();
    const [submitting, setSubmitting] = useState(false);
    const [avatarFile, setAvatarFile] = useState(null);
    const [bannerFile, setBannerFile] = useState(null);

    const handleSubmit = (values) => {
        router.post(
            submitUrl,
            {
                _method: 'put',
                ...values,
                avatar: avatarFile,
                banner: bannerFile,
            },
            {
                forceFormData: true,
                preserveScroll: true,
                preserveState: true,
                onStart: () => setSubmitting(true),
                onFinish: () => {
                    setSubmitting(false);
                    setAvatarFile(null);
                    setBannerFile(null);
                },
            },
        );
    };

    return (
        <Form
            form={form}
            layout="vertical"
            onFinish={handleSubmit}
            disabled={submitting}
            initialValues={{
                name: profile.name,
                email: profile.email,
                phone: profile.phone,
                address: profile.address,
            }}
        >
            <Row gutter={24}>
                <Col xs={24} md={12}>
                    <Form.Item
                        label="Avatar"
                        validateStatus={errors.avatar ? 'error' : undefined}
                        help={errors.avatar}
                    >
                        <ImageDropUpload
                            value={avatarFile}
                            onChange={setAvatarFile}
                            currentUrl={profile.avatarUrl}
                            height={160}
                        />
                    </Form.Item>
                </Col>
                <Col xs={24} md={12}>
                    <Form.Item
                        label="Banner"
                        validateStatus={errors.banner ? 'error' : undefined}
                        help={errors.banner}
                    >
                        <ImageDropUpload
                            value={bannerFile}
                            onChange={setBannerFile}
                            currentUrl={profile.bannerUrl}
                            height={160}
                        />
                    </Form.Item>
                </Col>
                <Col xs={24} md={12}>
                    <Form.Item
                        label="Nombre"
                        name="name"
                        rules={[{ required: true, message: 'El nombre es obligatorio.' }]}
                        validateStatus={errors.name ? 'error' : undefined}
                        help={errors.name}
                    >
                        <Input maxLength={255} />
                    </Form.Item>
                </Col>
                <Col xs={24} md={12}>
                    <Form.Item
                        label="Email"
                        name="email"
                        rules={[
                            { required: true, message: 'El email es obligatorio.' },
                            { type: 'email', message: 'Debe ser un email válido.' },
                        ]}
                        validateStatus={errors.email ? 'error' : undefined}
                        help={errors.email}
                    >
                        <Input maxLength={255} />
                    </Form.Item>
                </Col>
                <Col xs={24} md={12}>
                    <Form.Item
                        label="Teléfono"
                        name="phone"
                        rules={[{ required: true, message: 'El teléfono es obligatorio.' }]}
                        validateStatus={errors.phone ? 'error' : undefined}
                        help={errors.phone}
                    >
                        <Input maxLength={50} />
                    </Form.Item>
                </Col>
                <Col xs={24} md={12}>
                    <Form.Item
                        label="Dirección"
                        name="address"
                        rules={[{ required: true, message: 'La dirección es obligatoria.' }]}
                        validateStatus={errors.address ? 'error' : undefined}
                        help={errors.address}
                    >
                        <Input maxLength={255} />
                    </Form.Item>
                </Col>
            </Row>

            <Form.Item style={{ marginBottom: 0 }}>
                <Button type="primary" htmlType="submit" icon={<SaveOutlined />} loading={submitting}>
                    Actualizar perfil
                </Button>
            </Form.Item>
        </Form>
    );
}

function PasswordForm({ submitUrl }) {
    const { errors } = usePage().props;
    const [form] = Form.useForm();
    const [submitting, setSubmitting] = useState(false);

    const handleSubmit = (values) => {
        router.put(submitUrl, values, {
            preserveScroll: true,
            preserveState: true,
            onStart: () => setSubmitting(true),
            onFinish: () => setSubmitting(false),
            onSuccess: () => form.resetFields(),
        });
    };

    return (
        <Form
            form={form}
            layout="vertical"
            onFinish={handleSubmit}
            disabled={submitting}
            style={{ maxWidth: 420 }}
        >
            <Form.Item
                label="Nueva contraseña"
                name="password"
                rules={[
                    { required: true, message: 'La contraseña es obligatoria.' },
                    { min: 5, message: 'Mínimo 5 caracteres.' },
                ]}
                validateStatus={errors.password ? 'error' : undefined}
                help={errors.password}
            >
                <Input.Password autoComplete="new-password" />
            </Form.Item>

            <Form.Item
                label="Confirmar contraseña"
                name="password_confirmation"
                dependencies={['password']}
                rules={[
                    { required: true, message: 'Confirma la contraseña.' },
                    ({ getFieldValue }) => ({
                        validator(_, value) {
                            if (!value || getFieldValue('password') === value) {
                                return Promise.resolve();
                            }

                            return Promise.reject(new Error('Las contraseñas no coinciden.'));
                        },
                    }),
                ]}
            >
                <Input.Password autoComplete="new-password" />
            </Form.Item>

            <Form.Item style={{ marginBottom: 0 }}>
                <Button type="primary" htmlType="submit" icon={<SaveOutlined />} loading={submitting}>
                    Actualizar contraseña
                </Button>
            </Form.Item>
        </Form>
    );
}

export default function Index({ profile, urls }) {
    const tabItems = [
        {
            key: 'profile',
            label: 'Información',
            icon: <UserOutlined />,
            children: <ProfileForm profile={profile} submitUrl={urls.update} />,
        },
        {
            key: 'password',
            label: 'Contraseña',
            icon: <LockOutlined />,
            children: <PasswordForm submitUrl={urls.passwordUpdate} />,
        },
    ];

    return (
        <PageContainer
            title="Perfil"
            breadcrumbItems={[{ title: 'Panel de Control' }, { title: 'Perfil' }]}
            wrapInCard={false}
        >
            <Card>
                <Tabs items={tabItems} />
            </Card>
        </PageContainer>
    );
}
