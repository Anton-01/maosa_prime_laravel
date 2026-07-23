import React, { useEffect, useMemo, useState } from 'react';
import { Upload, Typography, theme } from 'antd';
import { InboxOutlined } from '@ant-design/icons';

const { Dragger } = Upload;
const { Text } = Typography;

/**
 * Standard drag-and-drop image uploader used across the admin/user forms.
 * Shows a dashed drop zone with a live preview of the selected file (or the
 * current image) and an optional hint below. Ant Design only.
 *
 * Works both controlled by state and inside antd Form.Item:
 *   value:      File | null (the picked file; Form.Item injects it)
 *   onChange:   (file: File | null) => void
 *   currentUrl: string — existing image to preview when no new file is picked
 *   hint:       caption shown under the drop zone
 *   accept:     input accept filter (default image/*)
 *   height:     drop zone height in px (default 180)
 */
export default function ImageDropUpload({
    value,
    onChange,
    currentUrl,
    hint = 'Solo se aceptan imágenes *.png, *.jpg y *.jpeg.',
    accept = 'image/*',
    height = 180,
}) {
    const { token } = theme.useToken();
    const [objectUrl, setObjectUrl] = useState(null);

    // Build (and clean up) an object URL for the picked file.
    useEffect(() => {
        if (value instanceof File) {
            const url = URL.createObjectURL(value);
            setObjectUrl(url);
            return () => URL.revokeObjectURL(url);
        }
        setObjectUrl(null);
        return undefined;
    }, [value]);

    const preview = objectUrl || currentUrl || null;
    const fileName = value instanceof File ? value.name : null;

    const draggerStyle = useMemo(
        () => ({
            padding: 0,
            borderRadius: token.borderRadiusLG,
            background: token.colorFillQuaternary,
        }),
        [token],
    );

    return (
        <div>
            <Dragger
                accept={accept}
                multiple={false}
                maxCount={1}
                showUploadList={false}
                beforeUpload={(file) => {
                    onChange?.(file);
                    return false;
                }}
                style={draggerStyle}
            >
                {preview ? (
                    <div style={{ position: 'relative' }}>
                        <img
                            src={preview}
                            alt="Vista previa"
                            style={{
                                width: '100%',
                                height,
                                objectFit: 'contain',
                                borderRadius: token.borderRadiusLG,
                                display: 'block',
                                padding: 8,
                            }}
                        />
                        <div
                            style={{
                                position: 'absolute',
                                inset: 0,
                                display: 'flex',
                                alignItems: 'flex-end',
                                justifyContent: 'center',
                                padding: 8,
                            }}
                        >
                            <Text
                                style={{
                                    background: 'rgba(0,0,0,0.55)',
                                    color: '#fff',
                                    padding: '4px 12px',
                                    borderRadius: 999,
                                    fontSize: 12,
                                }}
                            >
                                Arrastra o haz clic para cambiar
                            </Text>
                        </div>
                    </div>
                ) : (
                    <div style={{ padding: `${Math.max(height - 90, 24)}px 16px`, textAlign: 'center' }}>
                        <p style={{ margin: 0 }}>
                            <InboxOutlined style={{ fontSize: 40, color: token.colorPrimary }} />
                        </p>
                        <p style={{ margin: '8px 0 2px', fontSize: 15, fontWeight: 500 }}>
                            Arrastra o selecciona un archivo
                        </p>
                        <p style={{ margin: 0, color: token.colorTextSecondary, fontSize: 13 }}>
                            Suelta la imagen aquí o haz clic para explorar en tu equipo.
                        </p>
                    </div>
                )}
            </Dragger>

            {(hint || fileName) && (
                <Text
                    type="secondary"
                    style={{ display: 'block', marginTop: 8, fontSize: 12 }}
                >
                    {fileName ? `Seleccionado: ${fileName}` : hint}
                </Text>
            )}
        </div>
    );
}
