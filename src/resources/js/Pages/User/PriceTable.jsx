import React, { useEffect, useState } from 'react';
import { Head } from '@inertiajs/react';
import { App, Button, Card, Col, DatePicker, Empty, Row, Select, Spin } from 'antd';
import { FilePdfOutlined } from '@ant-design/icons';
import dayjs from 'dayjs';

const DATE_FORMAT = 'YYYY-MM-DD';

export default function PriceTable({ endpoints, dates }) {
    const { message } = App.useApp();

    const [stations, setStations] = useState([]);
    const [loadingStations, setLoadingStations] = useState(true);
    const [stationId, setStationId] = useState(null);
    const [date, setDate] = useState(dayjs(dates.today));
    const [priceHtml, setPriceHtml] = useState('');
    const [loadingPrices, setLoadingPrices] = useState(false);
    const [downloading, setDownloading] = useState(false);

    // Load the user's assigned stations once.
    useEffect(() => {
        let active = true;
        (async () => {
            try {
                const res = await fetch(endpoints.stations, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) throw new Error('stations');
                const data = await res.json();
                if (!active) return;
                setStations(data || []);
                if (data && data.length) setStationId(data[0].id_estacion);
            } catch (e) {
                if (active) message.error('Error al obtener la configuración de precios.');
            } finally {
                if (active) setLoadingStations(false);
            }
        })();
        return () => {
            active = false;
        };
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    // (Re)load the price fragment whenever the station or date changes.
    useEffect(() => {
        if (!stationId) return;
        let active = true;
        (async () => {
            setLoadingPrices(true);
            try {
                const params = new URLSearchParams({ estacion_id: stationId });
                if (date) params.append('fecha_vigencia', date.format(DATE_FORMAT));
                const res = await fetch(`${endpoints.html}?${params.toString()}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                const text = await res.text();
                if (active) setPriceHtml(text);
            } catch (e) {
                if (active) setPriceHtml('<p style="text-align:center;padding:24px">Error de conexión.</p>');
            } finally {
                if (active) setLoadingPrices(false);
            }
        })();
        return () => {
            active = false;
        };
    }, [stationId, date, endpoints.html]);

    const downloadPdf = async () => {
        if (downloading) return;
        setDownloading(true);
        try {
            const params = new URLSearchParams();
            if (date) params.append('fecha_vigencia', date.format(DATE_FORMAT));
            const res = await fetch(`${endpoints.pdf}?${params.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!res.ok) throw new Error('pdf');
            const blob = await res.blob();
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `maosa-prime-precios-combustible-${date.format(DATE_FORMAT)}.pdf`;
            document.body.appendChild(link);
            link.click();
            link.remove();
            URL.revokeObjectURL(url);
        } catch (e) {
            message.error('No fue posible descargar el PDF. Intente más tarde.');
        } finally {
            setDownloading(false);
        }
    };

    return (
        <>
            <Head title="Tabla de precios">
                <link rel="stylesheet" href="/frontend/css/maosa/table-prices.css" />
            </Head>

            <Card style={{ marginBottom: 24 }}>
                <Row gutter={[16, 16]} align="bottom">
                    {stations.length > 1 && (
                        <Col xs={24} md={10}>
                            <div style={{ marginBottom: 6, fontWeight: 600 }}>Estación</div>
                            <Select
                                style={{ width: '100%' }}
                                size="large"
                                loading={loadingStations}
                                value={stationId}
                                onChange={setStationId}
                                options={stations.map((s) => ({
                                    value: s.id_estacion,
                                    label: s.estacion || s.id_socio || `Estación ${s.id_estacion}`,
                                }))}
                            />
                        </Col>
                    )}
                    <Col xs={24} md={8}>
                        <div style={{ marginBottom: 6, fontWeight: 600 }}>Fecha de vigencia</div>
                        <DatePicker
                            style={{ width: '100%' }}
                            size="large"
                            value={date}
                            allowClear={false}
                            format="DD/MM/YYYY"
                            disabledDate={(current) =>
                                current &&
                                (current < dayjs(dates.min).startOf('day') ||
                                    current > dayjs(dates.max).endOf('day'))
                            }
                            onChange={(value) => value && setDate(value)}
                        />
                    </Col>
                    <Col xs={24} md={6}>
                        <Button
                            danger
                            size="large"
                            block
                            icon={<FilePdfOutlined />}
                            loading={downloading}
                            disabled={!stationId}
                            onClick={downloadPdf}
                        >
                            Descargar PDF
                        </Button>
                    </Col>
                </Row>
            </Card>

            <Card>
                {loadingStations ? (
                    <div style={{ textAlign: 'center', padding: 48 }}>
                        <Spin tip="Cargando configuración de precios..." />
                    </div>
                ) : !stations.length ? (
                    <Empty description="No tiene estaciones asignadas." style={{ padding: 40 }} />
                ) : (
                    <Spin spinning={loadingPrices}>
                        <div
                            className="price-table-frame"
                            dangerouslySetInnerHTML={{ __html: priceHtml }}
                        />
                    </Spin>
                )}
            </Card>
        </>
    );
}
