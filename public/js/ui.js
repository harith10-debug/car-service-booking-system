document.addEventListener('DOMContentLoaded', () => {
    const updatePriceEstimator = (select) => {
        const estimator = document.querySelector('[data-price-estimator]');
        if (!estimator || !select) return;

        const selectedOption = select.options[select.selectedIndex];
        const rawPrice = selectedOption ? Number(selectedOption.dataset.price || 0) : 0;
        const discountPercent = Number(select.dataset.discountPercent || 0);
        const discount = rawPrice * discountPercent / 100;
        const payable = Math.max(rawPrice - discount, 0);

        if (!rawPrice) {
            estimator.textContent = 'Choose a service to see estimated total.';
            return;
        }

        estimator.innerHTML = `
            <span>Service Price: <strong>RM ${rawPrice.toFixed(2)}</strong></span>
            <span>Subscription Discount: <strong>RM ${discount.toFixed(2)}</strong></span>
            <span>Total Payable: <strong>RM ${payable.toFixed(2)}</strong></span>
        `;
    };

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
                updatePriceEstimator(targetSelect);
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

            updatePriceEstimator(select);
        };

        select.addEventListener('change', updateCards);
        updateCards();
    });

    document.querySelectorAll('[data-use-location]').forEach((button) => {
        button.addEventListener('click', () => {
            if (!navigator.geolocation) {
                alert('Your browser does not support location detection.');
                return;
            }

            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Detecting...';

            navigator.geolocation.getCurrentPosition((position) => {
                const url = new URL(window.location.href);
                url.searchParams.set('lat', position.coords.latitude);
                url.searchParams.set('lng', position.coords.longitude);
                window.location.href = url.toString();
            }, () => {
                button.disabled = false;
                button.innerHTML = '<i class="bi bi-crosshair me-1"></i>Use My Location';
                alert('Unable to detect location. You can still search by city or service.');
            });
        });
    });
});
