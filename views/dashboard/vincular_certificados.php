<?php
// Header do Admin
require_once __DIR__ . '/../layouts/admin_header.php';
?>

<h2>Adição de Certificados</h2>
<p>Selecione uma oferta para visualizar os alunos inscritos e aprovados para vincular o certificado.</p>

<!-- Formulário de Filtro -->
<form id="form-selecao" method="GET" action="index.php">
    <input type="hidden" name="action" value="vincular_certificados">
    <div class="form-group">
        <label for="oferta_id">Selecione a Oferta:</label>
        <select name="oferta_id" id="oferta_id" required>
            <option value="">-- Escolha uma oferta --</option>
            <?php if (isset($ofertas) && is_array($ofertas)): ?>
                <?php foreach ($ofertas as $oferta): ?>
                    <option value="<?= htmlspecialchars($oferta['id']) ?>" <?= (isset($ofertaId) && $ofertaId == $oferta['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($oferta['nome']) ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Buscar Alunos</button>
</form>

<!-- Placeholder para notificações -->
<div id="notification-toast" class="toast">
    <span id="toast-message"></span>
</div>

<?php if (isset($ofertaId) && empty($inscritos)): ?>
    <div style="background: #eaf6ff; border-left: 5px solid #63a3d4; padding: 15px; margin-top: 20px;">Nenhum aluno aprovado para a oferta selecionada.</div>
<?php elseif (!empty($inscritos)): ?>
    <div id="tabela-alunos-container" class="table-responsive auto-responsive-cards active" style="margin-top: 30px;">
        <h4>Alunos Aprovados (<?= count($inscritos) ?>)</h4>
        <table class="responsive-table">
            <thead>
                <tr>
                    <th>Nome do Aluno</th>
                    <th>Email</th>
                    <th>Frequência</th>
                    <th>Nota Final</th>
                    <th>Certificado</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inscritos as $inscrito): ?>
                <tr>
                    <td data-label="Nome"><?= htmlspecialchars($inscrito['nome']) ?></td>
                    <td data-label="Email"><?= htmlspecialchars($inscrito['email']) ?></td>
                    <td data-label="Frequência"><?= htmlspecialchars($inscrito['frequencia_percentual']) ?>%</td>
                    <td data-label="Nota Final"><?= htmlspecialchars($inscrito['nota_final'] ?? 'N/A') ?></td>
                    <td data-label="Certificado">
                        <?php if ($inscrito['url_certificado']): ?>
                            <a href="<?= htmlspecialchars($inscrito['url_certificado']) ?>" target="_blank">Ver Certificado</a>
                        <?php else: ?>
                            Nenhum
                        <?php endif; ?>
                    </td>
                    <td data-label="Ação">
                        <?php if ($inscrito['frequencia_percentual'] > 75 && $inscrito['nota_final'] > 5): ?>
                            <button class="btn btn-primary btn-sm btn-open-modal" 
                                    data-aluno-id="<?= $inscrito['id'] ?>" 
                                    data-oferta-id="<?= $inscrito['oferta_id'] ?>"
                                    data-concluiu="<?= $inscrito['concluiu_curso'] ?>"
                                    data-codigo="<?= htmlspecialchars($inscrito['codigo_validacao'] ?? '') ?>"
                                    data-url-certificado="<?= htmlspecialchars($inscrito['url_certificado'] ?? '') ?>">
                                Vincular Certificado
                            </button>
                        <?php else: ?>
                            <span class="status-disabled" title="Requisitos não atendidos (Freq > 75% e Nota > 5)">Não elegível</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<!-- Modal: Upload de Certificado -->
<div id="certificadoModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h2>Submeter Certificado</h2>
            <span class="close-modal" data-modal-id="certificadoModal">&times;</span>
        </div>
        <div class="modal-body">
            
            <div id="already-exists-alert" style="display: none; background: #fff3cd; border-left: 5px solid #ffeeba; padding: 15px; margin-bottom: 20px;">
                <p><strong>Aviso:</strong> Já existe um certificado vinculado a este aluno.</p>
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <a id="btn-view-cert" href="#" target="_blank" class="btn btn-info btn-sm">Visualizar</a>
                    <button type="button" id="btn-send-other" class="btn btn-warning btn-sm">Enviar Outro</button>
                    <button type="button" class="btn btn-secondary btn-sm close-modal-btn">Cancelar</button>
                </div>
            </div>

            <form id="form-certificado" method="POST" enctype="multipart/form-data" style="display: none;">
                <input type="hidden" name="aluno_id" id="modal_aluno_id">
                <input type="hidden" name="oferta_id" id="modal_oferta_id">
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>
                        <input type="checkbox" name="concluiu_curso" id="modal_concluiu_curso" value="1">
                        Confirmar que o aluno participou da certificação (concluiu o curso)
                    </label>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="modal_codigo_validacao">Código de Validação:</label>
                    <input type="text" name="codigo_validacao" id="modal_codigo_validacao" class="form-control" placeholder="Digite o código de validação" required>
                </div>

                <div class="form-group" id="div_upload_certificado" style="margin-bottom: 15px; display: none;">
                    <label for="certificado">Arquivo do Certificado (PDF, JPG, PNG):</label>
                    <input type="file" name="certificado" id="certificado" class="form-control" accept=".pdf, .jpg, .jpeg, .png">
                    <small>O upload só será realizado se a conclusão do curso estiver marcada.</small>
                </div>

                <button type="submit" id="btn-submit-certificado" class="btn btn-success" style="width: 100%;">
                    <span id="btn-text">Salvar</span>
                    <i id="btn-spinner" class="fas fa-spinner fa-spin" style="display: none;"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('certificadoModal');
    const closeButtons = document.querySelectorAll('.close-modal, .close-modal-btn');
    const concluiuCheckbox = document.getElementById('modal_concluiu_curso');
    const divUpload = document.getElementById('div_upload_certificado');
    const fileInput = document.getElementById('certificado');
    const formCertificado = document.getElementById('form-certificado');
    
    const alertExists = document.getElementById('already-exists-alert');
    const btnViewCert = document.getElementById('btn-view-cert');
    const btnSendOther = document.getElementById('btn-send-other');

    function showToast(message, isSuccess = true) {
        const toast = document.getElementById('notification-toast');
        const toastMessage = document.getElementById('toast-message');
        
        toastMessage.textContent = message;
        toast.className = 'toast show ' + (isSuccess ? 'success' : 'error');

        setTimeout(() => {
            toast.className = toast.className.replace('show', '');
        }, 3000); 
    }

    closeButtons.forEach(btn => {
        btn.onclick = function() {
            modal.style.display = 'none';
        }
    });

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

    document.querySelectorAll('.btn-open-modal').forEach(btn => {
        btn.onclick = function() {
            document.getElementById('modal_aluno_id').value = this.getAttribute('data-aluno-id');
            document.getElementById('modal_oferta_id').value = this.getAttribute('data-oferta-id');
            document.getElementById('modal_codigo_validacao').value = this.getAttribute('data-codigo');
            
            const concluiu = this.getAttribute('data-concluiu') === '1';
            concluiuCheckbox.checked = concluiu;
            
            if (concluiu) {
                divUpload.style.display = 'block';
                fileInput.required = false;
            } else {
                divUpload.style.display = 'none';
                fileInput.required = true;
            }

            const urlCertificado = this.getAttribute('data-url-certificado');
            
            if (urlCertificado) {
                alertExists.style.display = 'block';
                formCertificado.style.display = 'none';
                btnViewCert.href = urlCertificado;
            } else {
                alertExists.style.display = 'none';
                formCertificado.style.display = 'block';
            }

            modal.style.display = 'block';
        }
    });

    btnSendOther.onclick = function() {
        alertExists.style.display = 'none';
        formCertificado.style.display = 'block';
    }

    concluiuCheckbox.addEventListener('change', function() {
        if (this.checked) {
            divUpload.style.display = 'block';
            fileInput.required = true; // when checked, file is required if no file exists? we keep the existing logic.
        } else {
            divUpload.style.display = 'none';
            fileInput.required = false;
        }
    });

    formCertificado.addEventListener('submit', function(e) {
        e.preventDefault();

        const btnSubmit = document.getElementById('btn-submit-certificado');
        const btnText = document.getElementById('btn-text');
        const btnSpinner = document.getElementById('btn-spinner');

        // Show spinner
        btnSubmit.disabled = true;
        btnText.textContent = 'Enviando... ';
        btnSpinner.style.display = 'inline-block';

        const formData = new FormData(this);

        fetch('index.php?action=upload_certificado', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, true);
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showToast(data.message || 'Erro ao vincular certificado.', false);
                // Reset button
                btnSubmit.disabled = false;
                btnText.textContent = 'Salvar';
                btnSpinner.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Ocorreu um erro ao enviar o certificado.', false);
            // Reset button
            btnSubmit.disabled = false;
            btnText.textContent = 'Salvar';
            btnSpinner.style.display = 'none';
        });
    });
});
</script>

<?php
// Footer do Admin
require_once __DIR__ . '/../layouts/admin_footer.php';
?>