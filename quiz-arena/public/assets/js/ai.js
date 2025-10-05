const aiGameEl = document.getElementById('aiGame');
const aiSetup = document.getElementById('aiSetup');

function renderAIQuestion(state){
  const q = state.questions[state.index];
  aiGameEl.innerHTML = `
    <div class="stat">Question ${state.index+1} / ${state.questions.length}</div>
    <div class="panel shimmer"><h3>${q.question_text}</h3></div>
    <div class="options">
      ${['A','B','C','D'].map(letter=>{
        const key = 'option_'+letter.toLowerCase();
        return `<div class="option" data-letter="${letter}">${q[key]}</div>`;
      }).join('')}
    </div>
    <div class="progress"><div style="width:${(state.index/state.questions.length)*100}%"></div></div>
  `;
  document.querySelectorAll('.option').forEach(el=>{
    el.addEventListener('click', ()=> selectAIAnswer(state, el.dataset.letter));
  })
}

async function selectAIAnswer(state, letter){
  const q = state.questions[state.index];
  const correct = letter === q.correct_option;
  const optionEls = document.querySelectorAll('.option');
  optionEls.forEach(el => {
    if(el.dataset.letter === q.correct_option) el.classList.add('correct');
    if(el.dataset.letter === letter && !correct) el.classList.add('wrong');
  });
  if (correct) state.score++;
  await new Promise(r=>setTimeout(r, 650));
  state.index++;
  if (state.index < state.questions.length){
    renderAIQuestion(state);
  } else {
    aiGameEl.innerHTML = `<div class="panel"><h3>Done!</h3><p>Your score: ${state.score}/${state.questions.length}</p><a class="btn" href="/quiz-arena/public/dashboard.php">Back to Dashboard</a></div>`;
  }
}

aiSetup?.addEventListener('submit', async (e)=>{
  e.preventDefault();
  const formData = new FormData(aiSetup);
  const category = formData.get('category');
  const difficulty = formData.get('difficulty');
  const res = await fetch(`/quiz-arena/api/get_questions.php?category=${category}&difficulty=${difficulty}&limit=10`);
  const out = await res.json();
  if (!out.ok){ alert(out.error || 'Failed to load questions'); return; }
  aiGameEl.classList.remove('hidden');
  const state = { questions: out.questions, index: 0, score: 0 };
  renderAIQuestion(state);
});
