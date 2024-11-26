<link href="css/cadastro.css" rel="stylesheet">
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="centralizada text-center">

        <img src="public/img/logo_white.png" alt="" class="img_logo" />
        <h2 class="login_title">Gabinete Digital</h2>
        <h6 class="host"><?php echo $_SERVER['HTTP_HOST'] ?></h6>

        <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
            <div class="col-md-12 col-12">
                <input type="text" class="form-control form-control-sm" name="usuario_nome" placeholder="Nome" required>
            </div>
            <div class="col-md-12 col-12">
                <input type="email" class="form-control form-control-sm" name="usuario_email" placeholder="Email" required>
            </div>
            <div class="col-md-6 col-6">
                <input type="text" class="form-control form-control-sm" name="usuario_telefone" placeholder="Celular (com DDD)" data-mask="(00) 00000-0000" maxlength="11" required>
            </div>
            <div class="col-md-6 col-6">
                <input type="text" class="form-control form-control-sm" name="usuario_aniversario" placeholder="dd/mm" data-mask="00/00" required>
            </div>
            <div class="col-md-6 col-6">
                <input type="password" class="form-control form-control-sm" name="usuario_senha" placeholder="Senha" required>
            </div>
            <div class="col-md-6 col-6">
                <input type="password" class="form-control form-control-sm" name="usuario_senha2" placeholder="Confirme a senha" required>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <button type="submit" name="btn_login" class="btn btn-primary">Salvar</button>
                <a type="button" href="?secao=login" class="btn btn-secondary">Voltar</a>

            </div>
        </form>
        <p class="mt-3 copyright">2024 | JS Digital System</p>
    </div>
</div>