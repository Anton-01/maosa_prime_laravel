import React, { useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import { Button, Col, Divider, Form, Input, Row, Typography } from 'antd';
import { SaveOutlined } from '@ant-design/icons';
import HtmlEditor from '../../../../Components/HtmlEditor';

const { Text } = Typography;

/**
 * Section groups of the public home page. Each group edits a
 * title/subtitle pair stored in the section_titles singleton row.
 */
const SECTION_GROUPS = [
    { label: 'Nuestras funciones', titleField: 'our_feature_title', subTitleField: 'our_feature_sub_title' },
    { label: 'Nuestras categorías', titleField: 'our_categories_title', subTitleField: 'our_categories_sub_title' },
    { label: 'Nuestras ubicaciones', titleField: 'our_location_title', subTitleField: 'our_location_sub_title' },
    { label: 'Nuestras ofertas destacadas', titleField: 'our_featured_listing_title', subTitleField: 'our_featured_listing_sub_title' },
    { label: 'Nuestro blog', titleField: 'our_blog_title', subTitleField: 'our_blog_sub_title' },
];

export default function SectionTitlesForm({ sectionTitles, submitUrl }) {
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
            initialValues={sectionTitles}
            onFinish={handleSubmit}
            disabled={submitting}
        >
            <Text type="secondary">
                Título y sub título que se muestran en cada sección de la página principal.
            </Text>

            {SECTION_GROUPS.map((group, index) => (
                <React.Fragment key={group.titleField}>
                    <Divider orientation="left" orientationMargin={0} style={index === 0 ? undefined : { marginTop: 32 }}>
                        {group.label}
                    </Divider>

                    <Row gutter={24}>
                        <Col xs={24} md={10}>
                            <Form.Item
                                label="Título"
                                name={group.titleField}
                                validateStatus={errors[group.titleField] ? 'error' : undefined}
                                help={errors[group.titleField]}
                            >
                                <Input maxLength={255} placeholder={`Título de "${group.label}"`} />
                            </Form.Item>
                        </Col>
                        <Col xs={24} md={14}>
                            <Form.Item
                                label="Sub título"
                                name={group.subTitleField}
                                validateStatus={errors[group.subTitleField] ? 'error' : undefined}
                                help={errors[group.subTitleField]}
                            >
                                <HtmlEditor rows={2} placeholder={`Sub título de "${group.label}"`} />
                            </Form.Item>
                        </Col>
                    </Row>
                </React.Fragment>
            ))}

            <Form.Item style={{ marginTop: 8 }}>
                <Button type="primary" htmlType="submit" icon={<SaveOutlined />} loading={submitting}>
                    Actualizar
                </Button>
            </Form.Item>
        </Form>
    );
}
