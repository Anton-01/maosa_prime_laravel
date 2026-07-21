import React, { useState } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import {
    Button,
    Card,
    Col,
    Input,
    Row,
    Select,
    Statistic,
    Tabs,
    Typography,
} from 'antd';
import {
    AppstoreOutlined,
    EnvironmentOutlined,
    SearchOutlined,
    ShopOutlined,
    TeamOutlined,
} from '@ant-design/icons';

import asset from '../../Utils/asset';
import HtmlContent from '../../Components/HtmlContent';
import SectionHeading from '../../Components/Frontend/SectionHeading';
import ListingCard from '../../Components/Frontend/ListingCard';

const { Title, Text, Paragraph } = Typography;

function SearchBanner({ hero, categories, locations }) {
    const { settings } = usePage().props;
    const brand = settings?.color || '#6777ef';
    const [search, setSearch] = useState('');
    const [category, setCategory] = useState();
    const [location, setLocation] = useState();

    const submit = () => {
        const params = {};
        if (search) params.search = search;
        if (category) params.category = category;
        if (location) params.location = location;
        router.get('/suppliers', params);
    };

    const bg = hero?.background
        ? `linear-gradient(135deg, rgba(15,23,41,0.82), rgba(15,23,41,0.6)), url(${asset(hero.background)})`
        : `linear-gradient(135deg, ${brand}, #0f1729)`;

    return (
        <div
            style={{
                background: bg,
                backgroundSize: 'cover',
                backgroundPosition: 'center',
                padding: '64px 24px',
            }}
        >
            <Row gutter={[32, 32]} align="middle" justify="space-between">
                <Col xs={24} lg={12}>
                    {hero?.title ? (
                        <HtmlContent
                            html={hero.title}
                            style={{ color: '#fff', fontSize: 40, fontWeight: 800, lineHeight: 1.15 }}
                        />
                    ) : (
                        <Title style={{ color: '#fff', fontSize: 40 }}>
                            Encuentra los mejores proveedores
                        </Title>
                    )}
                    {hero?.sub_title && (
                        <HtmlContent
                            html={hero.sub_title}
                            style={{ color: 'rgba(255,255,255,0.85)', marginTop: 12, fontSize: 16 }}
                        />
                    )}
                </Col>

                <Col xs={24} lg={10}>
                    <Card styles={{ body: { padding: 24 } }}>
                        <Title level={4} style={{ marginTop: 0 }}>
                            Encuentra los mejores proveedores
                        </Title>
                        <Input
                            size="large"
                            placeholder="¿Qué estás buscando?"
                            prefix={<SearchOutlined />}
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            onPressEnter={submit}
                            style={{ marginBottom: 12 }}
                        />
                        <Select
                            size="large"
                            placeholder="Categorías"
                            allowClear
                            value={category}
                            onChange={setCategory}
                            style={{ width: '100%', marginBottom: 12 }}
                            options={categories?.map((c) => ({ value: c.slug, label: c.name }))}
                        />
                        <Select
                            size="large"
                            placeholder="Ubicaciones"
                            allowClear
                            value={location}
                            onChange={setLocation}
                            style={{ width: '100%', marginBottom: 16 }}
                            options={locations?.map((l) => ({ value: l.slug, label: l.name }))}
                        />
                        <Button type="primary" size="large" block icon={<SearchOutlined />} onClick={submit}>
                            Buscar
                        </Button>
                    </Card>
                </Col>
            </Row>
        </div>
    );
}

function CategorySlider({ categories }) {
    if (!categories?.length) return null;
    return (
        <div style={{ padding: '32px 24px', background: '#fff' }}>
            <div style={{ display: 'flex', gap: 16, overflowX: 'auto', paddingBottom: 8 }}>
                {categories.map((category) => (
                    <Link
                        key={category.id}
                        href={`/suppliers?category=${category.slug}`}
                        style={{ textAlign: 'center', minWidth: 110 }}
                    >
                        <Card hoverable styles={{ body: { padding: 16 } }} style={{ width: 110 }}>
                            <img
                                src={asset(category.image_icon)}
                                alt={category.name}
                                style={{ width: 48, height: 48, objectFit: 'contain' }}
                            />
                            <Text style={{ display: 'block', marginTop: 8, fontSize: 13 }} ellipsis>
                                {category.name}
                            </Text>
                        </Card>
                    </Link>
                ))}
            </div>
        </div>
    );
}

function Features({ sectionTitle, ourFeatures }) {
    if (!ourFeatures?.length) return null;
    return (
        <div style={{ padding: '56px 24px' }}>
            <SectionHeading
                title={sectionTitle?.our_feature_title}
                subtitle={sectionTitle?.our_feature_sub_title}
            />
            <Row gutter={[24, 24]}>
                {ourFeatures.map((feature, index) => (
                    <Col xs={24} md={12} lg={8} key={feature.id}>
                        <Card style={{ height: '100%' }}>
                            <div
                                style={{
                                    display: 'flex',
                                    justifyContent: 'space-between',
                                    alignItems: 'flex-start',
                                }}
                            >
                                <Title level={4} style={{ margin: 0 }}>
                                    {feature.title}
                                </Title>
                                <Text style={{ fontSize: 32, fontWeight: 800, opacity: 0.12 }}>
                                    {String(index + 1).padStart(2, '0')}
                                </Text>
                            </div>
                            <Paragraph type="secondary" style={{ marginTop: 8 }}>
                                {feature.short_description}
                            </Paragraph>
                        </Card>
                    </Col>
                ))}
            </Row>
        </div>
    );
}

function FeaturedCategories({ sectionTitle, featuredCategories }) {
    if (!featuredCategories?.length) return null;
    return (
        <div style={{ padding: '56px 24px', background: '#fff' }}>
            <SectionHeading
                title={sectionTitle?.our_categories_title}
                subtitle={sectionTitle?.our_categories_sub_title}
            />
            <Row gutter={[24, 24]}>
                {featuredCategories.map((category) => (
                    <Col xs={24} sm={12} lg={8} key={category.id}>
                        <Link href={`/suppliers?category=${category.slug}`}>
                            <Card
                                hoverable
                                styles={{ body: { padding: 0 } }}
                                cover={
                                    <div style={{ position: 'relative', height: 180 }}>
                                        <img
                                            src={asset(category.background_image)}
                                            alt={category.name}
                                            style={{ width: '100%', height: '100%', objectFit: 'cover' }}
                                        />
                                        <div
                                            style={{
                                                position: 'absolute',
                                                inset: 0,
                                                background:
                                                    'linear-gradient(180deg, rgba(0,0,0,0) 40%, rgba(0,0,0,0.7))',
                                                display: 'flex',
                                                flexDirection: 'column',
                                                justifyContent: 'flex-end',
                                                padding: 16,
                                                color: '#fff',
                                            }}
                                        >
                                            <Text strong style={{ color: '#fff', fontSize: 18 }}>
                                                {category.name}
                                            </Text>
                                            <Text style={{ color: 'rgba(255,255,255,0.85)' }}>
                                                {category.listings_count}{' '}
                                                {category.listings_count === 1 ? 'proveedor' : 'proveedores'}
                                            </Text>
                                        </div>
                                    </div>
                                }
                            />
                        </Link>
                    </Col>
                ))}
            </Row>
        </div>
    );
}

function Counter({ userCount, suppliersCount, categories, locations }) {
    const items = [
        { title: 'Usuarios', value: userCount, icon: <TeamOutlined /> },
        { title: 'Proveedores', value: suppliersCount, icon: <ShopOutlined /> },
        { title: 'Categorías', value: categories?.length ?? 0, icon: <AppstoreOutlined /> },
        { title: 'Ubicaciones', value: locations?.length ?? 0, icon: <EnvironmentOutlined /> },
    ];
    return (
        <div style={{ padding: '48px 24px', background: '#0f1729' }}>
            <Row gutter={[24, 24]}>
                {items.map((item) => (
                    <Col xs={12} md={6} key={item.title} style={{ textAlign: 'center' }}>
                        <div style={{ fontSize: 30, color: '#fff', marginBottom: 8 }}>{item.icon}</div>
                        <Statistic
                            value={item.value}
                            valueStyle={{ color: '#fff', fontSize: 34, fontWeight: 700 }}
                        />
                        <Text style={{ color: 'rgba(255,255,255,0.7)' }}>{item.title}</Text>
                    </Col>
                ))}
            </Row>
        </div>
    );
}

function FeaturedLocations({ sectionTitle, featuredLocations }) {
    const withListings = (featuredLocations ?? []).filter((l) => l.listings?.length);
    if (!withListings.length) return null;

    const tabs = withListings.map((location) => ({
        key: String(location.id),
        label: location.name,
        children: (
            <Row gutter={[24, 24]}>
                {location.listings.map((listing) => (
                    <Col xs={24} sm={12} lg={6} key={listing.id}>
                        <ListingCard listing={listing} />
                    </Col>
                ))}
            </Row>
        ),
    }));

    return (
        <div style={{ padding: '56px 24px', background: '#fff' }}>
            <SectionHeading
                title={sectionTitle?.our_location_title}
                subtitle={sectionTitle?.our_location_sub_title}
            />
            <Tabs centered items={tabs} />
        </div>
    );
}

function FeaturedListings({ sectionTitle, featuredListings }) {
    if (!featuredListings?.length) return null;
    return (
        <div style={{ padding: '56px 24px' }}>
            <SectionHeading
                title={sectionTitle?.our_featured_listing_title}
                subtitle={sectionTitle?.our_featured_listing_sub_title}
            />
            <Row gutter={[24, 24]}>
                {featuredListings.map((listing) => (
                    <Col xs={24} sm={12} lg={8} key={listing.id}>
                        <ListingCard listing={listing} />
                    </Col>
                ))}
            </Row>
        </div>
    );
}

export default function Home(props) {
    const {
        hero,
        sectionTitle,
        ourFeatures,
        categories,
        locations,
        userCount,
        suppliersCount,
        featuredCategories,
        featuredLocations,
        featuredListings,
    } = props;

    return (
        <>
            <Head title="Inicio" />
            <SearchBanner hero={hero} categories={categories} locations={locations} />
            <CategorySlider categories={categories} />
            <Features sectionTitle={sectionTitle} ourFeatures={ourFeatures} />
            <FeaturedCategories sectionTitle={sectionTitle} featuredCategories={featuredCategories} />
            <Counter
                userCount={userCount}
                suppliersCount={suppliersCount}
                categories={categories}
                locations={locations}
            />
            <FeaturedLocations sectionTitle={sectionTitle} featuredLocations={featuredLocations} />
            <FeaturedListings sectionTitle={sectionTitle} featuredListings={featuredListings} />
        </>
    );
}
