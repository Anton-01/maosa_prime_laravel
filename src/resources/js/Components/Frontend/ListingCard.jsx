import React from 'react';
import { Link } from '@inertiajs/react';
import { Card, Tag, Typography } from 'antd';
import { CheckCircleFilled, EnvironmentOutlined, TagOutlined } from '@ant-design/icons';

import asset from '../../Utils/asset';
import useTranslation from '../../Hooks/useTranslation';

const { Text, Title } = Typography;

/**
 * Provider (listing) card used across the home, listings and location grids.
 */
export default function ListingCard({ listing }) {
    const { t } = useTranslation();

    return (
        <Card
            hoverable
            styles={{ body: { padding: 16 } }}
            cover={
                <Link href={`/suppliers/${listing.slug}`}>
                    <div style={{ position: 'relative', height: 190, overflow: 'hidden' }}>
                        <img
                            alt={listing.title}
                            src={asset(listing.image)}
                            style={{ width: '100%', height: '100%', objectFit: 'cover' }}
                        />
                        {!!listing.is_featured && (
                            <Tag
                                color="gold"
                                icon={<CheckCircleFilled />}
                                style={{ position: 'absolute', top: 10, left: 10, margin: 0 }}
                            >
                                {t('listings.featured')}
                            </Tag>
                        )}
                        {!!listing.is_previliged && (
                            <CheckCircleFilled
                                style={{
                                    position: 'absolute',
                                    top: 10,
                                    right: 10,
                                    color: '#52c41a',
                                    fontSize: 22,
                                    background: '#fff',
                                    borderRadius: '50%',
                                }}
                            />
                        )}
                    </div>
                </Link>
            }
        >
            <Link href={`/suppliers/${listing.slug}`}>
                <Title level={5} ellipsis={{ rows: 1 }} style={{ marginBottom: 8 }}>
                    {listing.title}
                </Title>
            </Link>
            {listing.category && (
                <div>
                    <Text type="secondary" style={{ fontSize: 13 }}>
                        <TagOutlined /> {listing.category.name}
                    </Text>
                </div>
            )}
            {listing.location && (
                <div>
                    <Text type="secondary" style={{ fontSize: 13 }}>
                        <EnvironmentOutlined /> {listing.location.name}
                    </Text>
                </div>
            )}
        </Card>
    );
}
