// --- LOGICA GEOLOCALIZZAZIONE ---

// Gestisce il successo della geolocalizzazione: riceve l'oggetto 'position',
// calcola la distanza tra l'utente e la destinazione e aggiorna l'interfaccia. 
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

    // // Inserisco il risultato formattato (2 decimali) all'interno dell'elemento HTML con ID "distanza-info"
    document.getElementById("distanza-info").innerHTML = 
        "La tua posizione attuale dista circa <b>" + distanza.toFixed(2) + " km</b> da The Bowler Club.";
}

// Funzione eseguita nel caso in cui l'utente neghi il permesso, o si verifichi un errore nel rilevamento GPS.
function errore() {
    document.getElementById("distanza-info").innerHTML = "Impossibile recuperare la tua posizione.";
}

/* Punto di ingresso: verifica se il browser supporta le API di geolocalizzazione.
   In caso positivo, richiede la posizione attuale (richiesta One-Shot). */
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(mostraPosizione, errore);
} else {
    // Gestione del caso in cui il browser sia troppo datato o non supporti la funzione
    document.getElementById("distanza-info").innerHTML = "Geolocalizzazione non supportata dal browser.";
}


// --- LOGICA CAROSELLO ---

// Permette di scorrere orizzontalmente il contenitore dei giochi di 320 pixel verso destra o verso 
// sinistra con un'animazione fluida, a seconda che il parametro direction sia positivo o negativo.
function scrollCarousel(direction) {
    const carousel = document.getElementById('gameCarousel');
    const scrollAmount = 320; // Larghezza card + gap
    carousel.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
}


// --- LOGICA SIDEBAR E EVIDENZIAZIONE ---
document.querySelectorAll('.sidebar-left a').forEach(link => {
    link.addEventListener('click', function (e) {
        // impediamo il comportamento standard del link
        e.preventDefault(); 

        const targetId = this.getAttribute('href').substring(1); // Prende la stringa dall'href e rimuove il # iniziale 
        const targetDiv = document.getElementById(targetId);

        if (targetDiv) {
            // logica per scorrere il carosello fino alla card
            // 'inline: center' è la magia che centra l'elemento orizzontalmente
            targetDiv.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest',
                inline: 'center'
            });

            // Rimuove l'evidenziazione da tutte le altre card
            document.querySelectorAll('.card-gioco-carousel').forEach(el => {
                el.classList.remove('evidenzia');
            });

            // Aggiunge l'effetto visivo alla card trovata
            // Usiamo un piccolo ritardo per permettere allo scroll di arrivare
            setTimeout(() => {
                targetDiv.classList.add('evidenzia');
            }, 300);

            // Rimuove l'effetto dopo 2.5 secondi
            setTimeout(() => {
                targetDiv.classList.remove('evidenzia');
            }, 1500);
        }
    });
});