import React, { useState } from 'react';
import { Card, Tabs } from 'antd';
import {
    EnvironmentOutlined,
    FileProtectOutlined,
    FileTextOutlined,
    InfoCircleOutlined,
} from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import useTranslation from '../../../Hooks/useTranslation';
import AboutForm from './Partials/AboutForm';
import ContactForm from './Partials/ContactForm';
import HtmlContentForm from './Partials/HtmlContentForm';

export default function Index({ initialTab, about, contact, privacyPolicy, terms, urls }) {
    const { t } = useTranslation();
    const [activeTab, setActiveTab] = useState(initialTab || 'about');

    const handleTabChange = (tabKey) => {
        setActiveTab(tabKey);
        window.history.replaceState(window.history.state, '', `?tab=${tabKey}`);
    };

    const tabItems = [
        {
            key: 'about',
            label: t('pages.tab_about'),
            icon: <InfoCircleOutlined />,
            children: <AboutForm about={about} submitUrl={urls.aboutUpdate} />,
        },
        {
            key: 'contact',
            label: t('pages.tab_contact'),
            icon: <EnvironmentOutlined />,
            children: <ContactForm contact={contact} submitUrl={urls.contactUpdate} />,
        },
        {
            key: 'privacy-policy',
            label: t('pages.tab_privacy'),
            icon: <FileProtectOutlined />,
            children: (
                <HtmlContentForm
                    content={privacyPolicy.description}
                    submitUrl={urls.privacyPolicyUpdate}
                    description={t('pages.privacy_description')}
                />
            ),
        },
        {
            key: 'terms',
            label: t('pages.tab_terms'),
            icon: <FileTextOutlined />,
            children: (
                <HtmlContentForm
                    content={terms.description}
                    submitUrl={urls.termsUpdate}
                    description={t('pages.terms_description')}
                />
            ),
        },
    ];

    return (
        <PageContainer
            title={t('pages.title')}
            breadcrumbItems={[{ title: t('common.control_panel') }, { title: t('pages.title') }]}
            wrapInCard={false}
        >
            <Card>
                <Tabs activeKey={activeTab} onChange={handleTabChange} items={tabItems} />
            </Card>
        </PageContainer>
    );
}
