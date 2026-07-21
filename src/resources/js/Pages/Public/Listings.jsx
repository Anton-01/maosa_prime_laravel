import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { Button, Card, Col, Empty, Input, Pagination, Row, Select } from 'antd';
import { SearchOutlined } from '@ant-design/icons';

import PageHeader from '../../Components/Frontend/PageHeader';
import ListingCard from '../../Components/Frontend/ListingCard';

export default function Listings({ listings, categories, locations, filters }) {
    const [search, setSearch] = useState(filters?.search || '');
    const [category, setCategory] = useState(filters?.category || undefined);
    const [location, setLocation] = useState(filters?.location || undefined);

    const applyFilters = (extra = {}) => {
        const params = {};
        if (search) params.search = search;
        if (category) params.category = category;
        if (location) params.location = location;
        router.get('/suppliers', { ...params, ...extra }, { preserveScroll: true, preserveState: true });
    };

    return (
        <>
            <Head title="Proveedores" />
            <PageHeader title="Proveedores" breadcrumb={[{ label: 'Proveedores' }]} />

            <div style={{ padding: '32px 24px' }}>
                <Card style={{ marginBottom: 24 }}>
                    <Row gutter={[12, 12]} align="middle">
                        <Col xs={24} md={8}>
                            <Input
                                placeholder="Buscar"
                                prefix={<SearchOutlined />}
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                onPressEnter={() => applyFilters()}
                                allowClear
                            />
                        </Col>
                        <Col xs={12} md={6}>
                            <Select
                                placeholder="Categorías"
                                allowClear
                                style={{ width: '100%' }}
                                value={category}
                                onChange={setCategory}
                                options={categories?.map((c) => ({ value: c.slug, label: c.name }))}
                            />
                        </Col>
                        <Col xs={12} md={6}>
                            <Select
                                placeholder="Ubicación"
                                allowClear
                                style={{ width: '100%' }}
                                value={location}
                                onChange={setLocation}
                                options={locations?.map((l) => ({ value: l.slug, label: l.name }))}
                            />
                        </Col>
                        <Col xs={24} md={4}>
                            <Button
                                type="primary"
                                block
                                icon={<SearchOutlined />}
                                onClick={() => applyFilters()}
                            >
                                Buscar
                            </Button>
                        </Col>
                    </Row>
                </Card>

                {listings?.data?.length ? (
                    <>
                        <Row gutter={[24, 24]}>
                            {listings.data.map((listing) => (
                                <Col xs={24} sm={12} lg={6} key={listing.id}>
                                    <ListingCard listing={listing} />
                                </Col>
                            ))}
                        </Row>

                        <div style={{ display: 'flex', justifyContent: 'center', marginTop: 32 }}>
                            <Pagination
                                current={listings.current_page}
                                total={listings.total}
                                pageSize={listings.per_page}
                                showSizeChanger={false}
                                onChange={(page) => applyFilters({ page })}
                            />
                        </div>
                    </>
                ) : (
                    <Empty description="No se encontraron proveedores" style={{ padding: 60 }} />
                )}
            </div>
        </>
    );
}
