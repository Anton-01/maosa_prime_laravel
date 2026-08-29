import React from 'react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import {
    Avatar,
    Button,
    Card,
    Col,
    Form,
    Input,
    Row,
    Space,
    Tag,
    Typography,
    Upload,
} from 'antd';
import {
    AppstoreOutlined,
    DollarOutlined,
    KeyOutlined,
    MailOutlined,
    SaveOutlined,
    UploadOutlined,
    UserOutlined,
} from '@ant-design/icons';

import asset from '../../Utils/asset';
import ImageDropUpload from '../../Components/ImageDropUpload';

const { Title, Text } = Typography;

function ImageUpload({ label, preview, onPick }) {
    return (
        <Form.Item label={label}>
            <ImageDropUpload currentUrl={preview} onChange={onPick} height={150} hint={null} />
        </Form.Item>
    );
}

export default function Profile({ profile }) {
    const { appUrls } = usePage().props;

    const infoForm = useForm({
        name: profile.name || '',
        email: profile.email || '',
        phone: profile.phone || '',
        address: profile.address || '',
        avatar: null,
        banner: null,
        old_avatar: profile.avatar || '',
        old_banner: profile.banner || '',
    });

    const passwordForm = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    const [avatarPreview, setAvatarPreview] = React.useState(
        profile.avatar ? asset(profile.avatar) : null,
    );
    const [bannerPreview, setBannerPreview] = React.useState(
        profile.banner ? asset(profile.banner) : null,
    );

    const pickAvatar = (file) => {
        infoForm.setData('avatar', file);
        setAvatarPreview(URL.createObjectURL(file));
    };
    const pickBanner = (file) => {
        infoForm.setData('banner', file);
        setBannerPreview(URL.createObjectURL(file));
    };

    const submitInfo = () => {
        infoForm
            .transform((data) => {
                const out = { ...data };
                if (!(out.avatar instanceof File)) delete out.avatar;
                if (!(out.banner instanceof File)) delete out.banner;
                return out;
            })
            .put(appUrls.userProfile, { preserveScroll: true, forceFormData: true });
    };

    const submitPassword = () => {
        passwordForm.put('/user/profile-password', { preserveScroll: true });
    };

    return (
        <>
            <Head title="Mi perfil" />

            {/* Welcome card */}
            <Card styles={{ body: { padding: 0 } }} style={{ marginBottom: 24, overflow: 'hidden' }}>
                <div
                    style={{
                        height: 110,
                        background: bannerPreview
                            ? `url(${bannerPreview}) center/cover`
                            : 'linear-gradient(135deg, var(--brand-color, #6777ef), #0f1729)',
                    }}
                />
                <div style={{ padding: '0 24px 24px', display: 'flex', gap: 18, alignItems: 'flex-end' }}>
                    <Avatar
                        size={80}
                        src={avatarPreview || undefined}
                        icon={<UserOutlined />}
                        style={{ marginTop: -40, border: '4px solid #fff', background: '#eee' }}
                    />
                    <div style={{ paddingTop: 12 }}>
                        <Title level={4} style={{ margin: 0 }}>
                            {profile.name}
                        </Title>
                        <Text type="secondary">
                            <MailOutlined /> {profile.email}
                        </Text>
                        <div style={{ marginTop: 8 }}>
                            <Space wrap>
                                <Tag color="blue">{profile.user_type}</Tag>
                                {profile.can_view_price_table && (
                                    <Tag color="orange" icon={<DollarOutlined />}>
                                        Precios Internacionales
                                    </Tag>
                                )}
                            </Space>
                        </div>
                    </div>
                </div>
            </Card>

            {/* Quick actions */}
            <Row gutter={[16, 16]} style={{ marginBottom: 24 }}>
                <Col xs={24} sm={8}>
                    <Link href={appUrls.userProfile}>
                        <Card hoverable style={{ textAlign: 'center' }}>
                            <UserOutlined style={{ fontSize: 24, color: 'var(--brand-color, #6777ef)' }} />
                            <div style={{ marginTop: 8 }}>Mi Perfil</div>
                        </Card>
                    </Link>
                </Col>
                {profile.can_view_price_table && (
                    <Col xs={24} sm={8}>
                        <Link href={appUrls.priceTable}>
                            <Card hoverable style={{ textAlign: 'center' }}>
                                <DollarOutlined style={{ fontSize: 24, color: '#fa8c16' }} />
                                <div style={{ marginTop: 8 }}>Precios Internacionales</div>
                            </Card>
                        </Link>
                    </Col>
                )}
                <Col xs={24} sm={8}>
                    <Link href={appUrls.listings}>
                        <Card hoverable style={{ textAlign: 'center' }}>
                            <AppstoreOutlined style={{ fontSize: 24, color: '#13c2c2' }} />
                            <div style={{ marginTop: 8 }}>Ver Proveedores</div>
                        </Card>
                    </Link>
                </Col>
            </Row>

            {/* Info form */}
            <Card title="Información básica" style={{ marginBottom: 24 }}>
                <Form layout="vertical" onFinish={submitInfo} disabled={infoForm.processing}>
                    <Row gutter={24}>
                        <Col xs={24} lg={16}>
                            <Row gutter={16}>
                                <Col xs={24} md={12}>
                                    <Form.Item
                                        label="Nombre"
                                        required
                                        validateStatus={infoForm.errors.name ? 'error' : undefined}
                                        help={infoForm.errors.name}
                                    >
                                        <Input
                                            value={infoForm.data.name}
                                            onChange={(e) => infoForm.setData('name', e.target.value)}
                                        />
                                    </Form.Item>
                                </Col>
                                <Col xs={24} md={12}>
                                    <Form.Item
                                        label="Teléfono"
                                        required
                                        validateStatus={infoForm.errors.phone ? 'error' : undefined}
                                        help={infoForm.errors.phone}
                                    >
                                        <Input
                                            value={infoForm.data.phone}
                                            onChange={(e) => infoForm.setData('phone', e.target.value)}
                                        />
                                    </Form.Item>
                                </Col>
                                <Col xs={24}>
                                    <Form.Item
                                        label="Correo electrónico"
                                        required
                                        validateStatus={infoForm.errors.email ? 'error' : undefined}
                                        help={infoForm.errors.email}
                                    >
                                        <Input
                                            type="email"
                                            value={infoForm.data.email}
                                            onChange={(e) => infoForm.setData('email', e.target.value)}
                                        />
                                    </Form.Item>
                                </Col>
                                <Col xs={24}>
                                    <Form.Item
                                        label="Dirección"
                                        required
                                        validateStatus={infoForm.errors.address ? 'error' : undefined}
                                        help={infoForm.errors.address}
                                    >
                                        <Input
                                            value={infoForm.data.address}
                                            onChange={(e) => infoForm.setData('address', e.target.value)}
                                        />
                                    </Form.Item>
                                </Col>
                            </Row>
                        </Col>
                        <Col xs={24} lg={8}>
                            <ImageUpload label="Foto de perfil" preview={avatarPreview} onPick={pickAvatar} />
                            <ImageUpload label="Imagen de fondo" preview={bannerPreview} onPick={pickBanner} />
                        </Col>
                    </Row>

                    <Button
                        type="primary"
                        htmlType="submit"
                        icon={<SaveOutlined />}
                        loading={infoForm.processing}
                    >
                        Guardar cambios
                    </Button>
                </Form>
            </Card>

            {/* Password form */}
            <Card title="Cambiar contraseña">
                <Form layout="vertical" onFinish={submitPassword} disabled={passwordForm.processing}>
                    <Row gutter={16}>
                        <Col xs={24} md={8}>
                            <Form.Item
                                label="Contraseña actual"
                                validateStatus={passwordForm.errors.current_password ? 'error' : undefined}
                                help={passwordForm.errors.current_password}
                            >
                                <Input.Password
                                    value={passwordForm.data.current_password}
                                    onChange={(e) =>
                                        passwordForm.setData('current_password', e.target.value)
                                    }
                                />
                            </Form.Item>
                        </Col>
                        <Col xs={24} md={8}>
                            <Form.Item
                                label="Nueva contraseña"
                                validateStatus={passwordForm.errors.password ? 'error' : undefined}
                                help={passwordForm.errors.password}
                            >
                                <Input.Password
                                    value={passwordForm.data.password}
                                    onChange={(e) => passwordForm.setData('password', e.target.value)}
                                />
                            </Form.Item>
                        </Col>
                        <Col xs={24} md={8}>
                            <Form.Item label="Confirmar contraseña">
                                <Input.Password
                                    value={passwordForm.data.password_confirmation}
                                    onChange={(e) =>
                                        passwordForm.setData('password_confirmation', e.target.value)
                                    }
                                />
                            </Form.Item>
                        </Col>
                    </Row>
                    <Button
                        htmlType="submit"
                        icon={<KeyOutlined />}
                        loading={passwordForm.processing}
                    >
                        Actualizar contraseña
                    </Button>
                </Form>
            </Card>
        </>
    );
}
