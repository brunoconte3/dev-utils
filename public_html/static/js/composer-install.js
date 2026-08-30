let d = document;
d.addEventListener("DOMContentLoaded", () => {
    d.querySelector('#btnStart').addEventListener('click', () => {
        d.querySelector('#btnStart').setAttribute('disabled', 'disabled');
        d.querySelector('#btnStart > span').innerHTML = 'Aguarde';
        d.querySelector('#btnStart > em').classList.remove('d-none');
        d.querySelector('#btnStart > em').classList.add('d-inline-block');
        showAlert('Este processo pode ser demorado...', 'info');
        appendToLog('Iniciando a Instalação das dependências');
        appendToLog('Este processo pode ser demorado...');

        start();
    });
});

async function start() {
    let counter = 0;
    let result = false;
    while (counter < 3) {
        counter++;
        d.querySelector('#textArea').innerHTML += `
------------------------------------------------------------------
                        TENTATIVA ${counter}
------------------------------------------------------------------
`;
        result = await runInstall();
        if (!result['error']) {
            showSuccess();
            break;
        } else {
            appendToLog(result['log']);
        }

    }

    if (result['error']) {
        let text = 'Limite máximo de tentativas excedido. Verifique o LOG e se necessário tente novamente!';
        showAlert(text);
        showError(text);
        updateButton(0);
    }
}

async function runInstall() {
    let completed = false;
    let log = '';
    let endpoint = 'Index.php';
    let form = new FormData();
    form.append('instalar', 'S');
    await fetch(new Request(endpoint, {
        method: 'POST',
        body: form
    })).then((response) => {
        return response.json();
    }).then((result) => {
        if (!result['error']) {
            completed = true;
            return;
        }
        log = result['log'];
    }).catch((error) => {
        appendToLog(error);
    });
    console.log(log.indexOf('Generating autoload files'));
    if (log.indexOf('Generating autoload files') > 0) {
        return { 'error': false, 'log': log }
    } else {
        return { 'error': !completed, 'log': log }
    }
};

function appendToLog(text) {
    let timestamp = Date(Date.now());
    d.querySelector('#textArea').innerHTML += timestamp.toLocaleString('pt-BR', {
        "dateStyle": "short"
    }) + '\n' + text;
    d.querySelector('#textArea').innerHTML += `
--------------------------------------------------------------------------------
`;
}

function updateButton(type) {
    switch (type) {
        case 1:
            d.querySelector('#btnStart > span').innerHTML = 'Erro!';
            d.querySelector('#btnStart > em').classList.add('d-none');
            d.querySelector('#btnStart > em').classList.remove('d-inline-block');
            break;
        case 2:

            d.querySelector('#btnStart > span').innerHTML = 'Sucesso!';
            d.querySelector('#btnStart > em').classList.add('d-none');
            d.querySelector('#btnStart > em').classList.remove('d-inline-block');
            break;
        case 0:
        default:
            d.querySelector('#btnStart').removeAttribute('disabled');
            d.querySelector('#btnStart > em').classList.add('d-none');
            d.querySelector('#btnStart > em').classList.remove('d-inline-block');
            d.querySelector('#btnStart > span').innerHTML = 'Clique Aqui';
            break;
    }
}

function showError(text) {
    appendToLog(text);
    updateButton(1);
    setTimeout(() => {
        updateButton(0);
    }, 2500);
}

function showSuccess() {
    updateButton(2);
    showAlert('Processo Finalizado! Agora você será redirecionado a página principal do Projeto!', 'success');
    setTimeout(() => {
        window.location.href = 'Index.php';
    }, 2500);
}
