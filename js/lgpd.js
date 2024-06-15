
const lgpdCookie = 'lgpdAccepted';

function setCookie(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function eraseCookie(name) {   
    document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

function acceptLGPD(){
    setCookie(lgpdCookie, 1, 365);
    let elements =  document.getElementsByClassName('lgpd');
    if(elements.length){
        for(i=0;i<elements.length;i++){
            elements[i].remove();
        }
    }
}

if(!getCookie(lgpdCookie)){
    document.body.innerHTML += `
    <style>
    .lgpd {
      background: #FFF; 
      z-index: 1000000; 
      position: fixed; 
      margin: 2rem; 
      bottom: 0; 
      width: calc(100% - 4rem); 
      padding: 2rem; 
      box-shadow: 2px 3px 3px rgba(0,0,0,.3); 
      border: 1px solid #DDD;
      border-radius: 2rem;
      display: flex; 
      justify-content: space-between }

    .lgpd p { font-size: 80% }  

    @media(min-width:768px){
      .lgpd p { margin: 0 }
      .lgpd > div { margin-left: 3rem }
    }

    @media(max-width:767px){
      .lgpd { flex-direction: column }
    }
    </style>
    
    <div class="lgpd">
        <p>
            N&oacute;s usamos cookies e outras tecnologias semelhantes para melhorar a sua experi&ecirc;ncia em nossos servi&ccedil;os, 
            personalizar publicidade e recomendar conte&uacute;do de seu interesse. Ao utilizar nossos servi&ccedil;os, voc&ecirc; 
            concorda com tal monitoramento. Conhe&ccedil;a nossa <a href="/politica-privacidade" target="_blank">Pol&iacute;tica de Privacidade</a> 
            e tamb&eacute;m nossa <a href="/politica-cookies" target="_blank">Pol&iacute;tica de Cookies</a>.
        </p>
        <div><button onclick="acceptLGPD();" class="btn btn-primary">Prosseguir</button></div>
    </div>`;
}
