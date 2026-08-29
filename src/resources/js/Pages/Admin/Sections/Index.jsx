import React, { useState } from 'react';
import { Card, Tabs } from 'antd';
import {
    FontSizeOutlined,
    GlobalOutlined,
    PictureOutlined,
    StarOutlined,
} from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import useTranslation from '../../../Hooks/useTranslation';
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
    const { t } = useTranslation();
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
            label: t('sections.tab_banner'),
            icon: <PictureOutlined />,
            children: (
                <BannerForm
                    hero={privateHero}
                    submitUrl={urls.privateHeroUpdate}
                    description={t('sections.private_banner_description')}
                />
            ),
        },
        {
            key: 'public-banner',
            label: t('sections.tab_public_banner'),
            icon: <GlobalOutlined />,
            children: (
                <BannerForm
                    hero={publicHero}
                    submitUrl={urls.publicHeroUpdate}
                    description={t('sections.public_banner_description')}
                />
            ),
        },
        {
            key: 'features',
            label: t('sections.tab_features'),
            icon: <StarOutlined />,
            children: <FeaturesPanel features={features} baseUrl={urls.featuresBase} />,
        },
        {
            key: 'titles',
            label: t('sections.tab_titles'),
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
            title={t('sections.title')}
            breadcrumbItems={[{ title: t('common.control_panel') }, { title: t('sections.title') }]}
            wrapInCard={false}
        >
            <Card>
                <Tabs activeKey={activeTab} onChange={handleTabChange} items={tabItems} />
            </Card>
        </PageContainer>
    );
}
