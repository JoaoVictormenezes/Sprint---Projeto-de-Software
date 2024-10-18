$(document).ready(function() {
    $('#formzin').submit(function(event) {
        // Impedir o envio padrão do formulário
        event.preventDefault();
        
        // Obter os dados do formulário
        var formData = new FormData($(this)[0]);
        
        // Enviar os dados do formulário usando AJAX
        $.ajax({
            url: 'insertprofile.php',
            type: 'POST',
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                // Exibir a resposta do servidor na página
                $('#result').html(response);
            }
        });
    });
});
