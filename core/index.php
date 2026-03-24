<?php require_once __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="container">
    <div class="section-title">
        <h2>Painel Administrativo</h2>
    </div>
    
    <div style="text-align: center; padding: 50px 0;">
        <h3>Bem-vindo, <?php echo $_SESSION['user_name'] ?? 'Administrador'; ?>!</h3>
        <p style="color: #666; margin-top: 10px;">Utilize o menu superior para gerenciar ofertas, usuários e visualizar relatórios.</p>
        
        <div style="margin-top: 40px; display: flex; justify-content: center; gap: 20px;">
            <a href="index.php?action=register-oferta" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Nova Oferta
            </a>
            <a href="index.php?action=relatorio_geral" class="btn btn-success">
                <i class="fas fa-chart-bar"></i> Relatórios
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>