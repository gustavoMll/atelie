<div class="<?= $view['modulo'] ?> overflow-auto">

    
    <?php if($view['tab-adicional']!=''){ ?>
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#gerenciar" type="button" role="tab" aria-controls="gerenciar" aria-selected="true">Gerenciar</button>
            </li>
            <?=$view['tab-adicional']?>
        </ul>
    
    
        <div class="tab-content">
            <div class="tab-pane fade show active p-1" id="gerenciar">
    <?php } ?>
                
            <div id="resultados">
            <p>Aguarde, buscando registros...</p>
            </div>
                
        <?php if($view['tab-adicional']!=''){ ?>    
            </div>
            <?=$view['content-tab-adicional']?>    
        </div>
    <?php } ?>    
    
</div>

<?php $view['end_scripts'] .= "tableList(`{$view['modulo']}`, `{$_SERVER['QUERY_STRING']}`,`resultados`, false);"; ?>