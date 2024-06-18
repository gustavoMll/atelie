<div class="home">
    <div>
        <div class="d-flex justify-content-between">
            <div>
                <h1>Bem-vindo ao GG, <strong><?= $objSession->getPessoa()->get('nome') ?></strong>!</h1>
            </div>

            <div class="align-items-center">
                <a class="btn btn-primary" href="<?=__PATH__?>novo-pedido"><i class="ti ti-plus"></i>Novo Pedido</a>
            </div>
        </div>
    </div>
</div>