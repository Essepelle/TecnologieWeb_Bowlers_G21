/**
 * Gestione Calendario Nativo e Generazione Orari
 */

function initPrenotazione(nomeGioco) {
    const dataInput = document.getElementById("data_prenotazione");
    const container = document.getElementById('orari-bottoni-container');

    // 1. Impostiamo la data minima a OGGI (usando l'orario locale, non UTC)
    const adesso = new Date();
    // Formatta in YYYY-MM-DD rispettando il fuso orario locale
    const anno = adesso.getFullYear();
    const mese = String(adesso.getMonth() + 1).padStart(2, '0');
    const giorno = String(adesso.getDate()).padStart(2, '0');
    const oggiLocale = `${anno}-${mese}-${giorno}`;
    
    dataInput.setAttribute("min", oggiLocale);

    // 2. ASCOLTA IL CAMBIO DATA
    dataInput.addEventListener("change", function() {
        const dataScelta = this.value; // Formato YYYY-MM-DD
        
        // Se l'utente cancella la data, svuota tutto e fermati
        if (!dataScelta) {
            container.innerHTML = "<p>Seleziona prima una data valida!</p>";
            return;
        }

        const dataObj = new Date(dataScelta);
        const giornoSettimana = dataObj.getDay(); // 0=Dom, 1=Lun, ..., 3=Mer, 5=Ven

        // --- CONTROLLO GIOCO CARTE ---
        // --- CONTROLLO GIOCO CARTE ---
        // (Solo Mercoledì=3 e Venerdì=5)
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

    // --- CREAZIONE BOTTONI NEL DOM ---
    if (orari.length === 0) {
        container.innerHTML = "<p>Nessun orario disponibile.</p>";
        return;
    }

    orari.forEach(slot => {
        const btn = document.createElement('button');
        btn.type = "button"; // Importante: evita che invii il form
        btn.className = "btn-orario"; // Usa la classe CSS definita
        btn.innerText = slot.label;

        let disabilitato = false;

        // --- LOGICA DISABILITAZIONE ORARI PASSATI ---
        if (dataScelta === oggiStr) {
            // Se l'orario è "domani mattina presto" (00:00 - 04:00) ed è "oggi" pomeriggio,
            // tecnicamente quegli orari appartengono alla notte che verrà, quindi sono validi.
            // MA se sono le 01:00 di notte ADESSO, le 00:00 sono passate.
            
            // Caso speciale: orari dopo mezzanotte (0, 1)
            if (slot.h < 5) {
                // Se sono le 00:30, non posso prenotare le 00:00
                if (oraCorrente < 5) { // Siamo nella notte
                    if (slot.h < oraCorrente || (slot.h === oraCorrente && slot.m <= minutiCorrenti)) {
                        disabilitato = true;
                    }
                }
                // Se sono le 17:00, le 00:00 e 01:00 sono "future" (stanotte), quindi OK.
            } 
            // Caso standard: orari serali (17 - 23)
            else {
                if (slot.h < oraCorrente || (slot.h === oraCorrente && slot.m <= minutiCorrenti)) {
                    disabilitato = true;
                }
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

            // --- AGGIUNTA: SE È L'ORARIO VECCHIO, SELEZIONALO E RIMETTI IL VALORE ---
            if (slot.label === orarioPrecedente) {
                btn.classList.add('selected');
                hiddenInput.value = slot.label; // Ripristina il valore nel form
            }
            // -----------------------------------------------------------------------
            
            // Evento click sul bottone orario
            btn.onclick = function() {
                // 1. Rimuovi classe 'selected' da tutti gli altri bottoni
                const allBtns = container.querySelectorAll('.btn-orario');
                allBtns.forEach(b => b.classList.remove('selected'));
                
                // 2. Aggiungi classe al bottone cliccato
                btn.classList.add('selected');
                
                // 3. Salva il valore nell'input nascosto per il form
                hiddenInput.value = slot.label;
            };
        }

        container.appendChild(btn);
    });
}