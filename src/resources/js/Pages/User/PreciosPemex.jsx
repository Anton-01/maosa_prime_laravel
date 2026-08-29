import React, { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import { Head } from '@inertiajs/react';
import { App, Alert, Button, Card, Col, Empty, Row, Select, Space, Spin, Typography } from 'antd';
import { FileExcelOutlined, FilePdfOutlined, ReloadOutlined } from '@ant-design/icons';

const { Title, Text } = Typography;

/**
 * Inyecta el fragmento HTML de la API y garantiza que la tabla quede dentro de
 * un contenedor con scroll horizontal: la API no siempre envuelve la tabla en
 * `.table-responsive` y `.precio-layout` recorta lo que se desborda.
 */
function LayoutFragment({ html }) {
    const containerRef = useRef(null);

    useEffect(() => {
        const container = containerRef.current;
        if (!container || !html) return;

        container.querySelectorAll('.precio-layout table').forEach((table) => {
            const parent = table.parentElement;
            if (!parent || parent.classList.contains('table-responsive') || parent.classList.contains('pl-table-scroll')) {
                return;
            }

            const scroller = document.createElement('div');
            scroller.className = 'pl-table-scroll';
            parent.insertBefore(scroller, table);
            scroller.appendChild(table);
        });
    }, [html]);

    return <div ref={containerRef} dangerouslySetInnerHTML={{ __html: html ?? '' }} />;
}

/**
 * Submódulo "Precios PEMEX" (REQ-04 a REQ-07).
 *
 * - Las estaciones son las asignadas al usuario en `usuario_estacion`; el
 *   selector sólo aparece cuando hay más de una (REQ-06).
 * - El layout se pide en HTML a
 *   `/api/precio_pemex/layout/estacion/{id_estacion}/HTML` y se inyecta tal
 *   cual; su apariencia la define `price_national_layout.css` (REQ-05).
 * - Los botones descargan `.xlsx` y `.pdf` de la(s) estación(es)
 *   seleccionada(s) (REQ-07).
 */
export default function PreciosPemex({ stations = [], endpoints, stylesheet }) {
    const { message } = App.useApp();

    const [selectedIds, setSelectedIds] = useState(() =>
        stations.length ? [stations[0].id_estacion] : [],
    );
    const [layouts, setLayouts] = useState({});
    const [loading, setLoading] = useState(false);
    const [failed, setFailed] = useState(false);
    const [downloading, setDownloading] = useState(null);

    const stationOptions = useMemo(
        () =>
            stations.map((station) => ({
                value: station.id_estacion,
                label: station.estacion,
            })),
        [stations],
    );

    const layoutUrl = useCallback(
        (idEstacion, format) => `${endpoints.layoutBase}/${idEstacion}/${format}`,
        [endpoints.layoutBase],
    );

    // (Re)carga el layout de cada estación seleccionada. Se pide siempre al
    // cambiar la selección para no mostrar precios de una consulta anterior.
    const loadLayouts = useCallback(
        async (ids, signal) => {
            if (!ids.length) {
                setLayouts({});
                return;
            }

            setLoading(true);
            setFailed(false);

            try {
                const fragments = await Promise.all(
                    ids.map(async (id) => {
                        const response = await fetch(layoutUrl(id, 'HTML'), {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            credentials: 'same-origin',
                            signal,
                        });

                        if (!response.ok) throw new Error(`layout ${id}`);

                        return [id, await response.text()];
                    }),
                );

                setLayouts(Object.fromEntries(fragments));
            } catch (error) {
                if (error.name === 'AbortError') return;
                setLayouts({});
                setFailed(true);
            } finally {
                if (!signal?.aborted) setLoading(false);
            }
        },
        [layoutUrl],
    );

    useEffect(() => {
        const controller = new AbortController();
        loadLayouts(selectedIds, controller.signal);

        return () => controller.abort();
    }, [selectedIds, loadLayouts]);

    // Descarga forzada: el archivo llega como blob y se ancla a un <a download>
    // temporal, así el navegador no intenta abrirlo en una pestaña.
    const download = async (format) => {
        if (!selectedIds.length || downloading) return;

        setDownloading(format);

        try {
            for (const id of selectedIds) {
                // eslint-disable-next-line no-await-in-loop
                const response = await fetch(layoutUrl(id, format), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                });

                if (!response.ok) throw new Error(`download ${id}`);

                // eslint-disable-next-line no-await-in-loop
                const blob = await response.blob();
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                const extension = format === 'Excel' ? 'xlsx' : 'pdf';

                link.href = url;
                link.download = `precios-pemex-estacion-${id}.${extension}`;
                document.body.appendChild(link);
                link.click();
                link.remove();
                URL.revokeObjectURL(url);
            }
        } catch (error) {
            message.error(
                format === 'Excel'
                    ? 'No fue posible descargar el archivo Excel. Intente más tarde.'
                    : 'No fue posible descargar el PDF. Intente más tarde.',
            );
        } finally {
            setDownloading(null);
        }
    };

    const stationName = (id) =>
        stations.find((station) => station.id_estacion === id)?.estacion ?? `Estación ${id}`;

    return (
        <>
            <Head title="Precios PEMEX">
                {/* Hoja de estilos del layout que devuelve la API (REQ-05). */}
                <link rel="stylesheet" href={stylesheet} />
            </Head>

            <Card style={{ marginBottom: 24 }}>
                <Row gutter={[16, 16]} align="bottom">
                    <Col xs={24} md={12} lg={14}>
                        <Title level={4} style={{ marginBottom: 4 }}>
                            Precios PEMEX
                        </Title>
                        <Text type="secondary">
                            Consulta el layout de precios vigente de tus estaciones asignadas.
                        </Text>

                        {/* REQ-06: el selector sólo se muestra con más de una estación. */}
                        {stations.length > 1 && (
                            <div style={{ marginTop: 16 }}>
                                <div style={{ marginBottom: 6, fontWeight: 600 }}>Estaciones</div>
                                <Select
                                    mode="multiple"
                                    style={{ width: '100%' }}
                                    size="large"
                                    allowClear
                                    showSearch
                                    optionFilterProp="label"
                                    maxTagCount="responsive"
                                    value={selectedIds}
                                    onChange={setSelectedIds}
                                    options={stationOptions}
                                    placeholder="Selecciona una o más estaciones"
                                />
                            </div>
                        )}
                    </Col>
                    <Col xs={24} md={12} lg={10}>
                        {/* REQ-07 */}
                        <Space wrap style={{ width: '100%', justifyContent: 'flex-end' }}>
                            <Button
                                icon={<ReloadOutlined />}
                                onClick={() => loadLayouts(selectedIds)}
                                disabled={!selectedIds.length || loading}
                            >
                                Actualizar
                            </Button>
                            <Button
                                icon={<FileExcelOutlined />}
                                loading={downloading === 'Excel'}
                                disabled={!selectedIds.length || downloading !== null}
                                onClick={() => download('Excel')}
                            >
                                Descargar Excel
                            </Button>
                            <Button
                                danger
                                icon={<FilePdfOutlined />}
                                loading={downloading === 'pdf'}
                                disabled={!selectedIds.length || downloading !== null}
                                onClick={() => download('pdf')}
                            >
                                Descargar PDF
                            </Button>
                        </Space>
                    </Col>
                </Row>
            </Card>

            {failed && (
                <Alert
                    type="error"
                    showIcon
                    style={{ marginBottom: 24 }}
                    message="No fue posible obtener los precios."
                    description="Vuelve a intentarlo con el botón Actualizar; si el problema continúa, contacta a soporte."
                />
            )}

            {!stations.length ? (
                <Card>
                    <Empty description="No tienes estaciones asignadas." style={{ padding: 40 }} />
                </Card>
            ) : (
                <Spin spinning={loading}>
                    <Space direction="vertical" size="middle" style={{ display: 'flex' }}>
                        {selectedIds.length === 0 && (
                            <Card>
                                <Empty
                                    description="Selecciona al menos una estación para ver sus precios."
                                    style={{ padding: 40 }}
                                />
                            </Card>
                        )}

                        {selectedIds.map((id) => (
                            <Card
                                key={id}
                                title={stations.length > 1 ? stationName(id) : undefined}
                                styles={{ body: { padding: 0 } }}
                                style={{ overflow: 'hidden' }}
                            >
                                {/* El fragmento trae su propio layout (.precio-layout). */}
                                <LayoutFragment html={layouts[id]} />
                            </Card>
                        ))}
                    </Space>
                </Spin>
            )}
        </>
    );
}
