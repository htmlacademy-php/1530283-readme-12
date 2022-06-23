(() => {
    const messagesListNode = document.querySelector('.messages__list');

    if (!messagesListNode) {
        return;
    }

    messagesListNode.scrollTop = messagesListNode.scrollHeight;
})();
