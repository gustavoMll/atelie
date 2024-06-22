# atelie

<div>
    <h1>Sistema de controle de aluguéis de fantasias e acessórios da loja Ateliê Festa e Fantasia.</h1>
    <h3>Este é um projeto desenvolvido em PHP.Para acessá-lo localmente, será necessário que a máquina possua um servidor local e o composer instalado.</h3>
    <h5>Passo a passo para instalar e rodar o projeto:</h5>
    <ul>
        <li>O primeiro passo é ter o PHP e um servidor local rodando em sua máquina. Recomendamos que seja utilizado o Xampp por sua praticidade. Ele pode ser baixado <a>aqui href="https://www.apachefriends.org/pt_br/download.html" target="_blank"</a>Ao baixar o Xampp, você irá baixar também o php.</li>
        <li>Após ter o php e o servidor, você deve ter o Composer em sua máquina. Ele pode ser baixado <a>aqui href="https://getcomposer.org/download/" target="_blank"</a>. Se estiver usando Windows, basta baixar o executável e realizar a instalação. Csao use uma distribuição Linux, siga o passo a passo disponível no site.</li>
        <li>Agora que o Xampp está baixado e isntalado, você deve garantir que o projeto esteja acessível dentro do servidor. Para isso, você deve encontrar a pasta do xampp no local que ele foi instalado (no disco local C, por exemplo). Dentro da pasta do xampp, encontre o diretório htdocs e coloque a pasta do projeto dentro desse diretório.</li>
        <li>O próximo passo é instalar o composer dentro do seu projeto. Utilize um terminal para acessar a pasta do projeto. Se o xampp foi instalado no disco local C, por exemplo, o caminho para o projeto será algo como C:\xampp\htdocs\atelie. Agora acesse a pasta _core e certifique-se que dentro dela há o arquivo composer.json. Caso esteja tudo certo, basta rodar o comando: composer install. Isso deve baixar todas as bibliotecas necessárias. Em caso de ocorrer um erro ao baixar as bibliotecas, tente executar o comando: composer install --ignore-platform-reqs</li>
        <li>Agora que o composer está dentro do projeto, o próximo passo é ligar o seu servidor local. Abra o xampp e clique em start no apache e no mysql. Com os dois iniciados, acesse o phpMyAdmin clicando na caixa admin em frente ao MySql do xampp, ou acesse o link <a>http://localhost/phpmyadmin/ hreh="http://localhost/phpmyadmin/" target="_blank"</a>. De volta ao projeto, observe o aquivo .env.example que fica na raiz e renomeie-o para .env apenas. Esse arquivo possui as configurações de conexão com o banco de dados. Crie um banco com o nome que estiver no campo DB_DATABASE, ele contém o nome do banco que será buscado no phpMyAdmin. Você pode dar o nome que quiser ao banco, desde que o nome dentro do arquivo .env seja o mesmo</li>
        <li>Com o banco criado, acesse a pasta db do projeto. Ela contém o arquivo base.sql. Você pode copiar e colar o conteúdo desse arquivo dentro do campo SQL do phpMyAdmin. Certifique-se de estar dentro do banco que você criou antes de executar para que não haja erro.</li>
        <li>Agora é só acessar o projeto através de localhost/atelie/gg. Você cairá em uma tela de login e os dados de acesso por padrão são: login = admin e senha = 123. Esses dados estarão criados na tabela de pessoas.</li>
    </ul>

</div>


