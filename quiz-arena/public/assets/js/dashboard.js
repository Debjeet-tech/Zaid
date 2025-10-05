async function loadDashboard(){
  const res = await fetch('/quiz-arena/api/dashboard_data.php');
  const out = await res.json();
  if (!out.ok){ return; }
  const grid = document.getElementById('dashboardStats');
  grid.innerHTML = `
    <div class="stat"><h3>Total Matches</h3><div>${out.total_matches}</div></div>
    <div class="stat"><h3>Wins</h3><div>${out.wins}</div></div>
    <div class="stat"><h3>Win Rate</h3><div>${out.win_rate}%</div></div>
  `;
}
loadDashboard();
