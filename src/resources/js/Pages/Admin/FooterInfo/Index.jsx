import React, { useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import { Button, Col, Form, Input, Row } from 'antd';
import { SaveOutlined } from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import HtmlEditor from '../../../Components/HtmlEditor';

export default function Index({ footerInfo, urls }) {
    const { errors } = usePage().props;
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
            title="Footer"
            breadcrumbItems={[{ title: 'Panel de Control' }, { title: 'Footer' }]}
        >
            <Form
                form={form}
                layout="vertical"
                onFinish={handleSubmit}
                disabled={submitting}
                initialValues={footerInfo}
            >
                <Form.Item
                    label="Descripción corta"
                    name="short_description"
                    rules={[{ required: true, message: 'La descripción corta es obligatoria.' }]}
                    validateStatus={errors.short_description ? 'error' : undefined}
                    help={errors.short_description}
                >
                    <HtmlEditor rows={3} placeholder="Descripción corta del footer" />
                </Form.Item>

                <Row gutter={24}>
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
                            <Input maxLength={255} />
                        </Form.Item>
                    </Col>
                    <Col xs={24} md={12}>
                        <Form.Item
                            label="Copyright"
                            name="copyright"
                            rules={[{ required: true, message: 'El copyright es obligatorio.' }]}
                            validateStatus={errors.copyright ? 'error' : undefined}
                            help={errors.copyright}
                        >
                            <Input maxLength={255} />
                        </Form.Item>
                    </Col>
                </Row>

                <Form.Item style={{ marginBottom: 0 }}>
                    <Button type="primary" htmlType="submit" icon={<SaveOutlined />} loading={submitting}>
                        Actualizar
                    </Button>
                </Form.Item>
            </Form>
        </PageContainer>
    );
}
