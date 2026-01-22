document.addEventListener("DOMContentLoaded", () => {
    loadStep1();
});

function setStep(n) {
    document.querySelectorAll(".step-dot").forEach(d => d.classList.remove("active"));
    const dot = document.getElementById("step-dot-" + n);
    if (dot) dot.classList.add("active");
}

function showLoader(show) {
    const loader = document.getElementById("loader");
    if (loader) loader.style.display = show ? "block" : "none";
}

/* =========================
   STEP 1 – ORGANIZATION
========================= */

function loadStep1() {
    setStep(1);
    showLoader(true);

    fetch("byteguess_get_orgs.php")
        .then(r => r.json())
        .then(data => {
            let options = `<option value=""> Select Organization </option>`;
            data.forEach(o => {
                options += `<option value="${o.ig_id}">${o.ig_name}</option>`;
            });

            document.getElementById("wizard-box").innerHTML = `
                <h3>Organization</h3>
                <p class="txt1" style="text-align:center;">Select Organization</p>
                <select class="section-label"id="org_select">${options}</select>
                <hr>
                <br>
                <p class="txt1" style="text-align:center;">Or Create New</p>
                <br>
                <label>Organization Name</label>
                <input id="org_name" placeholder="Eg">
                <br>
                <label for="org_desc">Description (optional)</label>
                <textarea id="org_desc" placeholder="Eg"></textarea>
                <button onclick="submitOrg()">Next</button>
            `;
        })
        .catch(err => console.error("Error loading orgs:", err))
        .finally(() => showLoader(false));
}

function submitOrg() {
    const orgSelect = document.getElementById("org_select");
    const orgNameInput = document.getElementById("org_name");
    const orgDescInput = document.getElementById("org_desc");

    const orgId = orgSelect.value;
    const name = orgNameInput.value.trim();
    const desc = orgDescInput.value.trim();

    let body;
    let url;

    if (orgId) {
        url = "byteguess_set_org.php";
        body = JSON.stringify({ ig_id: orgId });
    } else {
        if (!name) { 
            alert("Organization name required"); 
            return; // Exit early if validation fails
        }
        url = "byteguess_start_action.php";
        body = `ig_name=${encodeURIComponent(name)}&ig_description=${encodeURIComponent(desc)}`;
    }

    showLoader(true);

    fetch(url, {
        method: "POST",
        headers: orgId ? { "Content-Type":"application/json" } :
                         { "Content-Type":"application/x-www-form-urlencoded" },
        body
    })
    .then(r => r.json())
    .then(d => {
        if (d.status === "success") loadStep2();
        else alert(d.message);
    })
    .finally(() => showLoader(false));
}

/* =========================
   STEP 2 – GAME STRUCTURE
========================= */

function loadStep2() {
    setStep(2);
    document.getElementById("wizard-box").innerHTML = `
        <h3>Game Structure</h3>

        <label for="cg_name">Training Game Name</label>
        <input id="cg_name" placeholder="Eg: Memory Match">

        <label for="cg_des">Description</label>
        <textarea id="cg_des" placeholder="Eg: Card matching based training game"></textarea>

        <label for="c">Total Cards (C)</label>
        <input id="c" type="number" min="6" placeholder="Eg: 10">

        <label for="d">Cards Drawn (D)</label>
        <input id="d" type="number" placeholder="Eg: 6">

        <button onclick="submitStep2()">Next</button>

    `;
}

function submitStep2() {
    showLoader(true);

    fetch("byteguess_step2_action.php", {
        method:"POST",
        headers:{ "Content-Type":"application/json" },
        body: JSON.stringify({
            cg_name: document.getElementById("cg_name").value,
            cg_des: document.getElementById("cg_des").value,
            c: document.getElementById("c").value,
            d: document.getElementById("d").value
        })
    })
    .then(r=>r.json())
    .then(d=>{
        if(d.status==="success") loadStep3();
        else alert(d.message);
    })
    .finally(()=>showLoader(false));
}

/* =========================
   STEP 3 – GAME CONTEXT
========================= */

function loadStep3() {
    setStep(3);

    document.getElementById("wizard-box").innerHTML = `
    <h3>Game Context</h3>

    <label for="A">Training Topic / Participants</label>
    <input id="A" placeholder="Eg: Supply chain inventory management">

    <label for="A1">Industry</label>
    <input id="A1" placeholder="Eg: retail">

    <label for="B">Game Objective</label>
    <input id="B" placeholder="Eg: to appreciate cross functional collaboration">

    <label for="B1">Underlying Hypothesis</label>
    <input id="B1" placeholder="Eg: appreciate cross functional collaboration in demand planning">

    <button onclick="submitStep3()">Next</button>

    `;
}

function submitStep3() {
    showLoader(true);

    fetch("byteguess_step3_action.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            A: document.getElementById("A").value,
            A1: document.getElementById("A1").value,
            B: document.getElementById("B").value,
            B1: document.getElementById("B1").value
        })
    })
    .then(r => r.json())
    .then(d => {
        if (d.status === "success") loadStep4();
        else alert(d.message);
    })
    .finally(() => showLoader(false));
}

/* =========================
   STEP 4 – GENERATE CARDS
========================= */

function loadStep4() {
    setStep(4);

    document.getElementById("wizard-box").innerHTML = `
        <h3>Card Structure</h3>
        <label for="E">Card structure</label>
        <input id="E" placeholder=" Eg: statistics and others">
        <button onclick="submitStep4()">Generate Cards</button>
    `;
}

function submitStep4() {
    showLoader(true);

    fetch("byteguess_step4_action.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            E: document.getElementById("E").value
        })
    })
    .then(r => r.json())
    .then(d => {
        if (d.status === "success") loadStep5();
        else alert(d.message);
    })
    .finally(() => showLoader(false));
}

/* =========================
   STEP 5 – HYPOTHESIS OPTIONS
========================= */

let f1 = 10;
let f2 = 5;
let f3 = 1;

function loadStep5() {
    setStep(5);

    document.getElementById("wizard-box").innerHTML = `
      <div class="Points to Answers">

        <div class="option-card correct">
          <div class="option-info">
            <h4>Fully Correct Options</h4>
            <p>Answers that award 100% points.</p>
          </div>
          <div class="counter">
            <button onclick="changeF('f1', -1)">−</button>
            <span id="f1">${f1}</span>
            <button onclick="changeF('f1', 1)">+</button>
          </div>
        </div>

        <div class="option-card partial">
          <div class="option-info">
            <h4>Partially Correct Options</h4>
            <p>Answers that award partial points.</p>
          </div>
          <div class="counter">
            <button onclick="changeF('f2', -1)">−</button>
            <span id="f2">${f2}</span>
            <button onclick="changeF('f2', 1)">+</button>
          </div>
        </div>

        <div class="option-card incorrect">
          <div class="option-info">
            <h4>Incorrect Options</h4>
            <p>Distractors that award 0 points.</p>
          </div>
          <div class="counter">
            <button onclick="changeF('f3', -1)">−</button>
            <span id="f3">${f3}</span>
            <button onclick="changeF('f3', 1)">+</button>
          </div>
        </div>

        <button class="primary-btn" onclick="submitStep5()">Generate Options</button>

      </div>
    `;
}

function changeF(key, delta) {
    if (key === "f1") f1 = Math.max(0, f1 + delta);
    if (key === "f2") f2 = Math.max(0, f2 + delta);
    if (key === "f3") f3 = Math.max(0, f3 + delta);

    document.getElementById("f1").innerText = f1;
    document.getElementById("f2").innerText = f2;
    document.getElementById("f3").innerText = f3;
}

function submitStep5() {
    showLoader(true);

    fetch("byteguess_step5_action.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ f1, f2, f3 })
    })
    .then(r => r.json())
    .then(d => {
        if (d.status === "success") loadStep6();
        else alert(d.message);
    })
    .finally(() => showLoader(false));
}

/* =========================
   STEP 6 – ANSWER KEY
========================= */

function loadStep6() {
    setStep(6);

    document.getElementById("wizard-box").innerHTML = `
        <h3>Generate Answer Key</h3>
        <button onclick="submitStep6()">Generate Answer Key</button>
    `;
}

function submitStep6() {
    showLoader(true);

    fetch("byteguess_step6_action.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" }
    })
    .then(r => r.json())
    .then(d => {
        if (d.status === "success") loadStep7();
        else alert(d.message);
    })
    .finally(() => showLoader(false));
}

/* =========================
   STEP 7 – GAME GUIDELINES
========================= */

function loadStep7() {
    setStep(7);

    document.getElementById("wizard-box").innerHTML = `
        <h3>Game Guidelines</h3>
        <button onclick="submitStep7()">Generate Guidelines</button>
    `;
}

function submitStep7() {
    showLoader(true);

    fetch("byteguess_step7_action.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" }
    })
    .then(r => r.json())
    .then(d => {
        if (d.status === "success") {
            document.getElementById("wizard-box").innerHTML = `
                <h2>Exercise Created Successfully </h2>
            `;
        } else {
            alert(d.message);
        }
    })
    .finally(() => showLoader(false));
}
