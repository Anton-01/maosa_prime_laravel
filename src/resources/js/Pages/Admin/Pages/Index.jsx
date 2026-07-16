import React, { useState } from 'react';
import { Card, Tabs } from 'antd';
import {
    EnvironmentOutlined,
    FileProtectOutlined,
    FileTextOutlined,
    InfoCircleOutlined,
} from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import AboutForm from './Partials/AboutForm';
import ContactForm from './Partials/ContactForm';
import HtmlContentForm from './Partials/HtmlContentForm';

export default function Index({ initialTab, about, contact, privacyPolicy, terms, urls }) {
    const [activeTab, setActiveTab] = useState(initialTab || 'about');

    const handleTabChange = (tabKey) => {
        setActiveTab(tabKey);
        window.history.replaceState(window.history.state, '', `?tab=${tabKey}`);
    };

    const tabItems = [
        {
            key: 'about',
            label: 'Sobre nosotros',
            icon: <InfoCircleOutlined />,
            children: <AboutForm about={about} submitUrl={urls.aboutUpdate} />,
        },
        {
            key: 'contact',
            label: 'Contacto',
            icon: <EnvironmentOutlined />,
            children: <ContactForm contact={contact} submitUrl={urls.contactUpdate} />,
        },
        {
            key: 'privacy-policy',
            label: 'Política de privacidad',
            icon: <FileProtectOutlined />,
            children: (
                <HtmlContentForm
                    content={privacyPolicy.description}
                    submitUrl={urls.privacyPolicyUpdate}
                    description="Contenido de la página de política de privacidad."
                />
            ),
        },
        {
            key: 'terms',
            label: 'Términos y condiciones',
            icon: <FileTextOutlined />,
            children: (
                <HtmlContentForm
                    content={terms.description}
                    submitUrl={urls.termsUpdate}
                    description="Contenido de la página de términos y condiciones."
                />
            ),
        },
    ];

    return (
        <PageContainer
            title="Páginas"
            breadcrumbItems={[{ title: 'Panel de Control' }, { title: 'Páginas' }]}
            wrapInCard={false}
        >
            <Card>
                <Tabs activeKey={activeTab} onChange={handleTabChange} items={tabItems} />
            </Card>
        </PageContainer>
    );
}
