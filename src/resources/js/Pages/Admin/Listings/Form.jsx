import React, { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import {
    Alert,
    Button,
    Card,
    Col,
    Form,
    Image,
    Input,
    Row,
    Select,
    Space,
    Switch,
    Tabs,
    Typography,
    Upload,
} from 'antd';
import {
    ArrowLeftOutlined,
    DeleteOutlined,
    GlobalOutlined,
    InfoCircleOutlined,
    PlusOutlined,
    SaveOutlined,
    SearchOutlined,
    SettingOutlined,
    UploadOutlined,
} from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import HtmlEditor from '../../../Components/HtmlEditor';
import ImageDropUpload from '../../../Components/ImageDropUpload';

const { Text } = Typography;

function ImageField({ label, currentUrl, file, onSelect, error, required }) {
    return (
        <Form.Item label={label} required={required} validateStatus={error ? 'error' : undefined} help={error}>
            <ImageDropUpload value={file} onChange={onSelect} currentUrl={currentUrl} height={180} />
        </Form.Item>
    );
}

export default function ListingForm({ listing, options, urls }) {
    const { errors } = usePage().props;
    const [form] = Form.useForm();
    const [submitting, setSubmitting] = useState(false);
    const [imageFile, setImageFile] = useState(null);
    const [thumbnailFile, setThumbnailFile] = useState(null);

    const isEditing = Boolean(listing);
    const hasErrors = Object.keys(errors).length > 0;

    const initialValues = isEditing
        ? {
              ...listing,
              social_links: listing.social_links?.length ? listing.social_links : [],
              status: listing.status === 1,
              is_featured: listing.is_featured === 1,
              is_verified: listing.is_verified === 1,
              is_previliged: listing.is_previliged === 1,
          }
        : {
              social_links: [],
              status: true,
              is_featured: false,
              is_verified: false,
              is_previliged: false,
          };

    const handleSubmit = (values) => {
        const payload = {
            title: values.title,
            category: values.category,
            location: values.location,
            address: values.address,
            phone: values.phone,
            email: values.email,
            website: values.website ?? '',
            description: values.description,
            google_map_embed_code: values.google_map_embed_code ?? '',
            seo_title: values.seo_title ?? '',
            seo_description: values.seo_description ?? '',
            status: values.status ? 1 : 0,
            is_featured: values.is_featured ? 1 : 0,
            is_verified: values.is_verified ? 1 : 0,
            is_previliged: values.is_previliged ? 1 : 0,
            social_links: (values.social_links ?? []).filter(
                (link) => link?.social_network_id && link?.url,
            ),
            image: imageFile,
            thumbnail_image: thumbnailFile,
        };

        const visitOptions = {
            forceFormData: true,
            preserveScroll: true,
            onStart: () => setSubmitting(true),
            onFinish: () => setSubmitting(false),
        };

        if (isEditing) {
            router.post(`${urls.base}/${listing.id}`, { _method: 'put', ...payload }, visitOptions);
        } else {
            router.post(urls.base, payload, visitOptions);
        }
    };

    const generalTab = (
        <Row gutter={24}>
            <Col xs={24} lg={12}>
                <Form.Item
                    label="Nombre"
                    name="title"
                    rules={[{ required: true, message: 'El nombre es obligatorio.' }]}
                    validateStatus={errors.title ? 'error' : undefined}
                    help={errors.title}
                >
                    <Input maxLength={255} showCount />
                </Form.Item>

                <Form.Item
                    label="Categoría"
                    name="category"
                    rules={[{ required: true, message: 'La categoría es obligatoria.' }]}
                    validateStatus={errors.category ? 'error' : undefined}
                    help={errors.category}
                >
                    <Select options={options.categories} showSearch optionFilterProp="label" placeholder="Selecciona una categoría" />
                </Form.Item>

                <Form.Item
                    label="Ubicación"
                    name="location"
                    rules={[{ required: true, message: 'La ubicación es obligatoria.' }]}
                    validateStatus={errors.location ? 'error' : undefined}
                    help={errors.location}
                >
                    <Select options={options.locations} showSearch optionFilterProp="label" placeholder="Selecciona una ubicación" />
                </Form.Item>
            </Col>
            <Col xs={24} lg={12}>
                <ImageField
                    label="Imagen principal"
                    currentUrl={listing?.imageUrl}
                    file={imageFile}
                    onSelect={setImageFile}
                    error={errors.image}
                    required={!isEditing}
                />
                <ImageField
                    label="Imagen miniatura"
                    currentUrl={listing?.thumbnailImageUrl}
                    file={thumbnailFile}
                    onSelect={setThumbnailFile}
                    error={errors.thumbnail_image}
                    required={!isEditing}
                />
            </Col>
            <Col xs={24}>
                <Form.Item
                    label="Descripción"
                    name="description"
                    rules={[{ required: true, message: 'La descripción es obligatoria.' }]}
                    validateStatus={errors.description ? 'error' : undefined}
                    help={errors.description}
                >
                    <HtmlEditor rows={10} placeholder="Descripción del proveedor" />
                </Form.Item>
            </Col>
        </Row>
    );

    const contactTab = (
        <Row gutter={24}>
            <Col xs={24} lg={12}>
                <Form.Item
                    label="Dirección"
                    name="address"
                    rules={[{ required: true, message: 'La dirección es obligatoria.' }]}
                    validateStatus={errors.address ? 'error' : undefined}
                    help={errors.address}
                >
                    <Input maxLength={255} />
                </Form.Item>

                <Form.Item
                    label="Teléfono"
                    name="phone"
                    rules={[{ required: true, message: 'El teléfono es obligatorio.' }]}
                    validateStatus={errors.phone ? 'error' : undefined}
                    help={errors.phone}
                >
                    <Input maxLength={255} />
                </Form.Item>

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

                <Form.Item
                    label="Sitio web"
                    name="website"
                    validateStatus={errors.website ? 'error' : undefined}
                    help={errors.website}
                >
                    <Input placeholder="https://..." />
                </Form.Item>
            </Col>
            <Col xs={24} lg={12}>
                <Form.Item label="Redes sociales">
                    <Form.List name="social_links">
                        {(fields, { add, remove }) => (
                            <Space direction="vertical" size="small" style={{ display: 'flex' }}>
                                {fields.map(({ key, name, ...restField }) => (
                                    <Space key={key} align="baseline" wrap>
                                        <Form.Item
                                            {...restField}
                                            name={[name, 'social_network_id']}
                                            noStyle
                                        >
                                            <Select
                                                options={options.socialNetworks}
                                                placeholder="Red social"
                                                style={{ width: 160 }}
                                            />
                                        </Form.Item>
                                        <Form.Item {...restField} name={[name, 'url']} noStyle>
                                            <Input placeholder="https://..." style={{ width: 260 }} />
                                        </Form.Item>
                                        <Button
                                            type="text"
                                            danger
                                            icon={<DeleteOutlined />}
                                            onClick={() => remove(name)}
                                        />
                                    </Space>
                                ))}
                                <Button type="dashed" icon={<PlusOutlined />} onClick={() => add()}>
                                    Agregar red social
                                </Button>
                            </Space>
                        )}
                    </Form.List>
                </Form.Item>

                <Form.Item
                    label="Código embed de Google Maps"
                    name="google_map_embed_code"
                    validateStatus={errors.google_map_embed_code ? 'error' : undefined}
                    help={errors.google_map_embed_code}
                >
                    <Input.TextArea rows={4} placeholder='<iframe src="https://www.google.com/maps/..."></iframe>' />
                </Form.Item>
            </Col>
        </Row>
    );

    const seoTab = (
        <Row gutter={24}>
            <Col xs={24} lg={12}>
                <Form.Item
                    label="Título SEO"
                    name="seo_title"
                    validateStatus={errors.seo_title ? 'error' : undefined}
                    help={errors.seo_title}
                >
                    <Input maxLength={255} showCount />
                </Form.Item>
            </Col>
            <Col xs={24} lg={12}>
                <Form.Item
                    label="Descripción SEO"
                    name="seo_description"
                    validateStatus={errors.seo_description ? 'error' : undefined}
                    help={errors.seo_description}
                >
                    <Input.TextArea rows={3} maxLength={255} showCount />
                </Form.Item>
            </Col>
        </Row>
    );

    const settingsTab = (
        <Row gutter={[24, 8]}>
            <Col xs={12} md={6}>
                <Form.Item label="Activo" name="status" valuePropName="checked">
                    <Switch checkedChildren="Sí" unCheckedChildren="No" />
                </Form.Item>
            </Col>
            <Col xs={12} md={6}>
                <Form.Item label="Destacado" name="is_featured" valuePropName="checked">
                    <Switch checkedChildren="Sí" unCheckedChildren="No" />
                </Form.Item>
            </Col>
            <Col xs={12} md={6}>
                <Form.Item label="Verificado" name="is_verified" valuePropName="checked">
                    <Switch checkedChildren="Sí" unCheckedChildren="No" />
                </Form.Item>
            </Col>
            <Col xs={12} md={6}>
                <Form.Item label="Privilegiado" name="is_previliged" valuePropName="checked">
                    <Switch checkedChildren="Sí" unCheckedChildren="No" />
                </Form.Item>
            </Col>
        </Row>
    );

    const tabItems = [
        {
            key: 'general',
            label: 'Información general',
            icon: <InfoCircleOutlined />,
            forceRender: true,
            children: generalTab,
        },
        {
            key: 'contact',
            label: 'Contacto y redes',
            icon: <GlobalOutlined />,
            forceRender: true,
            children: contactTab,
        },
        {
            key: 'seo',
            label: 'SEO',
            icon: <SearchOutlined />,
            forceRender: true,
            children: seoTab,
        },
        {
            key: 'settings',
            label: 'Configuración',
            icon: <SettingOutlined />,
            forceRender: true,
            children: settingsTab,
        },
    ];

    return (
        <PageContainer
            title={isEditing ? `Editar proveedor: ${listing.title}` : 'Nuevo proveedor'}
            breadcrumbItems={[
                { title: 'Proveedores' },
                { title: 'Todos los Proveedores', href: urls.base },
                { title: isEditing ? 'Editar' : 'Crear' },
            ]}
            extra={
                <Link href={urls.base}>
                    <Button icon={<ArrowLeftOutlined />}>Volver al listado</Button>
                </Link>
            }
            wrapInCard={false}
        >
            {hasErrors && (
                <Alert
                    type="error"
                    showIcon
                    style={{ marginBottom: 16 }}
                    message="Revisa el formulario"
                    description="Hay campos con errores de validación en una o más pestañas."
                />
            )}

            <Card>
                <Form
                    form={form}
                    layout="vertical"
                    onFinish={handleSubmit}
                    disabled={submitting}
                    initialValues={initialValues}
                >
                    <Tabs items={tabItems} />

                    <Form.Item style={{ marginTop: 16, marginBottom: 0 }}>
                        <Button
                            type="primary"
                            htmlType="submit"
                            icon={<SaveOutlined />}
                            loading={submitting}
                        >
                            {isEditing ? 'Actualizar proveedor' : 'Crear proveedor'}
                        </Button>
                    </Form.Item>
                </Form>
            </Card>
        </PageContainer>
    );
}
