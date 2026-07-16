import React, { useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import { Button, Form, Image, Input, Space, Typography, Upload } from 'antd';
import { SaveOutlined, UploadOutlined } from '@ant-design/icons';
import HtmlEditor from '../../../../Components/HtmlEditor';

const { Text } = Typography;

export default function AboutForm({ about, submitUrl }) {
    const { errors } = usePage().props;
    const [form] = Form.useForm();
    const [submitting, setSubmitting] = useState(false);
    const [imageFile, setImageFile] = useState(null);
    const [previewUrl, setPreviewUrl] = useState(about.imageUrl);

    const handleSelectFile = (file) => {
        setImageFile(file);
        setPreviewUrl(URL.createObjectURL(file));

        return false;
    };

    const handleSubmit = (values) => {
        router.post(
            submitUrl,
            {
                image: imageFile,
                old_image: about.image,
                video_url: values.video_url,
                description: values.description,
                button_url: values.button_url,
            },
            {
                forceFormData: true,
                preserveScroll: true,
                preserveState: true,
                onStart: () => setSubmitting(true),
                onFinish: () => setSubmitting(false),
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
                video_url: about.video_url,
                description: about.description ?? '',
                button_url: about.button_url,
            }}
            style={{ maxWidth: 860 }}
        >
            <Form.Item
                label="Imagen"
                validateStatus={errors.image ? 'error' : undefined}
                help={errors.image}
            >
                <Space direction="vertical" size="small">
                    {previewUrl && (
                        <Image
                            src={previewUrl}
                            alt="Imagen de la página Sobre nosotros"
                            width={320}
                            style={{ borderRadius: 8, objectFit: 'cover' }}
                        />
                    )}
                    <Upload accept="image/*" maxCount={1} showUploadList={false} beforeUpload={handleSelectFile}>
                        <Button icon={<UploadOutlined />}>Seleccionar imagen</Button>
                    </Upload>
                    {imageFile && <Text type="secondary">{imageFile.name}</Text>}
                </Space>
            </Form.Item>

            <Form.Item
                label="URL del video"
                name="video_url"
                rules={[
                    { required: true, message: 'La URL del video es obligatoria.' },
                    { type: 'url', message: 'Debe ser una URL válida.' },
                ]}
                validateStatus={errors.video_url ? 'error' : undefined}
                help={errors.video_url}
            >
                <Input placeholder="https://..." />
            </Form.Item>

            <Form.Item
                label="Descripción"
                name="description"
                rules={[{ required: true, message: 'La descripción es obligatoria.' }]}
                validateStatus={errors.description ? 'error' : undefined}
                help={errors.description}
            >
                <HtmlEditor placeholder="Contenido de la página Sobre nosotros" />
            </Form.Item>

            <Form.Item
                label="URL del botón"
                name="button_url"
                rules={[{ type: 'url', message: 'Debe ser una URL válida.' }]}
                validateStatus={errors.button_url ? 'error' : undefined}
                help={errors.button_url}
            >
                <Input placeholder="https://..." />
            </Form.Item>

            <Form.Item>
                <Button type="primary" htmlType="submit" icon={<SaveOutlined />} loading={submitting}>
                    Actualizar
                </Button>
            </Form.Item>
        </Form>
    );
}
