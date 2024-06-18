<div class="container">
        <h1>Novo Pedido</h1>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="input-group">
                    <div class="form-floating required">
                        <div class="form-floating">
                            <input id="nomeCliente" type="text" placeholder="seu dado aqui" class="form-control autocomplete" data-table="pessoas" data-name="nome" data-field="id_cliente" value=""/>
                            <label for="id_cliente">Cliente</label>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm addcat px-3" onclick="javascript:modalForm(`clientes`,0);">
                        <i class="ti ti-plus"></i>
                    </button>
                </div>
            </div>

            <div class="col-md-3 mb-3 required">
                <div class="form-floating">
                    <input type="date" id="data" name="data" class="form-control" placeholder="Seu dado aqui">
                    <label>Data</label>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <button type="button" class="btn btn-secondary btn-sm addcat px-3" onclick="javascript:modalForm(`alugueis`,0);">
                    <i class="ti ti-plus"></i>Adicionar Aluguel
                </button>
            </div>
        </div>
</div>