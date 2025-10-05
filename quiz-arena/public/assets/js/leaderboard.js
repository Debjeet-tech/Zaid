async function loadLeaderboard(){
  const res = await fetch('/quiz-arena/api/leaderboard.php');
  const out = await res.json();
  const root = document.getElementById('leaderboard');
  if (!out.ok){ root.textContent = out.error || 'Failed to load leaderboard'; return; }
  root.innerHTML = out.rows.map((r, i)=>
    `<div class="row"><div>#${i+1} ${r.username}</div><div>${r.wins} wins</div></div>`
  ).join('');
}
loadLeaderboard();
