# TCC_Andiara_EKL
    Uma plataforma de gestão de projetos na indústria, especialmente projetada para empresas que terceirizam a execução de serviços específicos, com a necessidade de uma ferramenta dedicada ao controle eficiente de contratos.
    
    Acesso disponível em: https://andiara.willcode.tech 
    
# Requisitos para instalação
    - MySQL versão 8
    - PHP versão 8 
    - WebServer Apache ou Nginx
    - Linux Ubuntu (pode ser utilizado Lamp Server) ou Windows (pode ser utilizado Wamp Server)

# Instruções para subir o ambiente
    1 - Aponte o virtual host para o diretório "public", este será o diretório acessível via navegador
        Utilizando Apache e Ubuntu como exemplo, o arquivo de configuração do virtual host deve ser semelhante à configuração abaixo, 
        alterando os parâmetros "ServerName", "DocumentRoot", "Directory" e "php_value auto_prepend_file" de acordo com a sua estrutura de arquivos:

        <VirtualHost *:80>
            # The ServerName directive sets the request scheme, hostname and port that
            # the server uses to identify itself. This is used when creating
            # redirection URLs. In the context of virtual hosts, the ServerName
            # specifies what hostname must appear in the request's Host: header to
            # match this virtual host. For the default virtual host (this file) this
            # value is not decisive as it is used as a last resort host regardless.
            # However, you must set it for any further virtual host explicitly.
            #ServerName www.example.com

            ServerName projeto.andiara.local
            ServerAdmin webmaster@localhost
            DocumentRoot /var/www/html/projeto.andiara.local/tcc_andiara/public

            # Available loglevels: trace8, ..., trace1, debug, info, notice, warn,
            # error, crit, alert, emerg.
            # It is also possible to configure the loglevel for particular
            # modules, e.g.
            #LogLevel info ssl:warn

            ErrorLog ${APACHE_LOG_DIR}/error.log
            CustomLog ${APACHE_LOG_DIR}/access.log combined

            # For most configuration files from conf-available/, which are
            # enabled or disabled at a global level, it is possible to
            # include a line for only one particular virtual host. For example the
            # following line enables the CGI configuration for this host only
            # after it has been globally disabled with "a2disconf".
            #Include conf-available/serve-cgi-bin.conf

            <Directory "/var/www/html/projeto.andiara.local/tcc_andiara/public">
                Options Indexes FollowSymLinks MultiViews
                    Options FollowSymLinks MultiViews
                AllowOverride All
                Require all granted
                php_value auto_prepend_file /var/www/html/projeto.andiara.local/tcc_andiara/includes/config.php
            </Directory>
        </VirtualHost>

    2 - Altere as configurações no arquivo config.php no diretório "includes"
        * Preencha corretamente os parâmetros para conexão com o banco de dados na variável $db_config
        * Altere os caminhos de referência das constantes "TEMPLATES_DIR" e "UPLOAD_FILES_DIR" para sua estrutura de pastas correta
        
    3 - Configuração do arquivo ".htaccess" 
        * Altere os diretórios dos arquivos rereferênciados para sua estrutura de pastas 
