const queueForm = document.getElementById('queueForm');
const matchStatus = document.getElementById('matchStatus');
const arenaEl = document.getElementById('arena');
let pollId = null;
let currentMatchId = null;
let currentQuestion = null;
let currentIndex = 0;
let total = 10;
let startTime = 0;

function renderArena(){
  if(!currentQuestion){ return; }
  arenaEl.innerHTML = `
    <div class="stat">Question ${currentIndex+1} / ${total}</div>
    <div class="panel shimmer"><h3>${currentQuestion.question_text}</h3></div>
    <div class="options">
      ${['A','B','C','D'].map(letter=>{
        const key = 'option_'+letter.toLowerCase();
        return `<div class="option" data-letter="${letter}">${currentQuestion[key]}</div>`;
      }).join('')}
    </div>
    <div class="progress"><div style="width:${(currentIndex/total)*100}%"></div></div>
  `;
  document.querySelectorAll('.option').forEach(el=>{
    el.addEventListener('click', ()=> submitAnswer(el.dataset.letter));
  })
}

async function submitAnswer(letter){
  const elapsed = Date.now() - startTime;
  const out = await postJSON('../api/submit_answer.php', {
    match_id: currentMatchId,
    question_id: currentQuestion.id,
    answer: letter,
    time_ms: elapsed
  });
  if (!out.ok){ alert(out.error || 'Submit failed'); return; }
  // Advance handled server-side; next poll will deliver new state
}

async function pollMatch(){
  const res = await fetch(`../api/match_status.php?match_id=${currentMatchId}`);
  const out = await res.json();
  if (!out.ok){
    clearInterval(pollId);
    matchStatus.textContent = out.error || 'Match error';
    return;
  }
  matchStatus.textContent = out.status_text;
  if (out.completed){
    clearInterval(pollId);
    arenaEl.innerHTML = `<div class="panel"><h3>${out.winner_text}</h3><a class="btn" href="leaderboard.php">View Leaderboard</a></div>`;
    return;
  }
  if (out.question){
    currentQuestion = out.question;
    currentIndex = out.index;
    total = out.total;
    startTime = Date.now();
    arenaEl.classList.remove('hidden');
    renderArena();
  }
}

queueForm?.addEventListener('submit', async (e)=>{
  e.preventDefault();
  const fd = new FormData(queueForm);
  const category = fd.get('category');
  const difficulty = fd.get('difficulty');
  matchStatus.textContent = 'Searching opponent...';
  const out = await postForm('../api/queue.php', queueForm);
  if (!out.ok){ matchStatus.textContent = out.error || 'Queue failed'; return; }
  currentMatchId = out.match_id;
  matchStatus.textContent = 'Matched! Preparing questions...';
  pollId = setInterval(pollMatch, 1500);
});
