document.getElementById('chat-form').addEventListener('submit', function (e) {
    e.preventDefault();
    const userInput = document.getElementById('user-input').value;
    displayMessage(userInput, 'user');
    document.getElementById('user-input').value = '';

    fetch('chat.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ message: userInput })
    })
    .then(response => response.json())
    .then(data => {
        displayMessage(data.reply, 'bot');
    })
    .catch(error => {
        console.error('Error:', error);
    });
});

function displayMessage(message, sender) {
    const chatBox = document.getElementById('chat-box');
    const messageElement = document.createElement('div');
    messageElement.classList.add('message', sender);
    messageElement.textContent = message;
    chatBox.appendChild(messageElement);
    chatBox.scrollTop = chatBox.scrollHeight;
}
