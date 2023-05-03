window.onload = function () {
    generateEntities(document.querySelectorAll(".intermediate-property"));
}

function generateEntities(nodes) {
    nodes.forEach(node => {
        let intermediate = node.parentNode;
        let properties = JSON.parse(intermediate.dataset.properties);

        //Tlačítko pro odebrání
        let removeButton = document.createElement("button");
        removeButton.type = "button";
        removeButton.innerHTML = "Odebrat";
        removeButton.addEventListener("click", event => {intermediate.removeChild(node);});
        node.append(removeButton);

        properties.forEach(prop => {
            let row = document.createElement("div");
            row.classList.add("form-row");
            node.append(row);
            
            let label = document.createElement("label");
            label.for = prop.name;
            label.innerText = prop.displayName; 
            row.append(label);
            
            let col = document.createElement("div");
            col.classList.add("col");
            row.append(col);
            
            if(prop.type == "select") {
                let select = document.createElement("select");
                select.name = prop.name;

                let available = prop.available;
                available.forEach(optionVal => {
                    let option = document.createElement("option");
                    option.value = optionVal.value;
                    option.innerText = optionVal.display;
                    select.append(option);
                });

                col.append(select);
            } else {
                let input = document.createElement("input");
                input.name = prop.name;
                input.type = prop.type;

                col.append(input);
            }
        });
    });
}

function addRecord(target) {
    let newRecord = document.createElement("div");
    newRecord.classList.add("intermediate-property");
    newRecord.dataset.value = "";
    target.parentNode.append(newRecord);
    generateEntities([newRecord]);
}

/**
 * Je tohle ještě důležité?
 *            |
 *            v
 */

/**
 * Tohle je příklad funkce která je v `onload` zaregistrovaná na všech selectech pro mezitabulky
 * @param {Event} event
 */
function onchange(event) {
    // V proměnné `event` jsou teď uloženy informace o tom, co se stalo
    // https://developer.mozilla.org/en-US/docs/Web/API/Event
    console.log(event);

    console.log("nějaký select pro mezitabulku byl změněn");
}



/**
 * Tohle je funkce zavolaná automaticky z HTML po načtení body.
 */
function onload() {
    // Najdeme všchny řádky pomocí CSS selectoru, co mají atribut s daty z mezitabulek (data-intermediate)
    // víc info o selektorech je na https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_Selectors
    const ROW_SELECTOR = "div.form-row[data-intermediate]";
    let radky = document.querySelectorAll(ROW_SELECTOR);
    
    // pro každý řádek
    radky.forEach(radek => {
        
        // Z atributu toho řádku si vytáhneme uložený string (`data-` je speciální typ atributu, ze kterého lze číst pomocí vlastnosti `dataset`)
        const serializovanaData = radek.dataset.intermediate;

        // Serializované znamená, že je všechno zabalené do jednoho dlouhého stringu.
        // Takže to musíme rozbalit pomocí `JSON.parse(ten string)`
        const dataZMezitabulek = JSON.parse(serializovanaData);

        // Vypíšem do konzole, jaká data se tam tedy ve skutečnosti nachází
        console.log(dataZMezitabulek);
    });

    // Najdeme všechny selecty které se používají pro mezitabulky
    const SELECT_MEZITABULKY = "div.form-row[data-intermediate] .col select"
    let selecty = document.querySelectorAll(ROW_SELECTOR);

    // Zaregistrujeme funkci s názvem `onchange` jako handler `change` eventů pro všechny selecty
    // https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/change_event
    selecty.forEach(select => {
        select.addEventListener("change", onchange);
    });
}
