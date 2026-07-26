setTimeout(() => {
    const alertBox = document.getElementById('alertBox');
    if(alertBox){
        alertBox.style.opacity = '0';
        setTimeout(() => alertBox.remove(), 4000);
    }
}, 4000);