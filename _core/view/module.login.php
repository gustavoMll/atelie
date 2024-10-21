<main class="container-fluid bg-gradient-primary min-vh-100 d-flex align-items-center p-0">

    <div class="container position-relative min-vh-100 d-flex flex-column">

        <div class="col-12 col-xl-4 d-flex flex-column gap-3 my-auto">

            <form class="d-flex flex-column p-4" onsubmit="return doLogin(this)">

                <!-- <div class="d-flex align-items-center">
                    <img src="<?=__BASEPATH__?>img/brand.png" alt="" class="img-fluid" loading="lazy" width="100">
                </div> -->
                
                <h4 class="text-white h1 fw-bold lh-1">Boas vindas!</h1>
                <p class="text-white fs-5 mb-4">Acesse com seus dados abaixo.</p>

                <div class="d-flex flex-column gap-3 mb-5">

                    <div class="form-floating">
                        <input type="text" name="login" placeholder="login" id="login" class="form-control" required>
                        <label for="login" class="form-label">Login/E-mail/Telefone*</label>
                    </div>
                    <div>
                        <div class="form-floating">
                            <input type="password" name="senha" placeholder="senha" id="senha" class="form-control" data-type="togglePassword" required>
                            <label for="senha" class="form-label">Senha*</label>
                        </div>
                        <a href="<?=__PATH__?>esqueceu-senha" class="small d-block text-end mt-2 text-white-50 text-decoration-none">Recuperar senha</a>
                    </div>
                    
                    <button type="submit" class="btn btn-secondary fw-bold text-white flex-fill">Entrar</button>
                </div>

                <?php include 'inc.assinatura.php'; ?>


            </form>


        </div>


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
