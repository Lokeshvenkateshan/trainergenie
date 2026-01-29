document.addEventListener("DOMContentLoaded", loadStep1);

let ui_id = null;
let draftData = {}; // Local cache to persist data when navigating back/forth

/* ================= HELPERS ================= */

function val(id) {
    const el = document.getElementById(id);
    return el ? el.value.trim() : "";
}

function setStep(n) {
    document.querySelectorAll(".step-dot").forEach(d => d.classList.remove("active"));
    const dot = document.getElementById(`step-dot-${n}`);
    if (dot) dot.classList.add("active");
}

function showLoader(show) {
    const loader = document.getElementById("loader");
    if (loader) loader.style.display = show ? "block" : "none";
}

async function safeJSON(res) {
    const text = await res.text();
    try {
        return JSON.parse(text);
    } catch {
        throw new Error("Invalid server response");
    }
}

/**
 * Generates the standard button footer for the wizard
 */
function getButtonFooter(nextAction, nextLabel = "Next", prevAction = null) {
    let html = `<div class="button-footer" style="margin-top: 24px; display: flex; justify-content: space-between;">`;
    
    if (prevAction) {
        html += `<button type="button" onclick="${prevAction}" class="btn-secondary" style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; padding: 14px 24px; border-radius: 16px; font-weight: 700; cursor: pointer;">Previous</button>`;
    } else {
        html += `<span></span>`; // Spacer
    }
    
    html += `<button type="button" onclick="${nextAction}" style="padding: 14px 24px; border-radius: 16px; font-weight: 700; background: #2563eb; color: #ffffff; border: none; cursor: pointer;">${nextLabel}</button>`;
    html += `</div>`;
    return html;
}

/* ================= SAVE LOGIC ================= */

function saveStep(step, data, next) {
    // Update local cache
    draftData = { ...draftData, ...data };
    
    showLoader(true);
    fetch("api/byteguess_save_step.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ ui_id, step, data })
    })
    .then(safeJSON)
    .then(d => {
        if (d.status === "success") {
            if (d.ui_id) ui_id = d.ui_id; 
            if (next) next();
        } else {
            alert(d.message || "Save failed");
        }
    })
    .catch(err => alert("Server error: " + err.message))
    .finally(() => showLoader(false));
}

/* ================= STEPS ================= */

function loadStep1() {
    setStep(1);
    document.getElementById("wizard-box").innerHTML = `
        <h3>Byte Guess Game Creation</h3>
        <label>Game Name</label>
        <input id="ui_game_name" placeholder="Enter Game Name" value="${draftData.ui_game_name || ''}">
        
        <label>Game Description</label>
        <textarea id="ui_game_description" placeholder="Enter Game Description">${draftData.ui_game_description || ''}</textarea>
        
        <div style="display: flex; gap: 15px;">
            <div style="flex: 1;">
                <label>Total Cards</label>
                <input id="ui_total_cards" type="number" min="6" value="${draftData.ui_total_cards || 12}">
            </div>
            <div style="flex: 1;">
                <label>Cards Drawn</label>
                <input id="ui_cards_drawn" type="number" value="${draftData.ui_cards_drawn || 4}">
            </div>
        </div>

        <label>Card Structure</label>
        <textarea id="ui_card_structure" placeholder="Facts, signals, clues...">${draftData.ui_card_structure || ''}</textarea>
        
        ${getButtonFooter("submitStep1()")}
    `;
}

function submitStep1() {
    saveStep(1, {
        ui_game_name: val("ui_game_name"),
        ui_game_description: val("ui_game_description"),
        ui_total_cards: parseInt(val("ui_total_cards")),
        ui_cards_drawn: parseInt(val("ui_cards_drawn")),
        ui_card_structure: val("ui_card_structure")
    }, loadStep2);
}

function loadStep2() {
    setStep(2);
    document.getElementById("wizard-box").innerHTML = `
        <h3>Game Content</h3>
        <label>Training Topic</label>
        <input id="ui_training_topic" placeholder="Training Topic" value="${draftData.ui_training_topic || ''}">
        
        <label>Industry</label>
        <input id="ui_industry" placeholder="Industry" value="${draftData.ui_industry || ''}">
        
        <label>Objective</label>
        <input id="ui_objective" placeholder="Objective" value="${draftData.ui_objective || ''}">
        
        <label>Hypothesis</label>
        <textarea id="ui_hypothesis" placeholder="Hypothesis">${draftData.ui_hypothesis || ''}</textarea>
        
        ${getButtonFooter("submitStep2()", "Next", "loadStep1()")}
    `;
}

function submitStep2() {
    saveStep(2, {
        ui_training_topic: val("ui_training_topic"),
        ui_industry: val("ui_industry"),
        ui_objective: val("ui_objective"),
        ui_hypothesis: val("ui_hypothesis")
    }, loadStep3);
}

/* --- Helper for Counter Buttons --- */
function changeVal(id, delta) {
    const input = document.getElementById(id);
    if (!input) return;
    const newVal = parseInt(input.value) + delta;
    const min = parseInt(input.getAttribute('min')) || 0;
    if (newVal >= min) {
        input.value = newVal;
    }
}

function loadStep3() {
    setStep(3);
    const hasClues = draftData.ui_clue > 0;
    
    document.getElementById("wizard-box").innerHTML = `
        <h3>Options Mix & Clues</h3>
        
        <div class="option-card correct">
            <div class="option-icon">✔</div>
            <div class="option-info">
                <label>Fully Correct Options</label>
                <span>Answers that award 100% points.</span>
            </div>
            <div class="stepper-wrap">
                <button type="button" onclick="changeVal('opt_full', -1)">−</button>
                <input id="opt_full" type="number" value="${draftData.ui_options?.full || 1}" min="1" readonly>
                <button type="button" onclick="changeVal('opt_full', 1)">+</button>
            </div>
        </div>
        
        <div class="option-card partial">
            <div class="option-icon">⚠</div>
            <div class="option-info">
                <label>Partially Correct Options</label>
                <span>Answers that award partial points (e.g., 50%).</span>
            </div>
            <div class="stepper-wrap">
                <button type="button" onclick="changeVal('opt_partial', -1)">−</button>
                <input id="opt_partial" type="number" value="${draftData.ui_options?.partial || 1}" min="0" readonly>
                <button type="button" onclick="changeVal('opt_partial', 1)">+</button>
            </div>
        </div>
        
        <div class="option-card wrong">
            <div class="option-icon">✖</div>
            <div class="option-info">
                <label>Incorrect Options</label>
                <span>Distractors that award 0 points.</span>
            </div>
            <div class="stepper-wrap">
                <button type="button" onclick="changeVal('opt_wrong', -1)">−</button>
                <input id="opt_wrong" type="number" value="${draftData.ui_options?.wrong || 2}" min="1" readonly>
                <button type="button" onclick="changeVal('opt_wrong', 1)">+</button>
            </div>
        </div>
        
        <div class="clue-section-styled">
            <div class="clue-header">
                <div class="option-icon clue-icon">?</div>
                <div class="option-info">
                    <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                        <input type="checkbox" id="wants_clues" style="width:auto; margin:0;"
                               ${hasClues ? 'checked' : ''}
                               onchange="toggleClueSelection(this.checked)">
                        Did you want Clues?
                    </label>
                    <span>Additional hints to guide participants.</span>
                </div>
            </div>
            
            <div id="clue_num_wrapper" style="display: ${hasClues ? 'block' : 'none'}; margin-top: 15px; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 15px;">
                <div style="display:flex; align-items:center; justify-content: space-between;">
                    <label style="margin:0 !important;">Number of Clues</label>
                    <div class="stepper-wrap">
                        <button type="button" onclick="changeVal('ui_clue', -1)">−</button>
                        <input id="ui_clue" type="number" value="${draftData.ui_clue || 2}" min="1" readonly>
                        <button type="button" onclick="changeVal('ui_clue', 1)">+</button>
                    </div>
                </div>
            </div>
        </div>
        
        ${getButtonFooter("submitStep3()", "Click to Review", "loadStep2()")}
    `;
}

function toggleClueSelection(isChecked) {
    const wrapper = document.getElementById('clue_num_wrapper');
    if (wrapper) {
        wrapper.style.display = isChecked ? 'block' : 'none';
    }
}

function submitStep3() {
    const hasClues = document.getElementById("wants_clues").checked;
    const clueCount = hasClues ? parseInt(val("ui_clue")) : 0;

    saveStep(3, {
        ui_options: {
            full: val("opt_full"),
            partial: val("opt_partial"),
            wrong: val("opt_wrong")
        },
        ui_clue: clueCount
    }, loadStep4);
}

async function loadStep4() {
    setStep(4);
    showLoader(true);
    try {
        const res = await fetch(`api/byteguess_get_review.php?ui_id=${ui_id}`);
        const data = await safeJSON(res);
        const opts = typeof data.ui_options === 'string' ? JSON.parse(data.ui_options) : data.ui_options;

        document.getElementById("wizard-box").innerHTML = `
            <h3>Review</h3>
            <div class="review-grid" style="text-align: left;">
                <p><strong>Game Name:</strong> ${data.ui_game_name}</p>
                <p><strong>Description:</strong> ${data.ui_game_description}</p>
                <p><strong>Cards:</strong> ${data.ui_total_cards} Total / ${data.ui_cards_drawn} Drawn</p>
                <p><strong>Structure:</strong> ${data.ui_card_structure}</p>
                <hr style="border:0; border-top:1px solid #e2e8f0; margin:15px 0;">
                <p><strong>Context:</strong> ${data.ui_training_topic} | ${data.ui_industry}</p>
                <p><strong>Objective:</strong> ${data.ui_objective}</p>
                <p><strong>Hypothesis:</strong> ${data.ui_hypothesis}</p>
                <hr style="border:0; border-top:1px solid #e2e8f0; margin:15px 0;">
                <p><strong>Mix:</strong> Full (${opts.full}), Partial (${opts.partial}), Wrong (${opts.wrong})</p>
                <p><strong>Clues:</strong> ${data.ui_clue > 0 ? data.ui_clue : 'None'}</p>
            </div>
            ${getButtonFooter("generate()", "Generate", "loadStep3()")}
        `;
    } catch (e) {
        alert("Could not load review data.");
    } finally {
        showLoader(false);
    }
}

async function generate() {
    showLoader(true);
    setStep(6);
    try {
        const res = await fetch("api/byteguess_generate_game.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ ui_id })
        });
        const data = await safeJSON(res);
        if (data.status === "success") {
            document.getElementById("wizard-box").innerHTML = `
                <div style="text-align:center;">
                    <h3 style="color: #059669;">Exercise Created 🎉</h3>
                    <p style="font-size: 18px; margin: 20px 0;">Game ID: <strong>${data.cg_id}</strong></p>
                    <button onclick="location.reload()" style="padding: 14px 24px; border-radius: 16px; font-weight: 700; background: #2563eb; color: #ffffff; border: none; cursor: pointer;">Create New</button>
                </div>`;
        } else {
            alert(data.message);
            loadStep4(); // Go back to review on failure
        }
    } catch (e) {
        alert("Generation failed.");
        loadStep4();
    } finally {
        showLoader(false);
    }
}