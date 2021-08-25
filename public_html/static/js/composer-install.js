let d = document;
d.addEventListener("DOMContentLoaded", () => {
    d.querySelector('#btnIniciar').addEventListener('click', () => {
        d.querySelector('#btnIniciar').setAttribute('disabled', 'disabled');
        d.querySelector('#btnIniciar > span').innerHTML = 'Aguarde';
        d.querySelector('#btnIniciar > em').classList.remove('d-none');
        d.querySelector('#btnIniciar > em').classList.add('d-inline-block');
        dispararAlerta('Este processo pode ser demorado...', 'info');
        adicionarNoLog('Iniciando a Instalação das dependências');
        adicionarNoLog('Este processo pode ser demorado...');

        iniciar();
    });
});

async function iniciar() {
    let counter = 0;
    let retorno = false;
    while (counter < 3) {
        counter++;
        d.querySelector('#textArea').innerHTML += `
------------------------------------------------------------------
                        TENTATIVA ${counter}
------------------------------------------------------------------
`;
        retorno = await instalar();
        if (!retorno['erro']) {
            dispararSucesso();
            break;
        } else {
            adicionarNoLog(retorno['log']);
        }

    }

    if (retorno['erro']) {
        let texto = 'Limite máximo de tentativas excedido. Verifique o LOG e se necessário tente novamente!';
        dispararAlerta(texto);
        dispararErro(texto);
        alterarBotao(0);
    }
}

async function instalar() {
    let concluido = false;
    let log = '';
    let diretorio = 'Index.php';
    let form = new FormData();
    form.append('instalar', 'S');
    await fetch(new Request(diretorio, {
        method: 'POST',
        body: form
    })).then((resposta) => {
        return resposta.json();
    }).then((retorno) => {
        if (!retorno['erro']) {
            concluido = true;
            return;
        }
        log = retorno['log'];
    }).catch((erro) => {
        adicionarNoLog(erro);
    });
    console.log(log.indexOf('Generating autoload files'));
    if (log.indexOf('Generating autoload files') > 0) {
        return { 'erro': false, 'log': log }
    } else {
        return { 'erro': !concluido, 'log': log }
    }
};

function adicionarNoLog(texto) {
    let data = Date(Date.now());
    d.querySelector('#textArea').innerHTML += data.toLocaleString('pt-BR', {
        "dateStyle": "short"
    }) + '\n' + texto;
    d.querySelector('#textArea').innerHTML += `
--------------------------------------------------------------------------------
`;
}

function alterarBotao(tipo) {
    switch (tipo) {
        case 1:
            d.querySelector('#btnIniciar > span').innerHTML = 'Erro!';
            d.querySelector('#btnIniciar > em').classList.add('d-none');
            d.querySelector('#btnIniciar > em').classList.remove('d-inline-block');
            break;
        case 2:

            d.querySelector('#btnIniciar > span').innerHTML = 'Sucesso!';
            d.querySelector('#btnIniciar > em').classList.add('d-none');
            d.querySelector('#btnIniciar > em').classList.remove('d-inline-block');
            break;
        case 0:
        default:
            d.querySelector('#btnIniciar').removeAttribute('disabled');
            d.querySelector('#btnIniciar > em').classList.add('d-none');
            d.querySelector('#btnIniciar > em').classList.remove('d-inline-block');
            d.querySelector('#btnIniciar > span').innerHTML = 'Clique Aqui';
            break;
    }
}

function dispararErro(texto) {
    adicionarNoLog(texto);
    alterarBotao(1);
    setTimeout(() => {
        alterarBotao(0);
    }, 2500);
}

function dispararSucesso() {
    alterarBotao(2);
    dispararAlerta('Processo Finalizado! Agora você será redirecionado a página principal do Projeto!', 'success');
    setTimeout(() => {
        window.location.href = 'Index.php';
    }, 2500);
}
