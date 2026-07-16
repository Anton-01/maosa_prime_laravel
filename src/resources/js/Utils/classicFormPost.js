/**
 * Submit a classic (non-Inertia) POST with the CSRF token. Needed when the
 * server may redirect to a Blade page (login, logout), which an Inertia
 * visit cannot render. Validation errors still reach the next Inertia page
 * through the shared "errors" prop (they are flashed to the session).
 */
export default function classicFormPost(action, fields = {}) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = action;

    Object.entries({ _token: csrfToken, ...fields }).forEach(([name, value]) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value ?? '';
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
}
