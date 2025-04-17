$(document).ready(() => {

    let dashboardOriginal = $('#pagina').html();

    $('#btnDashboard').on('click', () => {
        console.log("clicado")
        $('#telaDashboard').removeClass('d-none').show()
    })
    
    $.ajax({
        type: 'GET',
        url: 'app.php',
        data: { dadosFixos: true },
        dataType: 'json',
        success: dados => { 
            $('#clientesAtivos').html(dados.clientesAtivos)
            $('#clientesInativos').html(dados.clientesInativos)
            $('#reclamacoes').html(dados.reclamacoes)
            $('#elogios').html(dados.elogios)
            $('#sugestoes').html(dados.sugestoes)
        },
        error: erro => { console.log(erro) }
    })
	
    $('#documentacao').on('click', () => {
        $.post('documentacao.html', data => {
            $('#pagina').html(data)
        })
    })

    $('#suporte').on('click', () => {
        $.post('suporte.html', data => {
            $('#pagina').html(data)
        })
    })

    //ajax
    $('#competencia').on('change', (e) => {

        let competencia = $(e.target).val()
        
        $.ajax({
            type: 'GET',
            url: 'app.php',
            data: `competencia=${competencia}`,
            dataType: 'json',
            success: dados => {
                console.log(dados);
                $('#numeroVendas').html(dados.dashboard.numeroVendas)
                $('#totalVendas').html(dados.dashboard.total_vendas)
                $('#despesas').html(dados.dashboard.despesas)
                },
            error: erro => {console.log(erro)}
        })
    })
})

