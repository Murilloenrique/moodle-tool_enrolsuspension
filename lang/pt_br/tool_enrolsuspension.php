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

/**
 * Strings de idioma em português do Brasil.
 *
 * @package    tool_enrolsuspension
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Plugin.
$string['pluginname'] = 'Controle de Suspensões de Inscrições';

// Permissões.
$string['enrolsuspension:manage'] = 'Gerenciar suspensões de inscrições';

// Menu.
$string['navigationcategory'] = 'Controle de Suspensões';
$string['dashboard'] = 'Dashboard';
$string['history'] = 'Histórico';

// Botões.
$string['search'] = 'Buscar';
$string['next'] = 'Próximo';
$string['suspend'] = 'Suspender';
$string['reactivate'] = 'Reativar';

// Campos do formulário.
$string['searchuser'] = 'Pesquisar aluno';
$string['searchusers'] = 'Pesquisar alunos';
$string['searchusersplaceholder'] = 'Digite nome, e-mail, usuário ou matrícula';
$string['selectedusers'] = 'Alunos selecionados';
$string['reason'] = 'Motivo';

// Mensagens.
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
$string['nocoursesfound'] = 'Nenhuma disciplina foi encontrada para os alunos selecionados.';
$string['shortnamecourse'] = 'Nome breve';
$string['enrolledselectedusers'] = 'Alunos matriculados';
$string['enrolledlabel'] = 'Matriculados';
$string['notenrolledlabel'] = 'Não matriculados';
$string['suspendallcurrentcourses'] = 'Suspender em todas as disciplinas atuais dos alunos selecionados';
$string['selectatleastonecourse'] = 'Selecione pelo menos uma disciplina.';
$string['suspensiondetails'] = 'Detalhes da suspensão';
$string['permanentsuspensionnotice'] = 'A suspensão será permanente e somente será removida por uma reativação manual.';
$string['review'] = 'Revisar';
$string['summaryitem'] = 'Item';
$string['details'] = 'Detalhes';
$string['usercoursepairstoaffect'] = 'Combinações aluno-disciplina que serão afetadas';
$string['enrolmentlinkstoaffect'] = 'Vínculos de inscrição que serão suspensos';
$string['enrolmentdetails'] = 'Detalhes dos vínculos de inscrição';
$string['enrolmentmethod'] = 'Método de inscrição';
$string['overlappingenrolmentsnotice'] = 'Foram encontrados {$a->links} vínculos ativos para {$a->pairs} combinação(ões) aluno-disciplina. Isso acontece quando o mesmo aluno está inscrito na mesma disciplina por mais de um método. Todos os vínculos ativos precisam ser suspensos para bloquear o acesso.';
$string['enrolmentstoaffect'] = 'Vínculos de inscrição que serão suspensos';
$string['suspensiontype'] = 'Tipo de suspensão';
$string['permanent'] = 'Permanente';
$string['confirmsuspension'] = 'Confirmar suspensão';
$string['suspensionsuccess'] = '{$a} vínculo(s) de inscrição suspenso(s) com sucesso.';
$string['suspensionsskipped'] = '{$a} vínculo(s) de inscrição não pôde(puderam) ser alterado(s).';
$string['enrolpluginunavailable'] = 'O método de inscrição {$a} não está disponível.';
$string['reactivationsuccess'] = '{$a} vínculo(s) de inscrição reativado(s) com sucesso.';
$string['activesuspensions'] = 'Suspensões ativas';
$string['reactivatedsuspensions'] = 'Suspensões reativadas';
$string['nohistoryrecords'] = 'Nenhum registro foi encontrado.';
$string['suspendedon'] = 'Suspenso em';
$string['performedby'] = 'Realizado por';
$string['reactivateselected'] = 'Reativar selecionados';
$string['importcsv'] = 'Importar CSV';
$string['csvfile'] = 'Arquivo CSV ou TXT';
$string['csvinstructions'] = 'Use uma coluna com nome de usuário, e-mail ou matrícula. O cabeçalho é opcional.';
$string['csvusersfound'] = '{$a} aluno(s) localizado(s) no arquivo.';
$string['csvnousers'] = 'Nenhum aluno correspondente foi localizado no arquivo.';
$string['coursesuspensions'] = 'Suspensões';
$string['privacy:metadata:table'] = 'Registros de suspensão e reativação de inscrições.';
$string['privacy:metadata:userid'] = 'O usuário cuja inscrição foi suspensa.';
$string['privacy:metadata:courseid'] = 'A disciplina associada à suspensão.';
$string['privacy:metadata:reason'] = 'O motivo informado para a suspensão.';
$string['privacy:metadata:createdby'] = 'O usuário que realizou a suspensão.';
$string['privacy:metadata:timecreated'] = 'A data em que a suspensão foi realizada.';
