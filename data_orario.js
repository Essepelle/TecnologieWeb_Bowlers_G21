/**
 * Inizializza il calendario e la logica degli orari
 * @param {string} nomeGioco - Nome del gioco passato da PHP
 */
function initPrenotazione(nomeGioco) {
    // Configurazione Calendario
    flatpickr("#data_prenotazione", {
        locale: "it",
        minDate: "today",
        dateFormat: "Y-m-d",
        disableMobile: "true",
        enable: [
            function(date) {
                // Se Carte, abilita solo Mercoledì(3) e Venerdì(5)
                if (nomeGioco === 'Carte') {
                    return (date.getDay() === 3 || date.getDay() === 5);
                }
                return true;
            }
        ],
        onChange: function(selectedDates, dateStr) {
            generaBottoniOrari(dateStr, nomeGioco);
        }
    });
}

function generaBottoniOrari(dataScelta, nomeGioco) {
    const container = document.getElementById('orari-bottoni-container');
    const hiddenInput = document.getElementById('ora_prenotazione_valore');
    const oraLocale = new Date();
    const oggiStr = oraLocale.toISOString().split('T')[0];
    const oraCorrente = oraLocale.getHours();
    const minutiCorrenti = oraLocale.getMinutes();

    container.innerHTML = ""; 
    hiddenInput.value = "";   

    let orari = [];
    if (nomeGioco === 'Carte') {
        orari.push({ h: 21, m: 0, label: "21:00" });
    } else {
        const base = [0, 1, 17, 18, 19, 20, 21, 22, 23];
        base.forEach(h => {
            orari.push({ h: h, m: 0, label: (h < 10 ? "0"+h : h) + ":00" });
            orari.push({ h: h, m: 30, label: (h < 10 ? "0"+h : h) + ":30" });
        });
    }

    orari.forEach(slot => {
        const btn = document.createElement('button');
        btn.type = "button";
        btn.className = "btn-orario";
        btn.innerText = slot.label;

        // Logica orario passato
        let disabilitato = false;
        if (dataScelta === oggiStr) {
            // Se l'orario è 00:00 o 01:00, sono le prime ore del mattino di OGGI.
            // Se l'utente sta navigando di pomeriggio/sera, queste ore sono già passate.
            if (slot.h < 5) {
                disabilitato = true; 
            } else {
                // Per gli orari serali (17-23), controllo standard
                if (slot.h < oraCorrente || (slot.h === oraCorrente && slot.m <= minutiCorrenti)) {
                    disabilitato = true;
                }
            }
        }

        if (disabilitato) {
            btn.disabled = true;
        } else {
            btn.onclick = function() {
                document.querySelectorAll('.btn-orario').forEach(b => b.classList.remove('selected'));
                btn.classList.add('selected');
                hiddenInput.value = slot.label;
            };
        }
        container.appendChild(btn);
    });
}