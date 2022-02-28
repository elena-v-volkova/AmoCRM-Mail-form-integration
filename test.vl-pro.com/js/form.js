'use strict';

const form = document.getElementById('form');
form.addEventListener('submit', formSend);

async function formSend(e) {
    e.preventDefault();
    let error = formValidate();
    let formData = new FormData(form);
    if (error === 0) {
        let response = await fetch('sender.php', {
            method: 'POST',
            body: formData
        });
        if (response.ok) {
            let result = await response.json();
            alert(result.message);
            form.reset();
        } else {
            alert('Ошибка')
        }
    } else {
        alert('Заполните номер телефона')
    }
}


function formValidate() {
    let error = 0;
    let formReq = document.querySelectorAll('._req');

    for (let i = 0; i < formReq.length; i++) {
        const input = formReq[i];
        formRemoveError(input);

        if (input.classList.contains('phone')) {
            if (input.value.length <= 16) {
                formAddError(input);
                error++;
            }
        }
    }

    return error;
}

function formAddError(input) {
    input.parentElement.classList.add('_error');
    input.classList.add('_error');
}

function formRemoveError(input) {
    input.parentElement.classList.remove('_error');
    input.classList.remove('_error');
}
