import React, { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import {
    Button,
    Card,
    Col,
    Form,
    Image,
    Input,
    Row,
    Space,
    Switch,
    Typography,
    Upload,
} from 'antd';
import { ArrowLeftOutlined, SaveOutlined, UploadOutlined } from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import HtmlEditor from '../../../Components/HtmlEditor';

const { Text } = Typography;

export default function BlogForm({ blog, urls }) {
    const { errors } = usePage().props;
    const [form] = Form.useForm();
    const [submitting, setSubmitting] = useState(false);
    const [imageFile, setImageFile] = useState(null);

    const isEditing = Boolean(blog);

    const initialValues = isEditing
        ? {
              title: blog.title,
              description: blog.description ?? '',
              is_popular: blog.is_popular === 1,
              status: blog.status === 1,
          }
        : {
              description: '',
              is_popular: false,
              status: true,
          };

    const handleSubmit = (values) => {
        const payload = {
            title: values.title,
            description: values.description,
            is_popular: values.is_popular ? 1 : 0,
            status: values.status ? 1 : 0,
            image: imageFile,
        };

        const visitOptions = {
            forceFormData: true,
            preserveScroll: true,
            onStart: () => setSubmitting(true),
            onFinish: () => setSubmitting(false),
        };

        if (isEditing) {
            router.post(`${urls.base}/${blog.id}`, { _method: 'put', ...payload }, visitOptions);
        } else {
            router.post(urls.base, payload, visitOptions);
        }
    };

    return (
        <PageContainer
            title={isEditing ? `Editar seminario: ${blog.title}` : 'Nuevo seminario'}
            breadcrumbItems={[
                { title: 'Seminarios', href: urls.base },
                { title: isEditing ? 'Editar' : 'Crear' },
            ]}
            extra={
                <Link href={urls.base}>
                    <Button icon={<ArrowLeftOutlined />}>Volver al listado</Button>
                </Link>
            }
            wrapInCard={false}
        >
            <Card>
                <Form
                    form={form}
                    layout="vertical"
                    onFinish={handleSubmit}
                    disabled={submitting}
                    initialValues={initialValues}
                >
                    <Row gutter={24}>
                        <Col xs={24} lg={12}>
                            <Form.Item
                                label="Título"
                                name="title"
                                rules={[{ required: true, message: 'El título es obligatorio.' }]}
                                validateStatus={errors.title ? 'error' : undefined}
                                help={errors.title}
                            >
                                <Input maxLength={255} showCount />
                            </Form.Item>

                            <Row gutter={[24, 8]}>
                                <Col xs={12}>
                                    <Form.Item label="Popular" name="is_popular" valuePropName="checked">
                                        <Switch checkedChildren="Sí" unCheckedChildren="No" />
                                    </Form.Item>
                                </Col>
                                <Col xs={12}>
                                    <Form.Item label="Activo" name="status" valuePropName="checked">
                                        <Switch checkedChildren="Sí" unCheckedChildren="No" />
                                    </Form.Item>
                                </Col>
                            </Row>
                        </Col>
                        <Col xs={24} lg={12}>
                            <Form.Item
                                label="Imagen"
                                required={!isEditing}
                                validateStatus={errors.image ? 'error' : undefined}
                                help={errors.image}
                            >
                                <Space direction="vertical" size="small">
                                    {blog?.imageUrl && !imageFile && (
                                        <Image
                                            src={blog.imageUrl}
                                            alt="Imagen actual"
                                            width={200}
                                            style={{ borderRadius: 6 }}
                                        />
                                    )}
                                    <Upload
                                        accept="image/*"
                                        maxCount={1}
                                        showUploadList={false}
                                        beforeUpload={(file) => {
                                            setImageFile(file);
                                            return false;
                                        }}
                                    >
                                        <Button icon={<UploadOutlined />}>Seleccionar imagen</Button>
                                    </Upload>
                                    {imageFile && <Text type="secondary">{imageFile.name}</Text>}
                                </Space>
                            </Form.Item>
                        </Col>
                        <Col xs={24}>
                            <Form.Item
                                label="Contenido"
                                name="description"
                                rules={[{ required: true, message: 'El contenido es obligatorio.' }]}
                                validateStatus={errors.description ? 'error' : undefined}
                                help={errors.description}
                            >
                                <HtmlEditor rows={14} placeholder="Contenido de la publicación" />
                            </Form.Item>
                        </Col>
                    </Row>

                    <Form.Item style={{ marginBottom: 0 }}>
                        <Button
                            type="primary"
                            htmlType="submit"
                            icon={<SaveOutlined />}
                            loading={submitting}
                        >
                            {isEditing ? 'Actualizar seminario' : 'Crear seminario'}
                        </Button>
                    </Form.Item>
                </Form>
            </Card>
        </PageContainer>
    );
}
