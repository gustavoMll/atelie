<main id="loginPage" class="bg-gradient-animate p-0 min-vh-100 d-flex align-items-center">

    <div class="container d-flex flex-column align-items-center justify-content-center p-0">

        <div class="overflow-hidden border border-2 border-secondary shadow-lg col-md-6 col-xl-4 col-sm-12">
            <div class="bg-primary">
                <form class="d-flex flex-column justify-content-center p-5" onsubmit="return doLogin(this)">

                    <div class="d-flex align-items-center pb-5">
                        <img src="<?=__BASEPATH__?>img/brand-white.png" alt="" class="img-fluid" loading="lazy" width="100">
                    </div>
                    <h1 class="text-secondary h4 fw-bold lh-1">Área do cliente</h1>
                    <p class="text-white small">Para acessar infome seus dados abaixo.</p>

                    <div class="">
                        <div class="form-floating mb-3">
                            <input type="text" name="login" placeholder="login" id="login" class="form-control" required>
                            <label for="login" class="form-label">Login/E-mail/Telefone*</label>
                        </div>
                        <div class="form-floating mb-2">
                            <input type="password" name="senha" placeholder="senha" id="senha" class="form-control" data-type="togglePassword" required>
                            <label for="senha" class="form-label">Senha*</label>
                        </div>
                        
                        <div class="text-end mb-3">
                            <a href="<?=__PATH__?>esqueceu-senha" class="text-secondary  text-decoration-none">Recuperar senha</a>
                        </div>
                        
                        <div class="d-flex flex-wrap-reverse flex-lg-nowrap align-items-center gap-3">
                            <button type="submit" class="btn btn-outline-secondary flex-fill">Entrar</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
        <?php include 'inc.assinatura.php'; ?>
    </div>
</main>

<script>
    function doLogin(form){
        const btn = $(`#btnLogin`);
        const textBefore = btn.html();
        const title = 'Login';
        
        btn.html('Aguarde...').prop('disabled', true);
        
        function responseAjax(resp){
            btn.html(textBefore).prop('disabled', false);
            if(resp.readyState == 4){
                // MessageBox.error(resp.responseText, title)
            }else if(resp.success){
                MessageBox.success(resp.message, title);
                setTimeout(function(){ location = '<?=$_SERVER['REQUEST_URI']?>'; }, 1000);

            }else{
                MessageBox.error(resp.message, title);
            }                
        }

        $(form).ajaxSubmit({
            url: `<?=__PATH__?>login/in`,
            type: "POST",
            dataType: "json",
            success: responseAjax,
            error: responseAjax
        });
        return false;
    }
</script>
