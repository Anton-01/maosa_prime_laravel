import React, { useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import { Button, Col, Form, Input, Row } from 'antd';
import { SaveOutlined } from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import HtmlEditor from '../../../Components/HtmlEditor';
import useTranslation from '../../../Hooks/useTranslation';

export default function Index({ footerInfo, urls }) {
    const { errors } = usePage().props;
    const { t } = useTranslation();
    const [form] = Form.useForm();
    const [submitting, setSubmitting] = useState(false);

    const handleSubmit = (values) => {
        router.post(urls.update, values, {
            preserveScroll: true,
            preserveState: true,
            onStart: () => setSubmitting(true),
            onFinish: () => setSubmitting(false),
        });
    };

    return (
        <PageContainer
            title={t('footer.title')}
            breadcrumbItems={[{ title: t('common.control_panel') }, { title: t('footer.title') }]}
        >
            <Form
                form={form}
                layout="vertical"
                onFinish={handleSubmit}
                disabled={submitting}
                initialValues={footerInfo}
            >
                <Form.Item
                    label={t('footer.short_description')}
                    name="short_description"
                    rules={[{ required: true, message: t('footer.short_description_required') }]}
                    validateStatus={errors.short_description ? 'error' : undefined}
                    help={errors.short_description}
                >
                    <HtmlEditor rows={3} placeholder={t('footer.short_description_placeholder')} />
                </Form.Item>

                <Row gutter={24}>
                    <Col xs={24} md={12}>
                        <Form.Item
                            label={t('common.address')}
                            name="address"
                            rules={[{ required: true, message: t('common.address_required') }]}
                            validateStatus={errors.address ? 'error' : undefined}
                            help={errors.address}
                        >
                            <Input maxLength={255} />
                        </Form.Item>
                    </Col>
                    <Col xs={24} md={12}>
                        <Form.Item
                            label={t('common.email')}
                            name="email"
                            rules={[
                                { required: true, message: t('common.email_required') },
                                { type: 'email', message: t('common.email_invalid') },
                            ]}
                            validateStatus={errors.email ? 'error' : undefined}
                            help={errors.email}
                        >
                            <Input maxLength={255} />
                        </Form.Item>
                    </Col>
                    <Col xs={24} md={12}>
                        <Form.Item
                            label={t('common.phone')}
                            name="phone"
                            rules={[{ required: true, message: t('common.phone_required') }]}
                            validateStatus={errors.phone ? 'error' : undefined}
                            help={errors.phone}
                        >
                            <Input maxLength={255} />
                        </Form.Item>
                    </Col>
                    <Col xs={24} md={12}>
                        <Form.Item
                            label={t('footer.copyright')}
                            name="copyright"
                            rules={[{ required: true, message: t('footer.copyright_required') }]}
                            validateStatus={errors.copyright ? 'error' : undefined}
                            help={errors.copyright}
                        >
                            <Input maxLength={255} />
                        </Form.Item>
                    </Col>
                </Row>

                <Form.Item style={{ marginBottom: 0 }}>
                    <Button type="primary" htmlType="submit" icon={<SaveOutlined />} loading={submitting}>
                        {t('common.update')}
                    </Button>
                </Form.Item>
            </Form>
        </PageContainer>
    );
}
