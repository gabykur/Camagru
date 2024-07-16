document.addEventListener("DOMContentLoaded", () => {
    const commentTextarea = document.querySelector('.textbox');
    const commentForm = document.getElementById('commentForm');

    commentTextarea.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            commentForm.submit();
        }
    });
});
