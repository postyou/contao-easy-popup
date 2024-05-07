// @ts-check
class EasyPopup {
    /** @type {HTMLDialogElement} */
    popup;

    /** @type {number} */
    timeout = 0;

    /** @type {boolean} */
    showOnLeave = false;

    /** @type {number|false} */
    delay = false;

    /**
     * @param {HTMLDialogElement} popup
     */
    constructor(popup) {
        this.popup = popup;
        this.timeout = parseInt(this.popup.dataset.timeout || '0');
        this.showOnLeave = this.popup.dataset.showOnLeave !== undefined;

        if ('delay' in this.popup.dataset) {
            this.delay = parseInt(this.popup.dataset.delay || '0');
        }

        this.popup.addEventListener('close', this.#onClose);

        if (this.showOnLeave) {
            this.#showOnMouseLeave();
        } else if (this.delay !== false) {
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
                this.showModal();
                this.#setTimeout();
            }
        };

        const registerMouseLeave = () => document.addEventListener('mouseleave', handleMouseLeave);
        this.delay !== false ? setTimeout(registerMouseLeave, this.delay) : registerMouseLeave();
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
     * @param {number|false} delay
     */
    showModal(delay = false) {
        const show = () => {
            this.popup.showModal();
            document.documentElement.classList.add('easy-popup-open');
        };

        delay !== false ? setTimeout(show, delay) : show();
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
