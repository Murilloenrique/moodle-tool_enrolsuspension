<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/** Strings de idioma em português do Brasil. @package tool_enrolsuspension */
defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Controle de Suspensões de Inscrições';
$string['navigationcategory'] = 'Controle de Suspensões';
$string['dashboard'] = 'Dashboard';
$string['history'] = 'Histórico';
$string['enrolsuspension:manage'] = 'Legado: gerenciar suspensões de inscrições';
$string['enrolsuspension:view'] = 'Visualizar histórico de suspensões';
$string['enrolsuspension:suspend'] = 'Pesquisar alunos e suspender inscrições';
$string['enrolsuspension:reactivate'] = 'Reativar inscrições suspensas';
$string['enrolsuspension:import'] = 'Importar alunos para suspensão';
$string['search'] = 'Buscar';
$string['next'] = 'Próximo';
$string['suspend'] = 'Suspender';
$string['reactivate'] = 'Reativar';
$string['searchuser'] = 'Pesquisar aluno';
$string['searchusers'] = 'Pesquisar alunos';
$string['searchusersplaceholder'] = 'Digite nome, e-mail, usuário ou matrícula';
$string['selectedusers'] = 'Alunos selecionados';
$string['reason'] = 'Motivo';
$string['searchresults'] = 'Alunos encontrados';
$string['nousersfound'] = 'Nenhum aluno foi encontrado.';
$string['minimumcharacters'] = 'Digite pelo menos 2 caracteres.';
$string['searching'] = 'Pesquisando...';
$string['removeuser'] = 'Remover aluno';
$string['nousersselected'] = 'Nenhum aluno foi selecionado.';
$string['selectuser'] = 'Selecionar';
$string['idnumberlabel'] = 'Matrícula';
$string['selectatleastoneuser'] = 'Selecione pelo menos um aluno.';
$string['selectusersfirst'] = 'Primeiro selecione um ou mais alunos.';
$string['selectcourses'] = 'Selecionar disciplinas';
$string['availablecourses'] = 'Disciplinas encontradas';
$string['nocoursesfound'] = 'Nenhuma disciplina atualmente ativa foi encontrada para os alunos selecionados.';
$string['shortnamecourse'] = 'Nome breve';
$string['enrolledselectedusers'] = 'Alunos matriculados';
$string['enrolledlabel'] = 'Matriculados';
$string['notenrolledlabel'] = 'Não matriculados';
$string['suspendallcurrentcourses'] = 'Suspender em todas as disciplinas atualmente ativas dos alunos selecionados';
$string['selectatleastonecourse'] = 'Selecione pelo menos uma disciplina.';
$string['suspensiondetails'] = 'Detalhes da suspensão';
$string['permanentsuspensionnotice'] = 'A suspensão não possui vencimento automático. A reativação é manual.';
$string['reasonrequired'] = 'Informe o motivo da suspensão.';
$string['review'] = 'Revisar';
$string['summaryitem'] = 'Item';
$string['details'] = 'Detalhes';
$string['usercoursepairstoaffect'] = 'Combinações aluno-disciplina';
$string['enrolmentlinkstoaffect'] = 'Vínculos exatos de inscrição';
$string['enrolmentdetails'] = 'Detalhes dos vínculos de inscrição';
$string['enrolmentmethod'] = 'Método de inscrição';
$string['supportstatus'] = 'Status de gerenciamento';
$string['supported'] = 'Suportado';
$string['unsupportedoperationblocked'] = 'Esta operação não pode ser confirmada porque um ou mais vínculos ativos são ' .
    'gerenciados por um método que não permite alterações manuais. Isso evita que uma fonte de sincronização reverta ' .
    'silenciosamente a suspensão.';
$string['unsupportedmethodreason'] = 'Não suportado ({$a})';
$string['suspensiontype'] = 'Tipo de suspensão';
$string['permanent'] = 'Permanente até reativação manual';
$string['confirmsuspension'] = 'Confirmar suspensão';
$string['suspensionsuccess'] = '{$a} vínculo(s) exato(s) de inscrição suspenso(s) com sucesso.';
$string['reactivationsuccess'] = '{$a} vínculo(s) exato(s) de inscrição reativado(s) com sucesso.';
$string['reactivationsskipped'] = '{$a} vínculo(s) não foram reativados. Verifique o histórico e o método de inscrição.';
$string['reactivationstale'] = 'Registro {$a}: o vínculo original não existe mais. O item do histórico foi marcado como obsoleto.';
$string['reactivationalreadychanged'] = 'Registro {$a}: o vínculo original já havia mudado de estado. ' .
    'O item do histórico foi marcado como obsoleto.';
$string['reactivationgenericerror'] = 'Registro {$a}: a reativação falhou. ' .
    'Os detalhes técnicos foram enviados ao modo de depuração.';
$string['activesuspensions'] = 'Suspensões ativas';
$string['reactivatedsuspensions'] = 'Suspensões reativadas';
$string['stalesuspensions'] = 'Registros obsoletos';
$string['nohistoryrecords'] = 'Nenhum registro foi encontrado.';
$string['suspendedon'] = 'Suspenso em';
$string['performedby'] = 'Realizado por';
$string['reactivationinfo'] = 'Reativação/alteração de status';
$string['reactivateselected'] = 'Reativar selecionados';
$string['deleteduserfallback'] = 'Usuário excluído/indisponível #{$a}';
$string['deletedcoursefallback'] = 'Disciplina excluída/indisponível #{$a}';
$string['unknownoperator'] = 'Operador desconhecido/excluído';
$string['importcsv'] = 'Importar CSV/TXT';
$string['csvfile'] = 'Arquivo CSV ou TXT';
$string['csvinstructions'] = 'O arquivo deve conter exatamente uma coluna com cabeçalho obrigatório: ' .
    'username, email ou idnumber. No máximo 1.000 valores são processados por envio.';
$string['csvdelimiter'] = 'Delimitador';
$string['csvdelimitercomma'] = 'Vírgula';
$string['csvdelimitersemicolon'] = 'Ponto e vírgula';
$string['csvdelimitertab'] = 'Tabulação';
$string['csvinvalidheader'] = 'Cabeçalho CSV inválido. Use exatamente uma coluna chamada username, email ou idnumber.';
$string['csvpreview'] = 'Pré-visualização da importação';
$string['csvfoundcount'] = 'Alunos identificados de forma única: {$a}';
$string['csvmissingcount'] = 'Valores não encontrados: {$a}';
$string['csvambiguouscount'] = 'Valores ambíguos: {$a}';
$string['csvmissing'] = 'Não encontrados';
$string['csvambiguous'] = 'Ambíguos';
$string['csvnousers'] = 'Nenhum aluno foi identificado de forma única.';
$string['continuewithfound'] = 'Continuar com os alunos identificados de forma única';
$string['coursesuspensions'] = 'Suspensões';
$string['operationexpired'] = 'Esta operação de suspensão expirou. Inicie uma nova operação.';
$string['operationlocked'] = 'Esta operação não pode mais ser editada.';
$string['operationnotready'] = 'A operação não está pronta para confirmação.';
$string['operationalreadyused'] = 'Esta operação já foi enviada ou está sendo processada.';
$string['operationstatechanged'] = 'O estado da inscrição mudou após a revisão. Nenhuma alteração foi aplicada. ' .
    'Revise a operação novamente.';
$string['operationcontainsunsupported'] = 'A operação revisada contém um método de inscrição não suportado.';
$string['alreadymanagedsuspension'] = 'Já existe uma suspensão ativa do plugin para um dos vínculos exatos de inscrição.';
$string['enrolmentstateunchanged'] = 'O Moodle não alterou o estado esperado da inscrição. A operação foi revertida.';
$string['noactiveenrolments'] = 'Não existem vínculos de inscrição atualmente ativos para esta seleção.';
$string['postrequired'] = 'Esta ação aceita somente requisições POST.';
$string['operationgenericerror'] = 'A operação não pôde ser concluída. Nenhuma suspensão parcial foi confirmada.';
$string['privacy:metadata:table'] = 'Registros de auditoria de suspensão e reativação de inscrições.';
$string['privacy:metadata:operation'] = 'Operações temporárias do fluxo criadas por operadores de suspensão.';
$string['privacy:metadata:operationitem'] = 'Vínculos exatos de inscrição congelados para revisão da operação.';
$string['privacy:metadata:operationid'] = 'A operação de fluxo associada ao registro de auditoria.';
$string['privacy:metadata:userid'] = 'O usuário cuja inscrição é afetada.';
$string['privacy:metadata:courseid'] = 'A disciplina associada à inscrição.';
$string['privacy:metadata:enrolid'] = 'O identificador da instância de inscrição.';
$string['privacy:metadata:userenrolmentid'] = 'O identificador exato do vínculo de inscrição do usuário.';
$string['privacy:metadata:activekey'] = 'Marcador interno de unicidade de uma suspensão ativa gerenciada pelo plugin.';
$string['privacy:metadata:reason'] = 'O motivo administrativo informado para a suspensão.';
$string['privacy:metadata:status'] = 'O status atual do registro de auditoria.';
$string['privacy:metadata:createdby'] = 'O usuário que criou ou executou a operação.';
$string['privacy:metadata:timecreated'] = 'A data e hora em que o registro foi criado.';
$string['privacy:metadata:reactivatedby'] = 'O usuário que reativou ou reconciliou o registro.';
$string['privacy:metadata:timereactivated'] = 'A data e hora da reativação ou reconciliação.';

$string['toomanyusers'] = 'No máximo 500 alunos podem ser processados em uma única operação.';
$string['toomanycourses'] = 'No máximo 500 disciplinas podem ser processadas em uma única operação.';
$string['reasontoolong'] = 'O motivo deve conter no máximo 1.000 caracteres.';
$string['privacy:metadata:operationuser'] = 'Alunos selecionados para um fluxo temporário de suspensão.';
$string['csvtoomanymatched'] = 'Foram encontrados {$a} alunos únicos. Reduza o arquivo para no máximo 500 alunos por operação.';
$string['exporthistory'] = 'Exportar histórico atual em CSV';
$string['taskcleanupoperations'] = 'Limpar operações de suspensão expiradas';
$string['privacy:metadata:operationtoken'] = 'Token aleatório usado para identificar o fluxo temporário.';
$string['privacy:metadata:courseids'] = 'As disciplinas selecionadas no fluxo temporário.';
$string['privacy:metadata:forcedcourseid'] = 'A disciplina que delimitou uma operação iniciada pelo atalho do curso.';
$string['privacy:metadata:operationstatus'] = 'O estado atual do fluxo temporário.';
$string['privacy:metadata:claimtoken'] = 'Token aleatório usado para tornar a confirmação de uso único.';
$string['privacy:metadata:timemodified'] = 'A última data e hora de alteração do fluxo temporário.';
$string['privacy:metadata:expiresat'] = 'A data e hora em que um fluxo temporário não confirmado expira.';
$string['privacy:metadata:consumedat'] = 'A data e hora em que o fluxo foi confirmado e consumido.';
$string['privacy:metadata:enroltype'] = 'O método de inscrição associado ao vínculo congelado.';
$string['privacy:metadata:supported'] = 'Indica se o método de inscrição era suportado no momento da revisão.';
$string['privacy:metadata:supportreason'] = 'O motivo pelo qual um método de inscrição não pôde ser gerenciado.';
$string['supportreason_methodnotallowlisted'] = 'o método de inscrição não está na lista explícita de métodos suportados';
$string['supportreason_pluginunavailable'] = 'o plugin de inscrição está indisponível ou desabilitado';
$string['supportreason_instancedisabled'] = 'a instância de inscrição está desabilitada';
$string['supportreason_methodprotected'] = 'o método de inscrição não permite gerenciamento manual';
$string['supportreason_missingmethodcapability'] = 'o operador não possui a permissão de gerenciamento do método de inscrição';
