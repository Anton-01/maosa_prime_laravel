import React, { useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import { Button, Card, ColorPicker, Form, Image, Input, Space, Tabs, Typography, Upload } from 'antd';
import {
    BgColorsOutlined,
    PictureOutlined,
    SaveOutlined,
    SettingOutlined,
    UploadOutlined,
} from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import ImageDropUpload from '../../../Components/ImageDropUpload';

const { Text } = Typography;

function GeneralSettingsForm({ general, submitUrl }) {
    const { errors } = usePage().props;
    const [form] = Form.useForm();
    const [submitting, setSubmitting] = useState(false);

    const handleSubmit = (values) => {
        router.post(submitUrl, values, {
            preserveScroll: true,
            preserveState: true,
            onStart: () => setSubmitting(true),
            onFinish: () => setSubmitting(false),
        });
    };

    return (
        <Form
            form={form}
            layout="vertical"
            onFinish={handleSubmit}
            disabled={submitting}
            initialValues={general}
            style={{ maxWidth: 640 }}
        >
            <Form.Item
                label="Nombre del sitio"
                name="site_name"
                rules={[{ required: true, message: 'El nombre del sitio es obligatorio.' }]}
                validateStatus={errors.site_name ? 'error' : undefined}
                help={errors.site_name}
            >
                <Input maxLength={255} />
            </Form.Item>

            <Form.Item
                label="Email del sitio"
                name="site_email"
                rules={[
                    { required: true, message: 'El email del sitio es obligatorio.' },
                    { type: 'email', message: 'Debe ser un email válido.' },
                ]}
                validateStatus={errors.site_email ? 'error' : undefined}
                help={errors.site_email}
            >
                <Input maxLength={255} />
            </Form.Item>

            <Form.Item
                label="Teléfono del sitio"
                name="site_phone"
                rules={[{ required: true, message: 'El teléfono del sitio es obligatorio.' }]}
                validateStatus={errors.site_phone ? 'error' : undefined}
                help={errors.site_phone}
            >
                <Input maxLength={255} />
            </Form.Item>

            <Form.Item style={{ marginBottom: 0 }}>
                <Button type="primary" htmlType="submit" icon={<SaveOutlined />} loading={submitting}>
                    Actualizar
                </Button>
            </Form.Item>
        </Form>
    );
}

function LogoSettingsForm({ logos, submitUrl }) {
    const { errors } = usePage().props;
    const [submitting, setSubmitting] = useState(false);
    const [logoFile, setLogoFile] = useState(null);
    const [faviconFile, setFaviconFile] = useState(null);

    const handleSubmit = () => {
        router.post(
            submitUrl,
            {
                logo: logoFile,
                favicon: faviconFile,
                old_logo: logos.logo,
                old_favicon: logos.favicon,
            },
            {
                forceFormData: true,
                preserveScroll: true,
                preserveState: true,
                onStart: () => setSubmitting(true),
                onFinish: () => {
                    setSubmitting(false);
                    setLogoFile(null);
                    setFaviconFile(null);
                },
            },
        );
    };

    return (
        <Space direction="vertical" size="large" style={{ display: 'flex', maxWidth: 640 }}>
            <div>
                <Text strong style={{ display: 'block', marginBottom: 8 }}>
                    Logo
                </Text>
                <ImageDropUpload
                    value={logoFile}
                    onChange={setLogoFile}
                    currentUrl={logos.logoUrl}
                    height={140}
                />
                {errors.logo && <Text type="danger">{errors.logo}</Text>}
            </div>

            <div>
                <Text strong style={{ display: 'block', marginBottom: 8 }}>
                    Favicon
                </Text>
                <ImageDropUpload
                    value={faviconFile}
                    onChange={setFaviconFile}
                    currentUrl={logos.faviconUrl}
                    height={120}
                />
                {errors.favicon && <Text type="danger">{errors.favicon}</Text>}
            </div>

            <Button
                type="primary"
                icon={<SaveOutlined />}
                loading={submitting}
                disabled={!logoFile && !faviconFile}
                onClick={handleSubmit}
            >
                Actualizar
            </Button>
        </Space>
    );
}

function AppearanceSettingsForm({ appearance, submitUrl }) {
    const { errors } = usePage().props;
    const [color, setColor] = useState(appearance.site_default_color || '#6777ef');
    const [submitting, setSubmitting] = useState(false);

    const handleSubmit = () => {
        router.post(submitUrl, { site_default_color: color }, {
            preserveScroll: true,
            preserveState: true,
            onStart: () => setSubmitting(true),
            onFinish: () => setSubmitting(false),
        });
    };

    return (
        <Space direction="vertical" size="large" style={{ display: 'flex', maxWidth: 640 }}>
            <div>
                <Text strong style={{ display: 'block', marginBottom: 8 }}>
                    Color por defecto del sitio
                </Text>
                <Space>
                    <ColorPicker
                        value={color}
                        showText
                        onChangeComplete={(value) => setColor(value.toHexString())}
                    />
                    <Text type="secondary">{color}</Text>
                </Space>
                {errors.site_default_color && (
                    <Text type="danger" style={{ display: 'block', marginTop: 8 }}>
                        {errors.site_default_color}
                    </Text>
                )}
            </div>

            <Button type="primary" icon={<SaveOutlined />} loading={submitting} onClick={handleSubmit}>
                Actualizar
            </Button>
        </Space>
    );
}

export default function Index({ initialTab, general, logos, appearance, urls }) {
    const [activeTab, setActiveTab] = useState(initialTab || 'general');

    const handleTabChange = (tabKey) => {
        setActiveTab(tabKey);
        window.history.replaceState(window.history.state, '', `?tab=${tabKey}`);
    };

    const tabItems = [
        {
            key: 'general',
            label: 'Ajustes generales',
            icon: <SettingOutlined />,
            children: <GeneralSettingsForm general={general} submitUrl={urls.generalUpdate} />,
        },
        {
            key: 'logos',
            label: 'Logo y favicon',
            icon: <PictureOutlined />,
            children: <LogoSettingsForm logos={logos} submitUrl={urls.logoUpdate} />,
        },
        {
            key: 'appearance',
            label: 'Apariencia',
            icon: <BgColorsOutlined />,
            children: (
                <AppearanceSettingsForm appearance={appearance} submitUrl={urls.appearanceUpdate} />
            ),
        },
    ];

    return (
        <PageContainer
            title="Ajustes"
            breadcrumbItems={[{ title: 'Panel de Control' }, { title: 'Ajustes' }]}
            wrapInCard={false}
        >
            <Card>
                <Tabs
                    activeKey={activeTab}
                    onChange={handleTabChange}
                    items={tabItems}
                    tabPosition="left"
                />
            </Card>
        </PageContainer>
    );
}
