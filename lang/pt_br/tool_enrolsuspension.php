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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/** Strings de idioma em português do Brasil. @package tool_enrolsuspension */
defined('MOODLE_INTERNAL') || die();

$string['activesuspensions'] = 'Suspensões ativas';
$string['alreadymanagedsuspension'] = 'Já existe uma suspensão ativa do plugin para um dos vínculos exatos de inscrição.';
$string['availablecourses'] = 'Disciplinas encontradas';
$string['confirmsuspension'] = 'Confirmar suspensão';
$string['continuewithfound'] = 'Continuar com os alunos identificados de forma única';
$string['coursesuspensions'] = 'Suspensões';
$string['csvambiguous'] = 'Ambíguos';
$string['csvambiguouscount'] = 'Valores ambíguos: {$a}';
$string['csvdelimiter'] = 'Delimitador';
$string['csvdelimitercomma'] = 'Vírgula';
$string['csvdelimitersemicolon'] = 'Ponto e vírgula';
$string['csvdelimitertab'] = 'Tabulação';
$string['csvfile'] = 'Arquivo CSV ou TXT';
$string['csvfoundcount'] = 'Alunos identificados de forma única: {$a}';
$string['csvinstructions'] = 'O arquivo deve conter exatamente uma coluna com cabeçalho obrigatório: ' .
    'username, email ou idnumber. No máximo 1.000 valores são processados por envio.';
$string['csvinvalidheader'] = 'Cabeçalho CSV inválido. Use exatamente uma coluna chamada username, email ou idnumber.';
$string['csvmissing'] = 'Não encontrados';
$string['csvmissingcount'] = 'Valores não encontrados: {$a}';
$string['csvnousers'] = 'Nenhum aluno foi identificado de forma única.';
$string['csvpreview'] = 'Pré-visualização da importação';
$string['csvtoomanymatched'] = 'Foram encontrados {$a} alunos únicos. Reduza o arquivo para no máximo 500 alunos por operação.';
$string['dashboard'] = 'Dashboard';
$string['deletedcoursefallback'] = 'Disciplina excluída/indisponível #{$a}';
$string['deleteduserfallback'] = 'Usuário excluído/indisponível #{$a}';
$string['details'] = 'Detalhes';
$string['enrolledlabel'] = 'Matriculados';
$string['enrolledselectedusers'] = 'Alunos matriculados';
$string['enrolmentdetails'] = 'Detalhes dos vínculos de inscrição';
$string['enrolmentlinkstoaffect'] = 'Vínculos exatos de inscrição';
$string['enrolmentmethod'] = 'Método de inscrição';
$string['enrolmentstateunchanged'] = 'O Moodle não alterou o estado esperado da inscrição. A operação foi revertida.';
$string['enrolsuspension:import'] = 'Importar alunos para suspensão';
$string['enrolsuspension:manage'] = 'Legado: gerenciar suspensões de inscrições';
$string['enrolsuspension:reactivate'] = 'Reativar inscrições suspensas';
$string['enrolsuspension:suspend'] = 'Pesquisar alunos e suspender inscrições';
$string['enrolsuspension:view'] = 'Visualizar histórico de suspensões';
$string['exporthistory'] = 'Exportar histórico atual em CSV';
$string['history'] = 'Histórico';
$string['idnumberlabel'] = 'Matrícula';
$string['importcsv'] = 'Importar CSV/TXT';
$string['minimumcharacters'] = 'Digite pelo menos 2 caracteres.';
$string['navigationcategory'] = 'Controle de Suspensões';
$string['next'] = 'Próximo';
$string['noactiveenrolments'] = 'Não existem vínculos de inscrição atualmente ativos para esta seleção.';
$string['nocoursesfound'] = 'Nenhuma disciplina atualmente ativa foi encontrada para os alunos selecionados.';
$string['nohistoryrecords'] = 'Nenhum registro foi encontrado.';
$string['notenrolledlabel'] = 'Não matriculados';
$string['nousersfound'] = 'Nenhum aluno foi encontrado.';
$string['nousersselected'] = 'Nenhum aluno foi selecionado.';
$string['operationalreadyused'] = 'Esta operação já foi enviada ou está sendo processada.';
$string['operationcontainsunsupported'] = 'A operação revisada contém um método de inscrição não suportado.';
$string['operationexpired'] = 'Esta operação de suspensão expirou. Inicie uma nova operação.';
$string['operationgenericerror'] = 'A operação não pôde ser concluída. Nenhuma suspensão parcial foi confirmada.';
$string['operationlocked'] = 'Esta operação não pode mais ser editada.';
$string['operationnotready'] = 'A operação não está pronta para confirmação.';
$string['operationstatechanged'] = 'O estado da inscrição mudou após a revisão. Nenhuma alteração foi aplicada. ' .
    'Revise a operação novamente.';
$string['performedby'] = 'Realizado por';
$string['permanent'] = 'Permanente até reativação manual';
$string['permanentsuspensionnotice'] = 'A suspensão não possui vencimento automático. A reativação é manual.';
$string['pluginname'] = 'Controle de Suspensões de Inscrições';
$string['postrequired'] = 'Esta ação aceita somente requisições POST.';
$string['privacy:metadata:activekey'] = 'Marcador interno de unicidade de uma suspensão ativa gerenciada pelo plugin.';
$string['privacy:metadata:claimtoken'] = 'Token aleatório usado para tornar a confirmação de uso único.';
$string['privacy:metadata:consumedat'] = 'A data e hora em que o fluxo foi confirmado e consumido.';
$string['privacy:metadata:courseid'] = 'A disciplina associada à inscrição.';
$string['privacy:metadata:courseids'] = 'As disciplinas selecionadas no fluxo temporário.';
$string['privacy:metadata:createdby'] = 'O usuário que criou ou executou a operação.';
$string['privacy:metadata:enrolid'] = 'O identificador da instância de inscrição.';
$string['privacy:metadata:enroltype'] = 'O método de inscrição associado ao vínculo congelado.';
$string['privacy:metadata:expiresat'] = 'A data e hora em que um fluxo temporário não confirmado expira.';
$string['privacy:metadata:forcedcourseid'] = 'A disciplina que delimitou uma operação iniciada pelo atalho do curso.';
$string['privacy:metadata:operation'] = 'Operações temporárias do fluxo criadas por operadores de suspensão.';
$string['privacy:metadata:operationid'] = 'A operação de fluxo associada ao registro de auditoria.';
$string['privacy:metadata:operationitem'] = 'Vínculos exatos de inscrição congelados para revisão da operação.';
$string['privacy:metadata:operationstatus'] = 'O estado atual do fluxo temporário.';
$string['privacy:metadata:operationtoken'] = 'Token aleatório usado para identificar o fluxo temporário.';
$string['privacy:metadata:operationuser'] = 'Alunos selecionados para um fluxo temporário de suspensão.';
$string['privacy:metadata:reactivatedby'] = 'O usuário que reativou ou reconciliou o registro.';
$string['privacy:metadata:reason'] = 'O motivo administrativo informado para a suspensão.';
$string['privacy:metadata:status'] = 'O status atual do registro de auditoria.';
$string['privacy:metadata:supported'] = 'Indica se o método de inscrição era suportado no momento da revisão.';
$string['privacy:metadata:supportreason'] = 'O motivo pelo qual um método de inscrição não pôde ser gerenciado.';
$string['privacy:metadata:table'] = 'Registros de auditoria de suspensão e reativação de inscrições.';
$string['privacy:metadata:timecreated'] = 'A data e hora em que o registro foi criado.';
$string['privacy:metadata:timemodified'] = 'A última data e hora de alteração do fluxo temporário.';
$string['privacy:metadata:timereactivated'] = 'A data e hora da reativação ou reconciliação.';
$string['privacy:metadata:userenrolmentid'] = 'O identificador exato do vínculo de inscrição do usuário.';
$string['privacy:metadata:userid'] = 'O usuário cuja inscrição é afetada.';
$string['reactivate'] = 'Reativar';
$string['reactivatedsuspensions'] = 'Suspensões reativadas';
$string['reactivateselected'] = 'Reativar selecionados';
$string['reactivationalreadychanged'] = 'Registro {$a}: o vínculo original já havia mudado de estado. ' .
    'O item do histórico foi marcado como obsoleto.';
$string['reactivationgenericerror'] = 'Registro {$a}: a reativação falhou. ' .
    'Os detalhes técnicos foram enviados ao modo de depuração.';
$string['reactivationinfo'] = 'Reativação/alteração de status';
$string['reactivationsskipped'] = '{$a} vínculo(s) não foram reativados. Verifique o histórico e o método de inscrição.';
$string['reactivationstale'] = 'Registro {$a}: o vínculo original não existe mais. O item do histórico foi marcado como obsoleto.';
$string['reactivationsuccess'] = '{$a} vínculo(s) exato(s) de inscrição reativado(s) com sucesso.';
$string['reason'] = 'Motivo';
$string['reasonrequired'] = 'Informe o motivo da suspensão.';
$string['reasontoolong'] = 'O motivo deve conter no máximo 1.000 caracteres.';
$string['removeuser'] = 'Remover aluno';
$string['review'] = 'Revisar';
$string['search'] = 'Buscar';
$string['searching'] = 'Pesquisando...';
$string['searchresults'] = 'Alunos encontrados';
$string['searchuser'] = 'Pesquisar aluno';
$string['searchusers'] = 'Pesquisar alunos';
$string['searchusersplaceholder'] = 'Digite nome, e-mail, usuário ou matrícula';
$string['selectatleastonecourse'] = 'Selecione pelo menos uma disciplina.';
$string['selectatleastoneuser'] = 'Selecione pelo menos um aluno.';
$string['selectcourses'] = 'Selecionar disciplinas';
$string['selectedusers'] = 'Alunos selecionados';
$string['selectuser'] = 'Selecionar';
$string['selectusersfirst'] = 'Primeiro selecione um ou mais alunos.';
$string['shortnamecourse'] = 'Nome breve';
$string['stalesuspensions'] = 'Registros obsoletos';
$string['summaryitem'] = 'Item';
$string['supported'] = 'Suportado';
$string['supportreason_instancedisabled'] = 'a instância de inscrição está desabilitada';
$string['supportreason_methodnotallowlisted'] = 'o método de inscrição não está na lista explícita de métodos suportados';
$string['supportreason_methodprotected'] = 'o método de inscrição não permite gerenciamento manual';
$string['supportreason_missingmethodcapability'] = 'o operador não possui a permissão de gerenciamento do método de inscrição';
$string['supportreason_pluginunavailable'] = 'o plugin de inscrição está indisponível ou desabilitado';
$string['supportstatus'] = 'Status de gerenciamento';
$string['suspend'] = 'Suspender';
$string['suspendallcurrentcourses'] = 'Suspender em todas as disciplinas atualmente ativas dos alunos selecionados';
$string['suspendedon'] = 'Suspenso em';
$string['suspensiondetails'] = 'Detalhes da suspensão';
$string['suspensionsuccess'] = '{$a} vínculo(s) exato(s) de inscrição suspenso(s) com sucesso.';
$string['suspensiontype'] = 'Tipo de suspensão';
$string['taskcleanupoperations'] = 'Limpar operações de suspensão expiradas';
$string['toomanycourses'] = 'No máximo 500 disciplinas podem ser processadas em uma única operação.';
$string['toomanyusers'] = 'No máximo 500 alunos podem ser processados em uma única operação.';
$string['unknownoperator'] = 'Operador desconhecido/excluído';
$string['unsupportedmethodreason'] = 'Não suportado ({$a})';
$string['unsupportedoperationblocked'] = 'Esta operação não pode ser confirmada porque um ou mais vínculos ativos são ' .
    'gerenciados por um método que não permite alterações manuais. Isso evita que uma fonte de sincronização reverta ' .
    'silenciosamente a suspensão.';
$string['usercoursepairstoaffect'] = 'Combinações aluno-disciplina';
