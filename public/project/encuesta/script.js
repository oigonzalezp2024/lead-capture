let config = [];
let currentIndex = 0;
let userRoute = null;
let answers = {};

async function startSurvey() {
    try {
        const res = await fetch('api.php');
        config = await res.json();
        renderCurrentQuestion();
    } catch (err) {
        document.getElementById('app').innerHTML = "Error cargando la encuesta.";
    }
}

function renderCurrentQuestion() {
    const container = document.getElementById('app');
    const q = config[currentIndex];

    if (!q) {
        submitSurvey();
        return;
    }

    // Lógica de ruteo: si la pregunta es de una ruta distinta a la elegida, saltar
    if (q.route !== 'COMMON' && q.route !== userRoute) {
        currentIndex++;
        renderCurrentQuestion();
        return;
    }

    // Actualizar barra de progreso
    const progress = ((currentIndex + 1) / config.length) * 100;
    document.getElementById('fill').style.width = `${progress}%`;

    container.innerHTML = `<h2>${q.question_text}</h2>`;

    if (q.question_type === 'choice') {
        q.options.forEach(opt => {
            const btn = document.createElement('button');
            btn.className = 'opt-btn';
            btn.innerText = opt.option_label;
            btn.onclick = () => {
                if (opt.next_route_map) userRoute = opt.next_route_map;
                answers.profile_type = opt.option_value;
                currentIndex++;
                renderCurrentQuestion();
            };
            container.appendChild(btn);
        });
    } else {
        const input = q.question_type === 'textarea' 
            ? document.createElement('textarea') 
            : document.createElement('input');
        input.id = 'current-input';
        container.appendChild(input);

        const nextBtn = document.createElement('button');
        nextBtn.className = 'next-btn';
        nextBtn.innerText = 'Siguiente';
        nextBtn.onclick = () => {
            const val = document.getElementById('current-input').value.trim();
            if (!val) return alert("Este campo es necesario.");
            answers[q.codigo_pregunta] = val;
            currentIndex++;
            renderCurrentQuestion();
        };
        container.appendChild(nextBtn);
        input.focus();
    }
}

async function submitSurvey() {
    document.getElementById('app').innerHTML = "<h2>Guardando información...</h2>";
    const payload = {
        profile_type: answers.profile_type,
        answers: answers
    };

    try {
        const res = await fetch('api.php', {
            method: 'POST',
            body: JSON.stringify(payload)
        });
        const result = await res.json();
        if (result.status === 'success') {
            document.getElementById('app').innerHTML = "<h2>¡Gracias! Diagnóstico enviado correctamente.</h2>";
        } else {
            throw new Error(result.message);
        }
    } catch (err) {
        document.getElementById('app').innerHTML = `<h2>Error: ${err.message}</h2>`;
    }
}

document.addEventListener('DOMContentLoaded', startSurvey);
