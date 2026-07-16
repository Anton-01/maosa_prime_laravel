import React, { useState } from 'react';
import { Card, Tabs } from 'antd';
import {
    FontSizeOutlined,
    GlobalOutlined,
    PictureOutlined,
    StarOutlined,
} from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import BannerForm from './Partials/BannerForm';
import FeaturesPanel from './Partials/FeaturesPanel';
import SectionTitlesForm from './Partials/SectionTitlesForm';

export default function Index({
    initialTab,
    privateHero,
    publicHero,
    features,
    sectionTitles,
    urls,
}) {
    const [activeTab, setActiveTab] = useState(initialTab || 'private-banner');

    const handleTabChange = (tabKey) => {
        setActiveTab(tabKey);

        // Keep the tab in the URL so refresh/back preserve the selection,
        // without triggering a server round trip.
        window.history.replaceState(window.history.state, '', `?tab=${tabKey}`);
    };

    const tabItems = [
        {
            key: 'private-banner',
            label: 'Banner',
            icon: <PictureOutlined />,
            children: (
                <BannerForm
                    hero={privateHero}
                    submitUrl={urls.privateHeroUpdate}
                    description="Banner principal que ven los usuarios autenticados."
                />
            ),
        },
        {
            key: 'public-banner',
            label: 'Banner público',
            icon: <GlobalOutlined />,
            children: (
                <BannerForm
                    hero={publicHero}
                    submitUrl={urls.publicHeroUpdate}
                    description="Banner que ven los visitantes sin sesión iniciada."
                />
            ),
        },
        {
            key: 'features',
            label: 'Nuestras funciones',
            icon: <StarOutlined />,
            children: <FeaturesPanel features={features} baseUrl={urls.featuresBase} />,
        },
        {
            key: 'titles',
            label: 'Títulos de secciones',
            icon: <FontSizeOutlined />,
            children: (
                <SectionTitlesForm
                    sectionTitles={sectionTitles}
                    submitUrl={urls.sectionTitlesUpdate}
                />
            ),
        },
    ];

    return (
        <PageContainer
            title="Secciones"
            breadcrumbItems={[{ title: 'Panel de Control' }, { title: 'Secciones' }]}
            wrapInCard={false}
        >
            <Card>
                <Tabs activeKey={activeTab} onChange={handleTabChange} items={tabItems} />
            </Card>
        </PageContainer>
    );
}
