// Crie uma conexão WebSocket
const socket = new WebSocket('ws://localhost:8080');

// Quando a conexão for aberta
socket.addEventListener('open', function (event) {
    console.log('Conectado ao servidor WebSocket');
});

// Quando receber uma mensagem do servidor
socket.addEventListener('message', function (event) {
    const message = event.data;
    // Adicione a lógica para exibir a mensagem no pop-up de chat
    console.log('Mensagem recebida:', message);
});

// Para enviar mensagens
function sendMessage(msg) {
    if (socket.readyState === WebSocket.OPEN) {
        socket.send(msg);
    } else {
        console.error('WebSocket não está aberto.'); 
    }
}

// Chame sendMessage() com a mensagem que deseja enviar
