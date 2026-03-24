
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById("editProfileModal");
    var span = document.getElementsByClassName("close-btn")[0];

    if(span) {
        span.onclick = function() {
            modal.style.display = "none";
        }
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    var profileForm = document.getElementById("profileForm");
    if(profileForm) {
        profileForm.onsubmit = function(e) {
            e.preventDefault();
            
            var formData = new FormData(this);
            
            fetch('index.php?action=update_profile', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    modal.style.display = "none";
                    showNotification("Perfil atualizado com sucesso!");
                } else {
                    alert('Erro: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocorreu um erro. Tente novamente.');
            });
        }
    }
});

function showNotification(message) {
    var notification = document.getElementById("notification");
    notification.textContent = message;
    notification.style.display = "block";
    
    setTimeout(function() {
        notification.style.display = "none";
    }, 3000);
}
