// --- LOGICA GEOLOCALIZZAZIONE ---
// Funzione invocata in caso di successo
function mostraPosizione(position) {
    // Coordinate di Via Picentino, 23, Salerno
    const latDest = 40.64379;
    const lonDest = 14.86524;
    
    // Coordinate dell'utente
    const latUser = position.coords.latitude;
    const lonUser = position.coords.longitude;

    // Calcolo semplificato della distanza (formula di Haversine)
    const R = 6371; // Raggio della Terra in km
    const dLat = (latDest - latUser) * Math.PI / 180;
    const dLon = (lonDest - lonUser) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(latUser * Math.PI / 180) * Math.cos(latDest * Math.PI / 180) * Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    const distanza = R * c;

    document.getElementById("distanza-info").innerHTML = 
        "La tua posizione attuale dista circa <b>" + distanza.toFixed(2) + " km</b> da The Bowler Club.";
}

// Funzione in caso di errore
function errore() {
    document.getElementById("distanza-info").innerHTML = "Impossibile recuperare la tua posizione.";
}

// Richiesta One-Shot (come indicato nelle tue slide)
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(mostraPosizione, errore);
} else {
    document.getElementById("distanza-info").innerHTML = "Geolocalizzazione non supportata dal browser.";
}


// --- LOGICA CAROSELLO ---
function scrollCarousel(direction) {
    const carousel = document.getElementById('gameCarousel');
    const scrollAmount = 320; // Larghezza card + gap
    carousel.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
}


// --- LOGICA SIDEBAR E EVIDENZIAZIONE ---
document.querySelectorAll('.sidebar-left a').forEach(link => {
    link.addEventListener('click', function (e) {
        // 1. Impediamo il comportamento standard del link (che farebbe saltare la pagina)
        e.preventDefault(); 

        const targetId = this.getAttribute('href').substring(1); // Prende l'ID (es. "Bowling")
        const targetDiv = document.getElementById(targetId);

        if (targetDiv) {
            // 2. Logica per scorrere il carosello fino alla card
            // 'inline: center' è la magia che centra l'elemento orizzontalmente
            targetDiv.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest',
                inline: 'center'
            });

            // 3. Rimuove l'evidenziazione da tutte le altre card (pulizia)
            document.querySelectorAll('.card-gioco-carousel').forEach(el => {
                el.classList.remove('evidenzia');
            });

            // 4. Aggiunge l'effetto visivo alla card trovata
            // Usiamo un piccolo ritardo per permettere allo scroll di iniziare
            setTimeout(() => {
                targetDiv.classList.add('evidenzia');
            }, 300);

            // 5. Rimuove l'effetto dopo 2.5 secondi
            setTimeout(() => {
                targetDiv.classList.remove('evidenzia');
            }, 1500);
        }
    });
});