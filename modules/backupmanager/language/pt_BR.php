<?php


$l["backupmanager"]='Gerente de Backup';
$l["rmachine"]='Servidor remoto';
$l["rport"]='Porta';
$l["rtarget"]='Pasta de destino';
$l["sources"]='Pastas de origem';
$l["rotations"]='Rotação';
$l["quiet"]='Silencioso';
$l["emailaddresses"]='Endereço de email';
$l["emailsubject"]='Assunto do email';
$l["rhomedir"]='Home Remoto';
$l["rpassword"]='Senha';
$l["publish"]='Publicar';
$l["enablebackup"]='Iniciar backup';
$l["disablebackup"]='Parar backup';
$l["successdisabledbackup"]='O backup foi desabilitado!';
$l["publishkey"]='Habilitar backup';
$l["publishSuccess"]='O backup foi habilitado.';
$l["helpText"]='Este módulo realiza backup dos arquivos e da base de dados MySQL. (se você incluir /home/mysqlbackup nas pastas de origem) para um servidor remoto usando rsync ou SSH. Quando o backup for habilitado, ele irá transferir uma chave SSH pública para o servidor remoto e verificar se a pasta de destino existe. Por padrão, o backup é agendado para a meia noite em /etc/cron.d/groupoffice-backup. Você pode ajustar o horário ou criar o arquivo se ele não existir. Você também pode rodar o script manual no terminal com "php /usr/share/groupoffice/modules/backupmanager/cron.php"';
$l['name']='Gerente de Backup';
$l['description']='Configura a tarefa de backup';
$l['save_error']='Erro ao salvar a configuração';
$l['empty_key']='A chave está vazia';
$l['connection_error']='Não foi possível conectar ao servidor';
$l['no_mysql_config']='{product_name} não encontrou o arquivo de configuração do mysql. Este arquivo é usado para criar um cópia completa do banco de dados. Você pode cria-lo adicionando um arquivo com nome backupmanager.inc.php em /etc/groupoffice/ com o seguinte conteúdo:;
    <br /><br />&lt;?php<br />;
    $bm_config[\'mysql_user\'] = \'\';<br />;
    $bm_config[\'mysql_pass\'] = \'\';<br />
    <br /><br />;
    Sem este arquivo apenas o backup dos arquivos sem o banco de dados será feito.';
$l['target_does_not_exist']='A pasta de destino não existe!';
