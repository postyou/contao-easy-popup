// @ts-check
class EasyPopup {
    /**
     * @param {HTMLDialogElement} popup
     */
    constructor(popup) {
        this.popup = popup;
        this.timeout = parseInt(this.popup.dataset.timeout || '0');
        this.delay = parseInt(this.popup.dataset.delay || '0');
        this.showOnLeave = this.popup.dataset.showOnLeave !== undefined;

        this.popup.addEventListener('close', this.#onClose);

        if (this.showOnLeave) {
            this.#showOnMouseLeave();
        } else if (this.delay) {
            this.#showAfterDelay();
        }
    }

    #showAfterDelay() {
        if (!this.#isTimeoutActive()) {
            this.showModal(this.delay);
            this.#setTimeout();
        }
    }

    #showOnMouseLeave() {
        /**
         * @param {MouseEvent} e
         */
        const handleMouseLeave = (e) => {
            if (this.#isTimeoutActive()) {
                return;
            }

            if (e.clientY < 10) {
                document.removeEventListener('mouseleave', handleMouseLeave);
                this.showModal(this.delay);
                this.#setTimeout();
            }
        };

        document.addEventListener('mouseleave', handleMouseLeave);
    }

    #setTimeout() {
        localStorage.setItem(this.popup.id, Date.now().toString());
    }

    #isTimeoutActive() {
        const expiry = localStorage.getItem(this.popup.id);

        if (!expiry) {
            return false;
        }

        if (Date.now() > parseInt(expiry) + this.timeout) {
            localStorage.removeItem(this.popup.id);

            return false;
        }

        return true;
    }

    /**
     * @param {number} delay
     */
    showModal(delay = 0) {
        setTimeout(() => {
            this.popup.showModal();
            document.documentElement.classList.add('easy-popup-open');
        }, delay);
    }

    #onClose() {
        document.documentElement.classList.remove('easy-popup-open');
    }
}

function initEasyPopups() {
    /** @type {NodeListOf<HTMLDialogElement>} */
    const popups = document.querySelectorAll('dialog[id^="easy-popup-"]');
    const popupMap = new Map();

    popups.forEach((popup) => popupMap.set('#' + popup.id, new EasyPopup(popup)));

    window.addEventListener('click', (e) => {
        if (e.target instanceof HTMLAnchorElement && popupMap.has(e.target.hash)) {
            e.preventDefault();
            popupMap.get(e.target.hash).showModal();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initEasyPopups();
});
