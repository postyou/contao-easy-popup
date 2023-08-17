const popupsLinks = document.querySelectorAll('a[data-easy-popup]');
const popupsMap = new Map();

popupsLinks.forEach((el) => {
    popupsMap.set(
        el.dataset.easyPopup,
        document.querySelector('div[data-easy-popup-content="' + el.dataset.easyPopup + '"]')
    );
    el.addEventListener('click', (e) => {
        e.preventDefault();
        popupsMap.get(el.dataset.easyPopup).classList.add('active');
    });
    document
        .querySelector('div[data-easy-popup-close="' + el.dataset.easyPopup + '"]')
        .addEventListener('click', () => {
            popupsMap.get(el.dataset.easyPopup).classList.remove('active');
        });
});
