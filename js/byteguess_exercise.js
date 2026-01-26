document.addEventListener("DOMContentLoaded", initWizard);


let ui_id = null;

/* =========================
   HELPERS
========================= */

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
    const l = document.getElementById("loader");
    if (l) l.style.display = show ? "block" : "none";
}

/* =========================
   SAFE FETCH JSON
========================= */
async function safeJSON(response) {
    const text = await response.text();
    try {
        return JSON.parse(text);
    } catch {
        console.error("Invalid JSON from server:", text);
        throw new Error("Invalid JSON");
    }
}

/* =========================
   SAVE STEP (COMMON)
========================= */
function saveStep(step, data, onSuccess) {
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
            onSuccess && onSuccess();
        } else {
            alert(d.message || "Save failed");
        }
    })
    .catch(() => alert("Server error (check PHP logs)"))
    .finally(() => showLoader(false));
}

/* =========================
   STEP 1 – ORGANIZATION
========================= */
function loadStep1() {
    setStep(1);
    showLoader(true);

    fetch("api/byteguess_get_orgs.php")
        .then(safeJSON)
        .then(orgs => {

            let options = `<option value="">-- Create New Organization --</option>`;
            orgs.forEach(o => {
                options += `<option value="${o.ig_id}">${o.ig_name}</option>`;
            });

            document.getElementById("wizard-box").innerHTML = `
                <h3>Step 1: Organization</h3>

                <label>Select Organization</label>
                <select id="org_id" onchange="toggleOrgFields()">
                    ${options}
                </select>

                <div id="new-org-fields" style="display:none;">
                    <label>Organization Name</label>
                    <input
                        id="org_name"
                        placeholder="e.g., Acme Technologies"
                    >

                    <label>Organization Description</label>
                    <textarea
                        id="org_description"
                        rows="3"
                        placeholder="e.g., A company focused on enterprise training"
                    ></textarea>
                </div>

                <button onclick="submitStep1()">Next</button>
            `;

            toggleOrgFields();
        })
        .catch(() => alert("Failed to load organizations"))
        .finally(() => showLoader(false));
}


/* =========================
   TOGGLE NEW ORG FIELDS
========================= */
function toggleOrgFields() {
    const orgId = val("org_id");
    document.getElementById("new-org-fields").style.display =
        orgId ? "none" : "block";
}


/* =========================
   SUBMIT STEP 1
========================= */
function submitStep1() {
    const org_id = val("org_id");

    let payload = { org_id };

    // NEW ORG FLOW
    if (!org_id) {
        const org_name = val("org_name");
        const org_description = val("org_description");

        if (!org_name || !org_description) {
            alert("Organization name and description are required");
            return;
        }

        payload.org_name = org_name;
        payload.org_description = org_description;
    }

    saveStep(1, payload, loadStep2);
}



/* =========================
   STEP 2 – GAME SETUP
========================= */
function loadStep2() {
    setStep(2);

    document.getElementById("wizard-box").innerHTML = `
        <h3>Step 2: Game Setup</h3>

        <label>Game Name</label>
        <input id="ui_game_name" placeholder="e.g., Cyber Awareness Challenge">

        <label>Total Cards</label>
        <input id="ui_total_cards" type="number" min="6" placeholder="e.g., 20">

        <label>Cards Drawn per Round</label>
        <input id="ui_cards_drawn" type="number" placeholder="e.g., 5">

        <button onclick="submitStep2()">Next</button>
    `;
}


function submitStep2() {
    const name = val("ui_game_name");
    const c = parseInt(val("ui_total_cards"));
    const d = parseInt(val("ui_cards_drawn"));

    if (!name || !c || !d || d > c) {
        alert("Invalid game setup");
        return;
    }

    saveStep(2, {
        ui_game_name: name,
        ui_total_cards: c,
        ui_cards_drawn: d
    }, loadStep3);
}

/* =========================
   STEP 3 – CONTEXT
========================= */
function loadStep3() {
    setStep(3);

    document.getElementById("wizard-box").innerHTML = `
        <h3>Step 3: Game Context</h3>

        <label>Training Topic</label>
        <input id="ui_training_topic" placeholder="e.g., Secure Coding Practices">

        <label>Industry</label>
        <input id="ui_industry" placeholder="e.g., IT / Banking / Healthcare">

        <label>Objective</label>
        <input id="ui_objective" placeholder="e.g., Improve awareness of threats">

        <label>Hypothesis</label>
        <input id="ui_hypothesis" placeholder="e.g., Practice improves retention">

        <button onclick="submitStep3()">Next</button>
    `;
}


function submitStep3() {
    saveStep(3, {
        ui_training_topic: val("ui_training_topic"),
        ui_industry: val("ui_industry"),
        ui_objective: val("ui_objective"),
        ui_hypothesis: val("ui_hypothesis")
    }, loadStep4);
}

/* =========================
   STEP 4 – CARD STRUCTURE
========================= */
function loadStep4() {
    setStep(4);

    document.getElementById("wizard-box").innerHTML = `
        <h3>Step 4: Card Structure</h3>

        <label>Card Content Structure</label>
        <input
            id="ui_card_structure"
            placeholder="e.g., Facts, Signals, Clues"
        >

        <button onclick="submitStep4()">Next</button>
    `;
}

function submitStep4() {
    saveStep(4, {
        ui_card_structure: val("ui_card_structure")
    }, loadStep5);
}

/* =========================
   STEP 5 – OPTIONS
========================= */
function loadStep5() {
    setStep(5);

    document.getElementById("wizard-box").innerHTML = `
        <h3>Step 5: Options Mix</h3>

        <label>Fully Correct Options</label>
        <input id="opt_full" type="number" value="1">

        <label>Partially Correct Options</label>
        <input id="opt_partial" type="number" value="1">

        <label>Incorrect Options</label>
        <input id="opt_wrong" type="number" value="2">

        <button onclick="submitStep5()">Next</button>
    `;
}


function submitStep5() {
    saveStep(5, {
        ui_options: {
            full: val("opt_full"),
            partial: val("opt_partial"),
            wrong: val("opt_wrong")
        }
    }, loadStep6);
}

/* =========================
   STEP 6 – GENERATE
========================= */
function loadStep6() {
    setStep(6);

    document.getElementById("wizard-box").innerHTML = `
        <h3>Step 6: Generate Exercise</h3>

        <p style="margin-bottom:16px;color:#475569;">
            Review all inputs and generate the exercise.
        </p>

        <button id="genBtn" onclick="generateExercise()">Generate</button>
        <p id="genStatus" style="margin-top:12px;color:#64748b;"></p>
    `;
}


async function generateExercise() {
    const btn = document.getElementById("genBtn");
    const status = document.getElementById("genStatus");

    btn.disabled = true;
    status.innerText = "Generating exercise… this may take up to 30 seconds.";
    showLoader(true);

    try {
        const res = await fetch("api/byteguess_generate_game.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ ui_id })
        });

        const text = await res.text();   // 👈 CRITICAL
        let data;

        try {
            data = JSON.parse(text);
        } catch {
            console.error("RAW SERVER RESPONSE:", text);
            throw new Error("Invalid JSON from server");
        }

        if (data.status !== "success") {
            throw new Error(data.message || "Generation failed");
        }

        document.getElementById("wizard-box").innerHTML = `
            <h3>Exercise Created Successfully 🎉</h3>
            <p>Exercise ID: ${data.cg_id}</p>
        `;

    } catch (err) {
        alert("Generation failed. Check server logs.");
        console.error(err);
        btn.disabled = false;
    } finally {
        showLoader(false);
    }
}
