<?php

class GG {

    public static function getLinksTable($module, $id, $name, $changeHref=true, $edit=true, $responsive=true){
        $string = '';
        
        if($edit) {
            $string.= '<a 
            class="text-uppercase text-reset text-decoration-underline"
            href="javascript:;" 
            onclick="'.($changeHref ? 'setHref(`'.$module.'/editar/id/'.$id.'`);' : '').'modalForm(`'. $module . '`, '.$id.', ``, function(){ getLine(\''.$module.'\', '.$id.'); '.($changeHref ? ' setHref(`'.$module.'`);' : '').' });" 
            data-bs-toggle="" 
            data-bs-placement="" 
            data-bs-title=""
            data-bs-trigger="hover"
            aria-label="Edit Record"
        >' . $name . '</a> ';
        } else {
            $string.= '<span>'.$name.'</span>';
        }
        if($responsive){
            $string.= '<a 
            href="javascript:;" 
            onclick="delFormAction(`'.$module.'`, '.$id.', function(){ $(`#tr-'.$module.$id.'`).fadeOut(`slow`, function(){ $(this).remove(); }); });" 
            style="z-index: 2;"
            data-bs-toggle="tooltip" 
            data-bs-placement="right" 
            data-bs-title="Excluir"
            data-bs-trigger="hover"
            aria-label="Delete record"
            class="position-absolute top-50 text-danger translate-middle-y fs-5" style="right: 6px;"
        ><i class="ti ti-trash"></i></a>
        ';
        }else{
            $string.= '<a 
            href="javascript:;" 
            onclick="delFormAction(`'.$module.'`, '.$id.', function(){ $(`#tr-'.$module.$id.'`).fadeOut(`slow`, function(){ $(this).remove(); }); });" 
            data-bs-toggle="tooltip" 
            data-bs-placement="right" 
            data-bs-title="Excluir"
            data-bs-trigger="hover"
            aria-label="Delete record"
            class="d-none d-sm-inline-block position-absolute top-50 end-0 translate-middle-y"
        ><i class="ti ti-trash"></i></a>
        ';
        }

        return $string;

    }

    public static function moduleLoadData($action, $text="Carregar dados"){
        return '
            <button class="btn btn-outline-primary" type="button" onclick="'.$action.'">
                <i class="ti ti-reload"></i></span> '.$text.'
            </button>
        ';
    }

    public static function getCheckboxHead(){
        return '<input type="checkbox" id="checkboxhead" class="form-check-input" placeholder="marcar checkbox" onclick="javascript:marcaCheckBoxGG(this);" />';
    }

    public static function getCheckboxLine($id){
        return '<input type="checkbox" id="checkboxline'.$id.'" placeholder="marcar checkbox" class="chkDel form-check-input" value="'.$id.'" onclick="javascript:controlDelButton();" />';

    }

    public static function getActiveControl($module, $id, $ativo=0){
        return '
        <td id="status'.$id.'" class="responsive-item align-middle">
            <a 
                href="javascript:;" 
                onclick="javascript: changeStatus(`'.$module.'`,'.$id.', '.($ativo==1?'0':'1').')"
            >
                <span 
                    class="ti ti-circle-check-filled text-'.($ativo==1? 'success' :'default').'" 
                    title="'.($ativo==1?'Desativar':'Ativar').'"
                ></span>
            </a>
        </td>';

    }

    public static function getOrderControl($id){
        return '
        <td class="drag-handler responsive-item" data-id="'.$id.'">
            <a href="javascript:;">
                <i class="ti ti-arrows-move"></i>
            </a>
        </td>';
    }

    public static function getOrderTbody($classe){
        $nometable = (new $classe())->getTableName();
        return '<tbody class="dragable" data-table="'.$nometable.'">';
    }

    public static function showImage($obj, $labelImg='Imagem Atual', $nmImg='img'){
        $img = $obj->getImage('r',0,'',$nmImg);
        $imgZ = $obj->getImage('z',0,'',$nmImg);
        if($imgZ==''){
            $imgZ = $img;
        }
        $string  = '';
        if($img != ''){
            $string .= '<div class="form-group col-xs-12" id="imagem_'.$nmImg.'_'.$obj->getTableName().'">';
            $string .= '<label for="">'.$labelImg.'</label>';
            $string .= '<a href="'.$imgZ.'" class="pull-right small" target="_blank">Exibir em tamanho real</a>';
            $string .= '<div class="imgPreview">';
            $string .= '<a href="javascript:;" onclick="javascript: deleteImage(\''.$obj->getTableName().'\',\''.$obj->get($obj->getPK()[0]).'\', \''.$nmImg.'\');" class="pull-right btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span> Excluir imagem</a>';
            $string .= '<img src="'.$img.'" />';
            $string .= '</div>';
            $string .= '</div>';
        }
        return $string;
    }

    public static function getResponsiveList($data, $obj){
        $string = '';

        if($data)
            foreach($data as $key => $value) $string .= '<p class="mb-2"><strong>'.$key.': </strong> '.$value.'</p>';

        $string .= '<span class="badge bg-secondary text-white">Visualizar conteúdo</span>';

        $string = '
        <td class="link-edit responsive-item d-md-none col-12">'.GG::getLinksTable($obj->getTableName(), $obj->get('id'), $string).'</td>
        ';

        return $string;
    }

    public static function getPreviewImage($obj, $nmImg = "img", $size=12){
        $nameImg = $obj->get($nmImg);
        $sourceImage = $obj->getImage('r', 0, '', $nmImg);
        $sourceImage = $obj->getImage('r', 0, '', $nmImg);
        $string = '';
        $string .= '<div class="col-sm-'.$size.' '.($nameImg != '' ? '' : 'd-none').' mb-3" id="imagem_'.$nmImg.'_'.$obj->getTableName().'">
            <article class="card text-white card-preview">
                <div class="preview-actions d-flex align-items-center gap-3 position-absolute">';
                    if($nameImg != ''){
                        $string .= '<button id="btndel_'.$nmImg.'_'.$obj->getTableName().'" type="button" class="btn btn-danger btn-sm" onclick="javascript: deleteImage(\''.$obj->getTableName().'\',\''.$obj->get($obj->getPK()[0]).'\', \''.$nmImg.'\');">
                        <i class="ti ti-trash"></i> Excluir imagem
                    </button>';
                    }
                    $string .= '<button id="btnchange_'.$nmImg.'_'.$obj->getTableName().'" type="button" class="btn btn-primary btn-sm" '.($nameImg != '' ? 'style="display:none;"' : '').' onclick="deletePreviewImage('.($nameImg != '' ? '`'.$sourceImage.'` ' : '``').', `'.$nmImg.'`, `'.$obj->getTableName().'`)">
                        <i class="ti ti-circle-x-filled"></i>
                        Cancelar alteração
                    </button>
                </div>';
                $string .= '<figure class="ratio ratio-21x9 card-img mb-0">
                    <img src="'.$sourceImage.'" id="preview_'.$nmImg.'_'.$obj->getTableName().'" class="img-preview object-fit-contain" alt=""/>
                </figure>
            </article>
        </div>';
        return $string;
    }

    public static function showFile($obj, $label="Arquivo Atual", $nmFile="file"){
        $file = $obj->getFile(0, '',$nmFile);
        $string  = '';
        if($file != ''){
            $string .= '<div class="form-group col-xs-12" id="arquivo_'.$nmFile.'_'.$obj->getTableName().'">';
            $string .= '<label for="">'.$label.'</label>';
            $string .= ' | <a href="javascript:;" id="link_del" onclick="javascript: deleteFile(`'.$obj->getTableName().'`, '.$obj->get($obj->getPK()[0]).', `'.$nmFile.'`);" class="btn btn-danger btn-sm">Excluir arquivo</a>';
            $string .= ' | <a href="'.$file.'" class="btn btn-primary btn-sm" target="_blank">Baixar</a>';
            $string .= '</div>';
        }
        return $string;
    }

    public static function loadConfigs(){
        $conn = new Connection();
        $sql = "SELECT chave, valor FROM configs";
        $Config = [];
        $rs = $conn->prepareStatement($sql)->executeReader();
        while($rs->next()){
            $Config += [$rs->getString('chave') => $rs->getString('valor')];
        }
        return $Config;
    }

    public static function saveConfig($key, $value){
        $key = Utils::replace('/[^a-zA-Z0-9\-\_]/','',$key);
        $value = Security::antiInjection($value);

        $conn = new Connection();
        $sql = "SELECT count(chave) FROM configs WHERE chave = '{$key}'";
        if($conn->prepareStatement($sql)->executeScalar() > 0){
            $sql = "UPDATE configs SET valor = '{$value}' WHERE chave = '{$key}'";
        }else{
            $sql = "INSERT INTO configs (chave, valor) VALUES ('{$key}', '{$value}')";
        }
        $conn->prepareStatement($sql)->executeQuery();
    }

}
