import React, { useCallback, useEffect, useState } from 'react';
import { Alert, Button, Checkbox, Col, Input, Modal, Row, Slider, Space, Tooltip, Typography } from 'antd';
import { CopyOutlined, ReloadOutlined } from '@ant-design/icons';

import useTranslation from '../Hooks/useTranslation';

const { Text } = Typography;

/** Conjuntos de caracteres disponibles, en el orden en que se muestran. */
const CHARACTER_SETS = [
    { key: 'uppercase', chars: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' },
    { key: 'lowercase', chars: 'abcdefghijklmnopqrstuvwxyz' },
    { key: 'numbers', chars: '0123456789' },
    { key: 'symbols', chars: '!@#$%^&*()-_=+[]{};:,.?' },
];

/** Caracteres que se confunden al dictar o transcribir una contraseña. */
const AMBIGUOUS = 'Il1O0';

const MIN_LENGTH = 8;
const MAX_LENGTH = 32;
const DEFAULT_LENGTH = 16;

/**
 * Entero aleatorio en [0, max) sin sesgo de módulo, tomado del generador
 * criptográfico del navegador (no de Math.random).
 */
function randomIndex(max) {
    const limit = Math.floor(0xffffffff / max) * max;
    const buffer = new Uint32Array(1);
    let value;

    do {
        crypto.getRandomValues(buffer);
        [value] = buffer;
    } while (value >= limit);

    return value % max;
}

/**
 * Contraseña con al menos un carácter de cada conjunto elegido; el resto se
 * completa del total y se mezcla para no dejar los obligatorios al inicio.
 */
function generatePassword(length, selectedKeys, avoidAmbiguous) {
    const clean = (chars) =>
        avoidAmbiguous
            ? [...chars].filter((char) => !AMBIGUOUS.includes(char)).join('')
            : chars;

    const pools = CHARACTER_SETS.filter((set) => selectedKeys.includes(set.key))
        .map((set) => clean(set.chars))
        .filter(Boolean);

    if (!pools.length) return '';

    const all = pools.join('');
    const characters = pools.map((pool) => pool[randomIndex(pool.length)]);

    while (characters.length < length) {
        characters.push(all[randomIndex(all.length)]);
    }

    // Fisher-Yates
    for (let i = characters.length - 1; i > 0; i -= 1) {
        const j = randomIndex(i + 1);
        [characters[i], characters[j]] = [characters[j], characters[i]];
    }

    return characters.slice(0, Math.max(length, pools.length)).join('');
}

/**
 * Modal para armar una contraseña segura eligiendo qué la compone. Al
 * confirmar entrega la contraseña al formulario (`onUse`), que es quien
 * decide qué hacer con ella.
 */
export default function PasswordGeneratorModal({ open, onCancel, onUse }) {
    const { t } = useTranslation();
    const [length, setLength] = useState(DEFAULT_LENGTH);
    const [selectedKeys, setSelectedKeys] = useState(['uppercase', 'lowercase', 'numbers', 'symbols']);
    const [avoidAmbiguous, setAvoidAmbiguous] = useState(true);
    const [password, setPassword] = useState('');

    const regenerate = useCallback(() => {
        setPassword(generatePassword(length, selectedKeys, avoidAmbiguous));
    }, [length, selectedKeys, avoidAmbiguous]);

    // Una propuesta lista al abrir y en cada cambio de criterio.
    useEffect(() => {
        if (!open) return;
        regenerate();
    }, [open, regenerate]);

    const hasSelection = selectedKeys.length > 0;

    return (
        <Modal
            open={open}
            onCancel={onCancel}
            title={t('password_generator.title')}
            width={520}
            footer={[
                <Button key="cancel" onClick={onCancel}>
                    {t('common.cancel')}
                </Button>,
                <Button
                    key="use"
                    type="primary"
                    disabled={!password}
                    onClick={() => onUse(password)}
                >
                    {t('password_generator.use')}
                </Button>,
            ]}
        >
            <Space direction="vertical" size="middle" style={{ display: 'flex' }}>
                <div>
                    <Text strong>{t('password_generator.length', { length })}</Text>
                    <Slider
                        min={MIN_LENGTH}
                        max={MAX_LENGTH}
                        value={length}
                        onChange={setLength}
                        marks={{ [MIN_LENGTH]: MIN_LENGTH, [MAX_LENGTH]: MAX_LENGTH }}
                    />
                </div>

                <div>
                    <Text strong>{t('password_generator.include')}</Text>
                    <Checkbox.Group
                        value={selectedKeys}
                        onChange={setSelectedKeys}
                        style={{ width: '100%', marginTop: 8 }}
                    >
                        <Row gutter={[8, 8]}>
                            {CHARACTER_SETS.map((set) => (
                                <Col xs={24} sm={12} key={set.key}>
                                    <Checkbox value={set.key}>
                                        {t(`password_generator.${set.key}`)}
                                    </Checkbox>
                                </Col>
                            ))}
                        </Row>
                    </Checkbox.Group>
                </div>

                <Checkbox
                    checked={avoidAmbiguous}
                    onChange={(event) => setAvoidAmbiguous(event.target.checked)}
                >
                    {t('password_generator.avoid_ambiguous')}
                </Checkbox>

                {!hasSelection && (
                    <Alert
                        type="warning"
                        showIcon
                        message={t('password_generator.select_one')}
                    />
                )}

                <Space.Compact style={{ width: '100%' }}>
                    <Input
                        readOnly
                        value={password}
                        placeholder={t('password_generator.empty')}
                        style={{ fontFamily: 'ui-monospace, SFMono-Regular, Menlo, monospace' }}
                    />
                    <Tooltip title={t('password_generator.regenerate')}>
                        <Button icon={<ReloadOutlined />} onClick={regenerate} disabled={!hasSelection} />
                    </Tooltip>
                </Space.Compact>

                <Text type="secondary" style={{ fontSize: 12 }}>
                    <CopyOutlined /> {t('password_generator.clipboard_hint')}
                </Text>
            </Space>
        </Modal>
    );
}
