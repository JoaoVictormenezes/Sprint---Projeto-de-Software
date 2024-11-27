const socket = io();
const messages = document.getElementById('messages');
const input = document.getElementById('input');
const recipientId = 'ID_DO_DESTINATARIO'; // Substitua pelo ID do destinatário ou obtenha dinamicamente

socket.emit('joinChat', recipientId);

document.getElementById('form').addEventListener('submit', (e) => {
    e.preventDefault();
    if (input.value) {
        socket.emit('message', input.value, recipientId);
        input.value = '';
    }
});

socket.on('message', (msg) => {
    const li = document.createElement('li');
    li.textContent = msg;
    messages.appendChild(li);
});
