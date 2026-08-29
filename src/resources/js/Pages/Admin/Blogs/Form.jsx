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
import ImageDropUpload from '../../../Components/ImageDropUpload';
import useTranslation from '../../../Hooks/useTranslation';

const { Text } = Typography;

export default function BlogForm({ blog, urls }) {
    const { errors } = usePage().props;
    const { t } = useTranslation();
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
            title={
                isEditing ? t('blogs.edit_title', { title: blog.title }) : t('blogs.create_title')
            }
            breadcrumbItems={[
                { title: t('blogs.title'), href: urls.base },
                { title: isEditing ? t('users.breadcrumb_edit') : t('users.breadcrumb_create') },
            ]}
            extra={
                <Link href={urls.base}>
                    <Button icon={<ArrowLeftOutlined />}>{t('common.back_to_list')}</Button>
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
                                label={t('common.title')}
                                name="title"
                                rules={[{ required: true, message: t('sections.title_required') }]}
                                validateStatus={errors.title ? 'error' : undefined}
                                help={errors.title}
                            >
                                <Input maxLength={255} showCount />
                            </Form.Item>

                            <Row gutter={[24, 8]}>
                                <Col xs={12}>
                                    <Form.Item label={t('blogs.popular')} name="is_popular" valuePropName="checked">
                                        <Switch checkedChildren={t('common.yes')} unCheckedChildren={t('common.no')} />
                                    </Form.Item>
                                </Col>
                                <Col xs={12}>
                                    <Form.Item label={t('listings.active')} name="status" valuePropName="checked">
                                        <Switch checkedChildren={t('common.yes')} unCheckedChildren={t('common.no')} />
                                    </Form.Item>
                                </Col>
                            </Row>
                        </Col>
                        <Col xs={24} lg={12}>
                            <Form.Item
                                label={t('blogs.image')}
                                required={!isEditing}
                                validateStatus={errors.image ? 'error' : undefined}
                                help={errors.image}
                            >
                                <ImageDropUpload
                                    value={imageFile}
                                    onChange={setImageFile}
                                    currentUrl={blog?.imageUrl}
                                    height={220}
                                />
                            </Form.Item>
                        </Col>
                        <Col xs={24}>
                            <Form.Item
                                label={t('blogs.content')}
                                name="description"
                                rules={[{ required: true, message: t('blogs.content_required') }]}
                                validateStatus={errors.description ? 'error' : undefined}
                                help={errors.description}
                            >
                                <HtmlEditor rows={14} placeholder={t('blogs.content_placeholder')} />
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
                            {isEditing ? t('blogs.submit_update') : t('blogs.submit_create')}
                        </Button>
                    </Form.Item>
                </Form>
            </Card>
        </PageContainer>
    );
}
