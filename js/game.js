function sendStep1() {
    const c = document.getElementById("c").value;
    const d = document.getElementById("d").value;

    fetch("game_action.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            step: 1,
            c: c,
            d: d
        })
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById("response").value = data.reply;
        document.getElementById("step1").classList.add("hidden");
        document.getElementById("step2").classList.remove("hidden");
    });
}

function sendStep2() {
    const topic = document.getElementById("topic").value;
    const industry = document.getElementById("industry").value;
    const objective = document.getElementById("objective").value;

    fetch("game_action.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            step: 2,
            topic: topic,
            industry: industry,
            objective: objective
        })
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById("response").value += "\n\n" + data.reply;
    });
}
