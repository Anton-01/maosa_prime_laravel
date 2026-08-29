import { usePage } from '@inertiajs/react';

/**
 * Traducción en el front a partir del diccionario que comparte
 * HandleInertiaRequests (lang/{es,en}/ui.php).
 *
 *   const { t, locale } = useTranslation();
 *   t('users.edit_title', { name: user.name });
 *
 * Si falta la clave devuelve la clave misma: los huecos se ven en pantalla
 * en lugar de quedar como texto vacío.
 */
export default function useTranslation() {
    const { translations, locale } = usePage().props;

    const t = (key, replacements = {}) => {
        const value = key
            .split('.')
            .reduce((carry, part) => (carry == null ? undefined : carry[part]), translations);

        if (typeof value !== 'string') return key;

        return Object.entries(replacements).reduce(
            (text, [name, replacement]) => text.replaceAll(`:${name}`, replacement),
            value,
        );
    };

    return { t, locale: locale ?? 'es' };
}
