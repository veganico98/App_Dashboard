$(document).ready(() => {

    // $.ajax({
    //     type: 'GET',
    //     url: 'app.php',
    //     data: { dadosFixos: true },
    //     dataType: 'json',
    //     success: dados => { 
    //          $('#clientesAtivos').html(dados.clientesAtivos)
    //     },
    //     error: erro => { console.log(erro) }
    // })
	
    $('#btnDashboard').on('click', () => {
        $('#telaDashboard').show();
    })
    
    
    $('#documentacao').on('click', () => {
        // $('#pagina').load('documentacao.html')
        
        // $.get('documentacao.html', data => {
        //     $('#pagina').html(data)
        // })

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
        // console.log(competencia)
        
        $.ajax({
            type: 'GET',
            url: 'app.php',
            data: `competencia=${competencia}`,
            dataType: 'json',
            success: dados => {
                $('#numeroVendas').html(dados.numeroVendas)
                $('#totalVendas').html(dados.total_vendas)
                $('#clientesAtivos').html(dados. clientesAtivos)
                },
            error: erro => {console.log(erro)}
        })

        //método, url, dados, sucesso/ erro
    })
})

