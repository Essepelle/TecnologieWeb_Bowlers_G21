/**
 * Gestione Calendario Nativo e Generazione Orari
 */

function initPrenotazione(nomeGioco) {
    const dataInput = document.getElementById("data_prenotazione");
    const container = document.getElementById('orari-bottoni-container');

    // Impostiamo la data minima a OGGI (usando l'orario locale, non UTC)
    const adesso = new Date();
    // Formatta in YYYY-MM-DD rispettando il fuso orario locale
    const anno = adesso.getFullYear();
    const mese = String(adesso.getMonth() + 1).padStart(2, '0');
    const giorno = String(adesso.getDate()).padStart(2, '0');
    const oggiLocale = `${anno}-${mese}-${giorno}`;
    
    //giorno minimo del calendario
    dataInput.setAttribute("min", oggiLocale);

    //operazioni da fare ad ogni cambio della data nel calendario
    dataInput.addEventListener("change", function() {
        const dataScelta = this.value; // Formato YYYY-MM-DD
        
        // Se l'utente cancella la data, svuota tutto e fermati
        if (!dataScelta) {
            container.innerHTML = "<p>Seleziona prima una data valida!</p>";
            return;
        }

        const dataObj = new Date(dataScelta);
        const giornoSettimana = dataObj.getDay(); // 0=Dom, 1=Lun, ..., 3=Mer, 5=Ven

        //Se il gioco è "Torneo di Carte" sono validi solo Mercoledì=3 e Venerdì=5
        if (nomeGioco === 'Torneo di Carte') { 
            if (giornoSettimana !== 3 && giornoSettimana !== 5) {
                alert("Per il Torneo di Carte si prenota solo di Mercoledì e Venerdì!");
                this.value = ""; // Cancella la data sbagliata
                container.innerHTML = "<p>Data non valida per il Torneo di Carte.</p>";
                return;
            }
        }

        // Se è tutto ok, genera i bottoni
        generaBottoniOrari(dataScelta, nomeGioco);
    });
}

function generaBottoniOrari(dataScelta, nomeGioco) {
    const container = document.getElementById('orari-bottoni-container');
    const hiddenInput = document.getElementById('ora_prenotazione_valore');

    // 1. Memorizza il valore che arriva dal PHP (Sticky)
    const orarioPrecedente = hiddenInput.value;
    
    // Reset del contenitore
    container.innerHTML = ""; 
    hiddenInput.value = "";   

    // Calcoliamo la data di oggi in stringa YYYY-MM-DD locale per i confronti
    const adesso = new Date();
    const anno = adesso.getFullYear();
    const mese = String(adesso.getMonth() + 1).padStart(2, '0');
    const giorno = String(adesso.getDate()).padStart(2, '0');
    const oggiStr = `${anno}-${mese}-${giorno}`;

    const oraCorrente = adesso.getHours();
    const minutiCorrenti = adesso.getMinutes();

    let orari = [];

    // --- DEFINIZIONE ORARI DISPONIBILI ---
    if (nomeGioco === 'Torneo di Carte') {
        // Le carte hanno solo un orario fisso
        orari.push({ h: 21, m: 0, label: "21:00" });
    } else {
        // Altri giochi: dalle 17:00 alle 01:00 (del giorno dopo)
        // Nota: 0 e 1 sono le ore del mattino successivo
        const base = [17, 18, 19, 20, 21, 22, 23, 0, 1]; 
        
        base.forEach(h => {
            // Aggiungi orario intero (es. 18:00)
            orari.push({ h: h, m: 0, label: (h < 10 ? "0"+h : h) + ":00" });
            // Aggiungi mezz'ora (es. 18:30)
            orari.push({ h: h, m: 30, label: (h < 10 ? "0"+h : h) + ":30" });
        });
    }

    // creazione di ogni singolo bottone associato ad ogni orario nell'array "orari"
    orari.forEach(slot => {
        const btn = document.createElement('button');
        btn.type = "button"; // Importante: evita che invii il form
        btn.className = "btn-orario"; // Usa la classe CSS definita
        btn.innerText = slot.label;

        let disabilitato = false;

        // logica per disabilitare i bottoni degli orari già passati 
        if (dataScelta === oggiStr) {
            // da 00:00 a 01:30 sono le prime ore della giornata corrente,
            // se siamo di pomeriggio sono orari già passati da molte ore.
            // basta solo un semplice controllo per tutte le ore del giorno:
            if (slot.h < oraCorrente || (slot.h === oraCorrente && slot.m <= minutiCorrenti)) {
                disabilitato = true;
            }
        }
        // Se la data scelta è nel passato (ma il min="today" dovrebbe impedirlo)
        else if (new Date(dataScelta) < new Date(oggiStr)) {
            disabilitato = true;
        }

        if (disabilitato) {
            btn.disabled = true;
            btn.title = "Orario passato";
        } else {

            // se l'orario corrente è uguale a quello salvato in precedenza (sticky), selezionalo e ripristina il valore nel form
            if (slot.label === orarioPrecedente) {                                               
                btn.classList.add('selected');
                hiddenInput.value = slot.label; // Ripristina il valore nel form
            }
            
            // evento click sul bottone orario
            btn.onclick = function() {
                // Rimuovi classe 'selected' da tutti gli altri bottoni con un forEach simile a quello usato in precedenza
                const allBtns = container.querySelectorAll('.btn-orario');
                allBtns.forEach(b => b.classList.remove('selected'));
                
                // aggiungi classe al bottone cliccato per lo stile CSS
                btn.classList.add('selected');
                
                // salva il valore nell'input nascosto per il form
                hiddenInput.value = slot.label;
            };
        }

        container.appendChild(btn);
    });
}