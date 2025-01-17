<?php

$string['pluginname'] = 'Login Único';
$string['auth_uniquelogintitle'] = 'Login único';
$string['auth_uniquelogerror'] = 'Já existe uma sessão ativa pelo que não é possível autenticar-se';
$string['auth_uniquelogindescription'] = 'Este plugin faz com que cada utilizador apenas possa ter uma sessão activa simultaneamente.<br /><br />Sempre que um utilizador fizer login com sucesso, todas as sessões desse utilizador que estejam activas são automaticamente terminadas.<br><br /><div style=\"font-weight: bold;\">Nota 1: Este plugin implica que as sessões dos utilizadores sejam guardadas em base de dados. Essa configuração pode ser feita na secção Gestão de sessões.</div><br />';
$string['aplly_to_admin'] = 'Aplicar Administrador';
$string['configaplly_to_admin'] = 'Aplicar a restrição de login único quando o utilizador tem um papel de Administrador no contexto de sistema';
$string['aplly_to_teacher'] = 'Aplicar Docente';
$string['configaplly_to_teacher'] = 'Aplicar a restrição de login único quando o utilizador tem um papel de professor em alguma disciplina do Moodle';
?>
