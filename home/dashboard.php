<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/dao/MovimentVacanciesDAO.php';
session_start();
if (!isset($_SESSION['user_id'])) header('Location: login.php');
$userId = $_SESSION['user_id'];

$dao = new MovimentVacanciesDAO($pdo);
$movimentos_recentes = $dao->getMovimentosRecentesPorUsuario($userId);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .table-img-thumbnail { width:60px; height:60px; object-fit:cover; cursor:pointer; border-radius:4px; }
    #autoRefreshPanel {
      position:fixed; bottom:0; left:0; width:100%; background:#f8f9fa; border-top:1px solid #dee2e6;
      padding:10px 20px; z-index:1050; display:flex; align-items:center; justify-content:center;
    }
    .toast-container { position:fixed; bottom:80px; right:20px; z-index:1060; }
  </style>
</head>
<body>
<div class="container-fluid">
  <h2 class="mt-3">Dashboard</h2>

  <!-- Busca por placa destacada -->
  <div class="row mt-4">
    <div class="col-lg-7 col-md-9 mx-auto">
      <div class="input-group input-group-lg mb-3 shadow" style="background:#f8f9fa;border-radius:10px;border:2px solid #007bff;">
        <input type="text" id="placaBuscaInput" class="form-control" placeholder="Digite a placa ..." autocomplete="off">
        <div class="input-group-append">
          <button id="buscarPlacaBtn" class="btn btn-primary" type="button"><i class="fas fa-search"></i> Buscar Placa</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Movimentos recentes em tabela -->
  <div class="row mt-3">
    <div class="col-md-12">
      <h4>Movimentos Recentes</h4>
      <?php if (!empty($movimentos_recentes)): ?>
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead class="thead-dark">
              <tr><th>ID Vaga</th><th>Placa</th><th>Registrado em</th><th>Status</th><th>Imagem</th><th>Ações</th></tr>
            </thead>
            <tbody>
              <?php foreach ($movimentos_recentes as $mov): ?>
                <?php
                  $isOcupado = !empty($mov['ocupado']);
                  $img = !empty($mov['file_path']) ? '../'.htmlspecialchars($mov['file_path']) : '../assets/img/no-image.png';
                ?>
                <tr class="<?= $isOcupado ? 'table-danger' : '' ?>">
                  <td><?= $mov['fk_vacancie'] ?></td>
                  <td><?= htmlspecialchars($mov['placa'] ?? 'N/A') ?></td>
                  <td><?= converterParaSaoPaulo(date('d/m/Y H:i', strtotime($mov['created_at']))) ?></td>
                  <td><?= $isOcupado ? '<span class="badge badge-danger">OCUPADA</span>' : '<span class="badge badge-success">LIVRE</span>' ?></td>
                  <td><img src="<?= $img ?>" class="table-img-thumbnail visualizar-imagem" data-imagem="<?= $img ?>" alt="Imagem"></td>
                  <td><a href="../cameras/historical?id=<?= $mov['fk_vacancie'] ?>" class="btn btn-sm btn-info">Ver Histórico</a></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="text-muted">Nenhum movimento recente encontrado.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Painel Auto-Refresh -->
<div id="autoRefreshPanel">
  <div class="form-check form-check-inline mb-0 mr-3">
    <input class="form-check-input" type="checkbox" id="autoRefreshToggle">
    <label class="form-check-label" for="autoRefreshToggle">Atualização Automática</label>
  </div>
  <span id="autoRefreshStatus" class="text-muted mr-3">Status: Desativado</span>
  <span id="autoRefreshCountdown" class="text-muted">Próximo em: --</span>
</div>

<!-- Modal de imagem -->
<div class="modal fade" id="imagemModal" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
  <div class="modal-header"><h5 class="modal-title">Visualizar Imagem</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
  <div class="modal-body text-center"><img src="" id="imagemModalImg" class="img-fluid" alt="Imagem"></div>
</div></div></div>

<!-- Modal busca placa -->
<div class="modal fade" id="searchResultsModal" tabindex="-1"><div class="modal-dialog modal-xl modal-dialog-centered"><div class="modal-content">
  <div class="modal-header"><h5 class="modal-title">Resultados da Busca por Placa</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
  <div class="modal-body" id="searchResultsBody"></div>
</div></div></div>

<div class="toast-container"></div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js"></script>

<script>
let autoEnable = false, countdown=60, timer=null, modalCount=0;
// atualiza status textual
function updateStatus(){ $('#autoRefreshStatus').text('Status: '+(autoEnable?'Ativado':'Desativado')); }
// atualiza contador
function updateCountdown(){ $('#autoRefreshCountdown').text(autoEnable?`Próximo em: ${countdown}s`:'Próximo em: --'); }
// reinicia contagem
function restartCountdown(){ countdown=60; updateCountdown(); }
// inicia timer 1s
function startTimer(){ if(timer) return; timer = setInterval(()=>{
  if(autoEnable && modalCount===0){
    countdown--;
    if(countdown<=0) location.reload();
    else updateCountdown();
  }
}, 1000); }
// pausa timer
function stopTimer(){ clearInterval(timer); timer=null; }

function showToast(msg,type='info'){
  const t = $(`<div class="toast bg-${type} text-white" role="alert" data-delay="3000"><div class="toast-header bg-${type} text-white"><strong class="mr-auto">${type}</strong><button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast"><span>&times;</span></button></div><div class="toast-body">${msg}</div></div>`);
  $('.toast-container').prepend(t);
  t.toast('show').on('hidden.bs.toast',()=>t.remove());
}

$(document).ready(()=>{
  autoEnable = localStorage.getItem('autoRefreshEnabled')==='true';
  $('#autoRefreshToggle').prop('checked', autoEnable);
  updateStatus(); updateCountdown();
  if(autoEnable){ restartCountdown(); startTimer(); }

  $('#autoRefreshToggle').on('change', function(){
    autoEnable = this.checked;
    localStorage.setItem('autoRefreshEnabled', autoEnable);
    updateStatus();
    if(autoEnable){ restartCountdown(); startTimer(); }
    else { stopTimer(); updateCountdown(); }
  });

  $(document).on('show.bs.modal', '.modal', ()=> modalCount++);
  $(document).on('hidden.bs.modal', '.modal', ()=>{
    modalCount--;
    if(modalCount===0 && autoEnable){ restartCountdown(); startTimer(); }
  });

  // modal imagem
  $(document).on('click', '.visualizar-imagem', function(){
    $('#imagemModalImg').attr('src', $(this).data('imagem'));
    $('#imagemModal').modal('show');
  });

  // busca placa
  $('#buscarPlacaBtn').on('click', function(){
    const placa = $('#placaBuscaInput').val().trim().toUpperCase();
    if(!placa){ showToast('Digite uma placa','danger'); return; }
    showToast('Buscando placa...', 'info');
    $.post('../api/get-plate.php',{ plate:placa, user:'<?= $userId ?>' }, function(res){
      if(res.success && res.data?.length){
        showToast(`Resultados para ${placa}`,'success');
        let html = `<div class="table-responsive"><table class="table table-striped table-hover"><thead class="thead-dark"><tr><th>ID Vaga</th><th>Placa</th><th>Registrado em</th><th>Imagem</th><th>Status</th><th>Ações</th></tr></thead><tbody>`;
        res.data.forEach(m=>{
          const img = m.file_path?`../${m.file_path}`:'../assets/img/no-image.png';
          const st = m.ocupado?;
            `<span class="badge badge-danger">OCUPADA</span>`:
            `<span class="badge badge-success">LIVRE</span>`;
          const cls = m.ocupado?'table-danger':'';
          const dt = new Date(m.created_at);
          const formatted = `${String(dt.getDate()).padStart(2,'0')}/${String(dt.getMonth()+1).padStart(2,'0')}/${dt.getFullYear()} ${String(dt.getHours()).padStart(2,'0')}:${String(dt.getMinutes()).padStart(2,'0')}`;
          html += `<tr class="${cls}"><td>${m.fk_vacancie}</td><td>${m.placa||'N/A'}</td><td>${formatted}</td><td><img src="${img}" class="table-img-thumbnail visualizar-imagem" data-imagem="${img}" alt="Imagem"></td><td>${st}</td><td><a href="../cameras/historical?id=${m.fk_vacancie}" class="btn btn-sm btn-info">Histórico</a></td></tr>`;
        });
        html += '</tbody></table></div>';
        $('#searchResultsBody').html(html);
      } else {
        showToast(`Nenhum resultado para ${placa}`, 'danger');
        $('#searchResultsBody').html('<p class="text-center text-muted">Nenhum registro encontrado.</p>');
      }
      $('#searchResultsModal').modal('show');
    }).fail(()=> showToast('Erro no servidor','danger'));
  });
});
</script>
</body>
</html>
