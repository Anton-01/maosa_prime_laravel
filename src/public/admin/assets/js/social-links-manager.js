/**
 * Social Links Manager
 * Manages dynamic add/remove of social network links in listing forms
 */

document.addEventListener('DOMContentLoaded', function() {
    const socialLinksManager = document.getElementById('social-links-manager');

    // Only initialize if the manager exists on the page
    if (!socialLinksManager) {
        return;
    }

    const container = document.getElementById('social-links-container');
    const addButton = document.getElementById('add-social-link');
    const template = document.getElementById('social-link-template');

    let rowIndex = getMaxIndex() + 1;

    /**
     * Get the maximum index from existing rows
     */
    function getMaxIndex() {
        const rows = container.querySelectorAll('.social-link-row');
        let maxIndex = -1;

        rows.forEach(row => {
            const index = parseInt(row.getAttribute('data-index'));
            if (index > maxIndex) {
                maxIndex = index;
            }
        });

        return maxIndex;
    }

    /**
     * Add a new social link row
     */
    function addSocialLinkRow() {
        // Clone the template content
        const templateContent = template.content.cloneNode(true);
        const newRow = templateContent.querySelector('.social-link-row');

        // Replace __INDEX__ placeholder with actual index
        newRow.setAttribute('data-index', rowIndex);
        newRow.innerHTML = newRow.innerHTML.replace(/__INDEX__/g, rowIndex);

        // Append to container
        container.appendChild(newRow);

        // Increment index for next row
        rowIndex++;

        // Attach remove handler to the new row's remove button
        const removeButton = newRow.querySelector('.remove-social-link');
        if (removeButton) {
            removeButton.addEventListener('click', function() {
                removeSocialLinkRow(this);
            });
        }
    }

    /**
     * Remove a social link row
     */
    function removeSocialLinkRow(button) {
        const row = button.closest('.social-link-row');
        const allRows = container.querySelectorAll('.social-link-row');

        // Don't allow removing the last row, just clear its values
        if (allRows.length === 1) {
            const select = row.querySelector('select');
            const input = row.querySelector('input[type="url"]');
            if (select) select.value = '';
            if (input) input.value = '';
            return;
        }

        // Remove the row with animation
        row.style.opacity = '0';
        row.style.transition = 'opacity 0.3s';

        setTimeout(() => {
            row.remove();
        }, 300);
    }

    // Add click handler to the add button
    if (addButton) {
        addButton.addEventListener('click', addSocialLinkRow);
    }

    // Attach remove handlers to existing remove buttons
    const existingRemoveButtons = container.querySelectorAll('.remove-social-link');
    existingRemoveButtons.forEach(button => {
        button.addEventListener('click', function() {
            removeSocialLinkRow(this);
        });
    });
});
