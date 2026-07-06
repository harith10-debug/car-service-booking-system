document.addEventListener('DOMContentLoaded', () => {
    const selectableCards = document.querySelectorAll('[data-service-card]');

    selectableCards.forEach((card) => {
        card.addEventListener('click', () => {
            const group = card.dataset.serviceGroup || 'default';
            const groupCards = document.querySelectorAll(`[data-service-card][data-service-group="${group}"]`);

            groupCards.forEach((item) => item.classList.remove('selected'));
            card.classList.add('selected');

            const packageId = card.dataset.packageId;
            const targetSelectId = card.dataset.targetSelect;
            const targetSelect = targetSelectId ? document.getElementById(targetSelectId) : null;

            if (targetSelect && packageId) {
                targetSelect.value = packageId;
                targetSelect.dispatchEvent(new Event('change', { bubbles: true }));
            }

            const radio = card.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
            }
        });

        card.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                card.click();
            }
        });
    });

    document.querySelectorAll('.service-package-select').forEach((select) => {
        const updateCards = () => {
            const packageId = select.value;
            const cards = document.querySelectorAll(`[data-target-select="${select.id}"]`);
            const helper = document.querySelector(`[data-selected-helper="${select.id}"]`);
            const selectedOption = select.options[select.selectedIndex];

            cards.forEach((card) => {
                card.classList.toggle('selected', card.dataset.packageId === packageId);
            });

            if (helper) {
                if (packageId && selectedOption) {
                    helper.classList.add('show');
                    helper.innerHTML = `<i class="bi bi-check-circle me-1"></i>Selected service: <strong>${selectedOption.text}</strong>`;
                } else {
                    helper.classList.remove('show');
                    helper.textContent = '';
                }
            }
        };

        select.addEventListener('change', updateCards);
        updateCards();
    });
});
