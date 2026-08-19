# Changelog


## 1.1.15-beta - 2026-08-19

- Corrige as violações restantes do Moodle Code Checker no histórico.
- Corrige PHPDoc de `history_manager::operation_label()`.
- Mantém o módulo AMD `course_selector` pronto para recompilação com o Grunt oficial do Moodle.

## 1.1.14-beta - 2026-08-18

- Replaces raw workflow IDs in the history with stable sequential operation codes (`OP-0001`, `OP-0002`, ...).
- Keeps the same operation code on every row created by the same batch suspension.
- Adds the internal operation ID as a separate Excel export column for audit traceability.


## 1.1.13-beta - 2026-08-18

- O histórico passa a abrir em uma visão completa, sem esconder registros reativados ou obsoletos em outra aba por padrão.
- Adiciona coluna de status, operação e método de inscrição no histórico.
- Persiste o tipo de inscrição (`manual`, `self`, etc.) no registro de auditoria para preservar a informação mesmo após alterações futuras na instância.
- Substitui a exportação CSV por arquivo Excel `.xlsx` usando a Dataformat API nativa do Moodle.
- Explica na própria tela o significado de registro obsoleto.
- Após uma reativação, retorna à visão completa do histórico para evitar a impressão de que cursos/registros desapareceram.
- Adiciona teste de regressão com dois alunos em cursos diferentes, validando suspensão, reativação e permanência dos dois cursos no histórico.

## 1.1.12-beta - 2026-08-18

- Ao marcar a suspensão em todas as disciplinas ativas, todas as disciplinas exibidas são selecionadas automaticamente.
- Os checkboxes individuais ficam bloqueados enquanto a opção geral estiver marcada e voltam a ficar disponíveis ao desmarcá-la.

## 1.1.11-beta - 2026-08-18

- Corrige a identificação visual de alunos matriculados na tela de seleção de disciplinas quando IDs retornam do banco como strings.
- Normaliza IDs de usuários para inteiros no mapa de disciplinas, mantendo comparações estritas e consistentes.


## 1.1.7-beta - 2026-08-18

- Corrige atualização de instalações existentes ao adicionar capabilities granulares.
- Remove atribuição prematura de capabilities dentro de `db/upgrade.php`.
- Usa `clonepermissionsfrom` em `db/access.php` para preservar permissões da capability legada `manage` durante o upgrade.
- Mantém a correção PSR-12 da tela de revisão validada pelo Moodle Plugin CI.

## 1.1.4-beta — 2026-08-17

- Corrige o padrão de cabeçalho/licença exigido pelo Moodle Code Checker.
- Corrige a formatação PSR-2/Moodle de chamadas multilinha em `courses.php`.

## 1.1.1-beta - 2026-08-17

- Renamed all plugin-owned database tables to use the full `tool_enrolsuspension` Frankenstyle prefix.
- Added an upgrade migration that preserves existing audit and workflow data while renaming tables.
- Recreated workflow foreign keys after table migration.
- Fixed strict PHPUnit user ID type assertions.
- Addresses the Moodle Plugin CI `validate` table-prefix failure.

## 1.1.0-beta - 2026-08-17

- Isola cada fluxo de suspensão em uma operação identificada por token aleatório.
- Torna a confirmação de suspensão de uso único e revalida os vínculos exatos revisados.
- Impede mais de uma suspensão ativa do plugin para o mesmo `user_enrolments.id`.
- Reativa somente o vínculo histórico original e marca registros obsoletos quando ele não existe mais.
- Restringe alterações aos métodos de inscrição `manual` e `self`, respeitando `allow_manage()` e capabilities do método.
- Completa os principais contratos da Privacy API, incluindo `core_userlist_provider`.
- Unifica a definição de inscrição ativa entre seleção e execução.
- Adiciona paginação e exportação CSV ao histórico.
- Reescreve a importação com `csv_import_reader`, consultas em lote e detecção de ambiguidades.
- Separa capabilities de visualização, suspensão, reativação e importação e declara riscos apropriados.
- Exige POST e `sesskey` nos endpoints que alteram estado.
- Melhora mensagens de falha parcial na reativação.
- Adiciona testes PHPUnit iniciais e workflow de Moodle Plugin CI para Moodle 4.4 e 4.5.
- Adiciona tarefa agendada para limpar operações temporárias expiradas.
