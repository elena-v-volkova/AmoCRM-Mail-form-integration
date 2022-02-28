const popupLink = document.querySelector('.popup-link');
const popupCloseBtn = document.querySelectorAll('.close-popup');

let currPopup = document.getElementById('popup');

popupLink.addEventListener('click', function (e) {
    currPopup.classList.add('open')
});

if (popupCloseBtn.length > 0) {
    for (let i = 0; i < popupCloseBtn.length; i++) {
        const el = popupCloseBtn[i];
        el.addEventListener('click', function (e) {
            popupClose(el.closest('.popup'));
            e.preventDefault();
        })
    }
};

function popupClose(popupActive) {
    popupActive.classList.remove('open');
};